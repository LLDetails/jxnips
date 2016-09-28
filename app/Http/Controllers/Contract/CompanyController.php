<?php

namespace App\Http\Controllers\Contract;

use App\Contract;
use App\ContractGrade;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use URL;

class CompanyController extends Controller
{
    public function index()
    {
        $company_id = auth()->user()->company_id;
        if (empty($company_id)) {
            App::abort(403, '抱歉，您不属于任何分公司');
        }
        $contracts = Contract::with(['grade', 'offer', 'offer.demand', 'offer.demand.basket', 'offer.supplier.supplier'])
            ->orderBy('created_at', 'desc')
            ->whereHas('offer.demand', function($query) use($company_id) {
                return $query->where('company_id', $company_id);
            });

        $contracts = $contracts->paginate(10);
        $pages = $contracts->appends([])->render();

        $contract_states = [
            'pending' => '待确认',
            'refused' => '有异议',
            'confirmed' => '已确认',
            'finished' => '已完成'
        ];

        return view('contract.company.index')
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
        $company = auth()->user()->company;
        if ( ! empty($company) and $company->id != $contract->offer->demand->company_id) {
            App::abort(403, '无权查看此合同');
        }

        $upper_total_price = $this->get_amount(($contract->offer->price + $contract->offer->delivery_costs) * $contract->offer->quantity);

        return view('contract.company.view')
            ->with('contract', $contract)
            ->with('goods', $goods)
            ->with('upper_total_price', $upper_total_price)
            ->with('data', $data);
    }

    public function edit(Contract $contract)
    {
        $data = json_decode($contract->addition, true);
        $goods = json_decode($contract->offer->demand->goods_static);
        $company = auth()->user()->company;
        if ( ! empty($company) and $company->id != $contract->offer->demand->company_id) {
            App::abort(403, '无权修改此合同');
        }

        $upper_total_price = $this->get_amount(($contract->offer->price + $contract->offer->delivery_costs) * $contract->offer->quantity);

        $suggestion = json_decode($contract->suggestion, true);

        return view('contract.company.edit')
            ->with('contract', $contract)
            ->with('goods', $goods)
            ->with('suggestion', $suggestion)
            ->with('upper_total_price', $upper_total_price)
            ->with('data', $data);
    }

    public function save(Request $request, Contract $contract)
    {
        $company = auth()->user()->company;
        if ( ! empty($company) and $company->id != $contract->offer->demand->company_id) {
            App::abort(403, '无权修改此合同');
        }
        $redirect_url = URL::full();
        $data = $request->get('data');
        $data = json_encode($data);
        $contract->addition = $data;
        $contract->state = 'pending';

        if ($contract->save()) {
            return redirect(route('contract.company.view', ['contract'=>$contract->id]))->with('tip_message', ['content' => '保存合同成功', 'state' => 'success', 'hold'=>true]);
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
        return view('contract.company.attachment')
            ->with('contract', $contract)
            ->with('attachments', $attachments);
    }

    public function saveAttachment(Request $request, Contract $contract)
    {
        $redirect_url = URL::full();
        $attachments = $request->get('attachment');
        if (empty($attachments)) {
            $attachments = [];
        }
        $contract->attachment = json_encode($attachments);
        if ($contract->save()) {
            return redirect($redirect_url)->with('tip_message', ['content' => '附件保存成功', 'state' => 'success']);
        } else {
            return redirect($redirect_url)->withInput()->withErrors(['form' => ['服务器繁忙，请稍后再试']]);
        }
    }

    public function confirm(Contract $contract)
    {
        $redirect_url = URL::previous();
        if (!$contract->offline) {
            return redirect($redirect_url)->withInput()->withErrors(['form' => ['只有附件合同才能进行此操作']]);
        } else {
            if ($contract->state != 'pending') {
                return redirect($redirect_url)->withInput()->withErrors(['form' => ['当前状态不能进行此操作']]);
            } else {
                $contract->confirmed_at = date('Y-m-d H:i:s');
                $contract->state = 'confirmed';
                if ($contract->save()) {
                    return redirect($redirect_url)->with('tip_message', ['content' => '操作成功', 'state' => 'success']);
                } else {
                    return redirect($redirect_url)->withInput()->withErrors(['form' => ['服务器繁忙，请稍后再试']]);
                }
            }
        }
    }

    public function finish(Contract $contract)
    {
        $redirect_url = URL::previous();
        if ($contract->state != 'confirmed') {
            return redirect($redirect_url)->withInput()->withErrors(['form' => ['当前状态不能进行此操作']]);
        } else {
            $contract->finished_at = date('Y-m-d H:i:s');
            $contract->state = 'finished';
            if ($contract->save()) {
                return redirect($redirect_url)->with('tip_message', ['content' => '操作成功', 'state' => 'success']);
            } else {
                return redirect($redirect_url)->withInput()->withErrors(['form' => ['服务器繁忙，请稍后再试']]);
            }
        }
    }

    public function grade(Request $request, Contract $contract)
    {
        $contract_grade = ContractGrade::where('contract_id', $contract->id)->first();
        if (!empty($contract_grade)) {
            if (!empty($contract_grade->company_grade_1) or !empty($contract_grade->company_grade_2)) {
                return response()->json([
                    'state' => 'error',
                    'msg' => '该合同已被评价了'
                ]);
            }
        }

        $company_grade_1 = intval($request->get('company_grade_1'));
        $company_grade_2 = intval($request->get('company_grade_2'));
        if (empty($company_grade_1) or empty($company_grade_2)) {
            return response()->json([
                'state' => 'error',
                'msg' => '有未评价的项'
            ]);
        } else {
            if ($company_grade_1 < 0) {
                $company_grade_1 = 1;
            } elseif ($company_grade_1 > 5) {
                $company_grade_1 = 5;
            }
            if ($company_grade_2 < 0) {
                $company_grade_2 = 1;
            } elseif ($company_grade_2 > 5) {
                $company_grade_2 = 5;
            }
            $current_time = date('Y-m-d H:i:s');
            if (!empty($contract_grade)) {
                $contract_grade->company_grade_1 = $company_grade_1;
                $contract_grade->company_grade_2 = $company_grade_2;
                $contract_grade->company_graded_at = $current_time;
                $result = $contract_grade->save();
            } else {
                $result = ContractGrade::create([
                    'contract_id' => $contract->id,
                    'company_grade_1' => $company_grade_1,
                    'company_grade_2' => $company_grade_2,
                    'company_graded_at' => $current_time
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
