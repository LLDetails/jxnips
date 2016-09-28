<?php

namespace App\Http\Controllers\Offer;

use App;
use App\OfferBasket;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class CompanyInformationController extends Controller
{
    public function index(Request $request)
    {
        $cond = $request->only(['date_start', 'date_stop']);
        $cond = array_map('trim', $cond);

        $company_id = auth()->user()->company_id;
        if (empty($company_id)) {
            App::abort(403, '不属于任何公司');
        }

        $offer_baskets = OfferBasket::with('information')->orderBy('name', 'desc');
        $offer_baskets = $offer_baskets->whereHas('information', function($query) {
            return $query;
        });

        if (!empty($cond['date_start'])) {
            $offer_baskets = $offer_baskets->where('name', '>=', $cond['date_start']);
        }
        if (!empty($cond['date_stop'])) {
            $offer_baskets = $offer_baskets->where('name', '<=', $cond['date_stop']);
        }

        $offer_baskets = $offer_baskets->paginate(10);
        $pages = $offer_baskets->appends($cond)->render();

        return view('offer.information.company.index')
            ->with('offer_baskets', $offer_baskets)
            ->with('pages', $pages);
    }

    public function informationList(Request $request, OfferBasket $offer_basket)
    {
        $order_by = $request->get('order_by', 'goods');
        $information = $offer_basket
            ->information()
            ->with('supplier.supplier');
        if ($order_by == 'goods') {
            $information = $information->orderBy('goods_id');
        } else {
            $information = $information->orderBy('supplier_id');
        }
        $information = $information->get();
        if ($order_by == 'goods') {
            return view('offer.information.company.list2')
                ->with('offer_basket', $offer_basket)
                ->with('information', $information);
        } else {
            return view('offer.information.company.list')
                ->with('offer_basket', $offer_basket)
                ->with('information', $information);
        }
    }
}
