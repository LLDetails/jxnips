<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Bid
 *
 * @property integer $id
 * @property string $code
 * @property integer $goods_id
 * @property integer $user_id
 * @property string $offer_start
 * @property string $offer_stop
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property string $goods_static
 * @property string $type
 * @property string $suppliers
 * @property-read User $user
 * @property-read Goods $goods
 * @property-read \Illuminate\Database\Eloquent\Collection|Demand[] $demands
 * @method static \Illuminate\Database\Query\Builder|\App\Bid whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Bid whereCode($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Bid whereGoodsId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Bid whereUserId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Bid whereOfferStart($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Bid whereOfferStop($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Bid whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Bid whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Bid whereGoodsStatic($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Bid whereType($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Bid whereSuppliers($value)
 * @property integer $basket_id
 * @method static \Illuminate\Database\Query\Builder|\App\Bid whereBasketId($value)
 * @property-read Basket $basket
 * @property-read \Illuminate\Database\Eloquent\Collection|Offer[] $offers
 * @property boolean $is_cancel
 * @method static \Illuminate\Database\Query\Builder|\App\Bid whereIsCancel($value)
 */
class Bid extends Model
{
    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function goods()
    {
        return $this->belongsTo(Goods::class, 'goods_id');
    }

    public function demands()
    {
        return $this->hasMany(Demand::class, 'bid_id');
    }

    public function basket()
    {
        return $this->belongsTo(Basket::class, 'basket_id');
    }

    public function offers()
    {
        return $this->hasMany(Offer::class, 'bid_id');
    }
}
