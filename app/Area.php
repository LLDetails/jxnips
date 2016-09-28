<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Area
 *
 * @property integer $id
 * @property string $name
 * @property integer $display_order
 * @property string $deleted_at
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|Company[] $companies
 * @property-read \Illuminate\Database\Eloquent\Collection|Supplier[] $suppliers
 * @method static \Illuminate\Database\Query\Builder|\App\Area whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Area whereName($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Area whereDisplayOrder($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Area whereDeletedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Area whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Area whereUpdatedAt($value)
 */
class Area extends Model
{
    protected $guarded = [];

    public function companies()
    {
        return $this->hasMany(Company::class, 'area_id');
    }

    public function suppliers()
    {
        return $this->hasMany(Supplier::class, 'area_id');
    }
}
