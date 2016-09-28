<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\SmsTemplate
 *
 * @property integer $id
 * @property string $title
 * @property string $txt
 * @property string $ali_code
 * @property boolean $enable
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @method static \Illuminate\Database\Query\Builder|\App\SmsTemplate whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\SmsTemplate whereTitle($value)
 * @method static \Illuminate\Database\Query\Builder|\App\SmsTemplate whereTxt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\SmsTemplate whereAliCode($value)
 * @method static \Illuminate\Database\Query\Builder|\App\SmsTemplate whereEnable($value)
 * @method static \Illuminate\Database\Query\Builder|\App\SmsTemplate whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\SmsTemplate whereUpdatedAt($value)
 */
class SmsTemplate extends Model
{
    protected $guarded = [];
}
