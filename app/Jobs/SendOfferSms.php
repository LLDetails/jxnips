<?php

namespace App\Jobs;

use App\Basket;
use App\Jobs\Job;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Bus\SelfHandling;
use Illuminate\Contracts\Queue\ShouldQueue;

use HSms;

class SendOfferSms extends Job implements SelfHandling, ShouldQueue
{
    use InteractsWithQueue, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public $bided_at;
    public $basket_name;
    public $phone;
    public $template_id;
    public $params;
    public function __construct($bided_at, $basket_name, $phone, $template_name, $params)
    {
        $this->bided_at = $bided_at;
        $this->basket_name = $basket_name;
        $this->phone = $phone;
        $this->params = $params;
        $templates = config('hsms.templates');
        $this->template_id = $templates[$template_name];
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $basket = Basket::where('name', $this->basket_name)->first();
        $datetime = date('Y-m-d H:i:s');
        if (!empty($basket) and !empty($basket->bided_at) and $basket->state != 'refused' and $datetime >= $basket->bided_at) {
            $result = HSms::send($this->template_id, $this->phone, $this->params);
            //$result = Ucp::sendCode($this->phone, $this->message);
            //$ucpaas_appid = config('ucpaas.appid');
            //$result = Ucpaas::templateSMS($ucpaas_appid, $this->phone, $this->template_id, $this->params);
        }
    }
}
