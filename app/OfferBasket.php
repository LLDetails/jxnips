<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\OfferBasket
 *
 * @property integer $id
 * @property string $name
 * @property string $state
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|OfferInformation[] $information
 * @method static \Illuminate\Database\Query\Builder|\App\OfferBasket whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\OfferBasket whereName($value)
 * @method static \Illuminate\Database\Query\Builder|\App\OfferBasket whereState($value)
 * @method static \Illuminate\Database\Query\Builder|\App\OfferBasket whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\OfferBasket whereUpdatedAt($value)
 */
class OfferBasket extends Model
{
    protected $guarded = [];

    public function information()
    {
        return $this->hasMany(OfferInformation::class, 'offer_basket_id');
    }
}
