<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Goods
 *
 * @property integer $id
 * @property integer $category_id
 * @property string $code
 * @property string $name
 * @property string $unit
 * @property string $quality_standard
 * @property string $addition
 * @property boolean $is_available
 * @property string $deleted_at
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property-read Category $category
 * @method static \Illuminate\Database\Query\Builder|\App\Goods whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Goods whereCategoryId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Goods whereCode($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Goods whereName($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Goods whereUnit($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Goods whereQualityStandard($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Goods whereAddition($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Goods whereIsAvailable($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Goods whereDeletedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Goods whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Goods whereUpdatedAt($value)
 * @property string $price_validity
 * @method static \Illuminate\Database\Query\Builder|\App\Goods wherePriceValidity($value)
 */
class Goods extends Model
{
    protected $guarded = [];

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }
}
