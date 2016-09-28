<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Offer
 *
 * @property integer $id
 * @property integer $bid_id
 * @property integer $demand_id
 * @property integer $user_id
 * @property string $delivery_mode
 * @property float $delivery_costs
 * @property float $price
 * @property float $quantity_floor
 * @property float $quantity_caps
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property-read Bid $bid
 * @property-read Demand $demand
 * @property-read Contract $contract
 * @property-read User $supplier
 * @method static \Illuminate\Database\Query\Builder|\App\Offer whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Offer whereBidId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Offer whereDemandId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Offer whereUserId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Offer whereDeliveryMode($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Offer whereDeliveryCosts($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Offer wherePrice($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Offer whereQuantityFloor($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Offer whereQuantityCaps($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Offer whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Offer whereUpdatedAt($value)
 * @property string $assign_quantity
 * @method static \Illuminate\Database\Query\Builder|\App\Offer whereAssignQuantity($value)
 * @property float $quantity
 * @method static \Illuminate\Database\Query\Builder|\App\Offer whereQuantity($value)
 * @property string $generated_at
 * @method static \Illuminate\Database\Query\Builder|\App\Offer whereGeneratedAt($value)
 * @property string $reason
 * @method static \Illuminate\Database\Query\Builder|\App\Offer whereReason($value)
 */
class Offer extends Model
{
    protected $guarded = [];

    public function bid()
    {
        return $this->belongsTo(Bid::class, 'bid_id');
    }

    public function demand()
    {
        return $this->belongsTo(Demand::class, 'demand_id');
    }

    public function contract()
    {
        return $this->hasOne(Contract::class, 'offer_id');
    }

    public function supplier()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
