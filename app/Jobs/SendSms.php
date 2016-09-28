<?php

namespace App\Jobs;

use App\Jobs\Job;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Bus\SelfHandling;
use Illuminate\Contracts\Queue\ShouldQueue;

use HSms;

class SendSms extends Job implements SelfHandling, ShouldQueue
{
    use InteractsWithQueue, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public $phone;
    public $template_id;
    public $params;
    public function __construct($phone, $template_name, $params)
    {
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
        $result = HSms::send($this->template_id, $this->phone, $this->params);
        //$ucpaas_appid = config('ucpaas.appid');
        //$result = Ucpaas::templateSMS($ucpaas_appid, $this->phone, $this->template_id, $this->params);
        //$result = Sms::sendCode($this->phone, $this->message);
        //file_put_contents('/vagrant/xips/storage/')
    }
}
