<?php

namespace App\Http\Controllers\Enquiry;

use App\Enquiry;
use App\Goods;
use URL;
use Validator;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class StaffController extends Controller
{
    public function index()
    {
        $enquiries = Enquiry::with('goods');
        $enquiries = $enquiries->orderBy('created_at', 'desc');
        $enquiries = $enquiries->paginate(10);
        $pages = $enquiries->appends([])->render();
        return view('enquiry.staff.index')
            ->with('enquiries', $enquiries)
            ->with('pages', $pages)
            ->with('datetime', date('Y-m-d H:i:s'));
    }

    public function add()
    {
        $goods_items = Goods::orderBy('code')->whereNull('deleted_at')->get();
        return view('enquiry.staff.add')
            ->with('goods_items', $goods_items);
    }

    public function create(Request $request)
    {
        $redirect_url = URL::full();
        $form_data = $request->only([
            'title', 'goods_id', 'quantity', 'quality', 'start_at',
            'sailing_date', 'terms_of_delivery', 'stop_at'
        ]);
        $form_data = array_map('trim', $form_data);

        $goods_ids = Goods::whereNull('deleted_at')->lists('id');
        if (empty($goods_ids)) {
            $goods_ids = [0];
        } else {
            $goods_ids = $goods_ids->toArray();
        }

        $rules = [
            'title'     => 'required|max:45',
            'goods_id'  => 'required|in:'.implode(',', $goods_ids),
            'quantity'  => 'required|numeric|min:0',
            'quality'   => 'required',
            'sailing_date' => 'required',
            'terms_of_delivery' => 'required',
            'start_at' => 'required|date',
            'stop_at' => 'required|date|after:'.$form_data['start_at']
        ];
        $messages = [
            'required' => '必填项不能为空',
            'unique'   => '已被使用，不可重复',
            'max'      => '超出最大字数:max',
            'numeric'  => '请填写数字',
            'quantity.min' => '数量必须大于0',
            'date' => '请填写日期格式',
            'stop_at.after' => '结束时间不能早于开始时间',
            'goods_id.in' => '请选择可用的品种'
        ];
        $validator = Validator::make($form_data, $rules, $messages);
        if ($validator->fails()) {
            return redirect($redirect_url)->withErrors($validator->errors())->withInput();
        } else {
            if (Enquiry::create($form_data)) {
                return redirect($redirect_url)->with('tip_message', ['content' => '发布成功', 'state' => 'success']);
            } else{
                return redirect($redirect_url)->withErrors(['form' => ['服务器繁忙，请稍后再试']]);
            }
        }
    }

    public function view(Enquiry $enquiry)
    {
        $replies = $enquiry->replies()->with('supplier.supplier')->orderBy('price', 'asc')->get();
        return view('enquiry.staff.view')
            ->with('replies', $replies)
            ->with('datetime', date('Y-m-d H:i:s'))
            ->with('enquiry', $enquiry);
    }
}
