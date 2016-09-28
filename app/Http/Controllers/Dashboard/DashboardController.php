<?php

namespace App\Http\Controllers\Dashboard;

//use App\Contract;
//use App\InstantPlanning;
//use App\ComparePlanning;
//use App\Bid;
use App\Basket;
use App\BasketLog;
use App\Setting;
use App\Bid;
use App\Contract;
use App\Demand;
use App\Offer;
use App\Enquiry;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Cache;
use App\Company;
use DB;

use App\Jobs\PassFlow;

class DashboardController extends Controller
{
    public function bootstrap()
    {
        $menu = config('menu.' . auth()->user()->type, []);
        $features = Cache::get('permission.role.' . auth()->user()->role->id);
        foreach ($menu as $k => $item) {
            if ($item['type'] == 'item' and ! in_array($item['route'], $features)) {
                unset($menu[$k]);
            }
            if ($item['type'] == 'parent') {
                $count = count($item['item']);
                foreach ($item['item'] as $item_key => $subitem) {
                    if ( ! in_array($subitem['route'], $features)) {
                        unset($menu[$k]['item'][$item_key]);
                        $count -= 1;
                    }
                }
                if ($count == 0) {
                    unset($menu[$k]);
                }
                if ($count == 1) {
                    $menu[$k] = array_shift($menu[$k]['item']);
                }
            }
        }

        $company = null;
        $company_id = auth()->user()->company_id;
        if ( ! empty($company_id)) {
            $company = Company::find($company_id);
        }

        return view('dashboard.bootstrap')->with('menu', $menu)->with('company', $company);
    }

    public function welcome()
    {
        //return 'Welcome!';
        $time = time();
        $date = date('Y-m-d', $time);
        $current_time = date('H:i:s', $time);
        $datetime = date('Y-m-d H:i:s', $time);
        $current_user = auth()->user();
        $basket_name = $date;

        $role_id = $current_user->role_id;
        $company_id = $current_user->company_id;
        $features = Cache::get('permission.role.' . auth()->user()->role->id);

        //待报价的报价单
        $enquiries = [];
        if (in_array('enquiry.supplier.reply', $features)) {
            $current_supplier = $current_user->supplier;
            $supply_goods = json_decode($current_supplier->goods, true);
            $enquiries = Enquiry::whereIn('goods_id', $supply_goods);
            $enquiries = $enquiries->orderBy('created_at', 'desc');
            $enquiries = $enquiries->where('stop_at', '>', $datetime);
            $enquiries = $enquiries->where('start_at', '<=', $datetime);
            $enquiries = $enquiries->whereHas('replies', function($query) use($current_user) {
                return $query->select(DB::raw("CASE WHEN count(*) = 0 THEN 1 ELSE 0 END"))->where('supplier_id', $current_user->id);
            });
            $enquiries = $enquiries->take(5)->get();
        }

        //待审核的需求
        $check_basket = [];
        if (in_array('demand.check.action', $features)) {
            $company_id = $current_user->company_id;
            $category_id = $current_user->category_id;
            $area_id = $current_user->area_id;
            $baskets = Basket::orderBy('name', 'desc');
            $check_flow_setting = Setting::where('name', 'check_flow')->first()->data;
            $check_flows = json_decode($check_flow_setting, true);

            $check_start_time = null;
            $check_stop_time = null;
            foreach ($check_flows as $k=>$flow) {
                if ($flow['role_id'] == $current_user->role_id) {
                    if (isset($check_flows[$k - 1])) {
                        $check_start_time = $check_flows[$k - 1]['time'];
                        $check_stop_time = $flow['time'];
                        break;
                    }
                }
            }
            if (empty($check_start_time) or empty($check_stop_time)) {
                $baskets = [];
            } else {
                if ($current_time < $check_start_time or $current_time > $check_stop_time) {
                    $baskets = [];
                } else {
                    $today = $date;

                    $baskets = $baskets->whereHas('logs', function($query) use($current_user) {
                        return $query->select(DB::raw("CASE WHEN count(*) = 0 THEN 1 ELSE 0 END"))->where('user_id', $current_user->id);
                    });

                    $baskets = $baskets->where('name', $today);
                    if (!empty($company_id)) {
                        $baskets = $baskets->whereHas('demands', function ($query) use($company_id) {
                            return $query->where('company_id', $company_id);
                        });
                    }
                    if (!empty($area_id) and empty($company_id)) {
                        $baskets = $baskets->whereHas('demands', function ($query) use($area_id) {
                            return $query->whereHas('company', function($query) use($area_id) {
                                return $query->where('area_id', $area_id);
                            });
                        });
                    }
                    if (empty($company_id) and empty($area_id)) {
                        $baskets = $baskets->whereHas('demands', function($query) {
                            return $query;
                        });
                    }
                    if (!empty($category_id)) {
                        $baskets = $baskets->whereHas('demands', function ($query) use($category_id) {
                            return $query->where('category_id', $category_id);
                        });
                    }
                    $baskets = $baskets->get();
                }
            }
            $check_basket = $baskets;
        }

        //待汇总的需求
        $collect_baskets = [];
        if (in_array('bid.demand.collect', $features)) {
            $check_flow_setting = Setting::where('name', 'check_flow')->first()->data;
            $check_flows = json_decode($check_flow_setting, true);
            $last_flow = end($check_flows);
            $last_flow_end_time = $last_flow['time'];
            $collect_baskets = Basket::where(function($query) use ($datetime, $last_flow_end_time) {
                return $query->where(function ($query) use ($datetime) {
                    return $query->where('state', 'refused')->where('bided_at', '<', $datetime);
                })->orWhere(function($query) use ($datetime, $last_flow_end_time) {
                    return $query->where('state', 'checked')->where(DB::raw("((name || ' ".$last_flow_end_time."') < '".$datetime."')"));
                });
                //->orWhere('state', 'checked');
            });
            $collect_baskets = $collect_baskets->whereHas('demands', function($query) {
                return $query;
            });
            $collect_baskets = $collect_baskets->get();
            //$collect_baskets = Basket::whereIn('state', ['checked', 'refused'])->get();
        }

        //待审核的汇总
        $bid_baskets = [];
        if (in_array('bid.check.view', $features)) {
            $bid_baskets = Basket::orderBy('name', 'desc');
            $bid_baskets = $bid_baskets->whereNotNull('bided_at')
                ->whereIn('state', ['bided', 'refused'])
                ->where('bided_at', '>', $datetime)
                ->get();
            foreach ($bid_baskets as $k=>$bb) {
                $exist_log = BasketLog::where('collected_at', $bb->collected_at)
                    ->where('user_id', $current_user->id)
                    ->first();
                if (!empty($exist_log)) {
                    unset($bid_baskets[$k]);
                }
            }
        }

        //待生成的合同
        $offers = [];
        if (in_array('bid.company.offer', $features)) {
            $offers = Offer::with(['contract', 'demand', 'demand.basket', 'supplier', 'supplier.supplier'])
                ->orderBy('updated_at')
                ->whereHas('demand', function($query) use($company_id) {
                    return $query->where('company_id', $company_id)
                        ->where('is_cancel', false);
                });
            $offers = $offers->whereHas('bid', function($query) {
                return $query->where('offer_stop', '<', date('Y-m-d H:i:s'));
            });

            $offers = $offers->whereHas('contract', function($query) use($current_user) {
                return $query->select(DB::raw("CASE WHEN count(*) = 0 THEN 1 ELSE 0 END"));
            });

            $offers = $offers->where('quantity', '>', 0);
            $offers = $offers->whereNull('generated_at');
            $offers = $offers->get();
        }

        //待报价的标书
        $bids = [];
        if (in_array('bid.supplier.offer', $features)) {
            $current_supplier = $current_user->supplier;
            $supply_goods = json_decode($current_supplier->goods, true);
            $bids = Bid::orderBy('created_at', 'desc')
                ->whereIn('goods_id', $supply_goods)
                ->where('offer_start', '<=', $datetime)
                ->where('offer_stop', '>=', $datetime)
                ->whereHas('basket', function ($query) use ($datetime) {
                    return $query->where('state', 'bided')->where('bided_at', '<=', $datetime);
                });

            $bids = $bids->whereHas('demands', function($query) {
                return $query->select(DB::raw('CASE WHEN sum("quantity") > 0 THEN 1 ELSE 0 END'))->groupBy('bid_id');
            });

            $bids = $bids->where(function ($query) use ($supply_goods, $current_user) {
                return $query->where(function ($query) use ($supply_goods) {
                    return $query->where('type', 'global')->whereIn('goods_id', $supply_goods);
                })->orWhere(function ($query) use ($current_user) {
                    return $query->where('type', 'invite')->whereRaw('\'["' . $current_user->id . '"]\'::jsonb <@ "suppliers"');
                });
            });

            $bids = $bids->get();
        }

        //待确认的合同
        $contracts = [];
        if (in_array('contract.supplier.confirm', $features)) {
            $contracts = Contract::with(['offer', 'offer.demand', 'offer.demand.company'])
                ->orderBy('created_at', 'desc')
                ->whereHas('offer', function($query) use($current_user) {
                    return $query->where('user_id', $current_user->id);
                });
            $contracts = $contracts->where('state', 'pending');
            $contracts = $contracts->where('offline', false);
            $contracts = $contracts->get();
        }

        //待修改的合同
        $edit_contracts = [];
        if (in_array('contract.company.edit', $features)) {
            $edit_contracts = Contract::with(['offer', 'offer.demand', 'offer.demand.company'])
                ->orderBy('created_at', 'desc')
                ->whereHas('offer.demand', function($query) use($company_id) {
                    return $query->where('company_id', $company_id);
                });
            $edit_contracts = $edit_contracts->where('state', 'refused');
            $edit_contracts = $edit_contracts->get();
        }



        return view('dashboard.welcome')
            ->with('edit_contracts', $edit_contracts)
            ->with('contracts', $contracts)
            ->with('bids', $bids)
            ->with('offers', $offers)
            ->with('bid_baskets', $bid_baskets)
            ->with('collect_baskets', $collect_baskets)
            ->with('check_basket', $check_basket)
            ->with('enquiries', $enquiries);
    }

    public function showAgreement()
    {
        return view('dashboard.agreement');
    }

    public function agreement()
    {
        $auth_user = auth()->user();
        $auth_user->accept_agreement = true;
        $auth_user->save();
        return redirect()->route('dashboard.bootstrap');
    }
}
