<?php

namespace App\Http\Controllers\User;

use App\Area;
use App\Company;
use App\Category;
use App\Role;
use App\Setting;
use App\Staff;
use App\User;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Validator;
use URL;
use Hash;
use DB;
use App;

class StaffController extends Controller
{
    public function manage(Request $request)
    {
        $areas = Area::orderBy('display_order', 'asc')
            ->whereNull('deleted_at')
            ->get();
        $roles = Role::orderBy('level', 'asc');
        $users = User::with(['staff', 'role', 'area'])->orderBy('area_id', 'desc')->orderBy('role_id')->where('type', 'staff');
        if ( ! empty(auth()->user()->company_id)) {
            $users = $users->where('company_id', auth()->user()->company_id);
        }
        if (auth()->user()->role->level > 0) {
            $users = $users->whereHas('role', function($query) {
                return $query->where('level', '>', 0);
            });
            $roles = $roles->where('level', '>', 0);
        } else {
            $users = $users->whereHas('role', function($query) {
                return $query->where('level', '>=', 0);
            });
            $roles = $roles->where('level', '>=', 0);
        }
        $roles = $roles->get();
        $search_param = $request->only(['area_id', 'role_id', 'username', 'realname']);
        $search_param = array_map('trim', $search_param);
        if ( ! empty($search_param['area_id'])) {
            $users = $users->where('area_id', $search_param['area_id']);
        }
        if ( ! empty($search_param['role_id'])) {
            $users = $users->where('role_id', $search_param['role_id']);
        }
        if ( ! empty($search_param['username'])) {
            $users = $users->where('username', $search_param['username']);
        }
        if ( ! empty($search_param['realname'])) {
            $users = $users->whereHas('staff', function($query) use($search_param) {
                return $query->where('realname', 'like', '%'.$search_param['realname'].'%');
            });
        }
        $users = $users->paginate(10);
        $pages = $users->appends($search_param)->render();
        return view('user.staff.manage')
            ->with('users', $users)
            ->with('pages', $pages)
            ->with('areas', $areas)
            ->with('roles', $roles);
    }

    public function add(Request $request)
    {
        $areas = Area::orderBy('display_order', 'asc')
            ->whereNull('deleted_at');
        $company_id = trim($request->get('company_id'));
        if ( ! empty($company_id)) {
            $areas = $areas->where('id', $company_id);
        }
        $areas = $areas->get();
        $roles = Role::orderBy('level', 'asc')->where('level', '>', 0)->get();
        $companies = Company::orderBy('area_id', 'asc')->orderBy('created_at', 'asc');
        $categories = Category::orderBy('code', 'asc')->orderBy('created_at', 'asc')->whereNull('deleted_at')->get();
        if ( ! empty(auth()->user()->company_id)) {
            $companies = $companies->where('id', auth()->user()->company_id);
        }
        $companies = $companies->get();
        $addition = Setting::where('name', 'staff_addition')->first();
        if (empty($addition) or empty($addition->data)) {
            $addition = [];
        } else {
            $addition = json_decode($addition->data);
        }
        return view('user.staff.add')
            ->with('areas', $areas)
            ->with('companies', $companies)
            ->with('categories', $categories)
            ->with('roles', $roles)
            ->with('addition', $addition);
    }

    public function create(Request $request)
    {
        //var_dump(URL::previous());exit;
        $redirect_url = URL::full();
        $addition = Setting::where('name', 'staff_addition')->first();
        if (empty($addition) or empty($addition->data)) {
            $addition_data = [];
        } else {
            $addition_data = json_decode($addition->data);
        }
        $areas = Area::orderBy('display_order', 'asc')
            ->whereNull('deleted_at');
        $company_id = trim($request->get('company_id'));
        if ( ! empty($company_id)) {
            $company = Company::find($company_id);
            if ( ! empty($company)) {
                $areas = $areas->where('id', $company->area_id);
            }
        }
        $area_ids = $areas->lists('id')->toArray();
        $role_ids = Role::orderBy('level', 'asc')->where('level', '>', 0)->lists('id')->toArray();
        $company_ids = Company::orderBy('area_id', 'asc')->orderBy('created_at', 'asc');
        if ( ! empty(auth()->user()->company_id)) {
            $company_ids = $company_ids->where('id', auth()->user()->company_id);
        }
        $company_ids = $company_ids->lists('id')->toArray();
        $category_ids = Category::whereNull('deleted_at')->lists('id')->toArray();
        $allow_input = ['role_id', 'area_id', 'company_id', 'category_id', 'username', 'password', 'hiredate', 'is_regular', 'address', 'phone', 'realname'];
        $rules = [
            'role_id' => 'required|in:'.implode(',', $role_ids),
            'area_id' => 'in:'.implode(',', $area_ids),
            'company_id' => 'in:'.implode(',', $company_ids),
            'category_id' => 'in:'.implode(',', $category_ids),
            'username' => 'required|max:20|unique:users,username',
            'password' => 'required|max:30',
            'hiredate' => 'date',
            'is_regular' => 'required|in:true,false',
            'realname' => 'required|max:30'
        ];
        if ( ! empty(auth()->user()->company_id)) {
            $rules['company_id'] = 'required|'.$rules['company_id'];
        }
        $messages = [
            'required' => '必填项不能为空',
            'max' => '字符长度超出:max',
            'unique' => '已经存在，不能重复',
            'date' => '请输入日期格式(如：'.date('Y-m-d').')',
            'in' => '非法的值',
            'is_regular.required' => '请选择入职方式'
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
        $form_data['phone'] = trim($form_data['phone']);
        $form_data['phone'] = str_replace("\r", '', $form_data['phone']);
        $form_data['phone'] = explode("\n", $form_data['phone']);
        $form_data['phone'] = json_encode($form_data['phone']);

        $result = DB::transaction(function() use($form_data) {
            $date = date('Y-m-d H:i:s');
            $user_data = ['type' => 'staff', 'created_at' => $date, 'updated_at'=>$date];
            foreach (['role_id', 'area_id', 'company_id', 'category_id', 'username', 'password'] as $k) {
                if (isset($form_data[$k])) {
                    $user_data[$k] = $form_data[$k];
                }
                unset($form_data[$k]);
            }
            if (empty($user_data['area_id'])) {
                $user_data['area_id'] = null;
            }
            if (empty($user_data['company_id'])) {
                $user_data['company_id'] = null;
            }
            if (empty($user_data['category_id'])) {
                $user_data['category_id'] = null;
            }
            $user = User::create(array_merge($user_data, ['edit_at' => date('Y-m-d H:i:s')]));
            if ( ! $user or ! $user->save()) {
                return false;
            }
            $form_data['user_id'] = $user->id;
            if ($form_data['is_regular'] == 'true') {
                $form_data['is_regular'] = true;
            } else {
                $form_data['is_regular'] = false;
            }
            $form_data['created_at'] = $date;
            $form_data['updated_at'] = $date;
            $staff = Staff::create($form_data);
            if ( ! $staff or ! $staff->save()) {
                DB::rollBack();
                return false;
            }
            return true;
        });

        if ($result) {
            return redirect($redirect_url)->with('tip_message', ['content' => '成功新建用户', 'state' => 'success']);
        } else {
            return redirect($redirect_url)->withErrors(['form' => ['服务器繁忙，请稍后再试']]);
        }
    }

    public function edit(Request $request, User $user)
    {
        if ($user->type != 'staff') {
            App::abort(404, '没有找到对应的用户');
        }
        if (!empty($user->deleted_at)) {
            App::abort(404, '用户已被删除');
        }

        if ( ! empty(auth()->user()->company_id) and $user->company_id != auth()->user()->company_id) {
            App::abort(403, '无权编辑此用户');
        }

        $user_addition = $user->staff->addition;
        if ( ! empty($user_addition)) {
            $addition_data = json_decode($user_addition);
        }
        if (empty($addition_data)) {
            $addition_data = [];
        }

        $areas = Area::orderBy('display_order', 'asc')
            ->whereNull('deleted_at');
        $company_id = trim($request->get('company_id'));
        if ( ! empty($company_id)) {
            $areas = $areas->where('id', $company_id);
        }
        $areas = $areas->get();
        $roles = Role::orderBy('level', 'asc')->where('level', '>', 0)->get();
        $companies = Company::orderBy('area_id', 'asc')->orderBy('created_at', 'asc');
        if ( ! empty(auth()->user()->company_id) and $user->company_id != auth()->user()->company_id) {
            $companies = $companies->where('id', auth()->user()->company_id);
        }
        $companies = $companies->get();
        $addition = Setting::where('name', 'staff_addition')->first();
        if (empty($addition) or empty($addition->data)) {
            $addition = [];
        } else {
            $addition = json_decode($addition->data);
        }
        $categories = Category::orderBy('code', 'asc')->orderBy('created_at', 'asc')->whereNull('deleted_at')->get();
        return view('user.staff.edit')
            ->with('user', $user)
            ->with('addition_data', $addition_data)
            ->with('areas', $areas)
            ->with('roles', $roles)
            ->with('companies', $companies)
            ->with('categories', $categories)
            ->with('addition', $addition);
    }

    public function update(Request $request, User $user)
    {
        $redirect_url = URL::full();

        if ( ! empty(auth()->user()->company_id) and $user->company_id != auth()->user()->company_id) {
            App::abort(403, '无权编辑此用户');
        }

        $addition = Setting::where('name', 'staff_addition')->first();
        if (empty($addition) or empty($addition->data)) {
            $addition_data = [];
        } else {
            $addition_data = json_decode($addition->data);
        }
        $areas = Area::orderBy('display_order', 'asc')
            ->whereNull('deleted_at');
        $company_id = trim($request->get('company_id'));
        if ( ! empty($company_id)) {
            $company = Company::find($company_id);
            if ( ! empty($company)) {
                $areas = $areas->where('id', $company->area_id);
            }
        }
        $area_ids = $areas->lists('id')->toArray();
        $role_ids = Role::orderBy('level', 'asc')->where('level', '>', 0)->lists('id')->toArray();
        $company_ids = Company::orderBy('area_id', 'asc')->orderBy('created_at', 'asc');
        if ( ! empty(auth()->user()->company_id) and $user->company_id != auth()->user()->company_id) {
            $company_ids = $company_ids->where('id', auth()->user()->company_id);
        }
        $company_ids = $company_ids->lists('id')->toArray();
        $category_ids = Category::whereNull('deleted_at')->lists('id')->toArray();
        $allow_input = ['role_id', 'area_id', 'company_id', 'category_id', 'username', 'password', 'hiredate', 'is_regular', 'phone', 'address', 'realname'];
        $rules = [
            'role_id' => 'required|in:'.implode(',', $role_ids),
            'area_id' => 'in:'.implode(',', $area_ids),
            'company_id' => 'in:'.implode(',', $company_ids),
            'category_id' => 'in:'.implode(',', $category_ids),
            'username' => 'required|max:20|unique:users,username,'.$user->id,
            'password' => 'max:30',
            'hiredate' => 'date',
            'is_regular' => 'required|in:true,false',
            'realname' => 'required|max:30'
        ];
        if ( ! empty(auth()->user()->company_id)) {
            $rules['company_id'] = 'required|'.$rules['company_id'];
        }
        $messages = [
            'required' => '必填项不能为空',
            'max' => '字符长度超出:max',
            'unique' => '已经存在，不能重复',
            'date' => '请输入日期格式(如：'.date('Y-m-d').')',
            'in' => '非法的值',
            'is_regular.required' => '请选择入职方式'
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
        if (isset($form_data['password']) and ! empty($form_data['password'])) {
            $form_data['password'] = Hash::make($form_data['password']);
        } else {
            unset($form_data['password']);
        }
        $form_data['phone'] = trim($form_data['phone']);
        $form_data['phone'] = str_replace("\r", '', $form_data['phone']);
        $form_data['phone'] = explode("\n", $form_data['phone']);
        $form_data['phone'] = json_encode($form_data['phone']);

        $result = DB::transaction(function() use($user, $form_data) {
            $date = date('Y-m-d H:i:s');
            $user_data = ['type' => 'staff', 'updated_at'=>$date];
            foreach (['role_id', 'area_id', 'company_id', 'category_id', 'username', 'password'] as $k) {
                if (isset($form_data[$k])) {
                    $user_data[$k] = $form_data[$k];
                }
                unset($form_data[$k]);
            }
            if (empty($user_data['area_id'])) {
                $user_data['area_id'] = null;
            }
            if (empty($user_data['company_id'])) {
                $user_data['company_id'] = null;
            }
            if (empty($user_data['category_id'])) {
                $user_data['category_id'] = null;
            }
            foreach ($user_data as $field => $data) {
                $user->$field = $data;
            }
            $user->edit_at = date('Y-m-d H:i:s');
            if (! $user->save()) {
                return false;
            }
            $form_data['user_id'] = $user->id;
            if ($form_data['is_regular'] == 'true') {
                $form_data['is_regular'] = true;
            } else {
                $form_data['is_regular'] = false;
            }
            $form_data['updated_at'] = $date;
            if ( ! Staff::where('user_id', $user->id)->update($form_data)) {
                DB::rollBack();
                return false;
            }
            return true;
        });

        if ($result) {
            return redirect($redirect_url)->with('tip_message', ['content' => '编辑用户成功', 'state' => 'success']);
        } else {
            return redirect($redirect_url)->withErrors(['form' => ['服务器繁忙，请稍后再试']]);
        }
    }

    public function view(User $user)
    {
        if ($user->type != 'staff') {
            App::abort(404, '没有找到对应的用户');
        }
        if (!empty($user->deleted_at)) {
            App::abort(404, '用户已被删除');
        }
        $user_addition = $user->staff->addition;
        if ( ! empty($user_addition)) {
            $addition_data = json_decode($user_addition);
        }
        if (empty($addition_data)) {
            $addition_data = [];
        }
        $addition = Setting::where('name', 'staff_addition')->first();
        if (empty($addition) or empty($addition->data)) {
            $addition = [];
        } else {
            $addition = json_decode($addition->data);
        }
        return view('user.staff.view')
            ->with('addition_data', $addition_data)
            ->with('addition', $addition)
            ->with('user', $user);
    }

    public function disable(User $user)
    {
        $redirect_url = URL::previous();

        if (!empty(auth()->user()->company_id) and $user->company_id != auth()->user()->company_id) {
            App::abort(403, '无权更改此用户数据');
        }

        if ($user->type != 'staff') {
            return redirect($redirect_url)->with('tip_message', ['content' => '没有找到对应的用户', 'state' => 'warning', 'hold' => true]);
        }
        if (!empty($user->deleted_at)) {
            return redirect($redirect_url)->with('tip_message', ['content' => '用户已被删除', 'state' => 'warning', 'hold' => true]);
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
