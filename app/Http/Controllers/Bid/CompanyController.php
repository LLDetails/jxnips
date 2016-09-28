<?php

namespace App\Http\Controllers\Bid;

use App\Offer;
use App\Contract;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App;
use URL;
use DB;
use App\Jobs\SendSms;

class CompanyController extends Controller
{
    public function uploadContract(Offer $offer)
    {
        $redirect_url = route('bid.company.index');
        $current_time = time();
        if (Contract::where('offer_id', $offer->id)->exists()) {
            return redirect($redirect_url)->with('tip_message', ['content' => '合同已经存在了', 'state' => 'warning']);
        }

        $goods = json_decode($offer->demand->goods_static);

        $contract_data = [];
        $contract_data['offer_id'] = $offer->id;
        $contract_data['title'] = '购销合同';

        $current_date_start = date('Y-m-d 00:00:00', $current_time);
        $current_date_stop = date('Y-m-d 23:59:59', $current_time);
        $contract_count = Contract::where('created_at', '>=', $current_date_start)
            ->where('created_at', '<=', $current_date_stop)
            ->whereHas('offer', function($query) use($goods) {
                return $query->whereHas('bid', function($query) use($goods) {
                    return $query->where('goods_id', $goods->id);
                });
            })
            ->count();
        if (empty($contract_count)) {
            $contract_count = 1;
        } else {
            $contract_count += 1;
        }

        $contract_data['code'] = 'YCH-'.auth()->user()->company->code.'-'.$goods->code.date('ymd').'C'.sprintf('%04s', $contract_count);
        $contract_data['addition'] = '[]';
        $contract_data['state'] = 'pending';
        $contract_data['offline'] = true;
        $contract_data['attachment_lock'] = false;

        try {
            $contract = Contract::create($contract_data);
            if (!empty($contract)) {
                Offer::where('id', $offer->id)->where('updated_at', $offer->updated_at)->update(['generated_at' => date('Y-m-d H:i:s', $current_time)]);
                return redirect(route('contract.attachment', ['contract' => $contract->id]));
            } else {
                return redirect($redirect_url)->with('tip_message', ['content' => '服务器繁忙', 'state' => 'danger']);
            }
        } catch (\Exception $e) {
            return redirect($redirect_url)->with('tip_message', ['content' => '服务器繁忙', 'state' => 'danger']);
        }
    }

    public function index()
    {
        $company_id = auth()->user()->company_id;
        if (empty($company_id)) {
            App::abort(403, '抱歉，您不属于任何分公司');
        }
        $offers = Offer::with(['contract', 'demand', 'demand.basket', 'supplier', 'supplier.supplier'])
            ->orderBy('updated_at', 'desc')
            ->whereHas('demand', function($query) use($company_id) {
                return $query->where('company_id', $company_id)->where('is_cancel', false);
            });
        $offers = $offers->whereHas('bid', function($query) {
            return $query->where('offer_stop', '<', date('Y-m-d H:i:s'));
        });
        $offers = $offers->where('quantity', '>', 0);
        $offers = $offers->paginate(10);
        $pages = $offers->appends([])->render();

        return view('bid.company.index')
            ->with('offers', $offers)
            ->with('pages', $pages);
    }

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

    public function offer(Offer $offer)
    {
        $company_id = auth()->user()->company_id;
        if (empty($company_id)) {
            App::abort(403, '抱歉，您不属于任何分公司');
        }
        $current_offer = Offer::with(['contract', 'demand', 'demand.basket', 'supplier', 'supplier.supplier'])
            ->orderBy('updated_at')
            ->whereHas('demand', function($query) use($company_id) {
                return $query->where('company_id', $company_id);
            });
        $current_offer = $current_offer->where('quantity', '>', 0);
        $current_offer = $current_offer->where('id', $offer->id);
        $current_offer = $current_offer->first();
        $goods = json_decode($current_offer->demand->goods_static);
        return view('bid.company.contract')
            ->with('company', auth()->user()->company)
            ->with('goods', $goods)
            ->with('offer', $current_offer)
            ->with('amount', $this->get_amount(($current_offer->price + $current_offer->delivery_costs) * $current_offer->quantity));
    }

    public function generateContract(Request $request, Offer $offer)
    {
        $redirect_url = URL::full();
        $current_time = time();
        $data = $request->get('data');
        $data = json_encode($data);

        $company_id = auth()->user()->company_id;
        if (empty($company_id)) {
            App::abort(403, '抱歉，您不属于任何分公司');
        }
        $current_offer = Offer::with(['contract', 'demand', 'demand.basket', 'supplier', 'supplier.supplier'])
            ->orderBy('updated_at')
            ->whereHas('demand', function($query) use($company_id) {
                return $query->where('company_id', $company_id);
            });
        $current_offer = $current_offer->where('quantity', '>', 0);
        $current_offer = $current_offer->where('id', $offer->id);
        $current_offer = $current_offer->first();
        $goods = json_decode($current_offer->demand->goods_static);

        $current_date_start = date('Y-m-d 00:00:00', $current_time);
        $current_date_stop = date('Y-m-d 23:59:59', $current_time);
        $count = Contract::where('created_at', '>=', $current_date_start)
            ->where('created_at', '<=', $current_date_stop)
            ->whereHas('offer', function($query) use($goods) {
                return $query->whereHas('bid', function($query) use($goods) {
                    return $query->where('goods_id', $goods->id);
                });
            })
            ->count();
        if (empty($count)) {
            $count = 1;
        } else {
            $count += 1;
        }

        $contract_data = [
            'offer_id' => $current_offer->id,
            'title' => '购销合同',
            'code' => 'YCH-'.auth()->user()->company->code.'-'.$goods->code.date('ymd').'C'.sprintf('%04s', $count),
            'state' => 'pending',
            'addition' => $data
        ];

        $result = DB::transaction(function() use($contract_data, $current_offer) {
            if (!Contract::create($contract_data)) {
                return false;
            }
            if (!DB::table('offers')->where('id', $current_offer->id)->update(['generated_at' => date('Y-m-d H:i:s')])) {
                DB::rollBack();
                return false;
            }
            return true;
        });
        //$result = Contract::create($contract_data);

        if ($result) {

            //通知：您中标的【物料名称】合同已生成，请登录平台进行合同确认并及时线下签订纸质合同。
            if (!empty($current_offer->supplier->phone)) {
                //$goods_name = preg_replace('#(\d+)%#', "百分之$1", $goods->name);
                //$message = '通知：您中标的'.$goods_name.'合同已生成，请登录平台进行合同确认并及时线下签订纸质合同。';
                $template = '确认合同';
                $params = json_encode(['contract' => $goods->name]);
                $this->dispatch(new SendSms($current_offer->supplier->phone, $template, $params));
            }

            return redirect($redirect_url)->with('tip_message', ['content' => '生成合同成功', 'state' => 'success']);
        } else {
            return redirect($redirect_url)->withErrors(['form' => ['服务器繁忙，请稍后再试']]);
        }
    }
}
