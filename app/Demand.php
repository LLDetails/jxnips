<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Demand
 *
 * @property integer $id
 * @property integer $basket_id
 * @property integer $user_id
 * @property integer $company_id
 * @property integer $goods_id
 * @property float $quantity
 * @property string $price_validity
 * @property float $price_floor
 * @property float $price_caps
 * @property string $delivery_date_start
 * @property string $delivery_date_stop
 * @property string $assign_rule
 * @property string $history
 * @property string $state
 * @property string $states
 * @property integer $trigger
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property string $goods_static
 * @property float $stock
 * @property float $pending
 * @property float $monthly_demand
 * @property integer $bid_id
 * @property string $assign_result
 * @property-read User $user
 * @property-read Company $company
 * @property-read Basket $basket
 * @property-read Goods $goods
 * @property-read Offer $offer
 * @method static \Illuminate\Database\Query\Builder|\App\Demand whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Demand whereBasketId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Demand whereUserId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Demand whereCompanyId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Demand whereGoodsId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Demand whereQuantity($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Demand wherePriceValidity($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Demand wherePriceFloor($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Demand wherePriceCaps($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Demand whereDeliveryDateStart($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Demand whereDeliveryDateStop($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Demand whereAssignRule($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Demand whereHistory($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Demand whereState($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Demand whereStates($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Demand whereTrigger($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Demand whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Demand whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Demand whereGoodsStatic($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Demand whereStock($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Demand wherePending($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Demand whereMonthlyDemand($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Demand whereBidId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Demand whereAssignResult($value)
 * @property-read Bid $bid
 * @property string $invoice
 * @property string $payment
 * @method static \Illuminate\Database\Query\Builder|\App\Demand whereInvoice($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Demand wherePayment($value)
 * @property integer $tmp_data_user_id
 * @property string $tmp_data
 * @method static \Illuminate\Database\Query\Builder|\App\Demand whereTmpDataUserId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Demand whereTmpData($value)
 * @property integer $category_id
 * @property-read Category $category
 * @method static \Illuminate\Database\Query\Builder|\App\Demand whereCategoryId($value)
 * @property boolean $is_cancel
 * @method static \Illuminate\Database\Query\Builder|\App\Demand whereIsCancel($value)
 */
class Demand extends Model
{
    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id');
    }

    public function basket()
    {
        return $this->belongsTo(Basket::class, 'basket_id');
    }

    public function goods()
    {
        return $this->belongsTo(Goods::class, 'goods_id');
    }

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function offer()
    {
        return $this->hasOne(Offer::class, 'demand_id');
    }

    public function bid()
    {
        return $this->belongsTo(Bid::class, 'bid_id');
    }
}
