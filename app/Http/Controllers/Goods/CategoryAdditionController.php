<?php

namespace App\Http\Controllers\Goods;

use App\Category;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\AdditionValidatorController;
use URL;

class CategoryAdditionController extends AdditionValidatorController
{
    public function index(Request $request, Category $category)
    {
        $deal_addition = intval($request->get('deal_addition', 0));
        if ($deal_addition == 1) {
            $addition = $category->deal_addition;
        } else {
            $addition = $category->addition;
        }
        if (empty($addition)) {
            $addition = [];
        } else {
            $addition = json_decode($addition);
        }
        $type = [
            'text'     => '单行文本',
            'select'   => '选项',
            'file'     => '文件上传',
            'textarea' => '多行文本'
        ];
        return view('goods.category.addition.index')
            ->with('addition', $addition)
            ->with('category', $category)
            ->with('deal_addition', $deal_addition)
            ->with('type', $type);
    }

    public function add(Request $request, Category $category)
    {
        $deal_addition = $request->get('deal_addition');
        $tpl = $request->get('tpl', 'text');
        $tpl = trim($tpl);
        $templates = config('addition.templates');
        $template_names = array_keys($templates);
        if ( ! in_array($tpl, $template_names)) {
            $tpl = 'text';
        }
        $view = $templates[$tpl]['view'];
        return view($view)
            ->with('layout', 'goods.category.addition.add')
            ->with('templates', $templates)
            ->with('category', $category)
            ->with('deal_addition', $deal_addition)
            ->with('tpl', $tpl);
    }

    public function append(Request $request, Category $category)
    {
        $deal_addition = $request->get('deal_addition');
        $tpl = $request->get('tpl');
        $validate_function = 'validate'.ucfirst($tpl).'Addition';
        $input_fields = $tpl.'Input';

        $form_data = $request->only($this->$input_fields);
        $redirect_url = URL::full();

        $result = $this->$validate_function($form_data);
        if ( ! empty($result)) {
            return redirect($redirect_url)->withErrors($result)->withInput();
        }

        if ($deal_addition == 1) {
            $addition = $category->deal_addition;
        } else {
            $addition = $category->addition;
        }
        if (empty($addition)) {
            $addition_data = [];
        } else {
            $addition_data = json_decode($addition);
        }
        array_push($addition_data, $form_data);
        if ($deal_addition == 1) {
            $category->deal_addition = json_encode($addition_data);
        } else {
            $category->addition = json_encode($addition_data);
        }
        if ($category->save()) {
            return redirect($redirect_url)->with('tip_message', ['content' => '创建属性成功', 'state' => 'success']);
        } else {
            return redirect($redirect_url)->withErrors(['form' => ['服务器繁忙，请稍后再试']]);
        }
    }

    public function edit(Request $request, Category $category, $addition_id)
    {
        $redirect_url = URL::full();
        $deal_addition = $request->get('deal_addition');
        if ($deal_addition == 1) {
            $addition = $category->deal_addition;
        } else {
            $addition = $category->addition;
        }
        if (empty($addition)) {
            $addition_data = [];
        } else {
            $addition_data = json_decode($addition);
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
            ->with('layout', 'goods.category.addition.edit')
            ->with('tpl', $addition_data[$addition_id]->tpl)
            ->with('templates', $templates)
            ->with('category', $category)
            ->with('deal_addition', $deal_addition)
            ->with('addition', $addition_data[$addition_id]);
    }

    public function save(Request $request, Category $category, $addition_id)
    {
        $redirect_url = URL::full();
        $deal_addition = $request->get('deal_addition');
        if ($deal_addition == 1) {
            $addition = $category->deal_addition;
        } else {
            $addition = $category->addition;
        }
        if (empty($addition)) {
            $addition_data = [];
        } else {
            $addition_data = json_decode($addition);
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
        if ($deal_addition == 1) {
            $category->deal_addition = json_encode($addition_data);
        } else {
            $category->addition = json_encode($addition_data);
        }
        if ($category->save()) {
            return redirect($redirect_url)->with('tip_message', ['content' => '创建编辑成功', 'state' => 'success']);
        } else {
            return redirect($redirect_url)->withErrors(['form' => ['服务器繁忙，请稍后再试']]);
        }
    }

    public function delete(Request $request, Category $category, $addition_id)
    {
        $redirect_url = URL::previous();
        $deal_addition = $request->get('deal_addition');
        if ($deal_addition == 1) {
            $addition = $category->deal_addition;
        } else {
            $addition = $category->addition;
        }
        if (empty($addition)) {
            $addition_data = [];
        } else {
            $addition_data = json_decode($addition);
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
        if ($deal_addition == 1) {
            $category->deal_addition = json_encode($addition_data);
        } else {
            $category->addition = json_encode($addition_data);
        }
        if ($category->save()) {
            return redirect($redirect_url)->with('tip_message', ['content' => '操作成功', 'state' => 'success', 'hold' => true]);
        } else {
            return redirect($redirect_url)->with('tip_message', ['content' => '服务器繁忙，请稍后再试', 'state' => 'danger', 'hold' => true]);
        }
    }
}
