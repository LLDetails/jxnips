<?php

namespace App\Http\Controllers\Bid;

use App\Basket;
use App\AssignRule;
use App\BasketLog;
use App\Bid;
use App\Demand;
use App\Setting;
use App\User;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use URL;
use Validator;
use DB;
use App\Jobs\SendSms;
use App\Jobs\SendOfferSms;
use App\Jobs\SendOfferResultSms;

class DemandController extends Controller
{
    public function index()
    {

        $check_flow_setting = Setting::where('name', 'check_flow')->first()->data;
        $check_flows = json_decode($check_flow_setting, true);
        //$check_stop = $check_flows[count($check_flows) - 1]['time'];
        $check_stop = end($check_flows)['time'];
        $time = time();
        $current_time = date('H:i:s', $time);
        $today = date('Y-m-d', $time);
        $baskets = Basket::orderBy('name', 'desc');
        if ($current_time >= $check_stop) {
            $baskets = $baskets->where('name', '<=', $today);
        } else {
            $baskets = $baskets->where('name', '<', $today);
        }
        $baskets = $baskets->has('demands');
        $baskets = $baskets->get();
        $display_states = [
            'pending' => '处理中',
            'refused' => '被退回',
            'checked' => '待汇总',
            'bided' => '已发标',
            'cancelled' => '已放弃',
            'assigned' => '已分配'
        ];

        return view('bid.demand.index')
            ->with('baskets', $baskets)
            ->with('display_states', $display_states);
    }

    public function collect(Basket $basket)
    {
        $check_flow_setting = Setting::where('name', 'check_flow')->first()->data;
        $check_flows = json_decode($check_flow_setting, true);
        $check_stop = end($check_flows)['time'];
        $datetime = date('Y-m-d H:i:s');
        if ($datetime < ($basket->name.' '.$check_stop)) {
            return redirect(route('bid.demand.index'))->with('tip_message', ['content' => '当前状态不能汇总', 'state' => 'warning']);
        }

        $current_user = auth()->user();
        $demands = Demand::with(['goods', 'company']);
        $demands = $demands->where('basket_id', $basket->id);
        $demands = $demands->orderBy('goods_id', 'asc');
        $demands = $demands->get();

        return view('bid.demand.collect')
            ->with('demands', $demands)
            ->with('current_user', $current_user)
            ->with('basket', $basket);
    }

    public function view(Basket $basket)
    {
        $current_user = auth()->user();
        /*$demands = Demand::with(['goods', 'company', 'bid']);
        $demands = $demands->where('basket_id', $basket->id);
        $demands = $demands->orderBy('goods_id', 'asc');
        $demands = $demands->get();*/
        $bids = Bid::with(['demands', 'demands.company'])->whereBasketId($basket->id)->get();

        return view('bid.demand.view')
            ->with('bids', $bids)
            ->with('current_user', $current_user)
            ->with('basket', $basket);
    }

    public function generate(Request $request, Basket $basket)
    {
        $redirect_url = URL::full();

        $check_flow_setting = Setting::where('name', 'check_flow')->first()->data;
        $check_flows = json_decode($check_flow_setting, true);
        $check_stop = end($check_flows)['time'];
        $datetime = date('Y-m-d H:i:s');
        if ($datetime < ($basket->name.' '.$check_stop)) {
            return redirect($redirect_url)->with('tip_message', ['content' => '当前状态不能汇总', 'state' => 'warning']);
        }

        if ($basket->state == 'bided') {
            return redirect(route('bid.demand.index'))->with('tip_message', ['content' => '当前状态不能汇总', 'state' => 'warning']);
        }

        $current_user = auth()->user();
        $form_data = $request->only([
            'offer_start', 'offer_stop', 'type', 'demand',
            'suppliers', 'goods_ids', 'goods_codes', 'goods_statics'
        ]);

        $goods_demands = [];
        foreach ($form_data['demand'] as $goods_demand) {
            list($goods_id, $demand_id) = explode(',', $goods_demand);
            $goods_demands[$goods_id][] = $demand_id;
        }

        $rules = [];
        $messages = [
            'required' => '必填项不能为空',
            'date' => '只能为日期格式',
            'after' => '必须晚于报价开始时间',
            'in' => '非法的数据',
            'array' => '非法的数据'
        ];
        $bids_data = [];
        foreach ($form_data['goods_ids'] as $k=>$goods_id) {
            $bid_data = [
                'basket_id' => $basket->id,
                'user_id' => $current_user->id,
                'goods_id' => $goods_id,
                'goods_static' => $form_data['goods_statics'][$k]
            ];
            $rules['offer_start.' . $k] = 'required|date';
            $bid_data['offer_start'] = $form_data['offer_start'][$k];

            $rules['offer_stop.' . $k] = 'required|date|after:' . $form_data['offer_start'][$k];
            $bid_data['offer_stop'] = $form_data['offer_stop'][$k];

            $rules['type.' . $k] = 'required|in:global,invite';
            $bid_data['type'] = $form_data['type'][$k];
            if ($form_data['type'][$k] == 'invite') {
                $rules['suppliers.' . $k] = 'required|array';
                $bid_data['suppliers'] = json_encode($form_data['suppliers'][$k]);
            } else {
                $bid_data['suppliers'] = '[]';
            }

            $bid_data['code'] = 'YCH' . str_replace('-', '', $basket->name) . $form_data['goods_codes'][$k] . 'B';
            $bids_data[$k] = $bid_data;
        }

        $validator = Validator::make($form_data, $rules, $messages);
        if ($validator->fails()) {
            return redirect($redirect_url)->withErrors($validator->errors())->withInput();
        }

        $setting = Setting::where('name', 'member_auto_pass_time')->first();
        $auto_time = $setting->data;
        $tomorrow = date('Y-m-d', strtotime('+1 day'));
        //$bided_at = date('Y-m-d H:i:s', time() + $auto_time * 60);
        $bided_at = $tomorrow . ' ' . $auto_time;
        $result = DB::transaction(function() use($goods_demands, $bids_data, $bided_at, $basket) {
            $bids = [];
            foreach ($bids_data as $bid_data) {

                if ($bid_data['type'] == 'global') {
                    $time_stop = $bid_data['offer_start'];
                    $bid_data['invitation_quantity'] = User::where('created_at', '<=', $time_stop)
                        ->where('type', 'supplier')
                        ->whereHas('supplier', function($query) use($bid_data) {
                            return $query->whereRaw('\'["' . $bid_data['goods_id'] . '"]\'::jsonb <@ "goods"');
                        })
                        ->count();
                } else {
                    $bid_data['invitation_quantity'] = count(json_decode($bid_data['suppliers'], true));
                }

                $bid = Bid::create($bid_data);
                if (!$bid) {
                    DB::rollBack();
                    return false;
                }

                $bids[] = $bid;

                foreach ($goods_demands[$bid->goods_id] as $demand_id) {
                    $rst = Demand::where('id', $demand_id)->update(['bid_id' => $bid->id]);
                    if (!$rst) {
                        DB::rollBack();
                        return false;
                    }
                }
            }


            $basket->state = 'bided';
            $basket->bided_at = $bided_at;
            $basket->collected_at = date('Y-m-d H:i:s');
            if (!$basket->save()) {
                DB::rollBack();
                return false;
            }

            return $bids;
        });

        if ($result) {
            $time = time();
            $check_members = json_decode(Setting::where('name', 'check_members')->first()->data, true);
            $members = User::whereIn('id', $check_members)->get();
            $member_auto_pass_time = Setting::where('name', 'member_auto_pass_time')->first()->data;
            $tomorrow = date('Y-m-d', strtotime('+1 day'));
            $tomorrow_md = date('m月d日', strtotime('+1 day'));
            $delay_seconds = strtotime($tomorrow.' '.$member_auto_pass_time) - $time;
            $mem_phone_numbers = [];
            foreach ($members as $t=>$user) {
                if ($user->allow_login) {
                    $mem_phone_numbers[] = $user->phone;
                }
                //$message = '通知：请您于'.$tomorrow_md.$member_auto_pass_time.'前登录平台审核采购计划，过时未审,系统将按计划发布标书。';
                //$this->dispatch(new SendSms($user->phone, $message));
            }
            $mem_phone_numbers = implode(',', $mem_phone_numbers);
            $mem_params = json_encode(['time' => $tomorrow_md.$member_auto_pass_time]);
            $this->dispatch(new SendSms($mem_phone_numbers, '审核标书', $mem_params));

            if (is_array($result)) {
                foreach ($result as $bid) {

                    $offer_sms_delay = strtotime($bid->offer_stop) - $time;
                    $result_job = (new SendOfferResultSms($bided_at, $bid->id, $bid->offer_stop))->delay($offer_sms_delay);
                    $this->dispatch($result_job);

                    $goods = json_decode($bid->goods_static, true);
                    $goods_name = $goods['name'];
                    //$goods_name = preg_replace('#(\d+)%#', "百分之$1", $goods_name);
                    $quantity = Demand::where('bid_id', $bid->id)->sum('quantity');
                    if ($quantity > 0) {
                        /*$offer_start = date('n/j G:i', strtotime($bid->offer_start));
                        $offer_start = str_replace(":00",'时', $offer_start);
                        $offer_stop = date('n/j G:i', strtotime($bid->offer_stop));
                        $offer_stop = str_replace(":00",'时', $offer_stop);*/
                        $offer_start = date('m月d日 H点i分', strtotime($bid->offer_start));
                        $offer_stop = date('m月d日 H点i分', strtotime($bid->offer_stop));
                        if ($bid->type == 'invite') {
                            $supplier_ids = json_decode($bid->suppliers, true);
                            $suppliers = User::whereNotNull('phone')->where('allow_login', true)->whereIn('id', $supplier_ids)->get();
                        } else {
                            $suppliers = User::whereNotNull('phone')
                                ->whereNull('deleted_at')
                                ->where('allow_login', true)
                                ->whereHas('supplier', function ($query) use ($goods) {
                                    return $query->whereRaw('\'["' . $goods['id'] . '"]\'::jsonb <@ "goods"');
                                })->get();
                        }
                        $phone_numbers = [];
                        foreach ($suppliers as $k => $user) {
                            $phone_numbers[] = $user->phone;
                            //$message = '通知：平台有'.$goods_name.'、'.strval((float)$quantity).$goods['unit'].'招标计划，请您于'.$offer_start.'—'.$offer_stop.'登陆平台进行投标。';
                        }
                        $phone_numbers = implode(',', $phone_numbers);
                        $params_arr = [];
                        $params_arr['bid'] = $goods_name . '，' . strval((float)$quantity) . $goods['unit'];
                        $params_arr['time'] = $offer_start . '-' . $offer_stop;
                        $params = json_encode($params_arr);
                        $offer_job = (new SendOfferSms($bided_at, $basket->name, $phone_numbers, '提醒投标', $params))->delay($delay_seconds);
                        $this->dispatch($offer_job);
                    }
                }
            }

            return redirect($redirect_url)->with('tip_message', ['content' => '操作成功', 'state' => 'success']);
        } else {
            return redirect($redirect_url)->withErrors(['form' => ['服务器繁忙，请稍后再试']]);
        }
    }

    public function cancel(Basket $basket)
    {
        $redirect_url = URL::previous();
        $datetime = date('Y-m-d H:i:s');
        if ($basket->state != 'refused') {
            return redirect($redirect_url)->with('tip_message', ['content' => '只有被拒绝的汇总可以被放弃', 'state' => 'warning', 'hold' => true]);
        }
        if ($basket->bided_at > $datetime) {
            return redirect($redirect_url)->with('tip_message', ['content' => '汇总审核还未结束，不能操作', 'state' => 'warning', 'hold' => true]);
        }
        $basket->state = 'cancelled';
        $basket->bided_at = null;
        if ($basket->save()) {
            return redirect(route('bid.demand.index'))->with('tip_message', ['content' => '操作成功', 'state' => 'success']);
        } else {
            return redirect($redirect_url)->with('tip_message', ['content' => '服务器繁忙，请稍后再试', 'state' => 'danger', 'hold' => true]);
        }
    }

    public function edit(Basket $basket)
    {
        $current_user = auth()->user();
        $bids = Bid::with(['demands', 'demands.company'])->whereBasketId($basket->id)->get();
        $assign_rules = AssignRule::orderBy('created_at')->whereNull('deleted_at')->get();

        $logs = BasketLog::with(['user', 'user.role', 'user.staff'])
            ->whereBasketId($basket->id)
            ->where('action', 'refuse')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('bid.demand.edit')
            ->with('bids', $bids)
            ->with('assign_rules', $assign_rules)
            ->with('basket', $basket)
            ->with('logs', $logs);
    }

    public function update(Request $request, Basket $basket)
    {
        $redirect_url = URL::full();
        $form_data = $request->only([
            'offer_start', 'offer_stop', 'type', 'bid_ids', 'suppliers'
        ]);

        $rules = [];
        $messages = [
            'required' => '必填项不能为空',
            'date' => '只能为日期格式',
            'after' => '必须晚于报价开始时间',
            'in' => '非法的数据',
            'array' => '非法的数据'
        ];
        $bids_update_data = [];
        foreach ($form_data['bid_ids'] as $k=>$bid_id) {
            $bid_update_data = [];
            $rules['offer_start.' . $k] = 'required|date';
            $bid_update_data['offer_start'] = $form_data['offer_start'][$k];

            $rules['offer_stop.' . $k] = 'required|date|after:' . $form_data['offer_start'][$k];
            $bid_update_data['offer_stop'] = $form_data['offer_stop'][$k];

            $rules['type.' . $k] = 'required|in:global,invite';
            $bid_update_data['type'] = $form_data['type'][$k];
            if ($form_data['type'][$k] == 'invite') {
                $rules['suppliers.' . $k] = 'required|array';
                $bid_update_data['suppliers'] = json_encode($form_data['suppliers'][$k]);
            } else {
                $bid_update_data['suppliers'] = '[]';
            }
            $bids_update_data[$bid_id] = $bid_update_data;
        }

        $validator = Validator::make($form_data, $rules, $messages);
        if ($validator->fails()) {
            return redirect($redirect_url)->withErrors($validator->errors())->withInput();
        }

        $setting = Setting::where('name', 'member_auto_pass_time')->first();
        $auto_time = $setting->data;
        $tomorrow = date('Y-m-d', strtotime('+1 day'));
        //$bided_at = date('Y-m-d H:i:s', time() + $auto_time * 60);
        $bided_at = $tomorrow . ' ' . $auto_time;
        $result = DB::transaction(function() use($bids_update_data, $bided_at, $basket) {
            foreach ($bids_update_data as $bid_id => $bid_update_data) {

                if ($bid_update_data['type'] == 'global') {
                    $time_stop = $bid_update_data['offer_start'];
                    $bid_update_data['invitation_quantity'] = User::where('created_at', '<=', $time_stop)
                        ->where('type', 'supplier')
                        ->whereHas('supplier', function($query) use($bid_update_data) {
                            return $query->whereRaw('\'["' . $bid_update_data['goods_id'] . '"]\'::jsonb <@ "goods"');
                        })
                        ->count();
                } else {
                    $bid_update_data['invitation_quantity'] = count(json_decode($bid_update_data['suppliers'], true));
                }

                if (!Bid::where('id', $bid_id)->update($bid_update_data)) {
                    DB::rollBack();
                    return false;
                }
            }

            $basket->state = 'bided';
            $basket->bided_at = $bided_at;
            $basket->collected_at = date('Y-m-d H:i:s');
            if (!$basket->save()) {
                DB::rollBack();
                return false;
            }

            BasketLog::where('basket_id', $basket->id)->where('action', 'accept')->delete();

            return true;
        });

        if ($result) {

            $time = time();
            $check_members = json_decode(Setting::where('name', 'check_members')->first()->data, true);
            $members = User::whereIn('id', $check_members)->get();
            $member_auto_pass_time = Setting::where('name', 'member_auto_pass_time')->first()->data;
            $tomorrow = date('Y-m-d', strtotime('+1 day'));
            $tomorrow_md = date('m月d日', strtotime('+1 day'));
            $delay_seconds = strtotime($tomorrow.' '.$member_auto_pass_time) - $time;
            $mem_phone_numbers = [];
            foreach ($members as $t=>$user) {
                $mem_phone_numbers[] = $user->phone;
            }
            $mem_phone_numbers = implode(',', $mem_phone_numbers);
            $mem_params = json_encode(['time' => $tomorrow_md.$member_auto_pass_time]);
            $this->dispatch(new SendSms($mem_phone_numbers, '审核标书', $mem_params));

            if (is_array($form_data['bid_ids'])) {
                $bids = Bid::whereIn('id', $form_data['bid_ids'])->get();
                foreach ($bids as $bid) {

                    $offer_sms_delay = strtotime($bid->offer_stop) - $time;
                    $result_job = (new SendOfferResultSms($bided_at, $bid->id, $bid->offer_stop))->delay($offer_sms_delay);
                    $this->dispatch($result_job);

                    $goods = json_decode($bid->goods_static, true);
                    $goods_name = $goods['name'];
                    //$goods_name = preg_replace('#(\d+)%#', "百分之$1", $goods_name);
                    $quantity = Demand::where('bid_id', $bid->id)->sum('quantity');
                    if ($quantity > 0) {
                        $offer_start = date('m月d日 H点i分', strtotime($bid->offer_start));
                        $offer_stop = date('m月d日 H点i分', strtotime($bid->offer_stop));
//                        $offer_start = date('n/j G:i', strtotime($bid->offer_start));
//                        $offer_start = str_replace(":00",'时', $offer_start);
//                        $offer_stop = date('n/j G:i', strtotime($bid->offer_stop));
//                        $offer_stop = str_replace(":00",'时', $offer_stop);
                        if ($bid->type == 'invite') {
                            $supplier_ids = json_decode($bid->suppliers, true);
                            $suppliers = User::whereNotNull('phone')->where('allow_login', true)->whereIn('id', $supplier_ids)->get();
                        } else {
                            $suppliers = User::whereNotNull('phone')
                                ->whereNull('deleted_at')
                                ->where('allow_login', true)
                                ->whereHas('supplier', function ($query) use ($goods) {
                                    return $query->whereRaw('\'["' . $goods['id'] . '"]\'::jsonb <@ "goods"');
                                })->get();
                        }
                        $phone_numbers = [];
                        foreach ($suppliers as $k => $user) {
                            $phone_numbers[] = $user->phone;
                        }
                        $phone_numbers = implode(',', $phone_numbers);
                        $params_arr = [];
                        $params_arr['bid'] = $goods_name . '，' . strval((float)$quantity) . $goods['unit'];
                        $params_arr['time'] = $offer_start . '-' . $offer_stop;
                        $params = json_encode($params_arr);
                        $offer_job = (new SendOfferSms($bided_at, $basket->name, $phone_numbers, '提醒投标', $params))->delay($delay_seconds);
                        $this->dispatch($offer_job);
                    }
                }
            }

            return redirect($redirect_url)->with('tip_message', ['content' => '操作成功', 'state' => 'success']);
        } else {
            return redirect($redirect_url)->withErrors(['form' => ['服务器繁忙，请稍后再试']]);
        }
    }
}
