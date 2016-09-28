<?php

namespace App\Http\Controllers\User;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\User;
use Input;
use Validator;
use URL;
use Config;
use HSms;

class PhoneController extends Controller
{
    public function vcode()
    {
        $phone_time = session('phone.time');
        $time = time();
        if ( ! empty($phone_time) and $time - $phone_time < 60) {
            return response()->json(['state'=>'error', 'msg' => '60秒内只能发送一次']);
        }
        $phone = trim(Input::get('phone'));
        if (auth()->user()->role->name == '供应商') {
            $result = User::where('phone', $phone)->count();
            if ($result > 0) {
                return response()->json(['state' => 'error', 'msg' => '此号码已在使用中']);
            }
        }

        $code = sprintf('%06d', mt_rand(0, pow(10, 6) - 1));
        $template_code = config('hsms.templates')['手机绑定'];
        $sms_param = json_encode(['code' => $code]);
        $result = HSms::send($template_code, $phone, $sms_param);

        $response_data_arr = [];
        if (empty($result)) {
            $response_data_arr['state'] = 'error';
            $response_data_arr['code'] = '-100';
            $response_data_arr['message'] = '短信接口响应错误';
        } else {
            if (isset($result->result->success)) {
                if ($result->result->success == 'true') {

                    session(['phone_time' => time()]);
                    session(['phone_code' => $code]);
                    session(['phone' => $phone]);

                    $response_data_arr['state'] = 'success';
                    $response_data_arr['code'] = '0';
                    $response_data_arr['message'] = '发送成功';
                } else {
                    $response_data_arr['state'] = 'error';
                    $response_data_arr['code'] = '-99';
                    $response_data_arr['message'] = '未知错误';
                }
            } else if (isset($result->code)) {
                $response_data_arr['state'] = 'error';
                $response_data_arr['code'] = $result->code;
                if (isset($result->sub_msg)) {
                    $response_data_arr['message'] = $result->sub_msg;
                } else {
                    $response_data_arr['message'] = '未知错误';
                }
            } else {
                $response_data_arr['state'] = 'error';
                $response_data_arr['code'] = '-98';
                $response_data_arr['message'] = '未知错误';
            }
        }

        return response()->json($response_data_arr);
    }

    public function bind()
    {
        $phone = trim(Input::get('phone'));
        $phone_vcode = trim(Input::get('phone_vcode'));
        if ($phone == session('phone') and $phone != auth()->user()->phone and $phone_vcode == session('phone_code')) {
            $id = auth()->user()->id;
            if ( ! User::where('id', $id)->update(['phone' => $phone])) {
                return response()->json(['state'=>'error', 'msg' => '服务器繁忙，请稍候再试']);
            }

            session()->forget('phone_time');
            session()->forget('phone_code');
            session()->forget('phone');

            auth()->logout();
            auth()->loginUsingId($id);

            return response()->json(['state'=>'success', 'msg' => '绑定成功']);
        } else {
            return response()->json(['state'=>'error', 'msg' => '验证码错误']);
        }
    }

    public function unbind()
    {
        $current_user = auth()->user();
        if (empty($current_user->phone)) {
            return response()->json(['state'=>'error', 'msg' => '抱歉，您没有绑定手机']);
        } else {
            try {
                $current_user->phone = null;
                $current_user->save();
                return response()->json(['state'=>'success', 'msg' => '解绑成功']);
            } catch (\Exception $e) {
                return response()->json(['state'=>'error', 'msg' => '服务器繁忙，请稍候再试']);
            }

        }
    }
}
