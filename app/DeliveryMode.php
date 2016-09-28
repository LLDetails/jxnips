<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\DeliveryMode
 *
 * @property integer $id
 * @property integer $company_id
 * @property string $mode
 * @property float $costs
 * @property string $deleted_at
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property-read App\Company $company
 * @method static \Illuminate\Database\Query\Builder|\App\DeliveryMode whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\DeliveryMode whereCompanyId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\DeliveryMode whereMode($value)
 * @method static \Illuminate\Database\Query\Builder|\App\DeliveryMode whereCosts($value)
 * @method static \Illuminate\Database\Query\Builder|\App\DeliveryMode whereDeletedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\DeliveryMode whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\DeliveryMode whereUpdatedAt($value)
 */
class DeliveryMode extends Model
{
    protected $guarded = [];

    public function company()
    {
        return $this->belongsTo(\App\Company::class, 'company_id');
    }
}
