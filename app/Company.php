<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Company
 *
 * @property integer $id
 * @property integer $area_id
 * @property string $name
 * @property string $code
 * @property string $delivery_address
 * @property string $addition
 * @property string $grade
 * @property string $deleted_at
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|User[] $users
 * @property-read \Illuminate\Database\Eloquent\Collection|BidRecord[] $bid_records
 * @property-read Area $area
 * @property-read \Illuminate\Database\Eloquent\Collection|DeliveryMode[] $delivery_modes
 * @property-read \Illuminate\Database\Eloquent\Collection|DemandRecord[] $demand_records
 * @method static \Illuminate\Database\Query\Builder|\App\Company whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Company whereAreaId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Company whereName($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Company whereCode($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Company whereDeliveryAddress($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Company whereAddition($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Company whereGrade($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Company whereDeletedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Company whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Company whereUpdatedAt($value)
 * @property string $contract_contact
 * @property string $contract_tel
 * @method static \Illuminate\Database\Query\Builder|\App\Company whereContractContact($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Company whereContractTel($value)
 * @property string $contract_fax
 * @method static \Illuminate\Database\Query\Builder|\App\Company whereContractFax($value)
 */
class Company extends Model
{
    protected $guarded = [];

    public function users()
    {
        return $this->hasMany(User::class, 'company_id');
    }

    public function bid_records()
    {
        return $this->hasMany(BidRecord::class, 'company_id');
    }

    public function area()
    {
        return $this->belongsTo(Area::class, 'area_id');
    }

    public function delivery_modes()
    {
        return $this->hasMany(DeliveryMode::class, 'company_id');
    }

    public function demand_records(){
        return $this->hasMany(DemandRecord::class, 'company_id');
    }
}
