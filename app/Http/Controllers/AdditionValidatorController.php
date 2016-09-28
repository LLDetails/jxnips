<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Validator;

class AdditionValidatorController extends Controller
{
    protected $textInput = [
        'tpl', 'widget', 'display', 'name',
        'size_min', 'size_max', 'default',
        'required', 'rule', 'prompt'
    ];

    protected $selectInput = [
        'tpl', 'widget', 'display', 'name',
        'default', 'other', 'list',
        'required', 'prompt'
    ];

    protected $fileInput = [
        'tpl', 'display', 'name', 'filetype',
        'size', 'required', 'prompt', 'count'
    ];

    protected $textareaInput = [
        'tpl', 'display', 'name',
        'default', 'required', 'prompt'
    ];

    protected function validateTextAddition($data)
    {
        $rules = [
            'tpl'      => 'required|in:text',
            'widget'   => 'required|in:text,password',
            'display'  => 'required',
            'name'     => 'required|alpha_dash',
            'size_min' => 'integer|min:0',
            'size_max' => 'integer|min:1',
            'required' => 'required|in:true,false',
            'rule'     => 'required|in:*,phone,email,date,numeric,integer'
        ];
        $messages = [
            'required'   => '必填项不能为空',
            'in'         => '错误的数据类型',
            'alpha_dash' => '只能填写字母、数字、下划线',
            'integer'    => '只能填写整数',
            'min'        => '允许的最小值为:min'
        ];
        $validator = Validator::make($data, $rules, $messages);
        if ($validator->fails()) {
            return $validator->errors();
        }
    }

    protected function validateSelectAddition($data)
    {
        $rules = [
            'tpl'      => 'required|in:select',
            'widget'   => 'required|in:radio,checkbox,select',
            'display'  => 'required',
            'name'     => 'required|alpha_dash',
            'list'     => 'required',
            'other'    => 'required|in:true,false',
            'required' => 'required|in:true,false'
        ];
        $messages = [
            'required'   => '必填项不能为空',
            'in'         => '错误的数据类型',
            'alpha_dash' => '只能填写字母、数字、下划线',
            'integer'    => '只能填写整数',
            'min'        => '允许的最小值为:min'
        ];
        $validator = Validator::make($data, $rules, $messages);
        if ($validator->fails()) {
            return $validator->errors();
        }
    }

    protected function validateFileAddition($data)
    {
        $rules = [
            'tpl'      => 'required|in:file',
            'display'  => 'required',
            'count'    => 'required|min:1',
            'name'     => 'required|alpha_dash',
            'size'     => 'required|integer|min:1',
            'filetype' => 'required|in:document,image,mixed',
            'required' => 'required|in:true,false'
        ];
        $messages = [
            'required'   => '必填项不能为空',
            'in'         => '错误的数据类型',
            'alpha_dash' => '只能填写字母、数字、下划线',
            'integer'    => '只能填写整数',
            'min'        => '允许的最小值为:min'
        ];
        $validator = Validator::make($data, $rules, $messages);
        if ($validator->fails()) {
            return $validator->errors();
        }
    }

    protected function validateTextareaAddition($data)
    {
        $rules = [
            'tpl'      => 'required|in:textarea',
            //'widget'   => 'required|in:radio,checkbox,select',
            'display'  => 'required',
            'name'     => 'required|alpha_dash',
            //'list'     => 'required',
            //'other'    => 'required|in:true,false',
            'required' => 'required|in:true,false'
        ];
        $messages = [
            'required'   => '必填项不能为空',
            'in'         => '错误的数据类型',
            'alpha_dash' => '只能填写字母、数字、下划线',
            'integer'    => '只能填写整数',
            'min'        => '允许的最小值为:min'
        ];
        $validator = Validator::make($data, $rules, $messages);
        if ($validator->fails()) {
            return $validator->errors();
        }
    }
}
