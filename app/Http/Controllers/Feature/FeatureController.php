<?php

namespace App\Http\Controllers\Feature;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Feature;
use App\Permission;
use URL;
use Validator;
use DB;

class FeatureController extends Controller
{
    public function manage()
    {
        $groups = Feature::orderBy('group', 'asc')->lists('group')->toArray();
        $groups = array_unique($groups);

        $feature_data = [];
        foreach ($groups as $group) {
            $features = Feature::where('group', $group)->orderBy('display_order', 'desc')->get();
            $feature_data[$group] = $features;
        }

        return view('feature.manage')->with('feature_data', $feature_data);
    }

    public function add()
    {
        return view('feature.add');
    }

    public function create(Request $request)
    {
        $redirect_url = URL::full();
        $form_data = $request->only(['name', 'route', 'group', 'display_order']);
        $form_data = array_map('trim', $form_data);
        $rules = [
            'name'  => 'required|max:60',
            'route' => 'required|max:60',
            'group' => 'required|max:60',
            'display_order' => 'integer'
        ];
        $messages = [
            'required' => '必填项不能为空',
            'max'      => '超出最大字数:max'
        ];
        $validator = Validator::make($form_data, $rules, $messages);
        if ($validator->fails()) {
            return redirect($redirect_url)->withErrors($validator->errors())->withInput();
        }

        $form_data['display_order'] = intval($form_data['display_order']);
        $feature = Feature::create($form_data);
        if ($feature->save()) {
            return redirect($redirect_url)->with('tip_message', ['content' => '成功添加功能', 'state' => 'success']);
        } else {
            return redirect($redirect_url)->withErrors(['form' => ['服务器繁忙，请稍后再试']]);
        }
    }

    public function edit(Feature $feature)
    {
        return view('feature.edit')->with('feature', $feature);
    }

    public function update(Request $request, Feature $feature)
    {
        $redirect_url = URL::full();
        $form_data = $request->only(['name', 'route', 'group', 'display_order']);
        $form_data = array_map('trim', $form_data);
        $rules = [
            'name'  => 'required|max:60',
            'route' => 'required|max:60',
            'group' => 'required|max:60',
            'display_order' => 'integer'
        ];
        $messages = [
            'required' => '必填项不能为空',
            'max'      => '超出最大字数:max'
        ];
        $validator = Validator::make($form_data, $rules, $messages);
        if ($validator->fails()) {
            return redirect($redirect_url)->withErrors($validator->errors())->withInput();
        }

        $feature->name = $form_data['name'];
        $feature->route = $form_data['route'];
        $feature->group = $form_data['group'];
        $feature->display_order = intval($form_data['display_order']);
        if ($feature->save()) {
            return redirect($redirect_url)->with('tip_message', ['content' => '修改成功', 'state' => 'success']);
        } else {
            return redirect($redirect_url)->withErrors(['form' => ['服务器繁忙，请稍后再试']]);
        }
    }

    public function delete(Feature $feature)
    {
        $redirect_url = URL::previous();
        $result = DB::transaction(function() use($feature) {
            Permission::where('feature_id', $feature->id)->delete();

            if ( ! Feature::where('id', $feature->id)->delete()) {
                DB::rollback();
                return false;
            }

            return true;
        });

        if ($result) {
            return redirect($redirect_url)->with('tip_message', ['content' => '操作成功', 'state' => 'success', 'hold' => true]);
        } else {
            return redirect($redirect_url)->with('tip_message', ['content' => '服务器繁忙，请稍后再试', 'state' => 'danger', 'hold' => true]);
        }
    }
}
