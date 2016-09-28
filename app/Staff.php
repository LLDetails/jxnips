<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Staff
 *
 * @property integer $id
 * @property integer $user_id
 * @property string $realname
 * @property string $hiredate
 * @property boolean $is_regular
 * @property string $phone
 * @property string $address
 * @property string $addition
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property-read User $user
 * @method static \Illuminate\Database\Query\Builder|\App\Staff whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Staff whereUserId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Staff whereRealname($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Staff whereHiredate($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Staff whereIsRegular($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Staff wherePhone($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Staff whereAddress($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Staff whereAddition($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Staff whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Staff whereUpdatedAt($value)
 */
class Staff extends Model
{
    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
