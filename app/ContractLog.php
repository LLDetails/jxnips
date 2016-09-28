<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\ContractLog
 *
 * @property integer $id
 * @property integer $contract_id
 * @property integer $user_id
 * @property string $action
 * @property string $remark
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property-read Contract $contract
 * @property-read User $user
 * @method static \Illuminate\Database\Query\Builder|\App\ContractLog whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\ContractLog whereContractId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\ContractLog whereUserId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\ContractLog whereAction($value)
 * @method static \Illuminate\Database\Query\Builder|\App\ContractLog whereRemark($value)
 * @method static \Illuminate\Database\Query\Builder|\App\ContractLog whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\ContractLog whereUpdatedAt($value)
 */
class ContractLog extends Model
{
    protected $guarded = [];

    public function contract()
    {
        return $this->belongsTo(Contract::class, 'contract_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
