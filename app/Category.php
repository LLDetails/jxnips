<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Category
 *
 * @property integer $id
 * @property string $name
 * @property string $code
 * @property string $addition
 * @property string $deal_addition
 * @property boolean $is_available
 * @property integer $display_order
 * @property string $deleted_at
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|Goods[] $goods_records
 * @method static \Illuminate\Database\Query\Builder|\App\Category whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Category whereName($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Category whereCode($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Category whereAddition($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Category whereDealAddition($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Category whereIsAvailable($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Category whereDisplayOrder($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Category whereDeletedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Category whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Category whereUpdatedAt($value)
 */
class Category extends Model
{
    protected $guarded = [];

    public function goods_records()
    {
        return $this->hasMany(Goods::class, 'category_id');
    }
}
