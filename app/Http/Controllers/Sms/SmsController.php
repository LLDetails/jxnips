<?php

namespace App\Http\Controllers\Sms;

use App\Area;
use App\Goods;
use App\Jobs\SendCustomSms;
use App\Services\TaobaoSDK\Sms;
use App\SmsTemplate;
use App\User;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use URL;
use Validator;

class SmsController extends Controller
{
    public function index()
    {
        $templates = SmsTemplate::orderBy('created_at', 'desc')->paginate(10);
        $pages = $templates->appends([])->render();
        return view('sms.index')->with('templates', $templates)->with('pages', $pages);
    }

    public function add()
    {
        return view('sms.add');
    }

    public function create(Request $request)
    {
        $redirect_url = URL::full();
        $form_data = $request->only(['title', 'txt']);
        $form_data = array_map('trim', $form_data);

        $rules = [
            'title'     => 'required|max:60|unique:sms_templates,title',
            'txt' => 'required|max:1024'
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

        $result = SmsTemplate::create($form_data);
        if ($result) {
            return redirect($redirect_url)->with('tip_message', ['content' => '成功添加模板', 'state' => 'success']);
        } else {
            return redirect($redirect_url)->withErrors(['form' => ['服务器繁忙，请稍后再试']]);
        }
    }

    public function check(SmsTemplate $sms)
    {
        return view('sms.check')->with('sms', $sms);
    }

    public function saveCheck(Request $request, SmsTemplate $sms)
    {
        $redirect_url = URL::full();

        $form_data = $request->only(['title', 'txt', 'ali_code']);
        $form_data = array_map('trim', $form_data);
        $rules = [
            'title'     => 'required|max:60|unique:sms_templates,title,'.$sms->id,
            'ali_code' => 'required|unique:sms_templates,ali_code,'.$sms->id,
            'txt' => 'required|max:1024'
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

        $sms->title = $form_data['title'];
        $sms->txt = $form_data['txt'];
        $sms->ali_code = $form_data['ali_code'];
        $sms->enable = true;

        if ($sms->save()) {
            $redirect_url = route('sms.check', ['sms' => $sms->id]);
            return redirect($redirect_url)->with('tip_message', ['content' => '审核成功', 'state' => 'success']);
        } else {
            return redirect($redirect_url)->withErrors(['form' => ['服务器繁忙，请稍后再试']]);
        }
    }

    public function showSendSupplierPage(Request $request, SmsTemplate $sms)
    {
        $goods = Goods::orderBy('code', 'asc')->whereNull('deleted_at')->get();

        $goods_id = $request->get('goods_id');
        if (!empty($goods_id)) {
            $suppliers = User::with('supplier')
                ->wherehas('supplier', function($query) use($goods_id) {
                    return $query->whereRaw('\'["' . $goods_id . '"]\'::jsonb <@ "goods"');
                })
                ->where('allow_login', true)
                ->whereNull('deleted_at')
                ->whereNotNull('phone')
                ->get();
        } else {
            $suppliers = User::with('supplier')
                ->where('type', 'supplier')
                ->where('allow_login', true)
                ->whereNull('deleted_at')
                ->whereNotNull('phone')
                ->get();
        }
        return view('sms.send.supplier')
            ->with('sms', $sms)
            ->with('goods', $goods)
            ->with('suppliers', $suppliers);
    }

    public function sendSupplier(Request $request, SmsTemplate $sms)
    {
        $redirect_url = URL::full();

        $phones = $request->get('phones');
        $ext_phones = trim($request->get('ext_phones'));
        $goods_id = $request->get('goods_id');
        $outer_phones = [];
        if (empty($phones)) {
            if (empty($goods_id)) {
                $phones = User::where('type', 'supplier')
                    ->where('allow_login', true)
                    ->whereNull('deleted_at')
                    ->whereNotNull('phone')
                    ->lists('phone');
            } else {
                $phones = User::wherehas('supplier', function($query) use($goods_id) {
                        return $query->whereRaw('\'["' . $goods_id . '"]\'::jsonb <@ "goods"');
                    })
                    ->where('allow_login', true)
                    ->whereNull('deleted_at')
                    ->whereNotNull('phone')
                    ->lists('phone');
            }
            if (empty($phones)) {
                $phones = [];
            } else {
                $phones = $phones->toArray();
            }
        }

        if (in_array('-1', $phones)) {
            $phones = [];
        }

        $phones_count = count($phones);
        if ($phones_count > 200) {
            for ($i = 200; $i < $phones_count; $i += 1) {
                array_push($outer_phones, $phones[$i]);
                unset($phones[$i]);
            }
        }

        if (!empty($ext_phones)) {
            $ext_phones = str_replace('，',',', $ext_phones);
            $ext_phones_list = explode(',', $ext_phones);
            foreach ($ext_phones_list as $ext_phone) {
                if (preg_match('#\d{11}#', $ext_phone) and !in_array($ext_phone, $phones)) {
                    array_push($phones, $ext_phone);
                }
            }
        }

        if (count($outer_phones) > 0) {
            return redirect($redirect_url)->withErrors(['form' => ['号码超出200个,一次只能发送两百个号码']]);
        }

        foreach ($phones as $k=>$phone) {
            $result_job = (new SendCustomSms($phone, $sms->ali_code, '{}'))->delay($k+1);
            $this->dispatch($result_job);
        }

        return redirect($redirect_url)->with('tip_message', ['content' => count($phones).'条给供应商的短信已添加到预发送队列', 'state' => 'success']);
    }

    public function showSendStaffPage(Request $request, SmsTemplate $sms)
    {
        $areas = Area::orderBy('display_order', 'asc')->whereNull('deleted_at')->get();

        $area_id = $request->get('area_id');
        if (!empty($area_id)) {
            $staffs = User::with(['staff', 'role'])
                ->whereHas('role', function($query) {
                    return $query->where('level', '>', 1);
                })
                ->where('type', 'staff')
                ->where('area_id', $area_id)
                ->where('allow_login', true)
                ->whereNull('deleted_at')
                ->whereNotNull('phone')
                ->get();
        } else {
            $staffs = User::with(['staff', 'role'])
                ->whereHas('role', function($query) {
                    return $query->where('level', '>', 1);
                })
                ->where('type', 'staff')
                ->where('allow_login', true)
                ->whereNull('deleted_at')
                ->whereNotNull('phone')
                ->get();
        }
        return view('sms.send.staff')
            ->with('sms', $sms)
            ->with('areas', $areas)
            ->with('staffs', $staffs);
    }

    public function sendStaff(Request $request, SmsTemplate $sms)
    {
        $redirect_url = URL::full();

        $phones = $request->get('phones');
        $ext_phones = trim($request->get('ext_phones'));
        $area_id = $request->get('area_id');
        $outer_phones = [];
        if (empty($phones)) {
            if (empty($area_id)) {
                $phones = User::where('type', 'staff')
                    ->whereHas('role', function($query) {
                        return $query->where('level', '>', 1);
                    })
                    ->where('allow_login', true)
                    ->whereNull('deleted_at')
                    ->whereNotNull('phone')
                    ->lists('phone');
            } else {
                $phones = User::where('area_id', $area_id)
                    ->whereHas('role', function($query) {
                        return $query->where('level', '>', 1);
                    })
                    ->where('type', 'staff')
                    ->where('allow_login', true)
                    ->whereNull('deleted_at')
                    ->whereNotNull('phone')
                    ->lists('phone');
            }
            if (empty($phones)) {
                $phones = [];
            } else {
                $phones = $phones->toArray();
            }
        }

        if (in_array('-1', $phones)) {
            $phones = [];
        }

        $phones_count = count($phones);
        if ($phones_count > 200) {
            for ($i = 200; $i < $phones_count; $i += 1) {
                array_push($outer_phones, $phones[$i]);
                unset($phones[$i]);
            }
        }

        if (!empty($ext_phones)) {
            $ext_phones = str_replace('，',',', $ext_phones);
            $ext_phones_list = explode(',', $ext_phones);
            foreach ($ext_phones_list as $ext_phone) {
                if (preg_match('#\d{11}#', $ext_phone) and !in_array($ext_phone, $phones)) {
                    if (count($phones) < 200) {
                        array_push($phones, $ext_phone);
                    } else {
                        array_push($outer_phones, $ext_phone);
                    }
                }
            }
        }

        if (count($outer_phones) > 0) {
            return redirect($redirect_url)->withErrors(['form' => ['号码超出200个,一次只能发送两百个号码']]);
        }

        foreach ($phones as $k=>$phone) {
            $result_job = (new SendCustomSms($phone, $sms->ali_code, '{}'))->delay($k+1);
            $this->dispatch($result_job);
        }

        return redirect($redirect_url)->with('tip_message', ['content' => count($phones).'条给采购方的短信已添加到预发送队列', 'state' => 'success']);
    }
}
