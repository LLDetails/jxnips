<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Supplier
 *
 * @property integer $id
 * @property integer $user_id
 * @property integer $area_id
 * @property string $name
 * @property string $goods
 * @property string $type
 * @property string $address
 * @property string $zipcode
 * @property string $tel
 * @property string $fax
 * @property string $email
 * @property string $website
 * @property string $business_license
 * @property string $organization_code
 * @property string $tax_id
 * @property float $registered_capital
 * @property string $company_scale
 * @property string $id_number
 * @property string $contact
 * @property string $bank
 * @property string $bank_account
 * @property string $grade
 * @property string $addition
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property-read User $user
 * @property-read Area $area
 * @method static \Illuminate\Database\Query\Builder|\App\Supplier whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Supplier whereUserId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Supplier whereAreaId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Supplier whereName($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Supplier whereGoods($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Supplier whereType($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Supplier whereAddress($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Supplier whereZipcode($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Supplier whereTel($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Supplier whereFax($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Supplier whereEmail($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Supplier whereWebsite($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Supplier whereBusinessLicense($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Supplier whereOrganizationCode($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Supplier whereTaxId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Supplier whereRegisteredCapital($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Supplier whereCompanyScale($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Supplier whereIdNumber($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Supplier whereContact($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Supplier whereBank($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Supplier whereBankAccount($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Supplier whereGrade($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Supplier whereAddition($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Supplier whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Supplier whereUpdatedAt($value)
 */
class Supplier extends Model
{
    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function area()
    {
        return $this->belongsTo(Area::class, 'area_id');
    }
}
