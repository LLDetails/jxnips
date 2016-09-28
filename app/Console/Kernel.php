<?php

namespace App\Console;

use App\Basket;
use App\Bid;
use App\BidCount;
use App\Demand;
use App\Goods;
use App\Offer;
use App\Setting;
use App\User;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Foundation\Bus\DispatchesJobs;
use App\Jobs\SendSms;
use Mockery\Exception;

class Kernel extends ConsoleKernel
{
    use DispatchesJobs;
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        \App\Console\Commands\Inspire::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        //生成统计任务
        $schedule->call(function () {
            $data = [];

            //计算运行天书天数
            $today = date('Y-m-d');
            $start_date = config('settings.system_start_at', '2015-10-15');
            $interval = date_diff(date_create($today), date_create($start_date));
            $data['days'] = $interval->format('%a');

            //计算发布标书数量
            $data['bid_counts'] = Demand::where('quantity', '>', 0)
                /*->where('created_at', '>=', $start_date. ' 00:00:00')
                ->where('created_at', '<=', $today. ' 23:59:59')*/
                ->whereHas('bid', function($query) use($start_date, $today) {
                    return $query->where('offer_stop', '>=', $start_date. ' 00:00:00')
                        ->where('offer_stop', '<=', $today. ' 23:59:59');
                })
                ->count();

            //计算流标次数
            $data['failed_bid_counts'] = Demand::where('quantity', '>', 0)
                /*->where('created_at', '>=', $start_date. ' 00:00:00')
                ->where('created_at', '<=', $today. ' 23:59:59')*/
                ->whereHas('bid', function($query) use($start_date, $today) {
                    return $query->where('offer_stop', '>=', $start_date. ' 00:00:00')
                        ->where('offer_stop', '<=', $today. ' 23:59:59');
                })
                ->where('is_cancel', true)
                ->count();

            //计算招标品种数
            /*$data['goods_counts'] = Demand::where('created_at', '>=', $start_date. ' 00:00:00')
                ->where('created_at', '<=', $today. ' 23:59:59')
                ->count(\DB::raw('distinct "goods_id"'));*/
            $data['goods_counts'] = Demand::whereHas('bid', function($query) use($start_date, $today) {
                return $query->where('offer_stop', '>=', $start_date. ' 00:00:00')
                    ->where('offer_stop', '<=', $today. ' 23:59:59');
            })->count(\DB::raw('distinct "goods_id"'));

            //计算成交数量
            $data['quantity'] = Offer::whereNotNull('quantity')
                ->where('quantity' , '>', 0)
                ->whereHas('demand', function($query) use($today, $start_date) {
                    return $query->where('quantity', '>', 0)
                        ->where('created_at', '>=', $start_date. ' 00:00:00')
                        ->where('created_at', '<=', $today. ' 23:59:59');
                })
                ->sum('quantity');

            //计算成交金额
            $data['amount'] = Offer::whereNotNull('quantity')
                ->where('quantity' , '>', 0)
                ->whereHas('demand', function($query) use($today, $start_date) {
                    return $query->where('quantity', '>', 0)
                        ->where('created_at', '>=', $start_date. ' 00:00:00')
                        ->where('created_at', '<=', $today. ' 23:59:59');
                })
                ->sum(\DB::raw('"quantity" * "price"'));

		     // lvze add num formate 20160831
             $data['amount']  = number_format( $data['amount'] ,5 ,"." , "");

            //计算中标供应商数量
            $data['supplier_counts'] = Offer::whereNotNull('quantity')
                ->where('quantity' , '>', 0)
                ->whereHas('demand', function($query) {
                    return $query->where('quantity', '>', 0);
                })->whereHas('bid', function($query) use($today, $start_date) {
                    return $query->where('offer_stop', '>=', $start_date. ' 00:00:00')
                        ->where('offer_stop', '<=', $today. ' 23:59:59');
                })
                ->count(\DB::raw('distinct "user_id"'));

            //计算参与报价供应商数量
            $data['offer_counts'] = Offer::whereNotNull('quantity')
                ->whereNull('reason')
                ->whereHas('demand', function($query) {
                    return $query->where('quantity', '>', 0);
                })->whereHas('bid', function($query) use($today, $start_date) {
                    return $query->where('offer_stop', '>=', $start_date. ' 00:00:00')
                        ->where('offer_stop', '<=', $today. ' 23:59:59');
                })
                ->count(\DB::raw('distinct "user_id"'));

            $data['generated_at'] = $today;

            try {
                if (BidCount::where('generated_at', $today)->exists()) {
                    BidCount::where('generated_at', $today)->update($data);
                } else {
                    BidCount::create($data);
                }
            } catch (\Exception $e) {

            }

        })->dailyAt('16:05');


        $time = time();
        $today = date('Y-m-d', $time);
        $basket = Basket::where('name', $today)->first();
        if (empty($basket)) {
            return;
        }

        $check_flow_setting = Setting::where('name', 'check_flow')->first();
        if (!empty($check_flow_setting)) {
            $check_flows = json_decode($check_flow_setting->data, true);
            foreach ($check_flows as $k=>$flow) {
                if ($k == 0) {
                    continue;
                }
                if (isset($check_flows[$k+1])) {
                    $role_id = $flow['role_id'];
                    $users = User::where('role_id', $role_id)->where('allow_login', true)->whereNotNull('phone')->whereNull('deleted_at')->get();
                    foreach ($users as $user) {
                        $company_id = $user->company_id;
                        $category_id = $user->category_id;
                        $area_id = $user->area_id;
                        $demands = Demand::where('basket_id', $basket->id);
                        if (!empty($company_id)) {
                            $demands = $demands->where('company_id', $company_id);
                        }
                        if (!empty($area_id)) {
                            $demands = $demands->whereHas('company', function ($query) use ($area_id) {
                                return $query->where('area_id', $area_id);
                            });
                        }
                        if (!empty($category_id)) {
                            $demands = $demands->where('category_id', $category_id);
                        }
                        $demands = $demands->select('goods_id')
                            ->distinct()
                            ->lists('goods_id');
                        $goods_ids = $demands->toArray();
                        if (count($demands) > 0) {
                            $goods_name = Goods::whereIn('id', $goods_ids)->lists('name')->toArray();
                            //$convert_goods_name = array_map($replace_percent_symbol, $goods_name);
                            $params_arr = [];
                            $params_arr['time'] = substr($flow['time'],0,5);
                            $params_arr['demand'] = implode('、',$goods_name);
                            //$message = '通知：请您于'.substr($flow['time'],0,5).'前登录平台审核'.implode('、',$convert_goods_name).'采购计划，过时未审,系统视为您默认通过。';
                            $schedule->call(function () use ($user, $params_arr) {
                                //$params = implode(',', $params_arr);
                                $params = json_encode($params_arr);
                                $this->dispatch(new SendSms($user->phone, '审核计划', $params));
                                //$this->dispatch(new SendSms($user->phone, $message));
                            })->dailyAt(substr($check_flows[$k-1]['time'],0,5));
                        }
                    }
                } else {
                    $demands_count = Demand::where('basket_id', $basket->id)->count();
                    if ($demands_count > 0) {
                        $role_id = $flow['role_id'];
                        $users = User::where('role_id', $role_id)->where('allow_login', true)->whereNotNull('phone')->whereNull('deleted_at')->get();
                        foreach ($users as $user) {
                            $params = '';
                            $params = json_encode(['time' => substr($flow['time'],0,5)]);
                            //$message = '通知：请您于'.substr($flow['time'],0,5).'前登录平台审核采购计划及确定招标事宜，过时未审,系统视为您选择默认方式并自动通过。';
                            $schedule->call(function () use ($user, $params) {
                                $this->dispatch(new SendSms($user->phone, '发布标书', $params));
                            })->dailyAt(substr($check_flows[$k-1]['time'],0,5));
                        }
                    }
                }
            }
        }
    }
}
