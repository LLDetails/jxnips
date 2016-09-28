<?php

namespace App\Http\Controllers\Setting;

use App\Bid;
use App\CheckFlowSaveLog;
use App\Role;
use App\Setting;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use DB;
use URL;

class CheckFlowController extends Controller
{
    public function index()
    {
        $flow = Setting::where('name', 'check_flow')->first();
        if (!empty($flow)) {
            $flow = json_decode($flow->data, true);
        } else {
            $flow = [];
        }
        $members = Setting::where('name', 'check_members')->first();
        if (!empty($members)) {
            $members = json_decode($members->data, true);
        } else {
            $members = [];
        }
        $member_auto_pass_time = Setting::where('name', 'member_auto_pass_time')->first();
        $member_auto_pass_time = $member_auto_pass_time->data;

        $roles = Role::with('users')
            ->orderBy('level', 'desc')
            ->whereNull('deleted_at')
            ->where('level', '>', 1)
            ->get();

        return view('setting.check_flow')
            ->with('flow', $flow)
            ->with('members', $members)
            ->with('member_auto_pass_time', $member_auto_pass_time)
            ->with('roles', $roles);
    }

    public function save(Request $request)
    {
        $redirect_url = URL::full();

        $flow_roles = $request->get('pws');
        $flow_times = $request->get('pwst');
        $member_auto_pass_time = trim($request->get('member_auto_pass_time'));
        //$member_auto_pass_time = intval($member_auto_pass_time);
        $members = $request->get('member');

        $result = DB::transaction(function() use ($request, $flow_roles, $flow_times, $member_auto_pass_time, $members) {
            $check_flow = [];
            foreach ($flow_roles as $k => $role_id) {
                $time = trim($flow_times[$k]);
                $check_flow[] = [
                    'role_id' => $role_id,
                    'time' => $time
                ];
            }
            $flow_setting = Setting::firstOrNew(['name' => 'check_flow']);
            $encoded_check_flow = json_encode($check_flow);
            $flow_setting->data = $encoded_check_flow;
            if (!$flow_setting->save()) {
                return false;
            }

            $flow_log = CheckFlowSaveLog::create([
                'user_id' => auth()->user()->id,
                'ip' => $request->getClientIp(),
                'data' => $encoded_check_flow
            ]);
            if (!$flow_log) {
                DB::rollBack();
                return false;
            }

            $member_auto_pass_time_setting = Setting::firstOrNew(['name' => 'member_auto_pass_time']);
            $member_auto_pass_time_setting->data = $member_auto_pass_time;
            if (!$member_auto_pass_time_setting->save()) {
                DB::rollBack();
                return false;
            }

            $check_members_setting = Setting::firstOrNew(['name' => 'check_members']);
            $check_members_setting->data = json_encode($members);
            if (!$check_members_setting->save()) {
                DB::rollBack();
                return false;
            }

            $demand_check_time_setting = Setting::firstOrNew(['name' => 'demand_check_time']);
            $demand_check_time_setting->data = $flow_times[0];
            if (!$demand_check_time_setting->save()) {
                DB::rollBack();
                return false;
            }

            return true;
        });

        if ($result) {
            return redirect($redirect_url)->with('tip_message', ['content' => '设置成功', 'state' => 'success', 'hold' => true]);
        } else {
            return redirect($redirect_url)->with('tip_message', ['content' => '服务器繁忙，请稍后再试', 'state' => 'warning', 'hold' => true]);
        }
    }

    public function offerMinNum()
    {
        $offer_min_num_setting = Setting::where('name', 'offer_min_num')->first();
        if (!empty($offer_min_num_setting)) {
            $offer_min_num = intval($offer_min_num_setting->data);
        } else {
            $offer_min_num = null;
        }

        return view('setting.offer_min_num')->with('offer_min_num', $offer_min_num);
    }

    public function saveOfferMinNum(Request $request)
    {
        $redirect_url = URL::full();

        $datetime = date('Y-m-d H:i:s');
        if (Bid::where('offer_start', '<=', $datetime)->where('offer_stop', '>=', $datetime)->exists()) {
            return redirect($redirect_url)->with('tip_message', ['content' => '有标书正在进行报价，不能修改', 'state' => 'warning', 'hold' => true]);
        }

        $num = $request->get('num');
        $num = intval($num);

        $offer_min_num_setting = Setting::firstOrNew(['name' => 'offer_min_num']);
        $offer_min_num_setting->data = $num;

        if ($offer_min_num_setting->save()) {
            return redirect($redirect_url)->with('tip_message', ['content' => '保存成功', 'state' => 'success', 'hold' => true]);
        } else {
            return redirect($redirect_url)->with('tip_message', ['content' => '服务器繁忙，请稍后再试', 'state' => 'warning', 'hold' => true]);
        }
    }
}
