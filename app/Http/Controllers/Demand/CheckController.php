<?php

namespace App\Http\Controllers\Demand;

use App\AssignRule;
use App\Basket;
use App\BasketLog;
use App\Demand;
use App\Role;
use App\Setting;
use App\User;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use URL;
use Validator;
use DB;
use App;

class CheckController extends Controller
{
    public function index(Request $request)
    {
        $cond = $request->only(['date_start', 'date_stop']);
        $cond = array_map('trim', $cond);
        $current_user = auth()->user();
        $company_id = $current_user->company_id;
        $category_id = $current_user->category_id;
        $area_id = $current_user->area_id;
        $baskets = Basket::orderBy('created_at', 'desc');
        //$check_time = config('demand.check_time');
        $check_flow_setting = Setting::where('name', 'check_flow')->first()->data;
        $check_flows = json_decode($check_flow_setting, true);

        $check_start_time = null;
        $check_stop_time = null;
        foreach ($check_flows as $k=>$flow) {
            if ($flow['role_id'] == $current_user->role_id) {
                if (isset($check_flows[$k - 1])) {
                    $check_start_time = $check_flows[$k - 1]['time'];
                    $check_stop_time = $flow['time'];
                    break;
                }
            }
        }
        if (empty($check_start_time) or empty($check_stop_time)) {
            App::abort(403, '抱歉，你目前不能审核需求');
        }

        $time = time();
        $current_time = date('H:i:s', $time);
        $today = date('Y-m-d', $time);
        if ($current_time < $check_start_time) {
            $baskets = $baskets->where('name', '<', $today);
        } else {
            $baskets = $baskets->where('name', '<=', $today);
        }
        if (!empty($company_id)) {
            $baskets = $baskets->whereHas('demands', function ($query) use($company_id) {
                return $query->where('company_id', $company_id);
            });
        }
        if (!empty($area_id) and empty($company_id)) {
            $baskets = $baskets->whereHas('demands', function ($query) use($area_id) {
                return $query->whereHas('company', function($query) use($area_id) {
                    return $query->where('area_id', $area_id);
                });
            });
        }
        if (empty($company_id) and empty($area_id)) {
            $baskets = $baskets->whereHas('demands', function($query) {
                return $query;
            });
        }
        if (!empty($category_id)) {
            $baskets = $baskets->whereHas('demands', function ($query) use($category_id) {
                return $query->where('category_id', $category_id);
            });
        }
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

        return view('demand.check.index')
            ->with('current_user', $current_user)
            ->with('baskets', $baskets)
            ->with('today', $today)
            ->with('current_time', $current_time)
            ->with('check_start_time', $check_start_time)
            ->with('check_stop_time', $check_stop_time)
            ->with('pages', $pages)
            ->with('display_states', $display_states);
    }

    public function view(Request $request, Basket $basket)
    {
        $redirect_url = route('demand.check.index');

        $current_user = auth()->user();
        $company_id = $current_user->company_id;
        $area_id = $current_user->area_id;
        $category_id = $current_user->category_id;

        $check_flow_setting = Setting::where('name', 'check_flow')->first()->data;
        $check_flows = json_decode($check_flow_setting, true);

        $check_start_time = null;
        $check_stop_time = null;
        foreach ($check_flows as $k=>$flow) {
            if ($flow['role_id'] == $current_user->role_id) {
                if (isset($check_flows[$k - 1])) {
                    $check_start_time = $check_flows[$k - 1]['time'];
                    $check_stop_time = $flow['time'];
                    break;
                }
            }
        }
        if (empty($check_start_time) or empty($check_stop_time)) {
            App::abort(403, '抱歉，你目前不能审核需求');
        }
        $time = time();
        $current_time = date('H:i:s', $time);
        $today = date('Y-m-d', $time);
        if ($basket->name != $today) {
            return redirect($redirect_url)->withInput()->withErrors(['form' => ['当前时间不能审核'.$basket->name.'需求']]);
        }

        if ($current_time < $check_start_time or $current_time > $check_stop_time) {
            return redirect($redirect_url)->withInput()->withErrors(['form' => ['当前时间不能审核'.$basket->name.'需求']]);
        }

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

        $assign_rules = AssignRule::orderBy('created_at')->whereNull('deleted_at')->get();
        $check_flow_setting = Setting::where('name', 'check_flow')->first();
        $check_flow = json_decode($check_flow_setting->data, true);
        $next_role = null;
        foreach ($check_flow as $k=>$flow) {
            if (($current_time < $flow['time']) and isset($check_flow[$k+1])) {
                if ($current_user->role_id != $flow['role_id']) {
                    return redirect($redirect_url)->with('tip_message', ['content' => '抱歉，您不在当前流程中', 'state' => 'warning']);
                }
                $next_role_id = $check_flow[$k+1]['role_id'];
                $next_role = Role::whereNull('deleted_at')->where('id', $next_role_id)->first();
                break;
            }
        }

        return view('demand.check.view')
            ->with('demands', $demands)
            ->with('current_user', $current_user)
            ->with('basket', $basket)
            ->with('assign_rules', $assign_rules)
            ->with('next_role', $next_role);
    }

    public function action(Request $request, Basket $basket)
    {
        $redirect_url = route('demand.check.index');

        $current_user = auth()->user();
        $company_id = $current_user->company_id;
        $area_id = $current_user->area_id;
        $category_id = $current_user->category_id;

        $check_flow_setting = Setting::where('name', 'check_flow')->first()->data;
        $check_flows = json_decode($check_flow_setting, true);

        $check_start_time = null;
        $check_stop_time = null;
        $check_is_over = false;
        foreach ($check_flows as $k=>$flow) {
            if ($flow['role_id'] == $current_user->role_id) {
                if (isset($check_flows[$k - 1])) {
                    if (!isset($check_flows[$k + 1])) {
                        $check_is_over = true;
                    }
                    $check_start_time = $check_flows[$k - 1]['time'];
                    $check_stop_time = $flow['time'];
                    break;
                }
            }
        }
        if (empty($check_start_time) or empty($check_stop_time)) {
            App::abort(403, '抱歉，你目前不能审核需求');
        }
        $time = time();
        $current_time = date('H:i:s', $time);
        $datetime = date('Y-m-d H:i:s', $time);
        $today = date('Y-m-d', $time);
        if ($basket->name != $today) {
            return redirect($redirect_url)->withInput()->withErrors(['form' => ['当前时间不能审核'.$basket->name.'需求']]);
        }

        if ($current_time < $check_start_time or $current_time > $check_stop_time) {
            return redirect($redirect_url)->withInput()->withErrors(['form' => ['当前时间不能审核'.$basket->name.'需求']]);
        }

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

        $result = DB::transaction(function() use ($demands, $current_user, $datetime, $basket, $check_is_over) {
            foreach ($demands as $demand) {
                if ($demand->tmp_data_user_id == $current_user->id) {
                    $tmp_data = json_decode($demand->tmp_data);
                    $update_data = [
                        'quantity' => $tmp_data->quantity,
                        'price_validity' => $tmp_data->price_validity,
                        'assign_rule' => $tmp_data->assign_rule,
                        'price_floor' => $tmp_data->price_floor,
                        'price_caps' => $tmp_data->price_caps
                    ];
                    $history = json_decode($demand->history, true);
                    $current_history = [
                        'user_id' => $current_user->id,
                        'role_id' => $current_user->role_id,
                        'realname' => $current_user->staff->realname,
                        'role_name' => $current_user->role->name,
                        'quantity' => $tmp_data->quantity,
                        'assign_rule' => $tmp_data->assign_rule,
                        'price_floor' => $tmp_data->price_floor,
                        'price_caps' => $tmp_data->price_caps,
                        'price_validity' => $tmp_data->price_validity,
                        'remark' => $tmp_data->remark,
                        'date' => $datetime
                    ];
                    array_unshift($history, $current_history);
                    $update_data['history'] = json_encode($history);
                    if (!Demand::where('id', $demand->id)->update($update_data)) {
                        DB::rollBack();
                        return false;
                    }
                }
            }
            $basket_log_data = [
                'basket_id' => $basket->id,
                'action' => 'pass',
                'user_id' => $current_user->id,
                'role_id' => $current_user->role_id,
                'remark' => ''
            ];
            if (!BasketLog::create($basket_log_data)) {
                DB::rollBack();
                return false;
            }
            if ($check_is_over) {
                $basket->state = 'checked';
                if (!$basket->save()) {
                    DB::rollBack();
                    return false;
                }
            }
            return true;
        });

        if ($result) {
            return redirect($redirect_url)->with('tip_message', ['content' => '操作成功', 'state' => 'success']);
        } else {
            return redirect($redirect_url)->withErrors(['form' => ['服务器繁忙，请稍后再试']]);
        }
    }

    public function edit(Request $request, Demand $demand)
    {
        $time = time();
        $current_time = date('H:i:s', $time);
        $today = date('Y-m-d', $time);
        $current_user = auth()->user();
        $category_id = $current_user->category_id;

        if (!empty($category_id) and $demand->category_id != $category_id) {
            return response()->json([
                'state' => 'error',
                'message' => ['form' => ['你不能编辑不属于你管理品类的需求']]
            ]);
        }

        $form_data = $request->only([
            'quantity', 'price_floor', 'price_caps',
            'assign_rule', 'price_validity', 'remark'
        ]);
        $form_data = array_map('trim', $form_data);
        $form_data['remark'] = strip_tags($form_data['remark']);

        $basket = $demand->basket;
        if ($today != $basket->name) {
            return response()->json([
                'state' => 'error',
                'message' => ['form' => ['你当前时间不能更改该需求']]
            ]);
        }

        $check_flow_setting = Setting::where('name', 'check_flow')->first()->data;
        $check_flows = json_decode($check_flow_setting, true);

        $check_start_time = null;
        $check_stop_time = null;
        foreach ($check_flows as $k=>$flow) {
            if ($flow['role_id'] == $current_user->role_id) {
                if (isset($check_flows[$k - 1])) {
                    $check_start_time = $check_flows[$k - 1]['time'];
                    $check_stop_time = $flow['time'];
                    break;
                }
            }
        }
        if (empty($check_start_time) or empty($check_stop_time)) {
            return response()->json([
                'state' => 'error',
                'message' => ['form' => ['你当前不能更改该需求']]
            ]);
        }

        if ($current_time < $check_start_time or $current_time > $check_stop_time) {
            return response()->json([
                'state' => 'error',
                'message' => ['form' => ['你当前时间不能更改该需求']]
            ]);
        }

        $assign_rule_ids = AssignRule::orderBy('created_at')->whereNull('deleted_at')->lists('id')->toArray();
        $assign_rule_ids = implode(',', $assign_rule_ids);

        $rules = [
            'quantity' => 'required|numeric|min:0|max:10000000',
            'price_floor' => 'required|numeric|min:0.01|max:10000000',
            'price_caps' => 'required|numeric|min:'.$form_data['price_caps'].'|max:10000000',
            'assign_rule' => 'required|in:'.$assign_rule_ids,
            'price_validity' => 'required',
            'remark' => 'required'
        ];
        $messages = [
            'quantity.required' => '采购数量不能为空',
            'quantity.numeric' => '采购数量只能为数字',
            'quantity.min' => '采购数量最小只能为:min',
            'quantity.max' => '采购数量最大只能为:max',
            'price_floor.required' => '报价最小值不能为空',
            'price_floor.numeric' => '报价最小值只能为数字',
            'price_floor.min' => '报价最小值最小只能为:min',
            'price_floor.max' => '报价最小值最大只能为:max',
            'price_caps.required' => '报价最大值不能为空',
            'price_caps.numeric' => '报价最大值只能为数字',
            'price_caps.min' => '报价最大值最小只能为:min',
            'price_caps.max' => '报价最大值最大只能为:max',
            'assign_rule.required' => '请选择分配方案',
            'assign_rule.in' => '分配方案选择有误',
            'price_validity.required' => '请填写报价有效期',
            'remark.required' => '请填写更改理由'
        ];

        $validator = Validator::make($form_data, $rules, $messages);

        if ($validator->fails()) {
            return response()->json([
                'state' => 'error',
                'message' => $validator->errors()
            ]);
        }

        $current_assign_rule = AssignRule::find($form_data['assign_rule']);
        $current_assign_rule_data = json_decode($current_assign_rule->rules, true);
        $form_data['assign_rule'] = $current_assign_rule->toJson();
        $form_data['assign_rule_txt'] = $current_assign_rule->name . '【'.implode('%，', $current_assign_rule_data).'%】';
        $form_data['assign_rule_id'] = $current_assign_rule->id;
        $demand->tmp_data = json_encode($form_data);
        $demand->tmp_data_user_id = $current_user->id;
        if ($demand->save()) {
            return response()->json([
                'state' => 'success',
                'data' => $form_data
            ]);
        } else {
            return response()->json([
                'state' => 'error',
                'message' => ['form' => ['服务器繁忙，请稍候再试']]
            ]);
        }
    }

    public function modify(Request $request, Demand $demand)
    {
        $time = time();
        $datetime = date('Y-m-d H:i:s', $time);
        $current_time = date('H:i:s', $time);
        $today = date('Y-m-d', $time);
        $current_user = auth()->user();
        $category_id = $current_user->category_id;

        if (!empty($category_id) and $demand->category_id != $category_id) {
            return response()->json([
                'state' => 'error',
                'message' => ['form' => ['你不能编辑不属于你管理品类的需求']]
            ]);
        }

        $form_data = $request->only([
            'quantity', 'price_floor', 'price_caps',
            'assign_rule', 'price_validity', 'remark'
        ]);
        $form_data = array_map('trim', $form_data);
        $form_data['remark'] = strip_tags($form_data['remark']);

        $basket = $demand->basket;
        if ($today != $basket->name) {
            return response()->json([
                'state' => 'error',
                'message' => ['form' => ['你当前时间不能更改该需求']]
            ]);
        }

        $assign_rule_ids = AssignRule::orderBy('created_at')->whereNull('deleted_at')->lists('id')->toArray();
        $assign_rule_ids = implode(',', $assign_rule_ids);

        $rules = [
            'quantity' => 'required|numeric|min:0|max:10000000',
            'price_floor' => 'required|numeric|min:0.01|max:10000000',
            'price_caps' => 'required|numeric|min:'.$form_data['price_caps'].'|max:10000000',
            'assign_rule' => 'required|in:'.$assign_rule_ids,
            'price_validity' => 'required',
            'remark' => 'required'
        ];
        $messages = [
            'quantity.required' => '采购数量不能为空',
            'quantity.numeric' => '采购数量只能为数字',
            'quantity.min' => '采购数量最小只能为:min',
            'quantity.max' => '采购数量最大只能为:max',
            'price_floor.required' => '报价最小值不能为空',
            'price_floor.numeric' => '报价最小值只能为数字',
            'price_floor.min' => '报价最小值最小只能为:min',
            'price_floor.max' => '报价最小值最大只能为:max',
            'price_caps.required' => '报价最大值不能为空',
            'price_caps.numeric' => '报价最大值只能为数字',
            'price_caps.min' => '报价最大值最小只能为:min',
            'price_caps.max' => '报价最大值最大只能为:max',
            'assign_rule.required' => '请选择分配方案',
            'assign_rule.in' => '分配方案选择有误',
            'price_validity.required' => '请填写报价有效期',
            'remark.required' => '请填写更改理由'
        ];

        $validator = Validator::make($form_data, $rules, $messages);

        if ($validator->fails()) {
            return response()->json([
                'state' => 'error',
                'message' => $validator->errors()
            ]);
        }

        $current_assign_rule = AssignRule::find($form_data['assign_rule']);
        $current_assign_rule_data = json_decode($current_assign_rule->rules, true);
        $form_data['assign_rule'] = $current_assign_rule->toJson();
        $form_data['assign_rule_txt'] = $current_assign_rule->name . '【'.implode('%，', $current_assign_rule_data).'%】';
        $form_data['assign_rule_id'] = $current_assign_rule->id;
        //$demand->tmp_data = json_encode($form_data);
        //$demand->tmp_data_user_id = $current_user->id;
        $update_data = [
            'quantity' => $form_data['quantity'],
            'price_validity' => $form_data['price_validity'],
            'assign_rule' => $form_data['assign_rule'],
            'price_floor' => $form_data['price_floor'],
            'price_caps' => $form_data['price_caps']
        ];
        $history = json_decode($demand->history, true);
        $current_history = [
            'user_id' => $current_user->id,
            'role_id' => $current_user->role_id,
            'realname' => $current_user->staff->realname,
            'role_name' => $current_user->role->name,
            'quantity' => $form_data['quantity'],
            'assign_rule' => $form_data['assign_rule'],
            'price_floor' => $form_data['price_floor'],
            'price_caps' => $form_data['price_caps'],
            'price_validity' => $form_data['price_validity'],
            'remark' => $form_data['remark'],
            'date' => $datetime
        ];
        array_unshift($history, $current_history);
        $update_data['history'] = json_encode($history);
        if (Demand::where('id', $demand->id)->update($update_data)) {
            return response()->json([
                'state' => 'success',
                'data' => $form_data
            ]);
        } else {
            return response()->json([
                'state' => 'error',
                'message' => ['form' => ['服务器繁忙，请稍候再试']]
            ]);
        }
    }
}
