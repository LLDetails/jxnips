<?php

namespace App\Http\Controllers\Statistics;

use App\Bid;
use App\BidCount;
use App\Contract;
use App\ContractGrade;
use App\Goods;
use App\Offer;
use App\Setting;
use App\Supplier;
use App\User;
use App\Demand;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use DB;

class StatisticsController extends Controller
{
    public function __construct()
    {
        $goods = Goods::whereNull('deleted_at')->get();
        view()->share('goods_data', $goods);
        view()->share('types', [
            '_total_quantity' => '成交总量',
            '_total_count' => '成交次数',
            '_offer_count' => '报价次数',
            '_offer_price' => '成交均价'
        ]);
        view()->share('company_data_types', [
            '_c_total_quantity' => '成交总量',
            '_c_total_count' => '成交次数',
            '_c_offer_price' => '成交均价'
        ]);
        view()->share('grade_types', [
            '_grade_supplier' => '供应商',
            '_grade_company' => '采购商'
        ]);
    }

    public function supplier(Request $request)
    {
        $search_data = $request->only(['date_start', 'date_stop', 'goods_id', 'supplier_id']);

        $invite_count = new Bid();
        $offer_count = Offer::where('price', '>', 0)->whereNull('reason');
        $join_count = Offer::where(function($query) {
            return $query->whereNotNull('reason')
                ->orWhere('price', '>', 0);
        });
        $deal_count = Offer::where('quantity', '>', 0)->where('price', '>', 0);
        $deal_amount = Offer::where('quantity', '>', 0)->where('price', '>', 0);
        $deal_quantity = Offer::where('quantity', '>', 0)->where('price', '>', 0);
        $total_deal_quantity = Offer::where('quantity', '>', 0)->where('price', '>', 0);

        if (!empty($search_data['date_start'])) {

            $invite_count = $invite_count->where('offer_start', '>=', $search_data['date_start'].' 00:00:00');
            $offer_count = $offer_count->whereHas('bid', function($query) use($search_data) {
                return $query->where('offer_start', '>=', $search_data['date_start'].' 00:00:00');
            });
            $join_count = $join_count->whereHas('bid', function($query) use($search_data) {
                return $query->where('offer_start', '>=', $search_data['date_start'].' 00:00:00');
            });
            $deal_count = $deal_count->whereHas('bid', function($query) use($search_data) {
                return $query->where('offer_start', '>=', $search_data['date_start'].' 00:00:00');
            });
            $deal_amount = $deal_amount->whereHas('bid', function($query) use($search_data) {
                return $query->where('offer_start', '>=', $search_data['date_start'].' 00:00:00');
            });
            $deal_quantity = $deal_quantity->whereHas('bid', function($query) use($search_data) {
                return $query->where('offer_start', '>=', $search_data['date_start'].' 00:00:00');
            });
            $total_deal_quantity = $total_deal_quantity->whereHas('bid', function($query) use($search_data) {
                return $query->where('offer_start', '>=', $search_data['date_start'].' 00:00:00');
            });
        }

        if (!empty($search_data['date_stop'])) {
            $invite_count = $invite_count->where('offer_start', '<=', $search_data['date_stop'].' 23:59:59');
            $offer_count = $offer_count->whereHas('bid', function($query) use($search_data) {
                return $query->where('offer_start', '<=', $search_data['date_stop']. ' 23:59:59');
            });
            $join_count = $join_count->whereHas('bid', function($query) use($search_data) {
                return $query->where('offer_start', '<=', $search_data['date_stop']. ' 23:59:59');
            });
            $deal_count = $deal_count->whereHas('bid', function($query) use($search_data) {
                return $query->where('offer_start', '<=', $search_data['date_stop']. ' 23:59:59');
            });
            $deal_amount = $deal_amount->whereHas('bid', function($query) use($search_data) {
                return $query->where('offer_start', '<=', $search_data['date_stop']. ' 23:59:59');
            });
            $deal_quantity = $deal_quantity->whereHas('bid', function($query) use($search_data) {
                return $query->where('offer_start', '<=', $search_data['date_stop']. ' 23:59:59');
            });
            $total_deal_quantity = $total_deal_quantity->whereHas('bid', function($query) use($search_data) {
                return $query->where('offer_start', '<=', $search_data['date_stop']. ' 23:59:59');
            });
        }

        if (!empty($search_data['goods_id'])) {
            $invite_count = $invite_count->where('goods_id', $search_data['goods_id']);
            $offer_count = $offer_count->whereHas('bid', function($query) use($search_data) {
                return $query->where('goods_id', $search_data['goods_id']);
            });
            $join_count = $join_count->whereHas('bid', function($query) use($search_data) {
                return $query->where('goods_id', $search_data['goods_id']);
            });
            $deal_count = $deal_count->whereHas('bid', function($query) use($search_data) {
                return $query->where('goods_id', $search_data['goods_id']);
            });
            $deal_amount = $deal_amount->whereHas('bid', function($query) use($search_data) {
                return $query->where('goods_id', $search_data['goods_id']);
            });
            $deal_quantity = $deal_quantity->whereHas('bid', function($query) use($search_data) {
                return $query->where('goods_id', $search_data['goods_id']);
            });
            $total_deal_quantity = $total_deal_quantity->whereHas('bid', function($query) use($search_data) {
                return $query->where('goods_id', $search_data['goods_id']);
            });
        }

        if (!empty($search_data['supplier_id'])) {
            $invite_count = $invite_count->whereHas('offers', function($query) use($search_data) {
                return $query->where('user_id', $search_data['supplier_id']);
            });
            $offer_count = $offer_count->where('user_id', $search_data['supplier_id']);
            $join_count = $join_count->where('user_id', $search_data['supplier_id']);
            $deal_count = $deal_count->where('user_id', $search_data['supplier_id']);
            $deal_amount = $deal_amount->where('user_id', $search_data['supplier_id']);
            $deal_quantity = $deal_quantity->where('user_id', $search_data['supplier_id']);
        }

        $invite_count = $invite_count->sum('invitation_quantity');
        $offer_count = $offer_count->count();
        $join_count = $join_count->count();
        $deal_count = $deal_count->count();
        $deal_quantity = $deal_quantity->sum('quantity');
        $total_deal_quantity = $total_deal_quantity->sum('quantity');
        $deal_amount = $deal_amount->sum(DB::raw('("delivery_costs" + "price")*"quantity"'));

        $goods = Goods::whereNull('deleted_at')->where('is_available', true)->get();
        if (!empty($search_data['goods_id'])) {
            $suppliers = Supplier::whereRaw('\'["' . $search_data['goods_id'] . '"]\'::jsonb <@ "goods"')->get();
        } else {
            $suppliers = Supplier::all();
        }

        return view('statistics.supplier.rate')
            ->with('goods', $goods)
            ->with('suppliers', $suppliers)
            ->with('invite_count', $invite_count)
            ->with('join_count', $join_count)
            ->with('offer_count', $offer_count)
            ->with('deal_count', $deal_count)
            ->with('deal_quantity', $deal_quantity)
            ->with('total_deal_quantity', $total_deal_quantity)
            ->with('deal_amount', $deal_amount);
    }
    /*public function supplier(Request $request)
    {
        $cond = $request->only(['date', 'goods_id', 'type']);
        $cond = array_map('trim', $cond);

        $fetch_data = false;
        if (empty($cond['date']) or empty($cond['goods_id']) or empty($cond['type'])) {
            $fetch_data = false;
        } else {
            if (method_exists($this, $cond['type'])) {
                $fetch_data = true;
            } else {
                $fetch_data = false;
            }
        }
        if ($fetch_data) {
            $date_start = $cond['date'] . '-01 00:00:00';
            $time_offset = strtotime($cond['date']);
            $date_stop = date('Y-m', strtotime('+7 month', $time_offset)) . '-01 00:00:00';
            $func_name = $cond['type'];
            return $this->$func_name($date_start, $date_stop, $cond['goods_id']);
        } else {
            return view('statistics.supplier.empty');
        }
    }*/

    private function _total_quantity($date_start, $date_stop, $goods_id) {
        $company_id = auth()->user()->company_id;

        $total_quantity = Offer::whereHas('demand', function($query) use($goods_id) {
            return $query->where('goods_id', $goods_id);
        })->whereHas('contract',  function($query) use($date_start, $date_stop) {
            return $query->whereNotNull('finished_at')
                ->where('finished_at', '>=', $date_start)
                ->where('finished_at', '<', $date_stop);
        })->sum('quantity');

        $data = DB::table('offers')
            ->join('demands', function($join) use($goods_id, $company_id) {
                if (!empty($company_id)) {
                    $join->on('offers.demand_id', '=', 'demands.id')
                        ->where('demands.goods_id', '=', $goods_id)
                        ->where('demands.company_id', '=', $company_id);
                } else {
                    $join->on('offers.demand_id', '=', 'demands.id')
                        ->where('demands.goods_id', '=', $goods_id);
                }
            })
            ->join('contracts', function($join) use($date_start, $date_stop) {
                $join->on('offers.id', '=', 'contracts.offer_id')
                    ->whereNotNull('finished_at')
                    ->where('finished_at', '>=', $date_start)
                    ->where('finished_at', '<', $date_stop);
            })
            ->join('suppliers', function($join) {
                $join->on('offers.user_id', '=', 'suppliers.user_id');
            })
            ->select(DB::raw('"ips_offers"."user_id", "ips_suppliers"."name", sum("ips_offers"."quantity") as "sum_quantity"'))
            ->groupBy(['offers.user_id', 'suppliers.name'])
            ->orderBy('sum_quantity', 'desc')
            ->get();

        return view('statistics.supplier.quantity')
            ->with('total_quantity', $total_quantity)
            ->with('date_start', $date_start)
            ->with('date_stop', $date_stop)
            ->with('data', $data);
    }

    private function _total_count($date_start, $date_stop, $goods_id)
    {
        $company_id = auth()->user()->company_id;

        $total_count = Offer::whereHas('demand', function($query) use($goods_id) {
            return $query->where('goods_id', $goods_id);
        })->whereHas('contract',  function($query) use($date_start, $date_stop) {
            return $query->whereNotNull('finished_at')
                ->where('finished_at', '>=', $date_start)
                ->where('finished_at', '<', $date_stop);
        })->count();

        $data = DB::table('offers')
            ->join('demands', function($join) use($goods_id, $company_id) {
                if (!empty($company_id)) {
                    $join->on('offers.demand_id', '=', 'demands.id')
                        ->where('demands.goods_id', '=', $goods_id)
                        ->where('demands.company_id', '=', $company_id);
                } else {
                    $join->on('offers.demand_id', '=', 'demands.id')
                        ->where('demands.goods_id', '=', $goods_id);
                }
            })
            ->join('contracts', function($join) use($date_start, $date_stop) {
                $join->on('offers.id', '=', 'contracts.offer_id')
                    ->whereNotNull('finished_at')
                    ->where('finished_at', '>=', $date_start)
                    ->where('finished_at', '<', $date_stop);
            })
            ->join('suppliers', function($join) {
                $join->on('offers.user_id', '=', 'suppliers.user_id');
            })
            ->select(DB::raw('"ips_offers"."user_id", "ips_suppliers"."name", count("ips_offers"."user_id") as "count"'))
            ->groupBy(['offers.user_id', 'suppliers.name'])
            ->orderBy('count', 'desc')
            ->get();

        return view('statistics.supplier.count')
            ->with('total_count', $total_count)
            ->with('date_start', $date_start)
            ->with('date_stop', $date_stop)
            ->with('data', $data);
    }

    private function _offer_count($date_start, $date_stop, $goods_id)
    {
        $company_id = auth()->user()->company_id;

        $offer_count = Offer::whereHas('demand', function($query) use($goods_id) {
            return $query->where('goods_id', $goods_id);
        })->where('updated_at', '>=', $date_start)->where('updated_at', '<', $date_stop)->count();

        $data = DB::table('offers')
            ->where('offers.updated_at', '>=', $date_start)->where('offers.updated_at', '<', $date_stop)
            ->join('demands', function($join) use($goods_id, $company_id) {
                if (!empty($company_id)) {
                    $join->on('offers.demand_id', '=', 'demands.id')
                        ->where('demands.goods_id', '=', $goods_id)
                        ->where('demands.company_id', '=', $company_id);
                } else {
                    $join->on('offers.demand_id', '=', 'demands.id')
                        ->where('demands.goods_id', '=', $goods_id);
                }
            })
            ->join('suppliers', function($join) {
                $join->on('offers.user_id', '=', 'suppliers.user_id');
            })
            ->select(DB::raw('"ips_offers"."user_id", "ips_suppliers"."name", count("ips_offers"."user_id") as "count"'))
            ->groupBy(['offers.user_id', 'suppliers.name'])
            ->orderBy('count', 'desc')
            ->get();

        return view('statistics.supplier.offer')
            ->with('offer_count', $offer_count)
            ->with('date_start', $date_start)
            ->with('date_stop', $date_stop)
            ->with('data', $data);
    }

    private function _offer_price($date_start, $date_stop, $goods_id)
    {
        $company_id = auth()->user()->company_id;

        $total_ravg_amount = Offer::whereHas('demand', function($query) use($goods_id) {
            return $query->where('goods_id', $goods_id);
        })->whereHas('contract',  function($query) use($date_start, $date_stop) {
            return $query->whereNotNull('finished_at')
                ->where('finished_at', '>=', $date_start)
                ->where('finished_at', '<', $date_stop);
        })->select(DB::raw('(sum("ips_offers"."price" * "ips_offers"."quantity")/sum("ips_offers"."quantity")) as "ravg_price"'))
        ->first();

        $data = DB::table('offers')
            ->join('demands', function($join) use($goods_id, $company_id) {
                if (!empty($company_id)) {
                    $join->on('offers.demand_id', '=', 'demands.id')
                        ->where('demands.goods_id', '=', $goods_id)
                        ->where('demands.company_id', '=', $company_id);
                } else {
                    $join->on('offers.demand_id', '=', 'demands.id')
                        ->where('demands.goods_id', '=', $goods_id);
                }
            })
            ->join('contracts', function($join) use($date_start, $date_stop) {
                $join->on('offers.id', '=', 'contracts.offer_id')
                    ->whereNotNull('finished_at')
                    ->where('finished_at', '>=', $date_start)
                    ->where('finished_at', '<', $date_stop);
            })
            ->join('suppliers', function($join) {
                $join->on('offers.user_id', '=', 'suppliers.user_id');
            })
            ->select(DB::raw('"ips_offers"."user_id", "ips_suppliers"."name", (sum("ips_offers"."price" * "ips_offers"."quantity")/sum("ips_offers"."quantity")) as "ravg_price"'))
            ->groupBy(['offers.user_id', 'suppliers.name'])
            ->orderBy('ravg_price', 'desc')
            ->get();

        return view('statistics.supplier.price')
            ->with('total_ravg_amount', $total_ravg_amount)
            ->with('date_start', $date_start)
            ->with('date_stop', $date_stop)
            ->with('data', $data);
    }

    public function company(Request $request)
    {
        $cond = $request->only(['date', 'goods_id', 'type']);
        $cond = array_map('trim', $cond);

        $supply_goods = auth()->user()->supplier->goods;
        $supply_goods = json_decode($supply_goods, true);

        $supply_goods_data = Goods::whereIn('id', $supply_goods)->whereNull('deleted_at')->get();
        view()->share('supply_goods_data', $supply_goods_data);

        $fetch_data = false;
        if (empty($cond['date']) or empty($cond['goods_id']) or empty($cond['type'])) {
            $fetch_data = false;
        } else {
            if (method_exists($this, $cond['type'])) {
                $fetch_data = true;
            } else {
                $fetch_data = false;
            }
        }
        if ($fetch_data) {
            $date_start = $cond['date'] . '-01 00:00:00';
            $time_offset = strtotime($cond['date']);
            $date_stop = date('Y-m', strtotime('+7 month', $time_offset)) . '-01 00:00:00';
            $func_name = $cond['type'];
            return $this->$func_name($date_start, $date_stop, $cond['goods_id']);
        } else {
            return view('statistics.company.empty');
        }
    }

    private function _c_total_quantity($date_start, $date_stop, $goods_id)
    {
        $total_quantity = Offer::where('user_id', auth()->user()->id)
        ->whereHas('demand', function($query) use($goods_id) {
            return $query->where('goods_id', $goods_id);
        })->whereHas('contract',  function($query) use($date_start, $date_stop) {
            return $query->whereNotNull('finished_at')
                ->where('finished_at', '>=', $date_start)
                ->where('finished_at', '<', $date_stop);
        })->sum('quantity');

        $data = DB::table('offers')
            ->where('offers.user_id', auth()->user()->id)
            ->join('demands', function($join) use($goods_id) {
                $join->on('offers.demand_id', '=', 'demands.id')
                    ->where('demands.goods_id', '=', $goods_id);
            })
            ->join('contracts', function($join) use($date_start, $date_stop) {
                $join->on('offers.id', '=', 'contracts.offer_id')
                    ->whereNotNull('finished_at')
                    ->where('finished_at', '>=', $date_start)
                    ->where('finished_at', '<', $date_stop);
            })
            ->join('companies', function($join) {
                $join->on('demands.company_id', '=', 'companies.id');
            })
            ->select(DB::raw('"ips_companies"."name", sum("ips_offers"."quantity") as "sum_quantity"'))
            ->groupBy(['demands.company_id', 'companies.name'])
            ->orderBy('sum_quantity', 'desc')
            ->get();

        return view('statistics.company.quantity')
            ->with('total_quantity', $total_quantity)
            ->with('date_start', $date_start)
            ->with('date_stop', $date_stop)
            ->with('data', $data);
    }

    private function _c_total_count($date_start, $date_stop, $goods_id)
    {
        $total_count = Offer::where('user_id', auth()->user()->id)
        ->whereHas('demand', function($query) use($goods_id) {
            return $query->where('goods_id', $goods_id);
        })->whereHas('contract',  function($query) use($date_start, $date_stop) {
            return $query->whereNotNull('finished_at')
                ->where('finished_at', '>=', $date_start)
                ->where('finished_at', '<', $date_stop);
        })->count();

        $data = DB::table('offers')
            ->where('offers.user_id', auth()->user()->id)
            ->join('demands', function($join) use($goods_id) {
                $join->on('offers.demand_id', '=', 'demands.id')
                    ->where('demands.goods_id', '=', $goods_id);
            })
            ->join('contracts', function($join) use($date_start, $date_stop) {
                $join->on('offers.id', '=', 'contracts.offer_id')
                    ->whereNotNull('finished_at')
                    ->where('finished_at', '>=', $date_start)
                    ->where('finished_at', '<', $date_stop);
            })
            ->join('companies', function($join) {
                $join->on('demands.company_id', '=', 'companies.id');
            })
            ->select(DB::raw('"ips_companies"."name", count("ips_offers"."user_id") as "count"'))
            ->groupBy(['demands.company_id', 'companies.name'])
            ->orderBy('count', 'desc')
            ->get();

        return view('statistics.company.count')
            ->with('total_count', $total_count)
            ->with('date_start', $date_start)
            ->with('date_stop', $date_stop)
            ->with('data', $data);
    }

    private function _c_offer_price($date_start, $date_stop, $goods_id)
    {
        $total_ravg_amount = Offer::where('user_id', auth()->user()->id)
        ->whereHas('demand', function($query) use($goods_id) {
            return $query->where('goods_id', $goods_id);
        })->whereHas('contract',  function($query) use($date_start, $date_stop) {
            return $query->whereNotNull('finished_at')
                ->where('finished_at', '>=', $date_start)
                ->where('finished_at', '<', $date_stop);
        })->select(DB::raw('(sum("ips_offers"."price" * "ips_offers"."quantity")/sum("ips_offers"."quantity")) as "ravg_price"'))
            ->first();

        $data = DB::table('offers')
            ->where('offers.user_id', auth()->user()->id)
            ->join('demands', function($join) use($goods_id) {
                $join->on('offers.demand_id', '=', 'demands.id')
                    ->where('demands.goods_id', '=', $goods_id);
            })
            ->join('contracts', function($join) use($date_start, $date_stop) {
                $join->on('offers.id', '=', 'contracts.offer_id')
                    ->whereNotNull('finished_at')
                    ->where('finished_at', '>=', $date_start)
                    ->where('finished_at', '<', $date_stop);
            })
            ->join('companies', function($join) {
                $join->on('demands.company_id', '=', 'companies.id');
            })
            ->select(DB::raw('"ips_companies"."name", (sum("ips_offers"."price" * "ips_offers"."quantity")/sum("ips_offers"."quantity")) as "ravg_price"'))
            ->groupBy(['demands.company_id', 'companies.name'])
            ->orderBy('ravg_price', 'desc')
            ->get();

        return view('statistics.company.price')
            ->with('total_ravg_amount', $total_ravg_amount)
            ->with('date_start', $date_start)
            ->with('date_stop', $date_stop)
            ->with('data', $data);
    }

    public function grade(Request $request)
    {
        $cond = $request->only(['date', 'type']);
        $cond = array_map('trim', $cond);

        $fetch_data = false;
        if (empty($cond['date']) or empty($cond['type'])) {
            $fetch_data = false;
        } else {
            if (method_exists($this, $cond['type'])) {
                $fetch_data = true;
            } else {
                $fetch_data = false;
            }
        }
        if ($fetch_data) {
            $date_start = $cond['date'] . '-01 00:00:00';
            $time_offset = strtotime($cond['date']);
            $date_stop = date('Y-m', strtotime('+7 month', $time_offset)) . '-01 00:00:00';
            $func_name = $cond['type'];
            return $this->$func_name($date_start, $date_stop);
        } else {
            return view('statistics.grade.empty');
        }
    }

    private function _grade_supplier($date_start, $date_stop)
    {
        $company_id = auth()->user()->company_id;

        $data = DB::table('offers')
            ->rightJoin('contracts', function($join) use($date_start, $date_stop) {
                $join->on('offers.id', '=', 'contracts.offer_id');
            })
            ->rightJoin('contract_grades', function($join) use($date_start, $date_stop) {
                $join->on('contract_grades.contract_id', '=', 'contracts.id')
                    ->whereNotNull('contract_grades.supplier_graded_at')
                    ->where('contract_grades.supplier_graded_at', '>=', $date_start)
                    ->where('contract_grades.supplier_graded_at', '<', $date_stop);
            })
            ->join('demands', function($join) use($company_id) {
                if (!empty($company_id)) {
                    $join->on('offers.demand_id', '=', 'demands.id')
                        ->where('demands.company_id', '=', $company_id);
                } else {
                    $join->on('offers.demand_id', '=', 'demands.id');
                }
            })
            ->join('suppliers', function($join) {
                $join->on('offers.user_id', '=', 'suppliers.user_id');
            })
            ->select(DB::raw('"ips_offers"."user_id", "ips_suppliers"."name", avg("ips_contract_grades"."supplier_grade_1") as "sgv1", avg("ips_contract_grades"."supplier_grade_2") as "sgv2", avg("ips_contract_grades"."supplier_grade_3") as "sgv3"'))
            ->groupBy(['offers.user_id', 'suppliers.name'])
            ->get();

        return view('statistics.grade.supplier')
            ->with('date_start', $date_start)
            ->with('date_stop', $date_stop)
            ->with('data', $data);
    }

    private function _grade_company($date_start, $date_stop)
    {
        $company_id = auth()->user()->company_id;

        $data = DB::table('offers')
            ->rightJoin('contracts', function($join) use($date_start, $date_stop) {
                $join->on('offers.id', '=', 'contracts.offer_id');
            })
            ->rightJoin('contract_grades', function($join) use($date_start, $date_stop) {
                $join->on('contract_grades.contract_id', '=', 'contracts.id')
                    ->whereNotNull('contract_grades.company_graded_at')
                    ->where('contract_grades.company_graded_at', '>=', $date_start)
                    ->where('contract_grades.company_graded_at', '<', $date_stop);
            })
            ->join('demands', function($join) use($company_id) {
                if (!empty($company_id)) {
                    $join->on('offers.demand_id', '=', 'demands.id')
                        ->where('demands.company_id', '=', $company_id);
                } else {
                    $join->on('offers.demand_id', '=', 'demands.id');
                }
            })
            ->join('companies', function($join) {
                $join->on('demands.company_id', '=', 'companies.id');
            })
            ->select(DB::raw('"ips_demands"."user_id", "ips_companies"."name", avg("ips_contract_grades"."company_grade_1") as "cgv1", avg("ips_contract_grades"."company_grade_2") as "cgv2"'))
            ->groupBy(['demands.user_id', 'companies.name'])
            ->get();

        return view('statistics.grade.company')
            ->with('date_start', $date_start)
            ->with('date_stop', $date_stop)
            ->with('data', $data);
    }

    public function bid_count()
    {
        $bid_counts = BidCount::orderBy('generated_at', 'asc')->paginate(15);
        $pages = $bid_counts->appends([])->render();
        return view('statistics.bid_count')
            ->with('pages', $pages)
            ->with('bid_counts', $bid_counts);
    }

    public function bid_rate(Request $request)
    {
        // $search_data = $request->only(['date_start', 'date_stop', 'goods_id', 'supplier_id']);
        $search_data = $request->only(['date_start', 'date_stop', 'goods_id', 'company_id','supplier_id']);
        $offers = Offer::with(['supplier.supplier', 'bid', 'bid.goods'])
            // 增加排序条件 需求单位 20160509 lvze
            ->orderBy('created_at','desc')
            ->orderBy('bid_id')
            ->orderBy('id');
            //->whereNotNull('quantity')
            //->where('quantity' , '>', 0);

        $invite_count = new Bid();
        $offer_count = Offer::where('price', '>', 0)->whereNull('reason');
        $join_count = Offer::where(function($query) {
            return $query->whereNotNull('reason')
                ->orWhere('price', '>', 0);
        });
        $deal_count = Offer::where('quantity', '>', 0)->where('price', '>', 0);
        $deal_amount = Offer::where('quantity', '>', 0)->where('price', '>', 0);
        $deal_quantity = Offer::where('quantity', '>', 0)->where('price', '>', 0);
        $total_deal_quantity = Offer::where('quantity', '>', 0)->where('price', '>', 0);

        if (!empty($search_data['date_start'])) {
            $offers = $offers->whereHas('bid', function($query) use($search_data) {
                return $query->where('offer_start', '>=', $search_data['date_start'].' 00:00:00');
            });

            $invite_count = $invite_count->where('offer_start', '>=', $search_data['date_start'].' 00:00:00');
            $offer_count = $offer_count->whereHas('bid', function($query) use($search_data) {
                return $query->where('offer_start', '>=', $search_data['date_start'].' 00:00:00');
            });
            $join_count = $join_count->whereHas('bid', function($query) use($search_data) {
                return $query->where('offer_start', '>=', $search_data['date_start'].' 00:00:00');
            });
            $deal_count = $deal_count->whereHas('bid', function($query) use($search_data) {
                return $query->where('offer_start', '>=', $search_data['date_start'].' 00:00:00');
            });
            $deal_amount = $deal_amount->whereHas('bid', function($query) use($search_data) {
                return $query->where('offer_start', '>=', $search_data['date_start'].' 00:00:00');
            });
            $deal_quantity = $deal_quantity->whereHas('bid', function($query) use($search_data) {
                return $query->where('offer_start', '>=', $search_data['date_start'].' 00:00:00');
            });
            $total_deal_quantity = $total_deal_quantity->whereHas('bid', function($query) use($search_data) {
                return $query->where('offer_start', '>=', $search_data['date_start'].' 00:00:00');
            });
        }

        if (!empty($search_data['date_stop'])) {
            $offers = $offers->whereHas('bid', function($query) use($search_data) {
                return $query->where('offer_start', '<=', $search_data['date_stop']. ' 23:59:59');
            });
            $invite_count = $invite_count->where('offer_start', '<=', $search_data['date_stop'].' 23:59:59');
            $offer_count = $offer_count->whereHas('bid', function($query) use($search_data) {
                return $query->where('offer_start', '<=', $search_data['date_stop']. ' 23:59:59');
            });
            $join_count = $join_count->whereHas('bid', function($query) use($search_data) {
                return $query->where('offer_start', '<=', $search_data['date_stop']. ' 23:59:59');
            });
            $deal_count = $deal_count->whereHas('bid', function($query) use($search_data) {
                return $query->where('offer_start', '<=', $search_data['date_stop']. ' 23:59:59');
            });
            $deal_amount = $deal_amount->whereHas('bid', function($query) use($search_data) {
                return $query->where('offer_start', '<=', $search_data['date_stop']. ' 23:59:59');
            });
            $deal_quantity = $deal_quantity->whereHas('bid', function($query) use($search_data) {
                return $query->where('offer_start', '<=', $search_data['date_stop']. ' 23:59:59');
            });
            $total_deal_quantity = $total_deal_quantity->whereHas('bid', function($query) use($search_data) {
                return $query->where('offer_start', '<=', $search_data['date_stop']. ' 23:59:59');
            });
        }

        if (!empty($search_data['goods_id'])) {
            $offers = $offers->whereHas('bid', function($query) use($search_data) {
                return $query->where('goods_id', $search_data['goods_id']);
            });
            $invite_count = $invite_count->where('goods_id', $search_data['goods_id']);
            $offer_count = $offer_count->whereHas('bid', function($query) use($search_data) {
                return $query->where('goods_id', $search_data['goods_id']);
            });
            $join_count = $join_count->whereHas('bid', function($query) use($search_data) {
                return $query->where('goods_id', $search_data['goods_id']);
            });
            $deal_count = $deal_count->whereHas('bid', function($query) use($search_data) {
                return $query->where('goods_id', $search_data['goods_id']);
            });
            $deal_amount = $deal_amount->whereHas('bid', function($query) use($search_data) {
                return $query->where('goods_id', $search_data['goods_id']);
            });
            $deal_quantity = $deal_quantity->whereHas('bid', function($query) use($search_data) {
                return $query->where('goods_id', $search_data['goods_id']);
            });
            $total_deal_quantity = $total_deal_quantity->whereHas('bid', function($query) use($search_data) {
                return $query->where('goods_id', $search_data['goods_id']);
            });
        }

        // 增加筛选条件 需求单位 20160509 lvze
        if (!empty($search_data['company_id'])) {
            // $offers = $offers->where('user_id', $search_data['company_id']);
            $offers = $offers->whereHas('demand', function($query) use($search_data) {
                return $query->where('company_id', $search_data['company_id']);
            });
        }

        if (!empty($search_data['supplier_id'])) {
            $offers = $offers->where('user_id', $search_data['supplier_id']);
            $invite_count = $invite_count->whereHas('offers', function($query) use($search_data) {
                return $query->where('user_id', $search_data['supplier_id']);
            });
            $offer_count = $offer_count->where('user_id', $search_data['supplier_id']);
            $join_count = $join_count->where('user_id', $search_data['supplier_id']);
            $deal_count = $deal_count->where('user_id', $search_data['supplier_id']);
            $deal_amount = $deal_amount->where('user_id', $search_data['supplier_id']);
            $deal_quantity = $deal_quantity->where('user_id', $search_data['supplier_id']);
        }

        $invite_count = $invite_count->sum('invitation_quantity');
        $offer_count = $offer_count->count();
        $join_count = $join_count->count();
        $deal_count = $deal_count->count();
        $deal_quantity = $deal_quantity->sum('quantity');
        $total_deal_quantity = $total_deal_quantity->sum('quantity');
        $deal_amount = $deal_amount->sum(DB::raw('("delivery_costs" + "price")*"quantity"'));

        if ($request->get('export') != 'yes') {
            $offers = $offers->paginate(12);
            $pages = $offers->appends($search_data)->render();

            // 增加需求单位的下拉数据源 20160509 lvze
            $companies = DB::table('companies')
                ->select(DB::raw('"ips_companies"."id", "ips_companies"."name"'))
                ->get();

            $goods = Goods::whereNull('deleted_at')->where('is_available', true)->get();
            if (!empty($search_data['goods_id'])) {
                $suppliers = Supplier::whereRaw('\'["' . $search_data['goods_id'] . '"]\'::jsonb <@ "goods"')->get();
            } else {
                $suppliers = Supplier::all();
            }

            return view('statistics.bid_rate')
                ->with('pages', $pages)
                ->with('offers', $offers)
                ->with('goods', $goods)
                // 增加需求单位的下拉数据源 20160509 lvze
                ->with('companies', $companies)
                ->with('suppliers', $suppliers)
                ->with('invite_count', $invite_count)
                ->with('offer_count', $offer_count)
                ->with('join_count', $join_count)
                ->with('deal_count', $deal_count)
                ->with('deal_quantity', $deal_quantity)
                ->with('total_deal_quantity', $total_deal_quantity)
                ->with('deal_amount', $deal_amount);
        } else {
            $offers = $offers->get();
            $view = view('statistics.bid_rate_export')
                ->with('offers', $offers);

            header("Content-type: application/x-excel");
            header("Accept-Ranges: bytes");
            header("Accept-Length: ".strlen($view));
            header("Content-Disposition: attachment; filename=" . date('YmdHis').'.xls');
            exit($view);
        }
    }

    public function test()
    {
        /*$bids = Bid::all();
        foreach ($bids as $bid) {
            if ($bid->type == 'global') {
                $time_stop = $bid->offer_start;
                $supplier_count = User::where('created_at', '<=', $time_stop)
                    ->where('type', 'supplier')
                    ->whereHas('supplier', function($query) use($bid) {
                        return $query->whereRaw('\'["' . $bid->goods_id . '"]\'::jsonb <@ "goods"');
                    })
                    ->count();
            } else {
                $supplier_ids = json_decode($bid->suppliers, true);
                $supplier_count = count($supplier_ids);
            }
            $bid->invitation_quantity = $supplier_count;
            $bid->save();
            echo $bid->id.' @ '.$supplier_count.'<hr />',"\n";
        }*/

        /*$d = [
            '2016-03-11',
            '2016-03-12',
            '2016-03-13',
            '2016-03-14',
            '2016-03-15',
            '2016-03-16',
            '2016-03-17',
            '2016-03-18',
            '2016-03-19',
            '2016-03-20',
            '2016-03-21',
            '2016-03-22'
        ];
        foreach ($d as $td) {
            $data = [];

            //计算运行天书天数
            $today = $td;
            $start_date = config('settings.system_start_at', '2015-10-15');
            $interval = date_diff(date_create($today), date_create($start_date));
            $data['days'] = $interval->format('%a');

            //计算发布标书数量
            $data['bid_counts'] = Demand::where('quantity', '>', 0)
                ->whereHas('bid', function ($query) use ($start_date, $today) {
                    return $query->where('offer_stop', '>=', $start_date . ' 00:00:00')
                        ->where('offer_stop', '<=', $today . ' 23:59:59');
                })
                ->count();

            //计算流标次数
            $data['failed_bid_counts'] = Demand::where('quantity', '>', 0)
                ->whereHas('bid', function ($query) use ($start_date, $today) {
                    return $query->where('offer_stop', '>=', $start_date . ' 00:00:00')
                        ->where('offer_stop', '<=', $today . ' 23:59:59');
                })
                ->where('is_cancel', true)
                ->count();

            //计算招标品种数

            $data['goods_counts'] = Demand::whereHas('bid', function ($query) use ($start_date, $today) {
                return $query->where('offer_stop', '>=', $start_date . ' 00:00:00')
                    ->where('offer_stop', '<=', $today . ' 23:59:59');
            })->count(\DB::raw('distinct "goods_id"'));

            //计算成交数量
            $data['quantity'] = Offer::whereNotNull('quantity')
                ->where('quantity', '>', 0)
                ->whereHas('demand', function ($query) use ($today, $start_date) {
                    return $query->where('quantity', '>', 0)
                        ->where('created_at', '>=', $start_date . ' 00:00:00')
                        ->where('created_at', '<=', $today . ' 23:59:59');
                })
                ->sum('quantity');

            //计算成交金额
            $data['amount'] = Offer::whereNotNull('quantity')
                ->where('quantity', '>', 0)
                ->whereHas('demand', function ($query) use ($today, $start_date) {
                    return $query->where('quantity', '>', 0)
                        ->where('created_at', '>=', $start_date . ' 00:00:00')
                        ->where('created_at', '<=', $today . ' 23:59:59');
                })
                ->sum(\DB::raw('"quantity" * "price"'));

            //计算中标供应商数量
            $data['supplier_counts'] = Offer::whereNotNull('quantity')
                ->where('quantity', '>', 0)
                ->whereHas('demand', function ($query) {
                    return $query->where('quantity', '>', 0);
                })->whereHas('bid', function ($query) use ($today, $start_date) {
                    return $query->where('offer_stop', '>=', $start_date . ' 00:00:00')
                        ->where('offer_stop', '<=', $today . ' 23:59:59');
                })
                ->count(\DB::raw('distinct "user_id"'));

            //计算参与报价供应商数量
            $data['offer_counts'] = Offer::whereNotNull('quantity')
                ->whereNull('reason')
                ->whereHas('demand', function ($query) {
                    return $query->where('quantity', '>', 0);
                })->whereHas('bid', function ($query) use ($today, $start_date) {
                    return $query->where('offer_stop', '>=', $start_date . ' 00:00:00')
                        ->where('offer_stop', '<=', $today . ' 23:59:59');
                })
                ->count(\DB::raw('distinct "user_id"'));

            $data['generated_at'] = $today;

            try {
                if (BidCount::where('generated_at', $today)->exists()) {
                    BidCount::where('generated_at', $today)->update($data);
                } else {
                    BidCount::create($data);
                }
            } catch (\Exception $e) {

            }
        }*/
    }
}
