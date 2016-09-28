<?php

namespace App\Http\Controllers\Offer;

use App\Company;
use App\OfferBasket;
use App\OfferInformation;
use App\Goods;
use Illuminate\Http\Request;
use URL;
use Validator;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class InformationController extends Controller
{
    public function index(Request $request)
    {
        $cond = $request->only(['date_start', 'date_stop']);
        $cond = array_map('trim', $cond);

        //$current_user = auth()->user();
        $offer_baskets = OfferBasket::with('information')->orderBy('name', 'desc');
        /*$offer_baskets = $offer_baskets->whereHas('information', function($query) {
            return $query->where('supplier_id', auth()->user()->id);
        });*/

        if (!empty($cond['date_start'])) {
            $offer_baskets = $offer_baskets->where('name', '>=', $cond['date_start']);
        }
        if (!empty($cond['date_stop'])) {
            $offer_baskets = $offer_baskets->where('name', '<=', $cond['date_stop']);
        }

        $offer_baskets = $offer_baskets->paginate(10);
        $pages = $offer_baskets->appends($cond)->render();

        return view('offer.information.index')
            ->with('offer_baskets', $offer_baskets)
            ->with('pages', $pages);
    }

    public function create()
    {
        $redirect_url = URL::previous();
        $today = date('Y-m-d');
        if (OfferBasket::where('name', $today)->exists()) {
            return redirect($redirect_url)->with('tip_message', ['content' => $today.'需求清单已经存在，请直接编辑清单即可', 'state' => 'warning']);
        } else {
            try {
                OfferBasket::create(['name' => $today]);
                return redirect($redirect_url)->with('tip_message', ['content' => $today.'清单创建成功', 'state' => 'success']);
            } catch (\Exception $e) {
                return redirect($redirect_url)->with('tip_message', ['content' => $today.'清单可能已经存在', 'state' => 'warning']);
            }
        }
    }

    public function informationList(Request $request, OfferBasket $offer_basket)
    {
        $current_user = auth()->user();
        $information = $offer_basket->information()->with('goods')->where('supplier_id', $current_user->id)->orderBy('goods_id')->get();
        $unpublished_exists = $offer_basket->information()->where('state', 'created')->where('supplier_id', $current_user->id)->exists();
        return view('offer.information.list')
            ->with('offer_basket', $offer_basket)
            ->with('unpublished_exists', $unpublished_exists)
            ->with('information', $information);
    }

    public function append(Request $request, OfferBasket $offer_basket)
    {
        $supplier = auth()->user()->supplier;
        $supply_goods = json_decode($supplier->goods, true);

        $goods_records = Goods::orderBy('code')->whereIn('id', $supply_goods);
        $goods_records = $goods_records->whereNull('deleted_at')->get();

        $companies = Company::with('delivery_modes')->whereNull('deleted_at')->orderBy('name')->get();

        $ports = config('address.ports', []);
        $stations = config('address.stations', []);

        return view('offer.information.append')
            ->with('goods_records', $goods_records)
            ->with('companies', $companies)
            ->with('ports', $ports)
            ->with('stations', $stations)
            ->with('basket', $offer_basket);
    }

    public function saveAppend(Request $request, OfferBasket $offer_basket)
    {
        $redirect_url = URL::full();

        if ($offer_basket->name < date('Y-m-d')) {
            return redirect($redirect_url)->withErrors(['msg' => [$offer_basket->name.' 已经过,不能再追加']])->withInput();
        }

        $supplier = auth()->user()->supplier;
        $supply_goods = json_decode($supplier->goods, true);

        $goods_records = Goods::orderBy('code')->whereIn('id', $supply_goods);
        $goods_ids = $goods_records->whereNull('deleted_at')->lists('id');
        if (empty($goods_ids)) {
            $goods_ids = [];
        } else {
            $goods_ids =  $goods_ids->toArray();
        }

        $form_data = $request->only([
            'goods_id', 'quantity', 'quantity_type', 'prices', 'addresses', 'bargaining', 'quality_standard',
            'payment', 'payment_day', 'price_validity', 'delivery_start', 'delivery_stop'
        ]);

        $rules = [
            'goods_id' => 'required|in:'.implode(',', $goods_ids),
            'quantity_type' => 'required|in:limited,infinite',
            'quantity' => 'required_if:quantity_type,limited|numeric|min:0',
            'prices' => 'required|array',
            'addresses' => 'required|array',
            'bargaining' => 'required|in:true,false',
            'payment' => 'required|in:先款后货,货到x天后付款',
            'price_validity' => 'required|integer|min:0',
            'delivery_start' => 'required|date',
            'delivery_stop' => 'required|date|after:'.$form_data['delivery_start'],
        ];
        if ($form_data['payment'] == '货到x天后付款') {
            $rules['payment_day'] = 'required|integer:min:0';
        }

        $messages = [
            'addresses.required' => '请至少选择一个地址',
            'prices.required' => '请至少填写一个有效报价',
            'required' => '必填项不能为空',
            'numeric' => '只能填写数字',
            'in' => '非法的数据',
            'array' => '非法的数据',
            'min' => '最小只能为:min',
            'integer' => '请填写整数',
            'max' => '字符数超出:max',
            'date' => '请填写日期格式',
            'after' => '日期应晚于:after'
        ];

        $validator = Validator::make($form_data, $rules, $messages);
        if ($validator->fails()) {
            return redirect($redirect_url)->withErrors($validator->errors())->withInput();
        }

        $addresses = [];
        foreach ($form_data['prices'] as $k=>$v) {
            $v = abs($v);
            $v = round($v, 2);
            $form_data['prices'][$k] = $v;
            if (empty($v)) {
                unset($form_data['prices'][$k]);
            } else {
                if (empty($form_data['addresses'][$k])) {
                    unset($form_data['prices'][$k]);
                } else {
                    $_exists_addr = [];
                    foreach ($form_data['addresses'][$k] as $type => $addr_list) {
                        $tmp_addr_list = array_map('trim', $addr_list);
                        if (count($addr_list) == 0 or implode('', $tmp_addr_list) == '') {
                            unset($form_data['addresses'][$k][$type]);
                        } else {
                            foreach ($tmp_addr_list as $key => $addr) {
                                if (!in_array($type . $addr, $_exists_addr)) {
                                    $_exists_addr[] = $type . $addr;
                                    if (empty($addr)) {
                                        unset($form_data['addresses'][$k][$type][$key]);
                                    } else {
                                        $addresses[$v][] = $addr;
                                        $form_data['addresses'][$k][$type][$key] = $addr;
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }

        if (empty($form_data['prices'])) {
            return redirect($redirect_url)->withErrors(['prices' => ['至少需要一个有效报价']])->withInput();
        }

        if (count($addresses) == 0) {
            return redirect($redirect_url)->withErrors(['prices' => ['至少需要一个有效地址']])->withInput();
        }

        $form_data['prices'] = $form_data['prices'];
        $form_data['addresses'] = $form_data['addresses'];
        $form_data['prices_with_addresses'] = $addresses;

        if ($form_data['payment'] != '先款后货') {
            //$form_data['payment'] = str_replace('x', (string)intval($form_data['payment_day']), '货到x天后付款');
            $form_data['payment'] = (string)intval($form_data['payment_day']);
        }

        if ($form_data['quantity_type'] == 'infinite') {
            $form_data['quantity'] = -1;
        }
        unset($form_data['quantity_type']);

        unset($form_data['payment_day']);
        /*$companies = [];
        $delivery_modes = [];
        foreach ($form_data['companies'] as $item) {
            list($mode, $company_id) = explode(',', $item);
            $companies[] = $company_id;
            $delivery_modes[] = $item;
        }
        $form_data['companies'] = json_encode($companies);
        $form_data['delivery_modes'] = json_encode($delivery_modes);
        */
        if ($form_data['bargaining'] == 'true') {
            $form_data['bargaining'] = true;
        } else {
            $form_data['bargaining'] = false;
        }

        $form_data['offer_basket_id'] = $offer_basket->id;
        $form_data['supplier_id'] = auth()->user()->id;
        $form_data['state'] = 'created';

        if (OfferInformation::create($form_data)) {
            return redirect($redirect_url)->with('tip_message', ['content' => '追加成功', 'state' => 'success']);
        } else {
            return redirect($redirect_url)->withInput()->withErrors(['form' => ['服务器繁忙，请稍后再试']]);
        }
    }

    public function edit(Request $request, OfferInformation $offer_information)
    {
        $offer_basket = $offer_information->offer_basket;

        $supplier = auth()->user()->supplier;
        $supply_goods = json_decode($supplier->goods, true);

        $goods_records = Goods::orderBy('code')->whereIn('id', $supply_goods);
        $goods_records = $goods_records->whereNull('deleted_at')->get();

        $companies = Company::with('delivery_modes')->whereNull('deleted_at')->orderBy('name')->get();

        $ports = config('address.ports', []);
        $stations = config('address.stations', []);

        return view('offer.information.edit')
            ->with('basket', $offer_basket)
            ->with('goods_records', $goods_records)
            ->with('companies', $companies)
            ->with('ports', $ports)
            ->with('stations', $stations)
            ->with('offer_information', $offer_information);
    }

    public function saveEdit(Request $request, OfferInformation $offer_information)
    {
        $redirect_url = URL::full();

        if ($offer_information->offer_basket->name < date('Y-m-d')) {
            return redirect($redirect_url)->withErrors(['msg' => [$offer_information->offer_basket->name.' 已经过,不能再修改']])->withInput();
        }

        if ($offer_information->supplier_id != auth()->user()->id) {
            return redirect($redirect_url)->withInput()->withErrors(['form' => ['无权修改此记录']]);
        }

        /*if ($offer_information->state == 'published') {
            return redirect($redirect_url)->withInput()->withErrors(['form' => ['记录已发布，不能修改']]);
        }*/

        $supplier = auth()->user()->supplier;
        $supply_goods = json_decode($supplier->goods, true);

        $goods_records = Goods::orderBy('code')->whereIn('id', $supply_goods);
        $goods_ids = $goods_records->whereNull('deleted_at')->lists('id');
        if (empty($goods_ids)) {
            $goods_ids = [];
        } else {
            $goods_ids =  $goods_ids->toArray();
        }

        $form_data = $request->only([
            'goods_id', 'quantity', 'quantity_type', 'prices', 'addresses', 'bargaining', 'quality_standard',
            'payment', 'payment_day', 'price_validity', 'delivery_start', 'delivery_stop'
        ]);

        $rules = [
            'goods_id' => 'required|in:'.implode(',', $goods_ids),
            'quantity_type' => 'required|in:limited,infinite',
            'quantity' => 'required_if:quantity_type,limited|numeric|min:0',
            'prices' => 'required|array',
            'addresses' => 'required|array',
            'bargaining' => 'required|in:true,false',
            'payment' => 'required|in:先款后货,货到x天后付款',
            'price_validity' => 'required|integer|min:0',
            'delivery_start' => 'required|date',
            'delivery_stop' => 'required|date|after:'.$form_data['delivery_start'],
        ];
        if ($form_data['payment'] == '货到x天后付款') {
            $rules['payment_day'] = 'required|integer:min:0';
        }

        $messages = [
            'addresses.required' => '请至少选择一个地址',
            'prices.required' => '请至少填写一个有效报价',
            'required' => '必填项不能为空',
            'numeric' => '只能填写数字',
            'in' => '非法的数据',
            'array' => '非法的数据',
            'min' => '最小只能为:min',
            'integer' => '请填写整数',
            'max' => '字符数超出:max',
            'date' => '请填写日期格式',
            'after' => '日期应晚于:after'
        ];

        $validator = Validator::make($form_data, $rules, $messages);
        if ($validator->fails()) {
            return redirect($redirect_url)->withErrors($validator->errors())->withInput();
        }

        $addresses = [];
        foreach ($form_data['prices'] as $k=>$v) {
            $v = abs($v);
            $v = round($v, 2);
            $form_data['prices'][$k] = $v;
            if (empty($v)) {
                unset($form_data['prices'][$k]);
            } else {
                if (empty($form_data['addresses'][$k])) {
                    unset($form_data['prices'][$k]);
                } else {
                    $_exists_addr = [];
                    foreach ($form_data['addresses'][$k] as $type => $addr_list) {
                        $tmp_addr_list = array_map('trim', $addr_list);
                        if (count($addr_list) == 0 or implode('', $tmp_addr_list) == '') {
                            unset($form_data['addresses'][$k][$type]);
                        } else {
                            foreach ($tmp_addr_list as $key => $addr) {
//                                if (empty($addr)) {
//                                    unset($form_data['addresses'][$k][$type][$key]);
//                                } else {
//                                    $addresses[$v][] = $addr;
//                                    $form_data['addresses'][$k][$type][$key] = $addr;
//                                }
                                if (!in_array($type . $addr, $_exists_addr)) {
                                    $_exists_addr[] = $type . $addr;
                                    if (empty($addr)) {
                                        unset($form_data['addresses'][$k][$type][$key]);
                                    } else {
                                        $addresses[$v][] = $addr;
                                        $form_data['addresses'][$k][$type][$key] = $addr;
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }

        if (empty($form_data['prices'])) {
            return redirect($redirect_url)->withErrors(['prices' => ['至少需要一个有效报价']])->withInput();
        }

        if (count($addresses) == 0) {
            return redirect($redirect_url)->withErrors(['prices' => ['至少需要一个有效地址']])->withInput();
        }

        $form_data['prices'] = $form_data['prices'];
        $form_data['addresses'] = $form_data['addresses'];
        $form_data['prices_with_addresses'] = $addresses;

        if ($form_data['payment'] != '先款后货') {
            //$form_data['payment'] = str_replace('x', (string)intval($form_data['payment_day']), '货到x天后付款');
            $form_data['payment'] = (string)intval($form_data['payment_day']);
        }

        if ($form_data['quantity_type'] == 'infinite') {
            $form_data['quantity'] = -1;
        }
        unset($form_data['quantity_type']);

        unset($form_data['payment_day']);
//        $companies = [];
//        $delivery_modes = [];
//        foreach ($form_data['companies'] as $item) {
//            list($mode, $company_id) = explode(',', $item);
//            $companies[] = $company_id;
//            $delivery_modes[] = $item;
//        }
//        $form_data['companies'] = json_encode($companies);
//        $form_data['delivery_modes'] = json_encode($delivery_modes);

        if ($form_data['bargaining'] == 'true') {
            $form_data['bargaining'] = true;
        } else {
            $form_data['bargaining'] = false;
        }

        foreach ($form_data as $field => $value) {
            $offer_information->$field = $value;
        }

        if ($offer_information->save()) {
            return redirect($redirect_url)->with('tip_message', ['content' => '编辑成功', 'state' => 'success']);
        } else {
            return redirect($redirect_url)->withInput()->withErrors(['form' => ['服务器繁忙，请稍后再试']]);
        }
    }

    public function publish(Request $request, OfferInformation $offer_information)
    {
        //$redirect_url = route('offer.information.list', ['offer_basket' => $offer_information->offer_basket->id]);
        $redirect_url = URL::previous();
        $current_user = auth()->user();
        $type = $request->get('type', '');
        $date = date('Y-m-d');

        if ($type != 'all') {

            if ($offer_information->state == 'published') {
                return redirect($redirect_url)->with('tip_message', ['content' => '已发布，不能重复发布', 'state' => 'warning']);
            }

            $offer_information->state = 'published';
            $offer_information->save();
        } else {
            OfferInformation::where('offer_basket_id', $offer_information->offer_basket->id)
                ->where('supplier_id', $current_user->id)
                ->update([
                    'state' => 'published',
                    'updated_at' => $date
                ]);
        }

        return redirect($redirect_url)->with('tip_message', ['content' => '发布成功', 'state' => 'success', 'hold' => true]);
    }

    public function delete(Request $request, OfferInformation $offer_information)
    {
        //$redirect_url = route('offer.information.list', ['offer_basket' => $offer_information->offer_basket_id]);
        $redirect_url = URL::previous();
        /*if ($offer_information->state == 'published') {
            return redirect($redirect_url)->with('tip_message', ['content' => '清单已发布，不能删除', 'state' => 'warning']);
        }*/
        $offer_information->delete();
        return redirect($redirect_url)->with('tip_message', ['content' => '删除成功', 'state' => 'success', 'hold' => true]);
    }

    public function view(Request $request, OfferInformation $offer_information)
    {
        return view('offer.information.view')
            ->with('basket', $offer_information->offer_basket)
            ->with('offer_information', $offer_information);
    }
}
