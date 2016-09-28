<?php

namespace App\Services;

use Cache;
use Queue;

class IPSHelper
{
    public static function hasPermission($permission)
    {
        return in_array($permission, Cache::get('permission.role.' . auth()->user()->role->id));
    }

    public static function showButton($options)
    {
        if (empty($options) or empty($options['type']) or !in_array($options['type'], ['frame', 'link'])) {
            return '';
        }

        if (empty($options['permission']) or !in_array($options['permission'], Cache::get('permission.role.' . auth()->user()->role->id))) {
            return '';
        }

        if ($options['type'] == 'frame') {
            if (empty($options['title']) or empty($options['text']) or empty($options['src'])) {
                return '';
            }
            if (!isset($options['confirm'])) {
                $options['confirm'] = '';
            }
            return '<a data-frame-title="'.$options['title'].'" data-confirm="'.$options['confirm'].'" data-frame-src="'.$options['src'].'" href="javascript:void(0);" class="'.(!empty($options['css'])?$options['css']:'').' frame-link" '.(!empty($options['style']) ? 'style="'.$options['style'].'"' : '').'>'.$options['text'].'</a>';
        } elseif ($options['type'] == 'link') {
            if (empty($options['text']) or empty($options['href'])) {
                return '';
            }
            return '<a class="'.(!empty($options['css'])?$options['css']:'').'" '.(!empty($options['confirm']) ? 'onclick="if(!confirm(\''.$options['confirm'].'\')){return false}"' : '').' href="'.$options['href'].'" '.(!empty($options['style']) ? 'style="'.$options['style'].'"' : '').'>'.$options['text'].'</a>';
        }
    }

    public static function deleteDelayedJob($job_id, $queue = 'default') {
        $redis_queue_instance = Queue::getRedis();
        // use redis ZSCAN with MATCH to find by pattern, its similar to SQL LIKE %jobid%
        $res = $redis_queue_instance->zscan('queues:'.$queue.':delayed', 0, 'MATCH', "*$job_id*");
        if ($res) { // make sure result is found
            if (isset($res[1])) { // first element is cursor, second is array with result
                $job_arr = $res[1];
                $jid = array_keys($job_arr);
                if (isset($jid[0])) { // make sure second element is array and has index 0
                    $job = $jid[0]; // get the job id
                    // remove the job which is literally removing element from Sorted Set
                    return $redis_queue_instance->zrem('queues:'.$queue.':delayed', $job);
                }
            }
        }
        return 0; // not removed
//      throw new RuntimeException("Job id: $job_id not found for queue: $queue");
    }
}