<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Feature
 *
 * @property integer $id
 * @property string $name
 * @property string $route
 * @property string $group
 * @property integer $display_order
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @method static \Illuminate\Database\Query\Builder|\App\Feature whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Feature whereName($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Feature whereRoute($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Feature whereGroup($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Feature whereDisplayOrder($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Feature whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Feature whereUpdatedAt($value)
 */
class Feature extends Model
{
    protected $guarded = [];
}
