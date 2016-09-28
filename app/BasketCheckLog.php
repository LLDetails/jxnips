<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\BasketCheckLog
 *
 * @property integer $id
 * @property integer $basket_id
 * @property integer $user_id
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property-read Basket $basket
 * @property-read User $user
 * @method static \Illuminate\Database\Query\Builder|\App\BasketCheckLog whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\BasketCheckLog whereBasketId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\BasketCheckLog whereUserId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\BasketCheckLog whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\BasketCheckLog whereUpdatedAt($value)
 */
class BasketCheckLog extends Model
{
    public function basket()
    {
        return $this->belongsTo(Basket::class, 'basket_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
