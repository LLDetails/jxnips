<?php

namespace App;

use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;

/**
 * App\User
 *
 * @property integer $id
 * @property string $username
 * @property string $password
 * @property string $phone
 * @property integer $role_id
 * @property integer $area_id
 * @property integer $company_id
 * @property string $type
 * @property boolean $allow_login
 * @property boolean $accept_agreement
 * @property string $remember_token
 * @property string $deleted_at
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property-read Role $role
 * @property-read Area $area
 * @property-read App\Company $company
 * @property-read Staff $staff
 * @property-read Supplier $supplier
 * @method static \Illuminate\Database\Query\Builder|\App\User whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\User whereUsername($value)
 * @method static \Illuminate\Database\Query\Builder|\App\User wherePassword($value)
 * @method static \Illuminate\Database\Query\Builder|\App\User wherePhone($value)
 * @method static \Illuminate\Database\Query\Builder|\App\User whereRoleId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\User whereAreaId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\User whereCompanyId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\User whereType($value)
 * @method static \Illuminate\Database\Query\Builder|\App\User whereAllowLogin($value)
 * @method static \Illuminate\Database\Query\Builder|\App\User whereAcceptAgreement($value)
 * @method static \Illuminate\Database\Query\Builder|\App\User whereRememberToken($value)
 * @method static \Illuminate\Database\Query\Builder|\App\User whereDeletedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\User whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\User whereUpdatedAt($value)
 * @property integer $category_id
 * @method static \Illuminate\Database\Query\Builder|\App\User whereCategoryId($value)
 * @property-read App\Category $category
 */
class User extends Model implements AuthenticatableContract
{
    use Authenticatable;

    protected $guarded = [];
    protected $hidden = ['password', 'remember_token'];

    public function role()
    {
        return $this->belongsTo(Role::class, 'role_id');
    }

    public function area()
    {
        return $this->belongsTo(Area::class, 'area_id');
    }

    public function company()
    {
        return $this->belongsTo(\App\Company::class, 'company_id');
    }

    public function category()
    {
        return $this->belongsTo(\App\Category::class, 'category_id');
    }

    public function staff()
    {
        return $this->hasOne(Staff::class, 'user_id');
    }

    public function supplier()
    {
        return $this->hasOne(Supplier::class, 'user_id');
    }
}
