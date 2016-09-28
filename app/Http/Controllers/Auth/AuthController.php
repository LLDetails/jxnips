<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\User;
use Auth;
use App;
use DB;
use URL;
use Validator;
use Hash;

use HSms;

use App\Jobs\SendSms;
use App\Jobs\SendOfferSms;
use App\Jobs\SendOfferResultSms;
use App\Jobs\SendCustomSms;

class AuthController extends Controller
{
    /**
     * 显示登录页面
     *
     * @return \Illuminate\View\View
     */
    public function login(Request $request)
    {
        session()->forget('forget_user');
        session()->forget('forget_vcode');
        session()->forget('forget_auth');
        return view('auth.login');

    }

    public function auth(Request $request)
    {
        $credentials = $request->only(['username', 'password']);
        $credentials = array_map('trim', $credentials);
        if (Auth::attempt($credentials)) {
            $auth_user = auth()->user();
            if ( ! $auth_user->allow_login) {
                auth()->logout();
                return redirect()->route('auth.login')->withInput()->withErrors(['message' => ['您输入的帐号已经被管理员锁定，目前无法登录']]);
            }
            switch ($auth_user->type) {
                case 'supplier':
                    $user_profile = $auth_user->supplier;
                    break;
                case 'staff':
                    $user_profile = $auth_user->staff;
                    break;
                default:
                    auth()->logout();
                    App::abort(403, 'User Type is unkown!');
                    break;
            }
            $addition = $user_profile->addition;
            if ( ! empty($addition)) {
                $addition = json_decode($addition);
            } else {
                $addition = [];
            }
            $user_profile->addition = $addition;
            session(['user_profile' => $user_profile]);
            if ($auth_user->type == 'supplier' and !$auth_user->accept_agreement) {
                return redirect()->route('dashboard.agreement');
            }
            return redirect()->route('dashboard.bootstrap');
        }
        return redirect()->route('auth.login')->withInput()->withErrors(['message' => ['您输入的用户名与密码不匹配，请核对后重新登录']]);
    }

    public function logout()
    {
        session()->forget('user_type');
        auth()->logout();
        return redirect()->route('auth.login');
    }

    public function password(Request $request)
    {
        $url = URL::previous();
        if (empty($url)) {
            $url = route('dashboard.bootstrap');
        }
        $user = auth()->user();

        $member = DB::table('users')->find($user->id);
        $old_password = trim($request->get('old_password'));
        if ( ! Hash::check($old_password, $member->password)) {
            $message = ['state' => 'error', 'content' => '原密码输入有误'];
            return redirect($url)->with('message', $message)->withInput()->with('action', 'password');
        }

        $member_data = $request->only(['password', 'password_confirmation']);
        $member_data = array_map('trim', $member_data);

        $rules = [
            'password' => 'required|max:30|confirmed'
        ];
        $messages = [
            'required'           => '必填项不能留空',
            'password.confirmed' => '两次密码输入不一致'
        ];
        $validator = Validator::make($member_data, $rules, $messages);
        if ($validator->fails()) {
            return redirect($url)->withErrors($validator->errors())->withInput()->with('action', 'password');
        }

        $user->password = Hash::make($member_data['password']);

        if ($user->save()) {
            $message = ['state' => 'success', 'content' => '密码修改成功，请用新密码重新登录'];
            return redirect($url)->with('message', $message)->with('action', 'password');
        } else {
            $message = ['state' => 'error', 'content' => '服务器繁忙，请稍后再试'];
            return redirect($url)->with('message', $message)->withInput()->with('action', 'password');
        }
    }

    public function resetPassword(Request $request)
    {
        $step = intval($request->get('step', 1));
        if ($step == 1) {
            session()->forget('forget_user');
            session()->forget('forget_vcode');
            session()->forget('forget_auth');
            return view('auth.password_reset_1');
        } else if ($step == 2) {
            if (empty(session('forget_user'))) {
                return redirect()->route('auth.password.reset', ['step' => 1]);
            } else {
                //session()->forget('forget_vcode');
                //session()->forget('forget_auth');
                return view('auth.password_reset_2');
            }
        } else if ($step == 3) {
            if (empty(session('forget_user')) or empty(session('forget_auth'))) {
                return redirect()->route('auth.password.reset', ['step' => 2]);
            } else {
                return view('auth.password_reset_3');
            }
        } else {
            App::abort(404, 'Page Not Found!');
        }
    }

    public function resetPasswordHandler(Request $request)
    {
        $step = intval($request->get('step', 1));
        if ($step == 1) {
            $username = trim($request->get('username'));
            $target_user = User::where('username', $username)->first();
            if (empty($target_user)) {
                return redirect()->route('auth.password.reset', ['step' => $step])->withInput()->withErrors(['msg' => ['没有找到对应帐号']]);
            } else {
                if (empty($target_user->phone)) {
                    return redirect()->route('auth.password.reset', ['step' => $step])->withInput()->withErrors(['msg' => ['未绑定手机,请联系管理员']]);
                } else {

                    $code = sprintf('%06d', mt_rand(0, pow(10, 6) - 1));
                    $template_code = config('hsms.templates')['手机绑定'];
                    $sms_param = json_encode(['code' => $code]);
                    $result = HSms::send($template_code, $target_user->phone, $sms_param);

                    if (empty($result)) {
                        return redirect()->route('auth.password.reset', ['step' => $step])->withInput()->withErrors(['msg' => ['短信接口响应错误']]);
                    } else {
                        if (isset($result->result->success)) {
                            if ($result->result->success == 'true') {
                                session(['forget_user' => $target_user]);
                                session(['forget_code' => $code]);
                                return redirect()->route('auth.password.reset', ['step' => $step + 1]);
                            } else {
                                return redirect()->route('auth.password.reset', ['step' => $step])->withInput()->withErrors(['msg' => ['未知错误']]);
                            }
                        } else if (isset($result->code)) {
                            if (isset($result->sub_msg)) {
                                return redirect()->route('auth.password.reset', ['step' => $step])->withInput()->withErrors(['msg' => [$result->sub_msg]]);
                            } else {
                                return redirect()->route('auth.password.reset', ['step' => $step])->withInput()->withErrors(['msg' => ['未知错误']]);
                            }
                        } else {
                            return redirect()->route('auth.password.reset', ['step' => $step])->withInput()->withErrors(['msg' => ['未知错误']]);
                        }
                    }
                }
            }
        } else if ($step == 2) {
            $vcode = trim($request->get('vcode'));
            if ($vcode != session('forget_code')) {
                return redirect()->route('auth.password.reset', ['step' => $step])->withInput()->withErrors(['msg' => ['验证码不匹配,请检查输入']]);
            } else {
                session(['forget_auth' => 1]);
                return redirect()->route('auth.password.reset', ['step' => $step + 1]);
            }
        } else if ($step == 3) {
            $password = trim($request->get('password'));
            $repassword = trim($request->get('repassword'));
            if (empty($password)) {
                return redirect()->route('auth.password.reset', ['step' => $step])->withInput()->withErrors(['msg' => ['新密码不能为空']]);
            } else {
                if ($password != $repassword) {
                    return redirect()->route('auth.password.reset', ['step' => $step])->withInput()->withErrors(['msg' => ['两次输入的密码不一致']]);
                } else {
                    try {
                        $forget_user = User::find(session('forget_user')->id);
                        $forget_user->password = Hash::make($password);
                        $forget_user->save();
                        session()->forget('forget_user');
                        session()->forget('forget_vcode');
                        session()->forget('forget_auth');
                        return view('auth.password_reset_success');
                    } catch (\Exception $e) {
                        return redirect()->route('auth.password.reset', ['step' => $step])->withInput()->withErrors(['msg' => ['服务器繁忙,请稍后再试']]);
                    }

                }
            }
        } else {
            App::abort(404, 'Page Not Found!');
        }
    }
}
