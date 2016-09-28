<?php

namespace App\Http\Controllers\Goods;

use App\Goods;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Category;
use App;
use URL;
use Validator;

class GoodsController extends Controller
{
    public function manage(Request $request)
    {
        $id = trim($request->get('id'));
        $categories = Category::orderBy('code')->whereNull('deleted_at')->get();
        $goods_records = Goods::with('category')->orderBy('category_id')->orderBy('code')->whereNull('deleted_at');
        $cond = $request->only('category_id', 'name', 'is_available');

        if ( ! empty($cond['is_available'])) {
            if ($cond['is_available'] == 'true') {
                $is_available = true;
            } else {
                $is_available = false;
            }
            $goods_records = $goods_records->where('is_available', $is_available);
        }

        if (!empty($cond['category_id'])) {
            $goods_records = $goods_records->where('category_id', $cond['category_id']);
        }
        if (!empty($cond['name'])) {
            $goods_records = $goods_records->where('name', 'like', '%'.$cond['name'].'%');
        }
        $goods_records = $goods_records->paginate(10);
        $pages = $goods_records->appends($cond)->render();
        return view('goods.manage')
            ->with('categories', $categories)
            ->with('goods_records', $goods_records)
            ->with('pages', $pages);
    }

    public function add(Request $request)
    {
        $current_category = null;
        $category_id = trim($request->get('category_id'));
        if ( ! empty($category_id)) {
            $current_category = Category::whereNull('deleted_at')->where('id', $category_id)->first();
            $addition = json_decode($current_category->addition);
        }
        if (empty($addition)) {
            $addition = [];
        }
        $categories = Category::orderBy('code')->whereNull('deleted_at')->get();
        return view('goods.add')
            ->with('categories', $categories)
            ->with('current_category', $current_category)
            ->with('addition', $addition);
    }

    public function create(Request $request)
    {
        $redirect_url = URL::full();
        $category_ids = Category::whereNull('deleted_at')->lists('id')->toArray();
        $allow_input = ['name', 'category_id', 'code', 'unit', 'quality_standard', 'price_validity'];
        $category_id = trim($request->get('category_id'));
        try {
            $addition = Category::find($category_id);
        } catch (\Exception $e) {
            $addition = null;
        }
        //$addition = Category::find($category_id);
        if (empty($addition) or empty($addition->addition)) {
            $addition_data = [];
        } else {
            $addition_data = json_decode($addition->addition);
        }

        $rules = [
            'name'  => 'required|max:100|unique:goods,name',
            'code' => 'required|max:30|unique:goods,code',
            'price_validity' => 'required|integer|min:1,max:72',
            'category_id' => 'required|in:'.implode(',', $category_ids),
            'unit' => 'required'
        ];
        $messages = [
            'price_validity.integer' => '只能填写整数',
            'price_validity.max' => '最大只能为:max',
            'required' => '必填项不能为空',
            'unique'   => '已被使用，不可重复',
            'max'      => '超出最大字数:max',
            'integer'  => '只能填写整数',
            'min' => '最小只能为:min',
            'in' => '错误的数据'
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
        $form_data = array_map('trim', $form_data);
        $form_data['quality_standard'] = strip_tags($form_data['quality_standard']);
        if (isset($form_data['code'])) {
            $form_data['code'] = strtoupper($form_data['code']);
        }
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

        $goods = Goods::create($form_data);
        if ($goods) {
            $redirect_url = route('goods.manage');
            return redirect($redirect_url)->with('tip_message', ['go' => $redirect_url, 'content' => '成功添加商品', 'state' => 'success']);
        } else {
            return redirect($redirect_url)->withErrors(['form' => ['服务器繁忙，请稍后再试']]);
        }
    }

    public function view(Request $request, Goods $goods)
    {
        $category_id = trim($request->get('category_id'));
        if ( ! empty($category_id)) {
            $category = Category::find($category_id);
        } else {
            $category = $goods->category;
        }
        $addition = json_decode($category->addition);
        if (empty($addition)) {
            $addition = [];
        }
        //$categories = Category::orderBy('code')->whereNull('deleted_at')->get();
        $addition_data = $goods->addition;
        $addition_data = json_decode($addition_data);
        if (empty($addition_data)) {
            $addition_data = [];
        }
        return view('goods.view')
            //->with('categories', $categories)
            ->with('goods', $goods)
            ->with('addition_data', $addition_data)
            ->with('addition', $addition);
    }

    public function edit(Request $request, Goods $goods)
    {
        $category_id = trim($request->get('category_id'));
        if ( ! empty($category_id)) {
            $category = Category::find($category_id);
        } else {
            $category = $goods->category;
        }
        $addition = json_decode($category->addition);
        if (empty($addition)) {
            $addition = [];
        }
        $categories = Category::orderBy('code')->whereNull('deleted_at')->get();
        $addition_data = $goods->addition;
        $addition_data = json_decode($addition_data);
        if (empty($addition_data)) {
            $addition_data = [];
        }
        return view('goods.edit')
            ->with('categories', $categories)
            ->with('goods', $goods)
            ->with('addition_data', $addition_data)
            ->with('addition', $addition);
    }

    public function update(Request $request, Goods $goods)
    {
        $redirect_url = URL::full();
        $category_ids = Category::lists('id')->toArray();
        $allow_input = ['name', 'category_id', 'code', 'unit', 'quality_standard', 'price_validity'];
        $category_id = trim($request->get('category_id'));
        $addition = Category::find($category_id);
        if (empty($addition) or empty($addition->addition)) {
            $addition_data = [];
        } else {
            $addition_data = json_decode($addition->addition);
        }

        $rules = [
            'name'  => 'required|max:100|unique:goods,name,' . $goods->id,
            'code' => 'required|max:30|unique:goods,code,' . $goods->id,
            'price_validity' => 'required|integer|min:1,max:72',
            'category_id' => 'required|in:'.implode(',', $category_ids),
            'unit' => 'required'
        ];
        $messages = [
            'price_validity.integer' => '只能填写整数',
            'price_validity.max' => '最大只能为:max',
            'required' => '必填项不能为空',
            'unique'   => '已被使用，不可重复',
            'max'      => '超出最大字数:max',
            'integer'  => '只能填写整数',
            'min' => '最小只能为:min',
            'in' => '错误的数据'
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
        $form_data = array_map('trim', $form_data);
        $form_data['quality_standard'] = strip_tags($form_data['quality_standard']);
        if (isset($form_data['code'])) {
            $form_data['code'] = strtoupper($form_data['code']);
        }
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
        foreach($form_data as $field => $value) {
            $goods->$field = $value;
        }
        if ($goods->save()) {
            $redirect_url = route('goods.manage');
            return redirect($redirect_url)->with('tip_message', ['go' => $redirect_url, 'content' => '成功添加商品', 'state' => 'success']);
        } else {
            return redirect($redirect_url)->withErrors(['form' => ['服务器繁忙，请稍后再试']]);
        }
    }

    public function disable(Goods $goods)
    {
        $redirect_url = URL::previous();
        $goods->is_available = ! $goods->is_available;
        if ($goods->save()) {
            return redirect($redirect_url)->with('tip_message', ['content' => '操作成功', 'state' => 'success', 'hold' => true]);
        } else {
            return redirect($redirect_url)->with('tip_message', ['content' => '服务器繁忙，请稍后再试', 'state' => 'warning', 'hold' => true]);
        }
    }
}
