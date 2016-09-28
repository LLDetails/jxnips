<?php

namespace App\Http\Controllers\Goods;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Category;
use Validator;
use URL;

class CategoryController extends Controller
{
    public function manage()
    {
        $categories = Category::orderBy('display_order', 'desc')->orderBy('created_at')->whereNull('deleted_at');
        $categories = $categories->get();
        return view('goods.category.manage')->with('categories', $categories);
    }

    public function add()
    {
        return view('goods.category.add');
    }

    public function create(Request $request)
    {
        $redirect_url = URL::full();
        $form_data = $request->only(['name', 'code', 'display_order']);
        $form_data = array_map('trim', $form_data);
        if (isset($form_data['code'])) {
            $form_data['code'] = strtoupper($form_data['code']);
        }
        if (isset($form_data['display_order'])) {
            $form_data['display_order'] = intval($form_data['display_order']);
        } else {
            $form_data['display_order'] = 0;
        }
        $rules = [
            'name'  => 'required|max:60|unique:categories,name',
            'code' => 'required|unique:categories,code',
            'display_order' => 'integer'
        ];
        $messages = [
            'required' => '必填项不能为空',
            'unique'   => '已被使用，不可重复',
            'max'      => '超出最大字数:max',
            'integer'  => '只能填写整数'
        ];
        $validator = Validator::make($form_data, $rules, $messages);
        if ($validator->fails()) {
            return redirect($redirect_url)->withErrors($validator->errors())->withInput();
        }

        $category = Category::create($form_data);
        if ($category) {
            return redirect($redirect_url)->with('tip_message', ['content' => '成功添加商品分类', 'state' => 'success']);
        } else {
            return redirect($redirect_url)->withErrors(['form' => ['服务器繁忙，请稍后再试']]);
        }
    }

    public function edit(Category $category)
    {
        return view('goods.category.edit')->with('category', $category);
    }

    public function update(Request $request, Category $category)
    {
        $redirect_url = URL::full();
        $form_data = $request->only(['name', 'code', 'display_order']);
        $form_data = array_map('trim', $form_data);
        if (isset($form_data['code'])) {
            $form_data['code'] = strtoupper($form_data['code']);
        }
        if (isset($form_data['display_order'])) {
            $form_data['display_order'] = intval($form_data['display_order']);
        } else {
            $form_data['order'] = 0;
        }
        $rules = [
            'name'  => 'required|max:60|unique:categories,name,' . $category->id,
            'code' => 'required|unique:categories,code,' . $category->id,
            'display_order' => 'integer'
        ];
        $messages = [
            'required' => '必填项不能为空',
            'unique'   => '已被使用，不可重复',
            'max'      => '超出最大字数:max',
            'integer'  => '只能填写整数'
        ];
        $validator = Validator::make($form_data, $rules, $messages);
        if ($validator->fails()) {
            return redirect($redirect_url)->withErrors($validator->errors())->withInput();
        }

        $category->name = $form_data['name'];
        $category->code = $form_data['code'];
        $category->display_order = $form_data['display_order'];
        if ($category->save()) {
            return redirect($redirect_url)->with('tip_message', ['content' => '编辑成功', 'state' => 'success']);
        } else {
            return redirect($redirect_url)->withErrors(['form' => ['服务器繁忙，请稍后再试']]);
        }
    }

    public function disable(Category $category)
    {
        $redirect_url = URL::previous();
        $category->is_available = ! $category->is_available;
        if ($category->save()) {
            return redirect($redirect_url)->with('tip_message', ['content' => '操作成功', 'state' => 'success', 'hold' => true]);
        } else {
            return redirect($redirect_url)->with('tip_message', ['content' => '服务器繁忙，请稍后再试', 'state' => 'warning', 'hold' => true]);
        }
    }
}
