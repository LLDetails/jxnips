<?php

namespace App\Http\Controllers\Role;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Role;
use App\Permission;
use App\Feature;
use Validator;
use URL;
use DB;
use Cache;

class RoleController extends Controller
{
    public function manage()
    {
        $roles = Role::orderBy('level')->orderBy('created_at')->whereNull('deleted_at');
        if (auth()->user()->role->level > 0) {
            $roles = $roles->where('level', '>', 0);
        }
        $roles = $roles->get();
        return view('role.manage')->with('roles', $roles);
    }

    public function add()
    {
        return view('role.add');
    }

    public function create(Request $request)
    {
        $redirect_url = URL::full();
        $form_data = $request->only(['name', 'level']);
        $form_data = array_map('trim', $form_data);
        $rules = [
            'name'  => 'required|max:60|unique:roles,name',
            'level' => 'integer|min:1'
        ];
        $messages = [
            'required' => '必填项不能为空',
            'unique'   => '已被使用，不可重复',
            'max'      => '超出最大字数:max',
            'level.min'=> '级别最小只能为1'
        ];
        $validator = Validator::make($form_data, $rules, $messages);
        if ($validator->fails()) {
            return redirect($redirect_url)->withErrors($validator->errors())->withInput();
        }

        $form_data['level'] = intval($form_data['level']);
        $role = Role::create($form_data);
        if ($role->save()) {
            return redirect($redirect_url)->with('tip_message', ['content' => '成功添加角色', 'state' => 'success']);
        } else {
            return redirect($redirect_url)->withErrors(['form' => ['服务器繁忙，请稍后再试']])->withInput();
        }
    }

    public function edit(Role $role)
    {
        $redirect_url = URL::full();
        if ($role->level < 1 and auth()->user()->role->level >= 1) {
            return redirect($redirect_url)->withErrors(['form' => ['没有权限修改此角色']])->withInput();
        }
        if (!empty($role->deleted_at)) {
            return redirect($redirect_url)->withErrors(['form' => ['该角色已被删除']])->withInput();
        }
        return view('role.edit')->with('role', $role);
    }

    public function update(Request $request, Role $role)
    {
        $redirect_url = URL::full();
        if ($role->level < 1 and auth()->user()->role->level >= 1) {
            return redirect($redirect_url)->withErrors(['form' => ['没有权限修改此角色']])->withInput();
        }
        if (!empty($role->deleted_at)) {
            return redirect($redirect_url)->withErrors(['form' => ['该角色已被删除']])->withInput();
        }
        $form_data = $request->only(['name', 'level']);
        $form_data = array_map('trim', $form_data);
        $rules = [
            'name'  => 'required|max:60|unique:roles,name,' . $role->id,
            'level' => 'integer|min:1'
        ];
        $messages = [
            'required' => '必填项不能为空',
            'unique'   => '已被使用，不可重复',
            'max'      => '超出最大字数:max',
            'level.min'=> '级别最小只能为1'
        ];
        $validator = Validator::make($form_data, $rules, $messages);
        if ($validator->fails()) {
            return redirect($redirect_url)->withErrors($validator->errors())->withInput();
        }

        $role->name = $form_data['name'];
        $role->level = $form_data['level'];
        if ($role->save()) {
            return redirect($redirect_url)->with('tip_message', ['content' => '编辑成功', 'state' => 'success']);
        } else {
            return redirect($redirect_url)->withErrors(['form' => ['服务器繁忙，请稍后再试']])->withInput();
        }
    }

    public function delete(Role $role)
    {
        $redirect_url = URL::previous();
        if ($role->level < 1 and auth()->user()->role->level >= 1) {
            return redirect($redirect_url)->with('tip_message', ['content' => '没有权限删除此角色', 'state' => 'warning', 'hold' => true]);
        }
        if (!empty($role->deleted_at)) {
            return redirect($redirect_url)->with('tip_message', ['content' => '角色已被删除', 'state' => 'warning', 'hold' => true]);
        }
        $date = date('Y-m-d H:i:s');
        $role->name = $role->name.'#delete@'.$date;
        $role->deleted_at = $date;
        if ($role->save()) {
            return redirect($redirect_url)->with('tip_message', ['content' => '操作成功', 'state' => 'success', 'hold' => true]);
        } else {
            return redirect($redirect_url)->with('tip_message', ['content' => '服务器繁忙，请稍后再试', 'state' => 'danger', 'hold' => true]);
        }
    }

    public function permission(Role $role)
    {
        $permissions = $role->permissions()->lists('feature_id')->toArray();
        $groups = Feature::orderBy('group', 'asc')->lists('group')->toArray();
        $groups = array_unique($groups);

        $feature_data = [];
        foreach ($groups as $group) {
            $features = Feature::where('group', $group)->orderBy('display_order', 'desc')->get();
            $feature_data[$group] = $features;
        }

        return view('role.permission')
            ->with('role', $role)
            ->with('feature_data', $feature_data)
            ->with('permissions', $permissions);
    }

    public function savePermission(Request $request, Role $role)
    {
        $redirect_url = URL::full();
        $feature_ids = $request->get('feature_id');

        $result = DB::transaction(function() use($role, $feature_ids) {
            Permission::where('role_id', $role->id)->delete();

            $permission_data = [];

            $date = date('Y-m-d H:i:s');
            if (is_array($feature_ids) and count($feature_ids) > 0) {
                foreach ($feature_ids as $id) {
                    $permission_data[] = [
                        'role_id' => $role->id,
                        'feature_id' => $id,
                        'created_at' => $date,
                        'updated_at' => $date
                    ];
                }
            }

            if ( ! DB::table('permissions')->insert($permission_data)) {
                DB::rollback();
                return false;
            }

            return true;
        });

        if ($result) {
            Cache::forget('permission.role.' . $role->id);
            return redirect($redirect_url)->with('tip_message', ['content' => '操作成功', 'state' => 'success', 'hold' => true]);
        } else {
            return redirect($redirect_url)->with('tip_message', ['content' => '服务器繁忙，请稍后再试', 'state' => 'danger', 'hold' => true]);
        }
    }
}
