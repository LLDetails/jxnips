<?php

namespace App\Http\Controllers\Company;

use App\DeliveryMode;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use URL;
use Validator;

class DeliveryModeController extends Controller
{
    public function manage()
    {
        $company = auth()->user()->company;
        $delivery_modes = DeliveryMode::orderBy('created_at')->whereNull('deleted_at')->where('company_id', $company->id);
        $delivery_modes = $delivery_modes->get();
        return view('company.delivery_mode.manage')->with('delivery_modes', $delivery_modes);
    }

    public function add()
    {
        return view('company.delivery_mode.add');
    }

    public function create(Request $request)
    {
        $redirect_url = URL::full();
        $company = auth()->user()->company;
        $form_data = $request->only(['mode', 'costs']);
        $form_data = array_map('trim', $form_data);
        $rules = [
            'mode'  => 'required|max:60|unique:delivery_modes,mode,NULL,id,company_id,'.$company->id,
            'costs' => 'required|numeric|min:0'
        ];
        $messages = [
            'required' => '必填项不能为空',
            'unique'   => '已被使用，不可重复',
            'max'      => '超出最大字数:max',
            'numeric' => '只能填写数字',
            'amount.min'=> '请填写正数'
        ];
        $validator = Validator::make($form_data, $rules, $messages);
        if ($validator->fails()) {
            return redirect($redirect_url)->withErrors($validator->errors())->withInput();
        }

        $form_data['company_id'] = $company->id;
        $delivery_mode = DeliveryMode::create($form_data);
        if ($delivery_mode->save()) {
            return redirect($redirect_url)->with('tip_message', ['content' => '成功添加到货方式', 'state' => 'success']);
        } else {
            return redirect($redirect_url)->withErrors(['form' => ['服务器繁忙，请稍后再试']])->withInput();
        }
    }

    public function edit(DeliveryMode $delivery_mode)
    {
        //$redirect_url = URL::full();
        return view('company.delivery_mode.edit')->with('delivery_mode', $delivery_mode);
    }

    public function update(Request $request, DeliveryMode $delivery_mode)
    {
        $redirect_url = URL::full();
        $company = auth()->user()->company;
        $form_data = $request->only(['mode', 'costs']);
        $form_data = array_map('trim', $form_data);
        $rules = [
            'mode'  => 'required|max:60|unique:delivery_modes,mode,'.$delivery_mode->id.',id,company_id,'.$company->id,
            'costs' => 'required|numeric|min:0'
        ];
        $messages = [
            'required' => '必填项不能为空',
            'unique'   => '已被使用，不可重复',
            'max'      => '超出最大字数:max',
            'numeric' => '只能填写数字',
            'amount.min'=> '请填写正数'
        ];
        $validator = Validator::make($form_data, $rules, $messages);
        if ($validator->fails()) {
            return redirect($redirect_url)->withErrors($validator->errors())->withInput();
        }

        $delivery_mode->mode = $form_data['mode'];
        $delivery_mode->costs = $form_data['costs'];
        if ($delivery_mode->save()) {
            return redirect($redirect_url)->with('tip_message', ['content' => '编辑成功', 'state' => 'success']);
        } else {
            return redirect($redirect_url)->withErrors(['form' => ['服务器繁忙，请稍后再试']])->withInput();
        }
    }
}
