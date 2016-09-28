<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Enquiry
 *
 * @property integer $id
 * @property integer $goods_id
 * @property string $title
 * @property string $remark
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @method static \Illuminate\Database\Query\Builder|\App\Enquiry whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Enquiry whereGoodsId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Enquiry whereTitle($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Enquiry whereRemark($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Enquiry whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Enquiry whereUpdatedAt($value)
 * @property float $quantity
 * @property string $sailing_date
 * @property string $terms_of_delivery
 * @property string $stop_at
 * @property-read \Illuminate\Database\Eloquent\Collection|EnquiryReply[] $replies
 * @method static \Illuminate\Database\Query\Builder|\App\Enquiry whereQuantity($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Enquiry whereSailingDate($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Enquiry whereTermsOfDelivery($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Enquiry whereStopAt($value)
 * @property string $quality
 * @property string $start_at
 * @property-read Goods $goods
 * @method static \Illuminate\Database\Query\Builder|\App\Enquiry whereQuality($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Enquiry whereStartAt($value)
 */
class Enquiry extends Model
{
    protected $guarded = [];

    public function goods()
    {
    	return $this->belongsTo(Goods::class, 'goods_id');
    }

    public function replies()
    {
    	return $this->hasMany(EnquiryReply::class, 'enquiry_id');
    }
}
