<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\BasketLog
 *
 * @property integer $id
 * @property integer $basket_id
 * @property string $action
 * @property integer $user_id
 * @property string $remark
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property integer $role_id
 * @property-read Basket $basket
 * @property-read User $user
 * @property-read Role $role
 * @method static \Illuminate\Database\Query\Builder|\App\BasketLog whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\BasketLog whereBasketId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\BasketLog whereAction($value)
 * @method static \Illuminate\Database\Query\Builder|\App\BasketLog whereUserId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\BasketLog whereRemark($value)
 * @method static \Illuminate\Database\Query\Builder|\App\BasketLog whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\BasketLog whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\BasketLog whereRoleId($value)
 * @property string $bided_at
 * @method static \Illuminate\Database\Query\Builder|\App\BasketLog whereBidedAt($value)
 * @property string $collected_at
 * @method static \Illuminate\Database\Query\Builder|\App\BasketLog whereCollectedAt($value)
 */
class BasketLog extends Model
{
    protected $guarded = [];

    public function basket()
    {
        return $this->belongsTo(Basket::class, 'basket_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function role()
    {
        return $this->belongsTo(Role::class, 'role_id');
    }
}
