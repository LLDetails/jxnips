<?php

namespace App\Http\Controllers\User;

use App\Area;
use App\Company;
use App\Goods;
use App\Role;
use App\Setting;
use App\Supplier;
use App\User;
use App\Category;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Validator;
use URL;
use Hash;
use DB;
use App;

class SupplierController extends Controller
{
    public function manage(Request $request)
    {
        $goods_data = Goods::orderBy('code')->where('is_available', true)->whereNull('deleted_at')->get();

        $areas = Area::orderBy('display_order', 'asc')
            ->whereNull('deleted_at')
            ->get();

        $search_param = $request->only(['username', 'name', 'area_id', 'goods_id', 'allow_login']);
        $search_param = array_map('trim', $search_param);
        $users = User::with(['supplier', 'area'])->orderBy('updated_at', 'desc')->where('type', 'supplier');
        if ( ! empty($search_param['goods_id'])) {
            $users = $users->whereHas('supplier', function($query) use($search_param) {
                return $query->whereRaw('\'["'.$search_param['goods_id'].'"]\'::jsonb <@ "goods"');
            });
        }

        if ( ! empty($search_param['allow_login'])) {
            if ($search_param['allow_login'] == 'true') {
                $allow_login = true;
            } else {
                $allow_login = false;
            }
            $users = $users->where('allow_login', $allow_login);
        }

        if ( ! empty($search_param['area_id'])) {
            $users = $users->where('area_id', $search_param['area_id']);
        }

        if ( ! empty($search_param['username'])) {
            $users = $users->where('username', $search_param['username']);
        }
        if ( ! empty($search_param['name'])) {
            $users = $users->whereHas('supplier', function($query) use($search_param) {
                return $query->where('name', 'like', '%'.$search_param['name'].'%');
            });
        }

        $users = $users->paginate(10);
        $pages = $users->appends($search_param)->render();
        return view('user.supplier.manage')
            ->with('goods_data', $goods_data)
            ->with('areas', $areas)
            ->with('users', $users)
            ->with('pages', $pages);
    }

    public function add(Request $request)
    {
        $supplier_type = trim($request->get('type'));
        $categories = Category::with('goods_records')
            ->whereHas('goods_records', function($query) {
                return $query->where('is_available', true)->whereNull('deleted_at');
            })
            ->orderBy('code')->get();
        $supplier_types = [
            '企业法人' => '企业法人',
            '个体户' => '个体户',
            '自然人' => '自然人'
        ];
        if ( ! in_array($supplier_type, array_keys($supplier_types))) {
            $supplier_type = null;
        }

        $areas = Area::orderBy('display_order', 'asc')
            ->whereNull('deleted_at')
            ->get();

        $addition = Setting::where('name', 'supplier_profile_addition')->first();
        if (empty($addition) or empty($addition->data)) {
            $addition = [];
        } else {
            $addition = json_decode($addition->data);
        }

        return view('user.supplier.add')
            ->with('areas', $areas)
            ->with('supplier_types', $supplier_types)
            ->with('supplier_type', $supplier_type)
            ->with('categories', $categories)
            ->with('addition', $addition);
    }

    public function create(Request $request)
    {
        $redirect_url = URL::full();
        $addition = Setting::where('name', 'supplier_profile_addition')->first();
        if (empty($addition) or empty($addition->data)) {
            $addition_data = [];
        } else {
            $addition_data = json_decode($addition->data);
        }
        //$goods_ids = Goods::lists('id')->toArray();
        $supplier_types = [
            '企业法人' => '企业法人',
            '个体户' => '个体户',
            '自然人' => '自然人'
        ];
        $allow_input = [
            'area_id', 'username', 'password', 'type',
            'goods', 'address', 'zipcode', 'tel', 'fax',
            'email', 'website', 'business_license', 'organization_code',
            'tax_id', 'registered_capital', 'company_scale', 'id_number',
            'contact', 'bank', 'bank_account', 'name'
        ];
        $rules = [
            'username' => 'required|max:20|unique:users,username',
            'type' => 'required|in:'.implode(',', array_keys($supplier_types)),
            'password' => 'required|max:30',
            'name' => 'required|max:60|unique:suppliers,name',
            'tel' => 'required',
            'registered_capital' => 'max:60',
            'company_scale' => 'integer|min:1',
            'goods' => 'required|array',
            'address' => 'max:128',
            'zipcode' => 'max:10',
            'fax' => 'max:100',
            'email' => 'email|max:100',
            'website' => 'max:128',
            'business_license' => 'max:100',
            'organization_code' => 'max:100',
            'tax_id' => 'max:100',
            'id_number' => 'max:20',
            'contact' => 'max:10',
            'bank' => 'max:100',
            'bank_account' => 'max:40'
        ];
        $allow_input[] = 'area_id';
        $area_ids = Area::orderBy('display_order', 'asc')
            ->whereNull('deleted_at')
            ->lists('id')
            ->toArray();
        $rules['area_id'] = 'required|in:'.implode(',', $area_ids);
        $messages = [
            'required' => '必填项不能为空',
            'max' => '字符长度超出:max',
            'unique' => '已经存在，不能重复',
            'date' => '请输入日期格式(如：'.date('Y-m-d').')',
            'in' => '非法的值',
            'regular.required' => '请选择入职方式',
            'array' => '错误的数据',
            'min' => '不能小于:min',
            'numeric' => '只能填写数字',
            'integer' => '只能填写整数'
        ];
        foreach ($addition_data as $field) {
            $allow_input[] = $field->name;
            $rule = [];
            if ($field->required == 'true') {
                $rule[] = 'required';
                $messages[$field->name.'.required'] = $field->prompt;
            }
            switch ($field->tpl) {
                case 'text':
                    if ( ! empty($field->size_min)) {
                        $rule[] = 'min:'.$field->size_min;
                        $messages[$field->name.'.min'] = $field->prompt;
                    }
                    if ( ! empty($field->size_max)) {
                        $rule[] = 'max:'.$field->size_max;
                        $messages[$field->name.'.max'] = $field->prompt;
                    }
                    if ($field->rule != '*') {
                        $rule[] = $field->rule;
                        $messages[$field->name.'.'.$field->rule] = $field->prompt;
                    }
                    break;
                case 'select':
                    if ($field->widget == 'checkbox') {
                        $rule[] = 'array';
                        $messages[$field->name.'.array'] = $field->prompt;
                    }
                    break;
                case 'file':
                    $rule[] = 'array';
                    $messages[$field->name.'.array'] = $field->prompt;
                    break;
                default:
                    break;
            }
            $rules[$field->name] = implode('|', $rule);
        }

        $form_data = $request->only($allow_input);

        $validator = Validator::make($form_data, $rules, $messages);
        if ($validator->fails()) {
            return redirect($redirect_url)->withErrors($validator->errors())->withInput();
        }

        $addition_data_arr = [];
        foreach ($addition_data as $field) {
            $addition_data_arr[$field->name] = $form_data[$field->name];
            unset($form_data[$field->name]);
        }
        $form_data['addition'] = json_encode($addition_data_arr);
        $form_data['password'] = Hash::make($form_data['password']);
        $form_data['tel'] = trim($form_data['tel']);
        $form_data['tel'] = str_replace("\r", '', $form_data['tel']);
        $form_data['tel'] = explode("\n", $form_data['tel']);
        $form_data['tel'] = json_encode($form_data['tel']);
        $form_data['goods'] = json_encode($form_data['goods']);

        $result = DB::transaction(function() use($form_data) {
            $role = Role::where('name', '供应商')->first();
            if (empty($role)) {
                return false;
            }
            $user_data = [
                'type' => 'supplier',
                'role_id' => $role->id
            ];
            foreach (['area_id', 'username', 'password'] as $k) {
                if (isset($form_data[$k])) {
                    $user_data[$k] = $form_data[$k];
                    unset($form_data[$k]);
                }
            }
            if (empty($user_data['company_id'])) {
                $user_data['company_id'] = null;
            }
            $user = User::create(array_merge($user_data, ['edit_at' => date('Y-m-d H:i:s')]));
            if ( ! $user) {
                return false;
            }
            $form_data['user_id'] = $user->id;
            $form_data['area_id'] = $user->area_id;
            $supplier_profile = Supplier::create($form_data);
            if ( ! $supplier_profile) {
                DB::rollBack();
                return false;
            }
            return true;
        });

        if ($result) {
            return redirect($redirect_url)->with('tip_message', ['content' => '成功新建供应商', 'state' => 'success']);
        } else {
            return redirect($redirect_url)->withErrors(['form' => ['服务器繁忙，请稍后再试']]);
        }
    }

    public function edit(User $user)
    {
        if ($user->type != 'supplier') {
            App::abort(404, '没有找到对应的供应商');
        }
        if (!empty($user->deleted_at)) {
            App::abort(404, '供应商已被删除');
        }
        $user_addition = $user->supplier->addition;
        if ( ! empty($user_addition)) {
            $addition_data = json_decode($user_addition);
        }
        if (empty($addition_data)) {
            $addition_data = [];
        }

        $categories = Category::with('goods_records')
            ->whereHas('goods_records', function($query) {
                return $query->where('is_available', true)->whereNull('deleted_at');
            })
            ->orderBy('code')->get();
        $supplier_types = [
            '企业法人' => '企业法人',
            '个体户' => '个体户',
            '自然人' => '自然人'
        ];
        $supplier_type = $user->supplier->type;
        $areas = Area::orderBy('display_order', 'asc')
            ->whereNull('deleted_at')
            ->get();
        $addition = Setting::where('name', 'supplier_profile_addition')->first();
        if (empty($addition) or empty($addition->data)) {
            $addition = [];
        } else {
            $addition = json_decode($addition->data);
        }
        return view('user.supplier.edit')
            ->with('user', $user)
            ->with('addition_data', $addition_data)
            ->with('areas', $areas)
            ->with('supplier_types', $supplier_types)
            ->with('supplier_type', $supplier_type)
            ->with('addition', $addition)
            ->with('categories', $categories);
    }

    public function update(Request $request, User $user)
    {
        $redirect_url = URL::full();
        $addition = Setting::where('name', 'supplier_profile_addition')->first();
        if (empty($addition) or empty($addition->data)) {
            $addition_data = [];
        } else {
            $addition_data = json_decode($addition->data);
        }
        $allow_input = [
            'area_id', 'username', 'password',
            'goods', 'address', 'zipcode', 'tel', 'fax',
            'email', 'website', 'business_license', 'organization_code',
            'tax_id', 'registered_capital', 'company_scale', 'id_number',
            'contact', 'bank', 'bank_account', 'name'
        ];
        $rules = [
            'username' => 'required|max:20|unique:users,username,'.$user->id,
            'password' => 'max:30',
            'name' => 'required|max:60|unique:suppliers,name,'.$user->supplier->id,
            'tel' => 'required',
            'registered_capital' => 'max:60',
            'company_scale' => 'integer|min:1',
            'goods' => 'required|array',
            'address' => 'max:128',
            'zipcode' => 'max:10',
            'fax' => 'max:100',
            'email' => 'email|max:100',
            'website' => 'max:128',
            'business_license' => 'max:100',
            'organization_code' => 'max:100',
            'tax_id' => 'max:100',
            'id_number' => 'max:20',
            'contact' => 'max:10',
            'bank' => 'max:100',
            'bank_account' => 'max:40'
        ];
        $allow_input[] = 'area_id';
        $area_ids = Area::orderBy('display_order', 'asc')
            ->where('deleted_at')
            ->lists('id')
            ->toArray();
        $rules['area_id'] = 'required|in:'.implode(',', $area_ids);
        $messages = [
            'required' => '必填项不能为空',
            'max' => '字符长度超出:max',
            'unique' => '已经存在，不能重复',
            'date' => '请输入日期格式(如：'.date('Y-m-d').')',
            'in' => '非法的值',
            'regular.required' => '请选择入职方式',
            'array' => '错误的数据',
            'min' => '不能小于:min',
            'numeric' => '只能填写数字',
            'integer' => '只能填写整数'
        ];
        foreach ($addition_data as $field) {
            $allow_input[] = $field->name;
            $rule = [];
            if ($field->required == 'true') {
                $rule[] = 'required';
                $messages[$field->name.'.required'] = $field->prompt;
            }
            switch ($field->tpl) {
                case 'text':
                    if ( ! empty($field->size_min)) {
                        $rule[] = 'min:'.$field->size_min;
                        $messages[$field->name.'.min'] = $field->prompt;
                    }
                    if ( ! empty($field->size_max)) {
                        $rule[] = 'max:'.$field->size_max;
                        $messages[$field->name.'.max'] = $field->prompt;
                    }
                    if ($field->rule != '*') {
                        $rule[] = $field->rule;
                        $messages[$field->name.'.'.$field->rule] = $field->prompt;
                    }
                    break;
                case 'select':
                    if ($field->widget == 'checkbox') {
                        $rule[] = 'array';
                        $messages[$field->name.'.array'] = $field->prompt;
                    }
                    break;
                case 'file':
                    $rule[] = 'array';
                    $messages[$field->name.'.array'] = $field->prompt;
                    break;
                default:
                    break;
            }
            $rules[$field->name] = implode('|', $rule);
        }

        $form_data = $request->only($allow_input);

        $validator = Validator::make($form_data, $rules, $messages);
        if ($validator->fails()) {
            return redirect($redirect_url)->withErrors($validator->errors())->withInput();
        }

        $addition_data_arr = [];
        foreach ($addition_data as $field) {
            $addition_data_arr[$field->name] = $form_data[$field->name];
            unset($form_data[$field->name]);
        }
        $form_data['addition'] = json_encode($addition_data_arr);
        if ( ! empty($form_data['password'])) {
            $form_data['password'] = Hash::make($form_data['password']);
        } else {
            unset($form_data['password']);
        }
        $form_data['tel'] = trim($form_data['tel']);
        $form_data['tel'] = str_replace("\r", '', $form_data['tel']);
        $form_data['tel'] = explode("\n", $form_data['tel']);
        $form_data['tel'] = json_encode($form_data['tel']);
        if ( ! empty($current_area_id)) {
            $form_data['area_id'] = $current_area_id;
        }
        $form_data['goods'] = json_encode($form_data['goods']);

        $result = DB::transaction(function() use($user, $form_data) {
            foreach (['area_id', 'username', 'password'] as $k) {
                if (isset($form_data[$k])) {
                    $user_data[$k] = $form_data[$k];
                    unset($form_data[$k]);
                }
            }
            if (empty($user_data['company_id'])) {
                $user_data['company_id'] = null;
            }
            if ( ! User::where('id', $user->id)->update(array_merge($user_data, ['edit_at' => date('Y-m-d H:i:s')]))) {
                return false;
            }
            $form_data['area_id'] = $user->area_id;
            if ( ! Supplier::where('id', $user->supplier->id)->update($form_data)) {
                DB::rollBack();
                return false;
            }
            return true;
        });
        if ($result) {
            return redirect($redirect_url)->with('tip_message', ['content' => '操作成功', 'state' => 'success']);
        } else {
            return redirect($redirect_url)->withErrors(['form' => ['服务器繁忙，请稍后再试']]);
        }
    }

    public function view(User $user)
    {
        if ($user->type != 'supplier') {
            App::abort(404, '没有找到对应的供应商');
        }
        if (!empty($user->deleted_at)) {
            App::abort(404, '供应商已被删除');
        }
        $user_addition = $user->supplier->addition;
        if ( ! empty($user_addition)) {
            $addition_data = json_decode($user_addition);
        }
        if (empty($addition_data)) {
            $addition_data = [];
        }
        $supplier_types = [
            '企业法人' => '企业法人',
            '个体户' => '个体户',
            '自然人' => '自然人'
        ];
        $supplier_type = $user->supplier->type;
        $addition = Setting::where('name', 'supplier_profile_addition')->first();
        if (empty($addition) or empty($addition->data)) {
            $addition = [];
        } else {
            $addition = json_decode($addition->data);
        }
        return view('user.supplier.view')
            ->with('user', $user)
            ->with('addition_data', $addition_data)
            ->with('supplier_types', $supplier_types)
            ->with('supplier_type', $supplier_type)
            ->with('addition', $addition);
    }

    public function disable(User $user)
    {
        $redirect_url = URL::previous();
        if ($user->type != 'supplier') {
            return redirect($redirect_url)->with('tip_message', ['content' => '没有找到对应的供应商', 'state' => 'warning', 'hold' => true]);
        }
        if (!empty($user->deleted_at)) {
            return redirect($redirect_url)->with('tip_message', ['content' => '供应商已被删除', 'state' => 'warning', 'hold' => true]);
        }
        $user->allow_login = ! $user->allow_login;
        $datetime = date('Y-m-d H:i:s');
        $user->updated_at = $datetime;
        $user->edit_at = $datetime;
        if ($user->save()) {
            return redirect($redirect_url)->with('tip_message', ['content' => '操作成功', 'state' => 'success', 'hold' => true]);
        } else {
            return redirect($redirect_url)->with('tip_message', ['content' => '服务器繁忙，请稍后再试', 'state' => 'warning', 'hold' => true]);
        }
    }
}
