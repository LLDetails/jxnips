<?php

namespace App\Http\Controllers\User;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\AdditionValidatorController;
use App\Setting;
use URL;

class SupplierAdditionController extends AdditionValidatorController
{
    public function index()
    {
        $addition = Setting::where('name', 'supplier_profile_addition')->first();
        if (empty($addition) or empty($addition->data)) {
            $addition = [];
        } else {
            $addition = json_decode($addition->data);
        }
        $type = [
            'text'     => '单行文本',
            'select'   => '选项',
            'file'     => '文件上传',
            'textarea' => '多行文本'
        ];
        return view('user.supplier.addition.index')
            ->with('addition', $addition)
            ->with('type', $type);
    }

    public function add(Request $request)
    {
        $tpl = $request->get('tpl', 'text');
        $tpl = trim($tpl);
        $templates = config('addition.templates');
        $template_names = array_keys($templates);
        if ( ! in_array($tpl, $template_names)) {
            $tpl = 'text';
        }
        $view = $templates[$tpl]['view'];
        return view($view)
            ->with('layout', 'user.supplier.addition.add')
            ->with('templates', $templates)
            ->with('tpl', $tpl);
    }

    public function append(Request $request)
    {
        $tpl = $request->get('tpl');
        $validate_function = 'validate'.ucfirst($tpl).'Addition';
        $input_fields = $tpl.'Input';

        $form_data = $request->only($this->$input_fields);
        $redirect_url = URL::full();

        $result = $this->$validate_function($form_data);
        if ( ! empty($result)) {
            return redirect($redirect_url)->withErrors($result)->withInput();
        }

        $addition = Setting::firstOrCreate(['name' => 'supplier_profile_addition']);
        if (empty($addition) or empty($addition->data)) {
            $addition_data = [];
        } else {
            $addition_data = json_decode($addition->data);
        }
        array_push($addition_data, $form_data);
        $addition->data = json_encode($addition_data);
        if ($addition->save()) {
            return redirect($redirect_url)->with('tip_message', ['content' => '创建属性成功', 'state' => 'success']);
        } else {
            return redirect($redirect_url)->withErrors(['form' => ['服务器繁忙，请稍后再试']]);
        }
    }

    public function edit($addition_id)
    {
        $redirect_url = URL::full();
        $addition = Setting::firstOrCreate(['name' => 'supplier_profile_addition']);
        if (empty($addition) or empty($addition->data)) {
            $addition_data = [];
        } else {
            $addition_data = json_decode($addition->data);
        }
        if (empty($addition_data[$addition_id])) {
            return redirect($redirect_url)->with('tip_message', ['content' => '没有找到对应属性', 'state' => 'warning']);
        }

        $templates = config('addition.templates');
        $template_names = array_keys($templates);
        if (empty($addition_data[$addition_id]->tpl) or  ! in_array($addition_data[$addition_id]->tpl, $template_names)) {
            return redirect($redirect_url)->with('tip_message', ['content' => '不支持的属性类型', 'state' => 'danger']);
        }
        $view = $templates[$addition_data[$addition_id]->tpl]['view'] . '_edit';

        return view($view)
            ->with('layout', 'user.supplier.addition.edit')
            ->with('tpl', $addition_data[$addition_id]->tpl)
            ->with('templates', $templates)
            ->with('addition', $addition_data[$addition_id]);
    }

    public function save(Request $request, $addition_id)
    {
        $redirect_url = URL::full();
        $addition = Setting::firstOrCreate(['name' => 'supplier_profile_addition']);
        if (empty($addition) or empty($addition->data)) {
            $addition_data = [];
        } else {
            $addition_data = json_decode($addition->data);
        }
        if (empty($addition_data[$addition_id])) {
            return redirect($redirect_url)->with('tip_message', ['content' => '没有找到对应属性', 'state' => 'warning']);
        }

        $templates = config('addition.templates');
        $template_names = array_keys($templates);
        if (empty($addition_data[$addition_id]->tpl) or  ! in_array($addition_data[$addition_id]->tpl, $template_names)) {
            return redirect($redirect_url)->with('tip_message', ['content' => '不支持的属性类型', 'state' => 'danger']);
        }

        $tpl = $request->get('tpl');
        $validate_function = 'validate'.ucfirst($tpl).'Addition';
        $input_fields = $tpl.'Input';
        $form_data = $request->only($this->$input_fields);

        $result = $this->$validate_function($form_data);
        if ( ! empty($result)) {
            return redirect($redirect_url)->withErrors($result)->withInput();
        }

        $addition_data[$addition_id] = $form_data;
        $addition->data = json_encode($addition_data);
        if ($addition->save()) {
            return redirect($redirect_url)->with('tip_message', ['content' => '创建编辑成功', 'state' => 'success']);
        } else {
            return redirect($redirect_url)->withErrors(['form' => ['服务器繁忙，请稍后再试']]);
        }
    }

    public function delete($addition_id)
    {
        $redirect_url = URL::previous();
        $addition = Setting::firstOrCreate(['name' => 'supplier_profile_addition']);
        if (empty($addition) or empty($addition->data)) {
            $addition_data = [];
        } else {
            $addition_data = json_decode($addition->data);
        }
        if (empty($addition_data[$addition_id])) {
            return redirect($redirect_url)->with('tip_message', ['content' => '没有找到对应属性', 'state' => 'warning', 'hold' => true]);
        }

        $templates = config('addition.templates');
        $template_names = array_keys($templates);
        if (empty($addition_data[$addition_id]->tpl) or  ! in_array($addition_data[$addition_id]->tpl, $template_names)) {
            return redirect($redirect_url)->with('tip_message', ['content' => '不支持的属性类型', 'state' => 'danger', 'hold' => true]);
        }

        unset($addition_data[$addition_id]);
        $addition_data = array_values($addition_data);
        $addition->data = json_encode($addition_data);
        if ($addition->save()) {
            return redirect($redirect_url)->with('tip_message', ['content' => '操作成功', 'state' => 'success', 'hold' => true]);
        } else {
            return redirect($redirect_url)->with('tip_message', ['content' => '服务器繁忙，请稍后再试', 'state' => 'danger', 'hold' => true]);
        }
    }
}
