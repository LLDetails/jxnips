<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Basket
 *
 * @property integer $id
 * @property string $name
 * @property string $state
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property string $bided_at
 * @property-read \Illuminate\Database\Eloquent\Collection|Demand[] $demands
 * @property-read \Illuminate\Database\Eloquent\Collection|BasketLog[] $logs
 * @property-read \Illuminate\Database\Eloquent\Collection|BasketCheckLog[] $check_logs
 * @method static \Illuminate\Database\Query\Builder|\App\Basket whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Basket whereName($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Basket whereState($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Basket whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Basket whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Basket whereBidedAt($value)
 * @property string $collected_at
 * @method static \Illuminate\Database\Query\Builder|\App\Basket whereCollectedAt($value)
 */
class Basket extends Model
{
    protected $guarded = [];

    public function demands()
    {
        return $this->hasMany(Demand::class, 'basket_id');
    }

    public function logs()
    {
        return $this->hasMany(BasketLog::class, 'basket_id');
    }

    public function check_logs()
    {
        return $this->hasMany(BasketCheckLog::class, 'basket_id');
    }
}
