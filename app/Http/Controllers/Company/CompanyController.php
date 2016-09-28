<?php

namespace App\Http\Controllers\Company;

use App\Company;
use App\Area;
use App\Setting;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Validator;
use URL;

class CompanyController extends Controller
{
    public function manage(Request $request)
    {
        $cond = $request->only(['area_id', 'name']);
        $cond = array_map('trim', $cond);
        $companies = Company::with('area')->orderBy('area_id', 'asc')->orderBy('created_at', 'asc');
        $companies = $companies->whereNull('deleted_at');
        if ( ! empty($cond['area_id'])) {
            $companies = $companies->where('area_id', $cond['area_id']);
        }
        if ( ! empty($cond['name'])) {
            $companies = $companies->where('name', 'like', '%'.$cond['name'].'%');
        }
        $companies = $companies->paginate(10);
        $pages = $companies->appends($cond)->render();

        $areas = Area::orderBy('display_order', 'asc')
            ->whereNull('deleted_at')
            ->get();

        return view('company.manage')
            ->with('companies', $companies)
            ->with('pages', $pages)
            ->with('areas', $areas);
    }

    public function add()
    {
        $areas = Area::orderBy('display_order', 'asc')
            ->whereNull('deleted_at')
            ->get();
        $addition = Setting::where('name', 'company_addition')->first();
        if (empty($addition) or empty($addition->data)) {
            $addition = [];
        } else {
            $addition = json_decode($addition->data);
        }
        return view('company.add')->with('addition', $addition)->with('areas', $areas);
    }

    public function create(Request $request)
    {
        $redirect_url = URL::full();
        $addition = Setting::where('name', 'company_addition')->first();
        if (empty($addition) or empty($addition->data)) {
            $addition_data = [];
        } else {
            $addition_data = json_decode($addition->data);
        }
        $area_ids = Area::orderBy('display_order', 'asc')
            ->whereNull('deleted_at')
            ->lists('id')
            ->toArray();
        //$form_data = $request->only(['name', 'area_id', 'receive_address', 'code']);
        $allow_input = ['name', 'area_id', 'delivery_address', 'code', 'contract_tel', 'contract_contact', 'contract_fax'];
        //$form_data = array_map('trim', $form_data);
        $rules = [
            'name'  => 'required|max:60|unique:companies,name',
            'code'  => 'required|max:6|unique:companies,code',
            'area_id' => 'required|in:'.implode(',',$area_ids),
            'contract_tel' => 'max:45',
            'contract_fax' => 'max:45',
            'contract_contact' => 'max:30'
        ];
        $messages = [
            'required' => '必填项不能为空',
            'unique'   => '已被使用，不可重复',
            'max'      => '超出最大字数:max',
            'area_id.in'=> '不存在的地区'
        ];

        foreach ($addition_data as $field) {
            $allow_input[] = $field->name;
            $rule = [];
            if ($field->required == 'true') {
                $rule[] = 'required';
                $messages[$field->name.'.required'] = $field->prompt;
            }
            switch ($field->tpl) {
                case 'text':
                    if ( ! empty($field->size_min)) {
                        $rule[] = 'min:'.$field->size_min;
                        $messages[$field->name.'.min'] = $field->prompt;
                    }
                    if ( ! empty($field->size_max)) {
                        $rule[] = 'max:'.$field->size_max;
                        $messages[$field->name.'.max'] = $field->prompt;
                    }
                    if ($field->rule != '*') {
                        $rule[] = $field->rule;
                        $messages[$field->name.'.'.$field->rule] = $field->prompt;
                    }
                    break;
                case 'select':
                    if ($field->widget == 'checkbox') {
                        $rule[] = 'array';
                        $messages[$field->name.'.array'] = $field->prompt;
                    }
                    break;
                case 'file':
                    $rule[] = 'array';
                    $messages[$field->name.'.array'] = $field->prompt;
                    break;
                default:
                    break;
            }
            $rules[$field->name] = implode('|', $rule);
        }

        $form_data = $request->only($allow_input);

        $validator = Validator::make($form_data, $rules, $messages);
        if ($validator->fails()) {
            return redirect($redirect_url)->withErrors($validator->errors())->withInput();
        }
        $addition_data_arr = [];
        foreach ($addition_data as $field) {
            $addition_data_arr[$field->name] = $form_data[$field->name];
            unset($form_data[$field->name]);
        }
        $form_data['addition'] = json_encode($addition_data_arr);
        $company = Company::create($form_data);
        if ($company->save()) {
            return redirect($redirect_url)->with('tip_message', ['content' => '成功添加分公司', 'state' => 'success']);
        } else {
            return redirect($redirect_url)->withErrors(['form' => ['服务器繁忙，请稍后再试']]);
        }
    }

    public function edit(Company $company)
    {
        $redirect_url = URL::full();
        $areas = Area::orderBy('display_order', 'asc')
            ->whereNull('deleted_at')
            ->get();
        if (!empty($company->deleted_at)) {
            return redirect($redirect_url)->withErrors(['form' => ['该公司已被删除']]);
        }

        $company_addition = $company->addition;
        if ( ! empty($company_addition)) {
            $addition_data = json_decode($company_addition);
        }
        if (empty($addition_data)) {
            $addition_data = [];
        }
        $addition = Setting::where('name', 'company_addition')->first();
        if (empty($addition) or empty($addition->data)) {
            $addition = [];
        } else {
            $addition = json_decode($addition->data);
        }

        return view('company.edit')
            ->with('areas', $areas)
            ->with('addition_data', $addition_data)
            ->with('addition', $addition)
            ->with('company', $company);
    }

    public function update(Request $request, Company $company)
    {
        $redirect_url = URL::full();
        if (!empty($company->deleted_at)) {
            return redirect($redirect_url)->withErrors(['form' => ['该公司已被删除']]);
        }

        $addition = Setting::where('name', 'company_addition')->first();
        if (empty($addition) or empty($addition->data)) {
            $addition_data = [];
        } else {
            $addition_data = json_decode($addition->data);
        }

        $area_ids = Area::orderBy('display_order', 'asc')
            ->whereNull('deleted_at')
            ->lists('id')
            ->toArray();
        //$form_data = $request->only(['name', 'area_id', 'receive_address', 'code']);
        $allow_input = ['name', 'area_id', 'delivery_address', 'code', 'contract_tel', 'contract_contact', 'contract_fax'];

        $rules = [
            'name'  => 'required|max:60|unique:companies,name,' . $company->id,
            'code'  => 'required|max:6|unique:companies,code,' . $company->id,
            'area_id' => 'required|in:'.implode(',', $area_ids),
            'contract_tel' => 'max:45',
            'contract_fax' => 'max:45',
            'contract_contact' => 'max:30'
        ];
        $messages = [
            'required' => '必填项不能为空',
            'unique'   => '已被使用，不可重复',
            'max'      => '超出最大字数:max',
            'area_id.in'=> '不存在的地区'
        ];

        foreach ($addition_data as $field) {
            $allow_input[] = $field->name;
            $rule = [];
            if ($field->required == 'true') {
                $rule[] = 'required';
                $messages[$field->name.'.required'] = $field->prompt;
            }
            switch ($field->tpl) {
                case 'text':
                    if ( ! empty($field->size_min)) {
                        $rule[] = 'min:'.$field->size_min;
                        $messages[$field->name.'.min'] = $field->prompt;
                    }
                    if ( ! empty($field->size_max)) {
                        $rule[] = 'max:'.$field->size_max;
                        $messages[$field->name.'.max'] = $field->prompt;
                    }
                    if ($field->rule != '*') {
                        $rule[] = $field->rule;
                        $messages[$field->name.'.'.$field->rule] = $field->prompt;
                    }
                    break;
                case 'select':
                    if ($field->widget == 'checkbox') {
                        $rule[] = 'array';
                        $messages[$field->name.'.array'] = $field->prompt;
                    }
                    break;
                case 'file':
                    $rule[] = 'array';
                    $messages[$field->name.'.array'] = $field->prompt;
                    break;
                default:
                    break;
            }
            $rules[$field->name] = implode('|', $rule);
        }

        $form_data = $request->only($allow_input);

        $validator = Validator::make($form_data, $rules, $messages);
        if ($validator->fails()) {
            return redirect($redirect_url)->withErrors($validator->errors())->withInput();
        }

        $addition_data_arr = [];
        foreach ($addition_data as $field) {
            $addition_data_arr[$field->name] = $form_data[$field->name];
            unset($form_data[$field->name]);
        }
        //$form_data['addition'] = json_encode($addition_data_arr);
        $company->addition = json_encode($addition_data_arr);
        $company->name = $form_data['name'];
        $company->area_id = $form_data['area_id'];
        $company->delivery_address = $form_data['delivery_address'];
        $company->code = $form_data['code'];
        $company->contract_tel = $form_data['contract_tel'];
        $company->contract_fax = $form_data['contract_fax'];
        $company->contract_contact = $form_data['contract_contact'];
        if ($company->save()) {
            return redirect($redirect_url)->with('tip_message', ['content' => '编辑成功', 'state' => 'success']);
        } else {
            return redirect($redirect_url)->withErrors(['form' => ['服务器繁忙，请稍后再试']]);
        }
    }

    public function delete(Company $company)
    {
        $redirect_url = URL::previous();
        if (!empty($company->deleted_at)) {
            return redirect($redirect_url)->with('tip_message', ['content' => '公司已被删除', 'state' => 'warning', 'hold' => true]);
        }
        $date = date('Y-m-d H:i:s');
        $company->name = $company->name . '#delete@'.$date;
        $company->deleted_at = $date;
        if ($company->save()) {
            return redirect($redirect_url)->with('tip_message', ['content' => '操作成功', 'state' => 'success', 'hold' => true]);
        } else {
            return redirect($redirect_url)->with('tip_message', ['content' => '服务器繁忙，请稍后再试', 'state' => 'danger', 'hold' => true]);
        }
    }

    public function information()
    {
        $company = auth()->user()->company;
        $redirect_url = URL::full();
        $areas = Area::orderBy('display_order', 'asc')
            ->whereNull('deleted_at')
            ->get();
        if ($company->deleted_at) {
            return redirect($redirect_url)->withErrors(['form' => ['该公司已被删除']]);
        }

        $company_addition = $company->addition;
        if ( ! empty($company_addition)) {
            $addition_data = json_decode($company_addition);
        }
        if (empty($addition_data)) {
            $addition_data = [];
        }
        $addition = Setting::where('name', 'company_addition')->first();
        if (empty($addition) or empty($addition->data)) {
            $addition = [];
        } else {
            $addition = json_decode($addition->data);
        }

        return view('company.edit')
            ->with('areas', $areas)
            ->with('addition_data', $addition_data)
            ->with('addition', $addition)
            ->with('company', $company);
    }

    public function saveInformation(Request $request)
    {
        $company = auth()->user()->company;
        $redirect_url = URL::full();
        if (!empty($company->deleted_at)) {
            return redirect($redirect_url)->withErrors(['form' => ['该公司已被删除']]);
        }

        $addition = Setting::where('name', 'company_addition')->first();
        if (empty($addition) or empty($addition->data)) {
            $addition_data = [];
        } else {
            $addition_data = json_decode($addition->data);
        }

        $area_ids = Area::orderBy('display_order', 'asc')
            ->whereNull('deleted_at')
            ->lists('id')
            ->toArray();
        $allow_input = ['name', 'area_id', 'delivery_address', 'code', 'contract_tel', 'contract_contact'];

        $rules = [
            'name'  => 'required|max:60|unique:companies,name,' . $company->id,
            'code'  => 'required|max:6|unique:companies,code,' . $company->id,
            'area_id' => 'required|in:'.implode(',', $area_ids),
            'contract_tel' => 'max:45',
            'contract_contact' => 'max:30'
        ];
        $messages = [
            'required' => '必填项不能为空',
            'unique'   => '已被使用，不可重复',
            'max'      => '超出最大字数:max',
            'area_id.in'=> '不存在的地区'
        ];

        foreach ($addition_data as $field) {
            $allow_input[] = $field->name;
            $rule = [];
            if ($field->required == 'true') {
                $rule[] = 'required';
                $messages[$field->name.'.required'] = $field->prompt;
            }
            switch ($field->tpl) {
                case 'text':
                    if ( ! empty($field->size_min)) {
                        $rule[] = 'min:'.$field->size_min;
                        $messages[$field->name.'.min'] = $field->prompt;
                    }
                    if ( ! empty($field->size_max)) {
                        $rule[] = 'max:'.$field->size_max;
                        $messages[$field->name.'.max'] = $field->prompt;
                    }
                    if ($field->rule != '*') {
                        $rule[] = $field->rule;
                        $messages[$field->name.'.'.$field->rule] = $field->prompt;
                    }
                    break;
                case 'select':
                    if ($field->widget == 'checkbox') {
                        $rule[] = 'array';
                        $messages[$field->name.'.array'] = $field->prompt;
                    }
                    break;
                case 'file':
                    $rule[] = 'array';
                    $messages[$field->name.'.array'] = $field->prompt;
                    break;
                default:
                    break;
            }
            $rules[$field->name] = implode('|', $rule);
        }

        $form_data = $request->only($allow_input);

        $validator = Validator::make($form_data, $rules, $messages);
        if ($validator->fails()) {
            return redirect($redirect_url)->withErrors($validator->errors())->withInput();
        }

        $addition_data_arr = [];
        foreach ($addition_data as $field) {
            $addition_data_arr[$field->name] = $form_data[$field->name];
            unset($form_data[$field->name]);
        }
        //$form_data['addition'] = json_encode($addition_data_arr);
        $company->addition = json_encode($addition_data_arr);
        $company->name = $form_data['name'];
        $company->area_id = $form_data['area_id'];
        $company->delivery_address = $form_data['delivery_address'];
        $company->code = $form_data['code'];
        $company->contract_tel = $form_data['contract_tel'];
        $company->contract_contact = $form_data['contract_contact'];
        if ($company->save()) {
            return redirect($redirect_url)->with('tip_message', ['content' => '编辑成功', 'state' => 'success', 'hold' => true]);
        } else {
            return redirect($redirect_url)->withErrors(['form' => ['服务器繁忙，请稍后再试']]);
        }
    }
}
