<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\CheckFlowSaveLog
 *
 * @property integer $id
 * @property integer $user_id
 * @property string $ip
 * @property string $data
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property-read User $user
 * @method static \Illuminate\Database\Query\Builder|\App\CheckFlowSaveLog whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\CheckFlowSaveLog whereUserId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\CheckFlowSaveLog whereIp($value)
 * @method static \Illuminate\Database\Query\Builder|\App\CheckFlowSaveLog whereData($value)
 * @method static \Illuminate\Database\Query\Builder|\App\CheckFlowSaveLog whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\CheckFlowSaveLog whereUpdatedAt($value)
 */
class CheckFlowSaveLog extends Model
{
    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
