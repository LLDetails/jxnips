<?php

namespace App\Http\Controllers\Contract;

use App\Contract;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
//use TCPdf;
use Elibyy\TCPDF\Facades\TCPdf;
//use \TCPDF_FONTS;

class ContractController extends Controller
{
    private function get_amount($num){
        $c1 = "零壹贰叁肆伍陆柒捌玖";
        $c2 = "分角元拾佰仟万拾佰仟亿";
        $num = round($num, 2);
        $num = $num * 100;
        if (strlen($num) > 10) {
            return "数据太长，没有这么大的钱吧，检查下";
        }
        $i = 0;
        $c = "";
        while (1) {
            if ($i == 0) {
                $n = substr($num, strlen($num)-1, 1);
            } else {
                $n = $num % 10;
            }
            $p1 = substr($c1, 3 * $n, 3);
            $p2 = substr($c2, 3 * $i, 3);
            if ($n != '0' || ($n == '0' && ($p2 == '亿' || $p2 == '万' || $p2 == '元'))) {
                $c = $p1 . $p2 . $c;
            } else {
                $c = $p1 . $c;
            }
            $i = $i + 1;
            $num = $num / 10;
            $num = (int)$num;
            if ($num == 0) {
                break;
            }
        }
        $j = 0;
        $slen = strlen($c);
        while ($j < $slen) {
            $m = substr($c, $j, 6);
            if ($m == '零元' || $m == '零万' || $m == '零亿' || $m == '零零') {
                $left = substr($c, 0, $j);
                $right = substr($c, $j + 3);
                $c = $left . $right;
                $j = $j-3;
                $slen = $slen-3;
            }
            $j = $j + 3;
        }

        if (substr($c, strlen($c)-3, 3) == '零') {
            $c = substr($c, 0, strlen($c)-3);
        }
        if (empty($c)) {
            return "零元整";
        }else{
            return $c . "整";
        }
    }

    public function download(Contract $contract)
    {
        $data = json_decode($contract->addition, true);
        $goods = json_decode($contract->offer->demand->goods_static);
        if (auth()->user()->type == 'supplier' and auth()->user()->id != $contract->offer->user_id) {
            App::abort(403, '无权下载此合同');
        }

        if (auth()->user()->type == 'staff') {
            $company = auth()->user()->company;
            if ( ! empty($company) and $company->id != $contract->offer->demand->company_id) {
                App::abort(403, '无权下载此合同');
            }
        }

        $upper_total_price = $this->get_amount(($contract->offer->price + $contract->offer->delivery_costs) * $contract->offer->quantity);

        $view = view('contract.download')
            ->with('contract', $contract)
            ->with('goods', $goods)
            ->with('upper_total_price', $upper_total_price)
            ->with('data', $data);
        $view = (string) $view;
        //exit($view);
        TCPdf::setPrintHeader(false);
        TCPdf::setPrintFooter(false);
        TCPdf::SetTitle($contract->title);
        TCPdf::SetMargins( 20, 18, 20, true );
        $fontname = \TCPDF_FONTS::addTTFfont(base_path('storage/fonts/simfang.ttf'), 'simfang', '', 32);
        //TCPdf::SetFont('stsongstdlight', '', 12);
        //echo base_path('public/fonts/simfang.ttf');exit;
        TCPdf::SetFont($fontname, '', 10);
        TCPdf::AddPage();
        //PDF::Write(0, 'Hello World');
        TCPdf::writeHTML($view, true, false, true, false, '');
        TCPdf::lastPage();
        $fname = $contract->title.'-'.$contract->code.'.pdf';
        if (preg_match( '/MSIE/i', $_SERVER['HTTP_USER_AGENT'])) {
            $fname = mb_convert_encoding($fname, 'GBK', 'UTF-8');
        }
        $pdf = TCPdf::Output($contract->title.'-'.$contract->code.'.pdf', 'S');
        header("Content-type: application/pdf");
        header("Accept-Ranges: bytes");
        header("Accept-Length: ".strlen($pdf));
        header("Content-Disposition: attachment; filename=" . $fname);
        exit($pdf);
    }
}
