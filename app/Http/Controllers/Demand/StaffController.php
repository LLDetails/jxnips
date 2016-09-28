<?php

namespace App\Http\Controllers\Demand;

use App\AssignRule;
use App\Basket;
use App\Demand;
use App\Goods;
use App\Role;
use App\Setting;
use App\User;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use URL;
use Validator;
use DB;

class StaffController extends Controller
{
    public function manage(Request $request)
    {
        $cond = $request->only(['date_start', 'date_stop']);
        $cond = array_map('trim', $cond);
        $baskets = Basket::orderBy('created_at', 'desc');

        if (!empty($cond['date_start'])) {
            $baskets = $baskets->where('name', '>=', $cond['date_start']);
        }
        if (!empty($cond['date_stop'])) {
            $baskets = $baskets->where('name', '<=', $cond['date_stop']);
        }
        $baskets = $baskets->paginate(10);
        $pages = $baskets->appends($cond)->render();

        $display_states = [
            'pending' => '处理中',
            'refused' => '被退回',
            'checked' => '待汇总',
            'bided' => '已发标',
            'cancelled' => '已放弃',
            'assigned' => '已分配'
        ];

        $time = time();
        $current_time = date('H:i:s');
        //$check_time = config('demand.check_time');
        $check_time = Setting::where('name', 'demand_check_time')->first()->data;
        $current_day = date('Y-m-d', $time);
        $today_start_time = strtotime($current_day.' 00:00:00');
        $tomorrow_start_time = $today_start_time + (24 * 3600) + 1;
        $tomorrow = date('Y-m-d', $tomorrow_start_time);

        return view('demand.staff.manage')
            ->with('baskets', $baskets)
            ->with('pages', $pages)
            ->with('tomorrow', $tomorrow)
            ->with('today', $current_day)
            ->with('current_time', $current_time)
            ->with('check_time', $check_time)
            ->with('display_states', $display_states);
    }

    public function add(Request $request)
    {
        $redirect_url = route('demand.staff.manage');

        $time = time();
        //$check_time = config('demand.check_time');
        $check_time = Setting::where('name', 'demand_check_time')->first()->data;
        $current_day = date('Y-m-d', $time);
        $current_time = date('H:i:s', $time);
        if ($current_time < $check_time) {
            $current_basket_name = $current_day;
        } else {
            $today_start_time = strtotime($current_day.' 00:00:00');
            $tomorrow_start_time = $today_start_time + (24 * 3600) + 1;
            $tomorrow_start_date = date('Y-m-d', $tomorrow_start_time);
            $current_basket_name = $tomorrow_start_date;
        }

        $exists_basket = Basket::where('name', $current_basket_name)->first();
        if (!empty($exists_basket)) {
            return redirect($redirect_url)->with('tip_message', ['content' => $current_basket_name.'需求清单已经存在，请直接编辑清单即可', 'state' => 'warning']);
        } else {
            $result = false;
            try {
                $result = Basket::create(['name' => $current_basket_name, 'state' => 'pending']);
            } catch (\Exception $e) {
                return redirect($redirect_url)->with('tip_message', ['content' => '服务器繁忙，请稍候再试', 'state' => 'danger']);
            }
            if ($result) {
                return redirect($redirect_url)->with('tip_message', ['content' => $current_basket_name.'需求清单已创建，请编辑清单', 'state' => 'success']);
            } else {
                return redirect($redirect_url)->with('tip_message', ['content' => '服务器繁忙，请稍候再试', 'state' => 'danger']);
            }
        }
    }

    public function demandList(Request $request, Basket $basket)
    {
        $redirect_url = route('demand.staff.manage');

        $current_user = auth()->user();
        $now = time();
        $time = date('H:i:s', $now);
        $company_id = $current_user->company_id;
        $area_id = $current_user->area_id;
        $category_id = $current_user->category_id;
        $demands = Demand::with(['goods', 'company']);
        $demands = $demands->where('basket_id', $basket->id);
        if ($request->get('orderBy') == 'company') {
            $demands = $demands->orderBy('company_id', 'asc');
        } else {
            $demands = $demands->orderBy('goods_id', 'asc');
        }

        if (!empty($company_id)) {
            $demands = $demands->where('company_id', $company_id);
        }
        if (!empty($area_id)) {
            $demands = $demands->whereHas('company', function ($query) use ($area_id) {
                return $query->where('area_id', $area_id);
            });
        }
        if (!empty($category_id)) {
            $demands = $demands->where('category_id', $category_id);
        }
        $demands = $demands->get();

        $company_id = $current_user->company_id;
        if (empty($company_id)) {
            return redirect($redirect_url)->with('tip_message', ['content' => '当前用户不属于任何公司', 'state' => 'warning']);
        }
        $today = date('Y-m-d', $now);
        $tomorrow = date("Y-m-d", strtotime("+1 day"));
        $check_date = $today;
        $check_time = Setting::where('name', 'demand_check_time')->first()->data;
        if ($time > $check_time) {
            if ($basket->name != $tomorrow) {
                return redirect($redirect_url)->with('tip_message', ['content' => '抱歉，'.$basket->name.'需求已开始审核', 'state' => 'warning']);
            }
            $check_date = $tomorrow;
        }
        $check_flow_setting = Setting::where('name', 'check_flow')->first();
        $check_flow = json_decode($check_flow_setting->data, true);
        $next_role = null;
        foreach ($check_flow as $k=>$flow) {
            if (($today. ' ' .$time < $check_date. ' ' . $flow['time']) and isset($check_flow[$k+1])) {
                if ($current_user->role_id != $flow['role_id']) {
                    return redirect($redirect_url)->with('tip_message', ['content' => '抱歉，您不在当前流程中', 'state' => 'warning']);
                }
                $next_role_id = $check_flow[$k+1]['role_id'];
                $next_role = Role::whereNull('deleted_at')->where('id', $next_role_id)->first();
                break;
            }
        }

        return view('demand.staff.list')
            ->with('demands', $demands)
            ->with('next_role', $next_role)
            ->with('basket', $basket);
    }

    public function append(Basket $basket)
    {
        $redirect_url = route('demand.staff.demand_list', ['basket'=>$basket->id]);

        $company_id = auth()->user()->company_id;
        $category_id = auth()->user()->category_id;
        if (empty($company_id)) {
            return redirect($redirect_url)->with('tip_message', ['content' => '当前用户不属于任何公司', 'state' => 'warning']);
        }
        $goods_records = Goods::orderBy('code');
        if (!empty($category_id)) {
            $goods_records = $goods_records->where('category_id', $category_id);
        }
        $goods_records = $goods_records->where('is_available', true)->whereNull('deleted_at')->get();

        $time = time();
        $current_time = date('H:i:s', $time);
        $today = date('Y-m-d', $time);
        $tomorrow = date("Y-m-d", strtotime("+1 day"));
        $check_time = Setting::where('name', 'demand_check_time')->first()->data;
        if ($current_time > $check_time) {
            if ($basket->name != $tomorrow) {
                return redirect($redirect_url)->with('tip_message', ['content' => '抱歉，'.$basket->name.'需求已开始审核', 'state' => 'warning']);
            }
        } else {
            if ($basket->name != $today) {
                return redirect($redirect_url)->with('tip_message', ['content' => '抱歉，'.$basket->name.'需求此刻不能编辑', 'state' => 'warning']);
            }
        }

        $assign_rules = AssignRule::orderBy('created_at')->whereNull('deleted_at')->get();

        return view('demand.staff.append')
            ->with('basket', $basket)
            ->with('goods_records', $goods_records)
            ->with('assign_rules', $assign_rules);
    }

    public function save(Request $request, Basket $basket)
    {
        $redirect_url = URL::full();

        $current_user = auth()->user();
        $company_id = $current_user->company_id;
        $category_id = $current_user->category_id;
        if (empty($company_id)) {
            return redirect($redirect_url)->with('tip_message', ['content' => '当前用户不属于任何公司', 'state' => 'warning']);
        }

        $time = time();
        $current_time = date('H:i:s', $time);
        $datetime = date('Y-m-d H:i:s', $time);
        $today = date('Y-m-d', $time);
        $tomorrow = date("Y-m-d", strtotime("+1 day"));
        $check_time = Setting::where('name', 'demand_check_time')->first()->data;
        if ($current_time > $check_time) {
            if ($basket->name != $tomorrow) {
                return redirect($redirect_url)->with('tip_message', ['content' => '抱歉，'.$basket->name.'需求已开始审核', 'state' => 'warning']);
            }
        } else {
            if ($basket->name != $today) {
                return redirect($redirect_url)->with('tip_message', ['content' => '抱歉，'.$basket->name.'需求此刻不能编辑', 'state' => 'warning']);
            }
        }

        $form_data = $request->only([
            'goods_id', 'quantity', 'price_floor',
            'price_caps', 'assign_rule', /*'price_validity',*/
            'delivery_date_start', 'delivery_date_stop',
            'stock', 'pending', 'monthly_demand','payment','invoice'
        ]);

        $goods_ids = Goods::orderBy('code');
        if (!empty($category_id)) {
            $goods_ids = $goods_ids->where('category_id', $category_id);
        }
        $goods_ids = $goods_ids->where('is_available', true)->whereNull('deleted_at')->lists('id')->toArray();
        $goods_ids = implode(',', $goods_ids);
        $assign_rule_ids = AssignRule::orderBy('created_at')->whereNull('deleted_at')->lists('id')->toArray();
        $assign_rule_ids = implode(',', $assign_rule_ids);

        $rules = [
            'goods_id' => 'required|in:'.$goods_ids,
            'assign_rule' => 'required|in:'.$assign_rule_ids,
            'quantity' => 'required|numeric|min:0.01',
            'price_floor' => 'required|numeric|min:0.01',
            'price_caps' => 'required|numeric|min:'.$form_data['price_floor'],
            //'price_validity' => 'required|max:255',
            'delivery_date_start' => 'required|date',
            'delivery_date_stop' => 'required|date|after:'.$form_data['delivery_date_start'],
            'stock' => 'required|numeric|min:0',
            'pending' => 'required|numeric|min:0',
            'monthly_demand' => 'required|numeric|min:0',
            'invoice' => 'required|in:增值税普通发票,增值税专用发票,增值税普通或专用发票',
            'payment' => 'required|max:60'
        ];
        $messages = [
            'required' => '必填项不能为空',
            'numeric' => '只能填写数字',
            'in' => '非法的数据',
            'min' => '最小只能为:min',
            'max' => '字符数超出:max',
            'date' => '请填写日期格式',
            'after' => '日期应晚于:after'
        ];

        $validator = Validator::make($form_data, $rules, $messages);
        if ($validator->fails()) {
            return redirect($redirect_url)->withErrors($validator->errors())->withInput();
        }

        $goods = Goods::find($form_data['goods_id']);
        $form_data['category_id'] = $goods->category_id;
        $form_data['price_validity'] = $goods->price_validity;
        $form_data['goods_static'] = $goods->toJson();
        $form_data['assign_rule'] = AssignRule::find($form_data['assign_rule'])->toJson();
        $form_data['user_id'] = $current_user->id;
        $form_data['company_id'] = $company_id;
        $form_data['basket_id'] = $basket->id;
        $form_data['history'] = json_encode([[
            'user_id' => $form_data['user_id'],
            'role_id' => $current_user->role_id,
            'realname' => $current_user->staff->realname,
            'role_name' => $current_user->role->name,
            'quantity' => $form_data['quantity'],
            'assign_rule' => $form_data['assign_rule'],
            'price_floor' => $form_data['price_floor'],
            'price_caps' => $form_data['price_caps'],
            'price_validity' => $form_data['price_validity'],
            'date' => $datetime
        ]]);

        if (Demand::create($form_data)) {
            return redirect($redirect_url)->with('tip_message', ['content' => '追加成功', 'state' => 'success']);
        } else {
            return redirect($redirect_url)->withInput()->withErrors(['form' => ['服务器繁忙，请稍后再试']]);
        }

    }

    public function edit(Demand $demand)
    {
        //$redirect_url = URL::previous();
        $redirect_url = route('demand.staff.demand_list', ['basket'=>$demand->basket_id]);

        $basket = $demand->basket;
        $company_id = auth()->user()->company_id;
        $category_id = auth()->user()->category_id;
        if (empty($company_id)) {
            return redirect($redirect_url)->with('tip_message', ['content' => '当前用户不属于任何公司', 'state' => 'warning']);
        }
        $goods_records = Goods::orderBy('code');
        if (!empty($category_id)) {
            $goods_records = $goods_records->where('category_id', $category_id);
        }
        $goods_records = $goods_records->where('is_available', true)->whereNull('deleted_at')->get();

        $states = Setting::where('name', 'check_flow')->first();
        if (empty($states)) {
            return redirect($redirect_url)->with('tip_message', ['content' => '还未设置审核流程', 'state' => 'warning']);
        }
        $states = json_decode($states->data, true);
        if (empty($states) or count($states) == 0) {
            return redirect($redirect_url)->with('tip_message', ['content' => '还未设置审核流程', 'state' => 'warning']);
        }

        $time = time();
        $current_time = date('H:i:s', $time);
        $today = date('Y-m-d', $time);
        $tomorrow = date("Y-m-d", strtotime("+1 day"));
        $check_time = Setting::where('name', 'demand_check_time')->first()->data;
        if ($current_time > $check_time) {
            if ($basket->name != $tomorrow) {
                return redirect($redirect_url)->with('tip_message', ['content' => '抱歉，'.$basket->name.'需求已开始审核', 'state' => 'warning']);
            }
        } else {
            if ($basket->name != $today) {
                return redirect($redirect_url)->with('tip_message', ['content' => '抱歉，'.$basket->name.'需求此刻不能编辑', 'state' => 'warning']);
            }
        }

        $assign_rules = AssignRule::orderBy('created_at')->whereNull('deleted_at')->get();

        return view('demand.staff.edit')
            ->with('basket', $basket)
            ->with('demand', $demand)
            ->with('goods_records', $goods_records)
            ->with('assign_rules', $assign_rules);
    }

    public function update(Request $request, Demand $demand)
    {
        $redirect_url = URL::full();

        $basket = $demand->basket;
        $current_user = auth()->user();
        $company_id = $current_user->company_id;
        $category_id = $current_user->category_id;
        if (empty($company_id)) {
            return redirect($redirect_url)->with('tip_message', ['content' => '当前用户不属于任何公司', 'state' => 'warning']);
        }

        $time = time();
        $current_time = date('H:i:s', $time);
        $datetime = date('Y-m-d H:i:s', $time);
        $today = date('Y-m-d', $time);
        $tomorrow = date("Y-m-d", strtotime("+1 day"));
        $check_time = Setting::where('name', 'demand_check_time')->first()->data;
        if ($current_time > $check_time) {
            if ($basket->name != $tomorrow) {
                return redirect($redirect_url)->with('tip_message', ['content' => '抱歉，'.$basket->name.'需求已开始审核', 'state' => 'warning']);
            }
        } else {
            if ($basket->name != $today) {
                return redirect($redirect_url)->with('tip_message', ['content' => '抱歉，'.$basket->name.'需求此刻不能编辑', 'state' => 'warning']);
            }
        }

        $form_data = $request->only([
            'goods_id', 'quantity', 'price_floor',
            'price_caps', 'assign_rule', 'price_validity',
            'delivery_date_start', 'delivery_date_stop',
            'stock', 'pending', 'monthly_demand','payment','invoice'
        ]);

        $goods_ids = Goods::orderBy('code');
        if (!empty($category_id)) {
            $goods_ids = $goods_ids->where('category_id', $category_id);
        }
        $goods_ids = $goods_ids->where('is_available', true)->whereNull('deleted_at')->lists('id')->toArray();
        $goods_ids = implode(',', $goods_ids);
        $assign_rule_ids = AssignRule::orderBy('created_at')->whereNull('deleted_at')->lists('id')->toArray();
        $assign_rule_ids = implode(',', $assign_rule_ids);

        $rules = [
            'goods_id' => 'required|in:'.$goods_ids,
            'assign_rule' => 'required|in:'.$assign_rule_ids,
            'quantity' => 'required|numeric|min:0.01',
            'price_floor' => 'required|numeric|min:0.01',
            'price_caps' => 'required|numeric|min:'.$form_data['price_floor'],
            'price_validity' => 'required|max:255',
            'delivery_date_start' => 'required|date',
            'delivery_date_stop' => 'required|date|after:'.$form_data['delivery_date_start'],
            'stock' => 'required|numeric|min:0',
            'pending' => 'required|numeric|min:0',
            'monthly_demand' => 'required|numeric|min:0',
            'invoice' => 'required|in:增值税普通发票,增值税专用发票,增值税普通或专用发票',
            'payment' => 'required|max:60'
        ];
        $messages = [
            'required' => '必填项不能为空',
            'numeric' => '只能填写数字',
            'in' => '非法的数据',
            'min' => '最小只能为:min',
            'max' => '字符数超出:max',
            'date' => '请填写日期格式',
            'after' => '日期应晚于:after'
        ];

        $validator = Validator::make($form_data, $rules, $messages);
        if ($validator->fails()) {
            return redirect($redirect_url)->withErrors($validator->errors())->withInput();
        }

        $goods = Goods::find($form_data['goods_id']);
        $form_data['category_id'] = $goods->category_id;
        $form_data['goods_static'] = Goods::find($form_data['goods_id'])->toJson();
        $form_data['assign_rule'] = AssignRule::find($form_data['assign_rule'])->toJson();
        $form_data['user_id'] = $current_user->id;
        $form_data['company_id'] = $company_id;
        $form_data['basket_id'] = $basket->id;
        $form_data['history'] = json_encode([[
            'user_id' => $form_data['user_id'],
            'role_id' => $current_user->role_id,
            'realname' => $current_user->staff->realname,
            'role_name' => $current_user->role->name,
            'quantity' => $form_data['quantity'],
            'assign_rule' => $form_data['assign_rule'],
            'price_floor' => $form_data['price_floor'],
            'price_caps' => $form_data['price_caps'],
            'price_validity' => $form_data['price_validity'],
            'date' => $datetime
        ]]);

        foreach ($form_data as $field => $value) {
            $demand->$field = $value;
        }

        if ($demand->save()) {
            return redirect($redirect_url)->with('tip_message', ['content' => '编辑成功', 'state' => 'success']);
        } else {
            return redirect($redirect_url)->withInput()->withErrors(['form' => ['服务器繁忙，请稍后再试']]);
        }
    }

    public function delete(Demand $demand)
    {
        $redirect_url = URL::previous();

        $basket = $demand->basket;
        $company_id = auth()->user()->company_id;
        if (empty($company_id)) {
            return redirect($redirect_url)->with('tip_message', ['content' => '当前用户不属于任何公司', 'state' => 'warning']);
        }

        $time = time();
        $current_time = date('H:i:s', $time);
        $datetime = date('Y-m-d H:i:s', $time);
        $today = date('Y-m-d', $time);
        $tomorrow = date("Y-m-d", strtotime("+1 day"));
        $check_time = Setting::where('name', 'demand_check_time')->first()->data;
        if ($current_time > $check_time) {
            if ($basket->name != $tomorrow) {
                return redirect($redirect_url)->with('tip_message', ['content' => '抱歉，'.$basket->name.'需求已开始审核', 'state' => 'warning']);
            }
        } else {
            if ($basket->name != $today) {
                return redirect($redirect_url)->with('tip_message', ['content' => '抱歉，'.$basket->name.'需求此刻不能编辑', 'state' => 'warning']);
            }
        }

        if ($demand->delete()) {
            return redirect($redirect_url)->with('tip_message', ['content' => '操作成功', 'state' => 'success', 'hold' => true]);
        } else {
            return redirect($redirect_url)->with('tip_message', ['content' => '服务器繁忙，请稍候再试', 'state' => 'danger', 'hold' => true]);
        }
    }

    public function view(Request $request, Basket $basket)
    {
        $check_flow_setting = Setting::where('name', 'check_flow')->first()->data;
        $check_flows = json_decode($check_flow_setting, true);
        $check_times = [];
        foreach ($check_flows as $flow) {
            $check_times[] = $basket->name.' '.$flow['time'];
        }

        $current_user = auth()->user();
        $company_id = $current_user->company_id;
        $area_id = $current_user->area_id;
        $demands = Demand::with(['goods', 'company']);
        $demands = $demands->where('basket_id', $basket->id);
        if ($request->get('orderBy') == 'company') {
            $demands = $demands->orderBy('company_id', 'asc');
        } else {
            $demands = $demands->orderBy('goods_id', 'asc');
        }

        if (!empty($company_id)) {
            $demands = $demands->where('company_id', $company_id);
        }
        if (!empty($area_id)) {
            $demands = $demands->whereHas('company', function ($query) use ($area_id) {
                return $query->where('area_id', $area_id);
            });
        }
        $demands = $demands->get();
        return view('demand.staff.view')
            ->with('current_user', $current_user)
            ->with('demands', $demands)
            ->with('check_times', $check_times)
            ->with('datetime', date('Y-m-d H:i:s'))
            ->with('basket', $basket);
    }
}
