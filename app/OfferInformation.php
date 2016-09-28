<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\OfferInformation
 *
 * @property integer $id
 * @property integer $goods_id
 * @property string $quality_standard
 * @property float $quantity
 * @property float $price
 * @property string $companies
 * @property string $delivery_modes
 * @property string $payment
 * @property string $price_validity
 * @property string $delivery_start
 * @property string $delivery_stop
 * @property boolean $bargaining
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @method static \Illuminate\Database\Query\Builder|\App\OfferInformation whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\OfferInformation whereGoodsId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\OfferInformation whereQualityStandard($value)
 * @method static \Illuminate\Database\Query\Builder|\App\OfferInformation whereQuantity($value)
 * @method static \Illuminate\Database\Query\Builder|\App\OfferInformation wherePrice($value)
 * @method static \Illuminate\Database\Query\Builder|\App\OfferInformation whereCompanies($value)
 * @method static \Illuminate\Database\Query\Builder|\App\OfferInformation whereDeliveryModes($value)
 * @method static \Illuminate\Database\Query\Builder|\App\OfferInformation wherePayment($value)
 * @method static \Illuminate\Database\Query\Builder|\App\OfferInformation wherePriceValidity($value)
 * @method static \Illuminate\Database\Query\Builder|\App\OfferInformation whereDeliveryStart($value)
 * @method static \Illuminate\Database\Query\Builder|\App\OfferInformation whereDeliveryStop($value)
 * @method static \Illuminate\Database\Query\Builder|\App\OfferInformation whereBargaining($value)
 * @method static \Illuminate\Database\Query\Builder|\App\OfferInformation whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\OfferInformation whereUpdatedAt($value)
 * @property integer $supplier_id
 * @property string $deleted_at
 * @method static \Illuminate\Database\Query\Builder|\App\OfferInformation whereSupplierId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\OfferInformation whereDeletedAt($value)
 * @property-read User $supplier
 * @property-read Goods $goods
 * @property integer $offer_basket_id
 * @property-read OfferBasket $offer_basket
 * @method static \Illuminate\Database\Query\Builder|\App\OfferInformation whereOfferBasketId($value)
 * @property string $state
 * @method static \Illuminate\Database\Query\Builder|\App\OfferInformation whereState($value)
 * @property string $prices
 * @property string $addresses
 * @property string $prices_with_addresses
 * @method static \Illuminate\Database\Query\Builder|\App\OfferInformation wherePrices($value)
 * @method static \Illuminate\Database\Query\Builder|\App\OfferInformation whereAddresses($value)
 * @method static \Illuminate\Database\Query\Builder|\App\OfferInformation wherePricesWithAddresses($value)
 */
class OfferInformation extends Model
{
    protected $table = 'offer_information';

    protected $guarded = [];

    protected $casts = [
        'prices' => 'array',
        'addresses' => 'array',
        'prices_with_addresses' => 'array'
    ];

    public function offer_basket()
    {
        return $this->belongsTo(OfferBasket::class, 'offer_basket_id');
    }

    public function supplier()
    {
        return $this->belongsTo(User::class, 'supplier_id');
    }

    public function goods()
    {
        return $this->belongsTo(Goods::class, 'goods_id');
    }
}
