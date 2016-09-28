<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\AssignRule
 *
 * @property integer $id
 * @property string $name
 * @property string $rules
 * @property string $deleted_at
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @method static \Illuminate\Database\Query\Builder|\App\AssignRule whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\AssignRule whereName($value)
 * @method static \Illuminate\Database\Query\Builder|\App\AssignRule whereRules($value)
 * @method static \Illuminate\Database\Query\Builder|\App\AssignRule whereDeletedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\AssignRule whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\AssignRule whereUpdatedAt($value)
 */
class AssignRule extends Model
{
    protected $guarded = [];
}
