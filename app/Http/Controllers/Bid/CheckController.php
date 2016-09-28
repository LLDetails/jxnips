<?php

namespace App\Http\Controllers\Bid;

use App\Basket;
use App\Bid;
use App\Setting;
use App\BasketLog;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App;
use DB;
use URL;

class CheckController extends Controller
{
    public function index()
    {
        $current_user = auth()->user();
        $datetime = date('Y-m-d H:i:s');

        $setting = Setting::where('name', 'check_members')->first();
        $members = json_decode($setting->data, true);
        if (!in_array($current_user->id, $members)) {
            App::abort(403, '对不起，您不在审核名单中');
        }

        $baskets = Basket::orderBy('name', 'desc');
        $baskets = $baskets->whereNotNull('bided_at');
            //->where('state', 'bided')
            //->where('bided_at', '>', date('Y-m-d H:i:s'));
        $baskets = $baskets->paginate(10);
        $pages = $baskets->appends([])->render();

        $display_states = [
            'pending' => '处理中',
            'refused' => '被退回',
            'checked' => '待汇总',
            'bided' => '已发标',
            'cancelled' => '已放弃',
            'assigned' => '已分配'
        ];

        return view('bid.check.index')
            ->with('current_user', $current_user)
            ->with('baskets', $baskets)
            ->with('pages', $pages)
            ->with('datetime', $datetime)
            ->with('display_states', $display_states);
    }

    public function view(Basket $basket)
    {
        $current_user = auth()->user();

        $setting = Setting::where('name', 'check_members')->first();
        $members = json_decode($setting->data, true);
        if (!in_array($current_user->id, $members)) {
            App::abort(403, '对不起，您不在审核名单中');
        }

        $exist_log = BasketLog::where('collected_at', $basket->collected_at)
            ->where('user_id', $current_user->id)
            ->first();
        $bids = Bid::with(['demands', 'demands.company'])->whereBasketId($basket->id)->get();
        if (!empty($exist_log)) {
            return view('bid.check.display')->with('bids', $bids)->with('log', [])->with('basket', $basket);
            //App::abort(403, '对不起，您在本次审核中已经操作过了');
        }

        if (/*$basket->state == 'refused' and */$basket->bided_at < date('Y-m-d H:i:s')) {
            $log = BasketLog::with(['user', 'user.role', 'user.staff'])
                ->whereBasketId($basket->id)
                ->where('action', 'refuse')
                ->orderBy('created_at', 'desc')
                ->first();
            return view('bid.check.display')->with('bids', $bids)->with('log', $log)->with('basket', $basket);
        } else {
            return view('bid.check.view')->with('bids', $bids)->with('basket', $basket);
        }
    }

    public function action(Request $request, Basket $basket)
    {
        $redirect_url = URL::full();
        $current_user = auth()->user();

        $setting = Setting::where('name', 'check_members')->first();
        $members = json_decode($setting->data, true);
        if (!in_array($current_user->id, $members)) {
            App::abort(403, '对不起，您不在审核名单中');
        }

        $exist_log = BasketLog::where('collected_at', $basket->collected_at)
            ->where('user_id', $current_user->id)
            ->first();
        if (!empty($exist_log)) {
            return redirect($redirect_url)->with('tip_message', ['content' => '对不起，您在本次审核中已经操作过了', 'state' => 'warning', 'hold' => true]);
            //App::abort(403, '对不起，您在本次审核中已经操作过了');
        }

        if ($request->get('action') == 'pass') {
            $result = DB::transaction(function() use ($current_user, $basket, $members) {
                $basket_log_data = [
                    'user_id' => $current_user->id,
                    'basket_id' => $basket->id,
                    'collected_at' => $basket->collected_at,
                    'action' => 'accept',
                    'role_id' => $current_user->role_id,
                    'remark' => ''
                ];
                $basket_log = BasketLog::create($basket_log_data);
                if (!$basket_log) {
                    return false;
                }

                $basket_log_accept_count = BasketLog::where('basket_id', $basket->id)
                    ->select('user_id')
                    ->where('action', 'accept')
                    ->whereIn('user_id', $members)
                    ->distinct()
                    ->count();
                if ($basket_log_accept_count >= count($members)) {
                    $basket->state = 'bided';
                    $basket->bided_at = date('Y-m-d H:i:s');
                    if (!$basket->save()) {
                        DB::rollBack();
                        return false;
                    }
                }

                return true;
            });

        } else {
            $remark = trim($request->get('remark'));
            $remark = strip_tags($remark);

            $result = DB::transaction(function() use ($remark, $current_user, $basket) {
                $basket_log_data = [
                    'user_id' => $current_user->id,
                    'basket_id' => $basket->id,
                    'collected_at' => $basket->collected_at,
                    'action' => 'refuse',
                    'role_id' => $current_user->role_id,
                    'remark' => $remark
                ];
                $basket_log = BasketLog::create($basket_log_data);
                if (!$basket_log) {
                    return false;
                }
                $basket->state = 'refused';
                if (!$basket->save()) {
                    DB::rollBack();
                    return false;
                }
                return true;
            });
        }

        if ($result) {
            return redirect($redirect_url)->with('tip_message', ['content' => '操作成功', 'state' => 'success']);
        } else {
            return redirect($redirect_url)->withErrors(['form' => ['服务器繁忙，请稍后再试']]);
        }
    }
}
