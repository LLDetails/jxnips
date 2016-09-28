<?php

namespace App\Http\Controllers\Area;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Area;
use URL;
use Validator;

class AreaController extends Controller
{
    public function manage()
    {
        $areas = Area::orderBy('display_order', 'asc');
        $areas = $areas->whereNull('deleted_at')->get();
        return view('area.manage')->with('areas', $areas);
    }

    public function add()
    {
        $areas = Area::orderBy('display_order', 'asc');
        $areas = $areas->get();
        return view('area.add')->with('areas', $areas);
    }

    public function create(Request $request)
    {
        $redirect_url = URL::full();
        $form_data = $request->only(['name', 'display_order']);
        $form_data = array_map('trim', $form_data);
        $rules = [
            'name'     => 'required|max:60|unique:areas,name',
            'display_order' => 'integer'
        ];
        $messages = [
            'required' => '必填项不能为空',
            'unique'   => '已被使用，不可重复',
            'max'      => '超出最大字数:max',
            'integer'  => '请填写整数'
        ];
        $validator = Validator::make($form_data, $rules, $messages);
        if ($validator->fails()) {
            return redirect($redirect_url)->withErrors($validator->errors())->withInput();
        }
        if (empty($form_data['display_order'])) {
            $form_data['display_order'] = 0;
        }
        $date = date('Y-m-d H:i:s');
        $form_data['created_at'] = $date;
        $form_data['updated_at'] = $date;
        $area = Area::create($form_data);
        if ($area->save()) {
            return redirect($redirect_url)->with('tip_message', ['content' => '成功添加地区', 'state' => 'success']);
        } else {
            return redirect($redirect_url)->withErrors(['form' => ['服务器繁忙，请稍后再试']]);
        }
    }

    public function edit(Area $area)
    {
        $redirect_url = URL::full();

        if (!empty($area->deleted_at)) {
            return redirect($redirect_url)->withErrors(['form' => ['该地区已被删除']]);
        }
        return view('area.edit')->with('area', $area);
    }

    public function update(Request $request, Area $area)
    {
        $redirect_url = URL::full();

        if (!empty($area->deleted_at)) {
            return redirect($redirect_url)->withErrors(['form' => ['该地区已被删除']]);
        }
        $form_data = $request->only(['name', 'display_order']);
        $form_data = array_map('trim', $form_data);
        $rules = [
            'name'     => 'required|max:60|unique:areas,name,' . $area->id,
            'display_order' => 'integer'
        ];
        $messages = [
            'required' => '必填项不能为空',
            'unique'   => '已被使用，不可重复',
            'max'      => '超出最大字数:max',
            'integer'  => '请填写整数'
        ];
        $validator = Validator::make($form_data, $rules, $messages);
        if ($validator->fails()) {
            return redirect($redirect_url)->withErrors($validator->errors())->withInput();
        }
        if (empty($form_data['display_order'])) {
            $form_data['display_order'] = 0;
        }
        $area->name = $form_data['name'];
        $area->display_order = $form_data['display_order'];
        if ($area->save()) {
            $redirect_url = route('area.edit', ['area' => $area->id]);
            return redirect($redirect_url)->with('tip_message', ['content' => '编辑成功', 'state' => 'success']);
        } else {
            return redirect($redirect_url)->withErrors(['form' => ['服务器繁忙，请稍后再试']]);
        }
    }

    public function delete(Area $area)
    {
        $redirect_url = URL::previous();

        if (!empty($area->deleted_at)) {
            return redirect($redirect_url)->with('tip_message', ['content' => '该地区已被删除', 'state' => 'warning', 'hold' => true]);
        }
        $date = date('Y-m-d H:i:s');
        $area->name = $area->name.'#delete@'.$date;
        $area->deleted_at = $date;
        if ($area->save()) {
            return redirect($redirect_url)->with('tip_message', ['content' => '操作成功', 'state' => 'success', 'hold' => true]);
        } else {
            return redirect($redirect_url)->with('tip_message', ['content' => '服务器繁忙，请稍后再试', 'state' => 'danger', 'hold' => true]);
        }
    }
}
