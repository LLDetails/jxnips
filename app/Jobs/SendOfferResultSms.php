<?php

namespace App\Jobs;

use App\Bid;
use App\Jobs\Job;
use App\Offer;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Bus\SelfHandling;
use Illuminate\Contracts\Queue\ShouldQueue;
use HSms;

class SendOfferResultSms extends Job implements SelfHandling, ShouldQueue
{
    use InteractsWithQueue, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public $bided_at;
    public $bid_id;
    public $offer_stop;
    public function __construct($bided_at, $bid_id, $offer_stop)
    {
        $this->bided_at = $bided_at;
        $this->bid_id = $bid_id;
        $this->offer_stop = $offer_stop;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $bid = Bid::with('basket')->find($this->bid_id);
        if (empty($bid) or $bid->offer_stop != $this->offer_stop) {
            return;
        }
        if ($bid->basket->state == 'refused' or $bid->basket->bided_at != $this->bided_at) {
            return;
        }

        //$ucpaas_appid = config('ucpaas.appid');
        $templates = config('hsms.templates');
        $template_id = '';
        $params = '';

        $offers = Offer::with(['supplier', 'demand', 'demand.user'])
            ->where('bid_id', $this->bid_id)
            ->whereNull('reason')
            ->get();
        $goods = json_decode($bid->goods_static, true);
        $goods_name = $goods['name'];
        //$goods_name = preg_replace('#(\d+)%#', "百分之$1", $goods_name);
        $message_date = date('m月d日', strtotime($bid->basket->name));
        foreach ($offers as $k=>$offer) {
            if (!empty($offer->supplier->phone)) {
                $offer_at = date('m月d日 H点i分', strtotime($offer->updated_at));
                if(!$offer->demand->is_cancel and $offer->quantity > 0) {

                    if (!empty($offer->demand->user->phone) and $offer->demand->user->allow_login) {
                        //通知：您于【月日--—--】【物料名称】的招标已完成，请登录平台查看并线上生成采购合同。
                        //$staff_message = '通知：您于' . $message_date . $goods_name . '的招标已完成，请登录平台查看并线上生成采购合同。';
                        //Sms::sendCode($offer->demand->user->phone, $staff_message);
                        $staff_template_id = $templates['投标完成提醒'];
                        $staff_params = json_encode(['bid' => $message_date . $goods_name]);
                        HSms::send($staff_template_id, $offer->demand->user->phone, $staff_params);
                        //Ucpaas::templateSMS($ucpaas_appid, $offer->demand->user->phone, $staff_template_id, $staff_params);
                    }
                    $template_id = $templates['中标提醒'];
                    $params = json_encode(['offer' => $offer_at.$goods_name]);
                    //$message = '通知：恭喜您于'.$offer_at.$goods_name.'的报价已中标，请登录平台查看详情。';
                } else {
                    if ($offer->demand->is_cancel) {
                        if (!empty($offer->demand->user->phone) and $offer->demand->user->allow_login) {
                            //通知：您于【月日--—--】【物料名称】的招标计划按交易规则流标，请登录平台查看详情。
                            //$message_date = date('m月d日', strtotime($bid->basket->name));
                            //$staff_message = '通知：您于' . $message_date . $goods_name . '的招标计划按交易规则流标，请登录平台查看详情。';
                            $staff_template_id = $templates['交易流标'];
                            $staff_params = json_encode(['demand' => $message_date . $goods_name]);
                            HSms::send($staff_template_id, $offer->demand->user->phone, $staff_params);
                            //Ucpaas::templateSMS($ucpaas_appid, $offer->demand->user->phone, $staff_template_id, $staff_params);
                            //Sms::sendCode($offer->demand->user->phone, $staff_message);
                        }
                        $template_id = $templates['流标提醒'];
                        $params = json_encode(['offer' => $offer_at.$goods_name]);
                        //$message = '通知：您于'.$offer_at.$goods_name.'的报价按交易规则流标，请登录平台查看详情，感谢您的参与。';
                    }
                    if (!$offer->demand->is_cancel and $offer->quantity == 0) {
                        //$message = '通知：您于'.$offer_at.$goods_name.'的报价未中标，请登录平台查看详情，感谢您的参与。';
                        $template_id = $templates['未中标提醒'];
                        $params = json_encode(['offer' => $offer_at.$goods_name]);
                    }
                }
                if (!empty($template_id) and $offer->supplier->allow_login) {
                    //$result = Sms::sendCode($offer->supplier->phone, $message);
                    $result = HSms::send($template_id, $offer->supplier->phone, $params);
                    //$result = Ucpaas::templateSMS($ucpaas_appid, $offer->supplier->phone, $template_id, $params);
                }
            }
        }

    }
}
