<?php

namespace App\Http\Controllers\Setting;

use App\AssignRule;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Validator;
use URL;

class AssignRuleController extends Controller
{
    public function manage()
    {
        $assign_rules = AssignRule::orderBy('created_at')->whereNull('deleted_at')->get();
        return view('setting.assign_rule.manage')->with('assign_rules', $assign_rules);
    }

    public function add()
    {
        return view('setting.assign_rule.add');
    }

    public function create(Request $request)
    {
        $redirect_url = URL::full();
        $form_data = $request->only(['name', 'rules']);
        $form_data = array_map('trim', $form_data);
        $rules = [
            'name'     => 'required|max:60|unique:assign_rules,name',
            'rules' => 'required'
        ];
        $messages = [
            'required' => '必填项不能为空',
            'unique'   => '已被使用，不可重复',
            'max'      => '超出最大字数:max'
        ];
        $validator = Validator::make($form_data, $rules, $messages);
        if ($validator->fails()) {
            return redirect($redirect_url)->withErrors($validator->errors())->withInput();
        }

        $assign_rules = str_replace("\r", '', $form_data['rules']);
        $assign_rules = explode("\n", $assign_rules);
        $assign_rules = array_map('trim', $assign_rules);
        $assign_rules = array_map('intval', $assign_rules);
        $assign_rules = array_map('abs', $assign_rules);
        if (array_sum($assign_rules) != 100) {
            return redirect($redirect_url)->withErrors(['form' => ['分配百分比不等于100%']])->withInput();
        }
        $form_data['rules'] = json_encode($assign_rules);

        $assign_rule = AssignRule::create($form_data);
        if ($assign_rule) {
            return redirect($redirect_url)->with('tip_message', ['content' => '成功添加分配规则', 'state' => 'success']);
        } else {
            return redirect($redirect_url)->withErrors(['form' => ['服务器繁忙，请稍后再试']]);
        }
    }

    public function edit(AssignRule $rule)
    {
        $rules = json_decode($rule->rules, true);
        $rules = implode("\r\n", $rules);
        return view('setting.assign_rule.edit')->with('rule', $rule)->with('rules', $rules);
    }

    public function update(Request $request, AssignRule $rule)
    {
        $redirect_url = URL::full();
        $form_data = array_map('trim', $request->only(['name', 'rules']));
        $rules = [
            'name'     => 'required|max:60|unique:assign_rules,name,'.$rule->id,
            'rules' => 'required'
        ];
        $messages = [
            'required' => '必填项不能为空',
            'unique'   => '已被使用，不可重复',
            'max'      => '超出最大字数:max'
        ];
        $validator = Validator::make($form_data, $rules, $messages);
        if ($validator->fails()) {
            return redirect($redirect_url)->withErrors($validator->errors())->withInput();
        }

        $assign_rules = str_replace("\r", '', $form_data['rules']);
        $assign_rules = explode("\n", $assign_rules);
        $assign_rules = array_map('trim', $assign_rules);
        $assign_rules = array_map('intval', $assign_rules);
        $assign_rules = array_map('abs', $assign_rules);
        if (array_sum($assign_rules) != 100) {
            return redirect($redirect_url)->withErrors(['form' => ['分配百分比不等于100%']])->withInput();
        }
        $rule->name = $form_data['name'];
        $rule->rules = json_encode($assign_rules);
        if ($rule->save()) {
            return redirect($redirect_url)->with('tip_message', ['content' => '操作成功', 'state' => 'success']);
        } else {
            return redirect($redirect_url)->withErrors(['form' => ['服务器繁忙，请稍后再试']]);
        }
    }

    public function delete(AssignRule $rule)
    {
        $redirect_url = URL::previous();

        if (!empty($rule->deleted_at)) {
            return redirect($redirect_url)->with('tip_message', ['content' => '该规则已被删除', 'state' => 'warning', 'hold' => true]);
        }
        $date = date('Y-m-d H:i:s');
        $rule->name = $rule->name.'#delete@'.$date;
        $rule->deleted_at = $date;
        if ($rule->save()) {
            return redirect($redirect_url)->with('tip_message', ['content' => '操作成功', 'state' => 'success', 'hold' => true]);
        } else {
            return redirect($redirect_url)->with('tip_message', ['content' => '服务器繁忙，请稍后再试', 'state' => 'danger', 'hold' => true]);
        }
    }
}
