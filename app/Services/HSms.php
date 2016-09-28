<?php

namespace App\Services;

class HSms
{
    public function __construct()
    {
    }

    public function send($template_code, $phone, $param, $extend = '')
    {
        $time = time();
        $config = config('hsms');
        $data = ['sms_param' => $param, 'sms_number' => $phone, 'sms_tpl_code' => $template_code, 'sms_sign_time' => $time];
        ksort($data);
        $data = http_build_query($data);
        $sign_key = $config['sign_key'];
        $sign = md5($data . '&' . $sign_key);
        $data .= '&sign='.$sign;
        $opts = [
            'http' => [
                'method'=>"POST",
                'header'=>"Content-type: application/x-www-form-urlencoded\r\n".
                    "Content-length:".strlen($data)."\r\n" .
                    "Cookie: foo=bar\r\n" .
                    "\r\n",
                'content' => $data,
            ]
        ];
        $cxContext = stream_context_create($opts);
        $sFile = file_get_contents($config['gateway'], false, $cxContext);

        $log_content = date('Y-m-d H:i:s')."\r\n".$data."\r\n".$sFile."\r\n\r\n";
        file_put_contents(storage_path('logs').'/hsms_'.date('Y-m-d').'.log', $log_content, FILE_APPEND);
        return json_decode($sFile);
    }
}