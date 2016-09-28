<?php

namespace App\Http\Controllers\Bid;

use App\Bid;
use App\Demand;
use App\Offer;
use App\Setting;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use URL;
use Validator;
use DB;
class SupplierController extends Controller
{
    public function index()
    {
        $current_user = auth()->user();
        $current_supplier = $current_user->supplier;
        $supply_goods = json_decode($current_supplier->goods, true);
        $datetime = date('Y-m-d H:i:s');

        $bids = Bid::orderBy('created_at', 'desc')
            ->whereHas('basket', function($query) use($datetime) {
                return $query->where('state', 'bided')->where('bided_at', '<=', $datetime);
            });

        $bids = $bids->whereHas('demands', function($query) {
            return $query->select(DB::raw('CASE WHEN sum("quantity") > 0 THEN 1 ELSE 0 END'))->groupBy('bid_id');
        });

        $bids = $bids->where(function($query) use($supply_goods, $current_user) {
            return $query->where(function($query) use($supply_goods) {
                return $query->where('type', 'global')->whereIn('goods_id', $supply_goods);
            })->orWhere(function($query) use($current_user) {
                return $query->where('type', 'invite')->whereRaw('\'["' . $current_user->id . '"]\'::jsonb <@ "suppliers"');
            });
        });

        $bids = $bids->paginate(10);
        $pages = $bids->appends([])->render();

        return view('bid.supplier.index')
            ->with('current_user', $current_user)
            ->with('bids', $bids)
            ->with('datetime', $datetime)
            ->with('pages', $pages);
    }

    public function pending()
    {
        $current_user = auth()->user();
        $current_supplier = $current_user->supplier;
        $supply_goods = json_decode($current_supplier->goods, true);
        $datetime = date('Y-m-d H:i:s');

        $bids = Bid::orderBy('created_at', 'desc')
            ->whereHas('basket', function($query) use($datetime) {
                return $query->where('state', 'bided')->where('bided_at', '<=', $datetime);
            });

        $bids = $bids->whereHas('demands', function($query) {
            return $query->select(DB::raw('CASE WHEN sum("quantity") > 0 THEN 1 ELSE 0 END'))->groupBy('bid_id');
        });

        $bids = $bids->where(function($query) use($supply_goods, $current_user) {
            return $query->where(function($query) use($supply_goods) {
                return $query->where('type', 'global')->whereIn('goods_id', $supply_goods);
            })->orWhere(function($query) use($current_user) {
                return $query->where('type', 'invite')->whereRaw('\'["' . $current_user->id . '"]\'::jsonb <@ "suppliers"');
            });
        });

        $bids = $bids->where('offer_stop', '>=', $datetime)->where('offer_start', '<=', $datetime);

        $bids = $bids->paginate(10);
        $pages = $bids->appends([])->render();

        return view('bid.supplier.pending')
            ->with('current_user', $current_user)
            ->with('bids', $bids)
            ->with('datetime', $datetime)
            ->with('pages', $pages);
    }

    public function done()
    {
        $current_user = auth()->user();
        $current_supplier = $current_user->supplier;
        $supply_goods = json_decode($current_supplier->goods, true);
        $datetime = date('Y-m-d H:i:s');

        $bids = Bid::orderBy('created_at', 'desc')
            ->whereHas('basket', function($query) use($datetime) {
                return $query->where('state', 'bided')->where('bided_at', '<=', $datetime);
            });

        $bids = $bids->whereHas('demands', function($query) {
            return $query->select(DB::raw('CASE WHEN sum("quantity") > 0 THEN 1 ELSE 0 END'))->groupBy('bid_id');
        });

        $bids = $bids->where(function($query) use($supply_goods, $current_user) {
            return $query->where(function($query) use($supply_goods) {
                return $query->where('type', 'global')->whereIn('goods_id', $supply_goods);
            })->orWhere(function($query) use($current_user) {
                return $query->where('type', 'invite')->whereRaw('\'["' . $current_user->id . '"]\'::jsonb <@ "suppliers"');
            });
        });

        $bids = $bids->where('offer_stop', '<', $datetime);
        $bids = $bids->whereHas('offers', function($query) use($current_user) {
            return $query->where('user_id', $current_user->id);
        });

        $bids = $bids->paginate(10);
        $pages = $bids->appends([])->render();

        return view('bid.supplier.done')
            ->with('current_user', $current_user)
            ->with('bids', $bids)
            ->with('datetime', $datetime)
            ->with('pages', $pages);
    }

    public function offer(Bid $bid)
    {
        $datetime = date('Y-m-d H:i:s');
        if ($datetime > $bid->offer_stop) {
            return redirect(route('bid.supplier.pending'))->with('tip_message', ['content' => '已超过报价时间', 'state' => 'danger']);
        }
        if ($datetime < $bid->offer_start) {
            return redirect(route('bid.supplier.pending'))->with('tip_message', ['content' => '报价还未开始', 'state' => 'danger']);
        }
        $current_user = auth()->user();
        $demands = Demand::with(['company', 'company.delivery_modes', 'offer'])->whereBidId($bid->id)->where('quantity', '>', 0)->get();
        $goods = json_decode($bid->goods_static);

        return view('bid.supplier.offer')
            ->with('demands', $demands)
            ->with('goods', $goods)
            ->with('bid', $bid)
            ->with('current_user', $current_user);
    }

    public function saveOffer(Request $request, Bid $bid)
    {
        $current_user = auth()->user();
        $time = time();
        $datetime = date('Y-m-d H:i:s', $time);
        if ($bid->offer_stop < $datetime) {
            return response()->json([
                'state' => 'error',
                'message' => ['form' => ['报价时间已过']]
            ]);
        }
        $type = trim($request->get('type', ''));
        if (empty($type)) {
            return response()->json([
                'state' => 'error',
                'message' => ['form' => ['非法的请求']]
            ]);
        }
        $demand_id = $request->get('demand_id');
        $demand = Demand::find($demand_id);
        if ($type == 'refuse') {
            $reason = trim($request->get('reason', ''));
            $other = trim($request->get('other', ''));
            if ($reason == '其他' and !empty($other)) {
                $reason = $other;
            }
            $offer = Offer::firstOrNew([
                'bid_id' => $bid->id,
                'demand_id' => $demand_id,
                'user_id' => $current_user->id
            ]);
            if ($offer->exists) {
                return response()->json([
                    'state' => 'error',
                    'message' => ['form' => ['已经报价或提交了不参与操作']]
                ]);
            }
            $offer->delivery_mode = '无';
            $offer->delivery_costs = 0;
            $offer->price = 0;
            $offer->quantity_floor = 0;
            $offer->quantity_caps = 0;
            $offer->created_at = $datetime;
            $offer->updated_at = $datetime;
            $offer->reason = $reason;
            try {
                if ($offer->save()) {
                    return response()->json([
                        'state' => 'success'
                    ]);
                } else {
                    return response()->json([
                        'state' => 'error',
                        'message' => ['form' => ['服务器繁忙，请稍候再试']]
                    ]);
                }
            } catch (\Exception $e) {
                return response()->json([
                    'state' => 'error',
                    'message' => ['form' => ['服务器繁忙，请稍候再试']]
                ]);
            }
        } else {

            $offer_min_num_setting = Setting::where('name', 'offer_min_num')->first();
            if (empty($offer_min_num_setting)) {
                $offer_min_num = 3;
            } else {
                $offer_min_num = intval($offer_min_num_setting->data);
            }

            $assign_rule = json_decode($demand->assign_rule);
            $assign_rule_data = $assign_rule->rules;
            $assign_rule_data = json_decode($assign_rule_data, true);
            $assign = array_filter($assign_rule_data);
            arsort($assign);
            $assign_count = count($assign);
            $min_assign = min($assign);
            $quantity_floor = ($demand->quantity*$min_assign)/100;
            $form_data = $request->only(['quantity_caps', 'price', 'delivery_mode']);
            $rules = [
                'quantity_caps' => 'required|numeric|min:'.$quantity_floor,
                'price' => 'required|numeric|min:'.$demand->price_floor.'|max:'.$demand->price_caps,
                'delivery_mode' => 'required'
            ];
            $messages = [
                'quantity_caps.required' => '最高成交量不能为空',
                'quantity_caps.numeric' => '最高成交量只能为数字',
                'quantity_caps.min' => '最高成交量最小只能为:min',
                'price.required' => '报价不能为空',
                'price.numeric' => '报价只能为数字',
                'price.min' => '报价过低，请勿恶意报价',
                'price.max' => '报价过高',
                'delivery_mode' => '请选择到货方式'
            ];
            $validator = Validator::make($form_data, $rules, $messages);

            if ($validator->fails()) {
                return response()->json([
                    'state' => 'error',
                    'message' => $validator->errors()
                ]);
            }

            $offer = Offer::firstOrNew([
                'bid_id' => $bid->id,
                'demand_id' => $demand_id,
                'user_id' => $current_user->id
            ]);
            list($costs, $mode) = explode(',', $form_data['delivery_mode']);
            $offer->delivery_mode = $mode;
            $offer->delivery_costs = $costs;
            $offer->price = $form_data['price'];
            $offer->quantity_floor = $quantity_floor;
            $offer->quantity_caps = $form_data['quantity_caps'];
            $offer->created_at = $datetime;
            $offer->updated_at = $datetime;

            $result = DB::transaction(function() use ($offer_min_num, $offer, $current_user, $bid, $demand, $assign, $assign_count, $datetime) {
                if (!$offer->save()) {
                    return false;
                }

                $offers = Offer::orderBy(DB::raw('"price"+"delivery_costs"'))
                    ->orderBy('updated_at', 'asc')
                    ->where('price', '>', 0)
                    ->where('demand_id', $demand->id)
                    ->where('bid_id', $bid->id)
                    //->take($assign_count)
                    ->get();

                $offer_count = Offer::where('price', '>', 0)
                    ->where('demand_id', $demand->id)
                    ->where('bid_id', $bid->id)
                    ->whereNull('reason')
                    ->count();
                if ($offer_count >= $offer_min_num) {
                    $demand->is_cancel = false;
                    if (!$demand->save()) {
                        DB::rollBack();
                        return false;
                    }
                }

                //DB::table('offers')->where('demand_id', $demand->id)->where('bid_id', $bid->id)->update(['quantity' => 0]);

                foreach ($offers as $k=>$offer_item) {
                    if ($assign_count < $k+1) {
                        $assign_quantity = 0;
                    } else {
                        $assign_quantity = ($demand->quantity * $assign[$k]) / 100;
                        $assign_quantity = sprintf('%.5f', $assign_quantity);
                        if ($offer_item->quantity_floor > $assign_quantity) {
                            $assign_quantity = 0;
                        }
                        if ($offer_item->quantity_caps < $assign_quantity) {
                            $assign_quantity = $offer_item->quantity_caps;
                        }
                    }
                    $update_data = ['quantity' => $assign_quantity];
                    if (!DB::table('offers')->where('id', $offer_item->id)->where('updated_at', $offer_item->updated_at)->update($update_data)) {
                        DB::rollBack();
                        return false;
                    }
                }

                return true;
            });
            if ($result) {
                return response()->json([
                    'state' => 'success'
                ]);
            } else {
                return response()->json([
                    'state' => 'error',
                    'message' => ['form' => ['服务器繁忙，请稍候再试']]
                ]);
            }
        }
    }

    public function saveOffer2(Request $request, Bid $bid)
    {
        $redirect_url = URL::full();
        $current_user = auth()->user();
        $datetime = date('Y-m-d H:i:s');
        if ($datetime > $bid->offer_stop) {
            return redirect(route('bid.supplier.pending'))->with('tip_message', ['content' => '已超过报价时间', 'state' => 'danger']);
        }
        if ($datetime < $bid->offer_start) {
            return redirect(route('bid.supplier.pending'))->with('tip_message', ['content' => '报价还未开始', 'state' => 'danger']);
        }
        $form_data = $request->only([
            'price', 'delivery_mode', 'quantity_floor',
            'quantity_caps', 'demand_id'
        ]);

        $offers_insert_data = [];
        $rules = [];
        $messages = [];
        $demand_ids = [];
        foreach ($form_data['demand_id'] as $k => $demand_id) {
            if (!empty($form_data['price'][$k])) {
                $demand_ids[] = $demand_id;
                $offer_insert_data = [
                    'bid_id' => $bid->id,
                    'demand_id' => $demand_id,
                    'user_id' => $current_user->id
                ];

                $demand = Demand::find($demand_id);

                $rules['price.' . $k] = 'numeric|min:' . $demand->price_floor . '|max:' . $demand->price_caps;
                $messages['price.' . $k . '.numeric'] = '只能填写数字';
                $messages['price.' . $k . '.min'] = '您的报价过低了';
                $messages['price.' . $k . '.max'] = '您的报价过高了';
                $offer_insert_data['price'] = $form_data['price'][$k];

                list($delivery_costs, $delivery_mode) = explode(',', $form_data['delivery_mode'][$k]);
                $offer_insert_data['delivery_costs'] = $delivery_costs;
                $offer_insert_data['delivery_mode'] = $delivery_mode;

                $rules['quantity_floor.' . $k] = 'numeric|min:0';
                $messages['quantity_floor.' . $k . '.numeric'] = '只能填写数字';
                $messages['quantity_floor.' . $k . '.min'] = '最小只能为:min';
                $offer_insert_data['quantity_floor'] = $form_data['quantity_floor'][$k];

                $rules['quantity_caps.' . $k] = 'numeric|min:' . $form_data['quantity_floor'][$k];
                $messages['quantity_caps.' . $k . '.numeric'] = '只能填写数字';
                $messages['quantity_caps.' . $k . '.min'] = '最小只能为:min';
                $offer_insert_data['quantity_caps'] = $form_data['quantity_caps'][$k];

                $offers_insert_data[] = $offer_insert_data;
            }
        }

        $validator = Validator::make($form_data, $rules, $messages);
        if ($validator->fails()) {
            return redirect($redirect_url)->withErrors($validator->errors())->withInput();
        }

        if (count($offers_insert_data) > 0) {
            $rst = DB::transaction(function() use ($offers_insert_data, $datetime) {
                foreach ($offers_insert_data as $offer_insert_data) {
                    $offer = Offer::firstOrNew([
                        'bid_id' => $offer_insert_data['bid_id'],
                        'demand_id' => $offer_insert_data['demand_id'],
                        'user_id' => $offer_insert_data['user_id']
                    ]);
                    if (!$offer->exists) {
                        $offer->created_at = $datetime;
                        $offer->updated_at = $datetime;
                    } else {
                        $offer->updated_at = $datetime;
                    }
                    if (!$offer) {
                        DB::rollBack();
                        return false;
                    }
                    $offer->price = $offer_insert_data['price'];
                    $offer->delivery_costs = $offer_insert_data['delivery_costs'];
                    $offer->delivery_mode = $offer_insert_data['delivery_mode'];
                    $offer->quantity_floor = $offer_insert_data['quantity_floor'];
                    $offer->quantity_caps = $offer_insert_data['quantity_caps'];
                    if (!$offer->save()) {
                        DB::rollBack();
                        return false;
                    }
                }
                return true;
            });
            if (!$rst) {
                return redirect($redirect_url)->withErrors(['form' => ['服务器繁忙，请稍后再试']]);
            }

            $result = DB::transaction(function() use($demand_ids, $bid, $current_user, $datetime) {
                foreach ($demand_ids as $demand_id) {
                    $demand = Demand::find($demand_id);
                    $assign_rule = json_decode($demand->assign_rule, true)['rules'];
                    $assign_rule = json_decode($assign_rule, true);
                    $count = count($assign_rule);
                    $offers = Offer::orderBy(DB::raw('"price"+"delivery_costs"'))
                        ->orderBy('updated_at', 'asc')
                        ->where('demand_id', $demand_id)
                        ->where('bid_id', $bid->id)
                        ->take($count)
                        ->get();
                    foreach ($offers as $k=>$offer) {
                        $assign_quantity = ($demand->quantity * $assign_rule[$k]) / 100;
                        $assign_quantity = sprintf('%.2f', $assign_quantity);
                        if ($offer->quantity_floor > $assign_quantity) {
                            $assign_quantity = 0;
                        }
                        if ($offer->quantity_caps < $assign_quantity) {
                            $assign_quantity = $offer->quantity_caps;
                        }
                        if (!Offer::where('id', $offer->id)->where('updated_at', $offer->updated_at)->update(['quantity' => $assign_quantity, 'updated_at' => $datetime])) {
                            DB::rollBack();
                            return false;
                        }
                    }
                }
                return true;
            });
        } else {
            $result = true;
        }

        if ($result) {
            return redirect($redirect_url)->with('tip_message', ['content' => '报价成功', 'state' => 'success']);
        } else {
            return redirect($redirect_url)->withErrors(['form' => ['服务器繁忙，请稍后再试']]);
        }
    }

    public function view(Bid $bid)
    {
        $datetime = date('Y-m-d H:i:s');
        $current_user = auth()->user();
        $demands = Demand::with(['company', 'company.delivery_modes', 'offer'])->whereBidId($bid->id)->where('quantity', '>', 0)->get();
        $goods = json_decode($bid->goods_static);

        return view('bid.supplier.view')
            ->with('demands', $demands)
            ->with('datetime', $datetime)
            ->with('goods', $goods)
            ->with('bid', $bid)
            ->with('current_user', $current_user);
    }
}
