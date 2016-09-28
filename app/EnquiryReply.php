<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\EnquiryReply
 *
 * @property integer $id
 * @property integer $enquiry_id
 * @property integer $supplier_id
 * @property float $price
 * @property string $remark
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @method static \Illuminate\Database\Query\Builder|\App\EnquiryReply whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\EnquiryReply whereEnquiryId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\EnquiryReply whereSupplierId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\EnquiryReply wherePrice($value)
 * @method static \Illuminate\Database\Query\Builder|\App\EnquiryReply whereRemark($value)
 * @method static \Illuminate\Database\Query\Builder|\App\EnquiryReply whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\EnquiryReply whereUpdatedAt($value)
 * @property string $quality
 * @property string $payment
 * @property-read Enquiry $enquiry
 * @property-read User $supplier
 * @method static \Illuminate\Database\Query\Builder|\App\EnquiryReply whereQuality($value)
 * @method static \Illuminate\Database\Query\Builder|\App\EnquiryReply wherePayment($value)
 * @property string $price_validity
 * @method static \Illuminate\Database\Query\Builder|\App\EnquiryReply wherePriceValidity($value)
 */
class EnquiryReply extends Model
{
    protected $guarded = [];

    public function enquiry()
    {
    	return $this->belongsTo(Enquiry::class, 'enquiry_id');
    }

    public function supplier()
    {
    	return $this->belongsTo(User::class, 'supplier_id');
    }
}
