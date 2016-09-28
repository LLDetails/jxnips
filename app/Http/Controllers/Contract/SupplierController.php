<?php

namespace App\Http\Controllers\Contract;

use App\Contract;
use App\ContractGrade;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use URL;
use App;

class SupplierController extends Controller
{
    public function index()
    {
        $current_user = auth()->user();
        $contracts = Contract::with(['grade', 'offer', 'offer.demand', 'offer.demand.company'])
            ->orderBy('created_at', 'desc')
            ->whereHas('offer', function($query) use($current_user) {
                return $query->where('user_id', $current_user->id);
            });
        $contracts = $contracts->where('state', '!=', 'refused');

        $contracts = $contracts->paginate(10);
        $pages = $contracts->appends([])->render();

        $contract_states = [
            'pending' => '待确认',
            'refused' => '有异议',
            'confirmed' => '已确认',
            'finished' => '已完成'
        ];

        return view('contract.supplier.index')
            ->with('contracts', $contracts)
            ->with('pages', $pages)
            ->with('contract_states', $contract_states);
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

    public function view(Contract $contract)
    {
        $data = json_decode($contract->addition, true);
        $goods = json_decode($contract->offer->demand->goods_static);
        if (auth()->user()->id != $contract->offer->user_id) {
            App::abort(403, '无权查看此合同');
        }

        $upper_total_price = $this->get_amount(($contract->offer->price + $contract->offer->delivery_costs) * $contract->offer->quantity);

        $suggestion = json_decode($contract->suggestion, true);

        return view('contract.supplier.view')
            ->with('contract', $contract)
            ->with('goods', $goods)
            ->with('suggestion', $suggestion)
            ->with('upper_total_price', $upper_total_price)
            ->with('data', $data);
    }

    public function suggest(Request $request, Contract $contract)
    {
        $redirect_url = route('contract.supplier.view', ['contract' => $contract->id]);
        $suggest = $request->get('content');
        if (auth()->user()->id != $contract->offer->user_id) {
            App::abort(403, '无权提交此合同的修改意见');
        }
        $old_suggest = json_decode($contract->suggestion, true);
        if (empty($old_suggest)) {
            $old_suggest = [];
        }
        $old_suggest[] = [
            'content' => $suggest,
            'date' => date('Y-m-d H:i:s')
        ];
        $contract->suggestion = json_encode($old_suggest);
        $contract->state = 'refused';
        if ($contract->save()) {
            return redirect($redirect_url)->with('tip_message', ['content' => '提交合同修改意见成功', 'state' => 'success']);
        } else {
            return redirect($redirect_url)->with('tip_message', ['content' => '服务器繁忙，请稍后再试', 'state' => 'danger', 'hold'=>true]);
        }
    }

    public function confirm(Contract $contract)
    {
        if (auth()->user()->id != $contract->offer->user_id) {
            App::abort(403, '无权确认此合同');
        }
        $redirect_url = route('contract.supplier.view', ['contract' => $contract->id]);
        $contract->state = 'confirmed';
        $contract->confirmed_at = date('Y-m-d H:i:s');
        if ($contract->save()) {
            return redirect($redirect_url)->with('tip_message', ['content' => '确认合同成功', 'state' => 'success']);
        } else {
            return redirect($redirect_url)->with('tip_message', ['content' => '服务器繁忙，请稍后再试', 'state' => 'danger', 'hold'=>true]);
        }
    }

    public function attachment(Contract $contract)
    {
        $attachments = $contract->attachment;
        if ( ! empty($attachments)) {
            $attachments = json_decode($attachments, true);
        } else {
            $attachments = [];
        }
        return view('contract.supplier.attachment')
            ->with('contract', $contract)
            ->with('attachments', $attachments);
    }

    public function grade(Request $request, Contract $contract)
    {
        $contract_grade = ContractGrade::where('contract_id', $contract->id)->first();
        if (!empty($contract_grade)) {
            if (!empty($contract_grade->supplier_grade_1) or !empty($contract_grade->supplier_grade_2) or !empty($contract_grade->supplier_grade_3)) {
                return response()->json([
                    'state' => 'error',
                    'msg' => '该合同已被评价了'
                ]);
            }
        }

        $supplier_grade_1 = intval($request->get('supplier_grade_1'));
        $supplier_grade_2 = intval($request->get('supplier_grade_2'));
        $supplier_grade_3 = intval($request->get('supplier_grade_3'));
        if (empty($supplier_grade_1) or empty($supplier_grade_2) or empty($supplier_grade_3)) {
            return response()->json([
                'state' => 'error',
                'msg' => '有未评价的项'
            ]);
        } else {
            if ($supplier_grade_1 < 0) {
                $supplier_grade_1 = 1;
            } elseif ($supplier_grade_1 > 5) {
                $supplier_grade_1 = 5;
            }
            if ($supplier_grade_2 < 0) {
                $supplier_grade_2 = 1;
            } elseif ($supplier_grade_2 > 5) {
                $supplier_grade_2 = 5;
            }
            if ($supplier_grade_3 < 0) {
                $supplier_grade_3 = 1;
            } elseif ($supplier_grade_3 > 5) {
                $supplier_grade_3 = 5;
            }
            $current_time = date('Y-m-d H:i:s');
            if (!empty($contract_grade)) {
                $contract_grade->supplier_grade_1 = $supplier_grade_1;
                $contract_grade->supplier_grade_2 = $supplier_grade_2;
                $contract_grade->supplier_grade_3 = $supplier_grade_3;
                $contract_grade->supplier_graded_at = $current_time;
                $result = $contract_grade->save();
            } else {
                $result = ContractGrade::create([
                    'contract_id' => $contract->id,
                    'supplier_grade_1' => $supplier_grade_1,
                    'supplier_grade_2' => $supplier_grade_2,
                    'supplier_grade_3' => $supplier_grade_3,
                    'supplier_graded_at' => $current_time
                ]);
            }
            if ($result) {
                return response()->json([
                    'state' => 'success'
                ]);
            } else {
                return response()->json([
                    'state' => 'error',
                    'msg' => '服务器繁忙，请稍候再试'
                ]);
            }
        }
    }
}
