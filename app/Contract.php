<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Contract
 *
 * @property integer $id
 * @property integer $offer_id
 * @property string $title
 * @property string $code
 * @property string $addition
 * @property string $state
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property-read Offer $offer
 * @property-read ContractGrade $grade
 * @property-read \Illuminate\Database\Eloquent\Collection|ContractLog[] $logs
 * @method static \Illuminate\Database\Query\Builder|\App\Contract whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Contract whereOfferId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Contract whereTitle($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Contract whereCode($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Contract whereAddition($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Contract whereState($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Contract whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Contract whereUpdatedAt($value)
 * @property string $suggestion
 * @method static \Illuminate\Database\Query\Builder|\App\Contract whereSuggestion($value)
 * @property string $attachment
 * @method static \Illuminate\Database\Query\Builder|\App\Contract whereAttachment($value)
 * @property boolean $offline
 * @property boolean $attachment_lock
 * @property string $confirmed_at
 * @property string $finished_at
 * @method static \Illuminate\Database\Query\Builder|\App\Contract whereOffline($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Contract whereAttachmentLock($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Contract whereConfirmedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Contract whereFinishedAt($value)
 */
class Contract extends Model
{
    protected $guarded = [];

    public function offer()
    {
        return $this->belongsTo(Offer::class, 'offer_id');
    }

    public function grade()
    {
        return $this->hasOne(ContractGrade::class, 'contract_id');
    }

    public function logs()
    {
        return $this->hasMany(ContractLog::class, 'contract_id');
    }
}
