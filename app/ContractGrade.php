<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\ContractGrade
 *
 * @property integer $id
 * @property integer $contract_id
 * @property string $company_grade
 * @property string $company_graded_at
 * @property string $supplier_grade
 * @property string $supplier_graded_at
 * @property string $company_remark
 * @property string $supplier_remark
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property-read Contract $contract
 * @method static \Illuminate\Database\Query\Builder|\App\ContractGrade whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\ContractGrade whereContractId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\ContractGrade whereCompanyGrade($value)
 * @method static \Illuminate\Database\Query\Builder|\App\ContractGrade whereCompanyGradedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\ContractGrade whereSupplierGrade($value)
 * @method static \Illuminate\Database\Query\Builder|\App\ContractGrade whereSupplierGradedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\ContractGrade whereCompanyRemark($value)
 * @method static \Illuminate\Database\Query\Builder|\App\ContractGrade whereSupplierRemark($value)
 * @method static \Illuminate\Database\Query\Builder|\App\ContractGrade whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\ContractGrade whereUpdatedAt($value)
 * @property integer $company_grade_1
 * @property integer $company_grade_2
 * @property integer $supplier_grade_1
 * @property integer $supplier_grade_2
 * @property integer $supplier_grade_3
 * @method static \Illuminate\Database\Query\Builder|\App\ContractGrade whereCompanyGrade1($value)
 * @method static \Illuminate\Database\Query\Builder|\App\ContractGrade whereCompanyGrade2($value)
 * @method static \Illuminate\Database\Query\Builder|\App\ContractGrade whereSupplierGrade1($value)
 * @method static \Illuminate\Database\Query\Builder|\App\ContractGrade whereSupplierGrade2($value)
 * @method static \Illuminate\Database\Query\Builder|\App\ContractGrade whereSupplierGrade3($value)
 */
class ContractGrade extends Model
{
    protected $guarded = [];

    public function contract()
    {
        return $this->belongsTo(Contract::class, 'contract_id');
    }
}
