<?php

namespace App\Http\Controllers\Enquiry;

use App\Enquiry;
use App\EnquiryReply;
use URL;
use Validator;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class SupplierController extends Controller
{
    public function index()
    {
        $datetime = date('Y-m-d H:i:s');
    	$current_user = auth()->user();
    	$current_supplier = $current_user->supplier;
        $supply_goods = json_decode($current_supplier->goods, true);

    	$enquiries = Enquiry::whereIn('goods_id', $supply_goods);
        $enquiries = $enquiries->orderBy('created_at', 'desc');
        $enquiries = $enquiries->where('start_at', '<=', $datetime);
        $enquiries = $enquiries->paginate(10);
        $pages = $enquiries->appends([])->render();
        return view('enquiry.supplier.index')
            ->with('enquiries', $enquiries)
            ->with('pages', $pages)
            ->with('datetime', $datetime);
    }

    public function view(Enquiry $enquiry)
    {
    	$current_user = auth()->user();
    	$datetime = date('Y-m-d H:i:s');
    	$my_reply = $enquiry->replies()->where('supplier_id', $current_user->id)->first();
    	
    	if ($enquiry->stop_at > $datetime and empty($my_reply)) {
    		$show_form = true;
    	} else {
    		$show_form = false;
    	}

    	return view('enquiry.supplier.view')
    		->with('my_reply', $my_reply)
    		->with('enquiry', $enquiry)
    		->with('show_form', $show_form)
    		->with('datetime', $datetime);
    }

    public function reply(Request $request, Enquiry $enquiry)
    {
    	$redirect_url = URL::full();
    	$datetime = date('Y-m-d H:i:s');
    	$supplier_id = auth()->user()->id;
    	if ($enquiry->stop_at < $datetime) {
    		return redirect($redirect_url)->with('tip_message', ['content' => '抱歉，报价已结束', 'state' => 'warning']);
    	} else {
    		if ($enquiry->replies()->where('supplier_id', $supplier_id)->exists()) {
	    		return redirect($redirect_url)->with('tip_message', ['content' => '已经报过价了', 'state' => 'warning']);
	    	} else {
		        $form_data = $request->only([
		        	'quality', 'payment', 'price', 'remark', 'price_validity'
		        ]);
		        $form_data = array_map('trim', $form_data);
		        $form_data['quality'] = strip_tags($form_data['quality']);
		        $form_data['payment'] = strip_tags($form_data['payment']);
		        $form_data['remark'] = strip_tags($form_data['remark']);

		        $rules = [
		            'quality'     => 'required',
                    'price_validity' => 'required|numeric|min:0',
		            'price'  => 'required|numeric|min:0',
		            'payment' => 'required',
		        ];
		        $messages = [
		            'required' => '必填项不能为空',
		            'numeric'  => '请填写数字',
		            'price.min' => '价格必须大于0',
		        ];
		        $validator = Validator::make($form_data, $rules, $messages);
		        if ($validator->fails()) {
		            return redirect($redirect_url)->withErrors($validator->errors())->withInput();
		        } else {
		        	$enquiry_reply_data = $form_data;
		        	$enquiry_reply_data['supplier_id'] = $supplier_id;
		        	$enquiry_reply_data['enquiry_id'] = $enquiry->id;
		            if (EnquiryReply::create($enquiry_reply_data)) {
		                return redirect($redirect_url)->with('tip_message', ['content' => '询价单报价成功', 'state' => 'success']);
		            } else{
		                return redirect($redirect_url)->withErrors(['form' => ['服务器繁忙，请稍后再试']]);
		            }
		        }
	    	}
    	}
    }
}
