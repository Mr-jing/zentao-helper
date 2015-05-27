<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Task extends Model
{
    protected $table = 'zt_task';

    public $timestamps = false;

    public function user()
    {
        return $this->belongsTo('App\User', 'finishedBy', 'account');
    }


    /**
     * 日期转换器
     */
    public function getDates()
    {
        return array(
            'deadline', // 任务预计截至时间
            'finishedDate', // 任务实际完成时间
        );
    }


    /**
     * 获取工时
     *
     * @param $projectId
     * @return mixed
     */
    public function runningHours($projectId)
    {
        return $this->select(DB::raw('SUM(consumed) as consumed_sum, SUM(estimate) as estimate_sum, finishedBy'))
            ->where('project', $projectId)
//            ->where('finishedBy', '!=', '')
            ->where('status', 'done')
            ->groupBy('finishedBy')
            ->get();
    }


    /**
     * 获取用户在某个项目中的任务列表
     *
     * @param $account
     * @param $projectId
     * @return mixed
     */
    public function getUserTasksByProjectId($account, $projectId)
    {
        return $this->select('finishedBy', 'estimate', 'consumed', 'deadline', 'finishedDate')
//        return $this
            ->where('status', 'done')
            ->where('finishedBy', $account)
            ->where('project', $projectId)
            ->get();
    }


    public function scopeUserDone($query, $account)
    {
        return $query->where('status', 'done')
            ->where('finishedBy', $account);
    }

    /**
     * 计算工时、工期的偏差
     *
     * @param $account
     * @param $projectId
     * @return array
     */
    public function deviation($account, $projectId)
    {
        $tasks = $this->getUserTasksByProjectId($account, $projectId);

        $result = array(
            'hour_plus_deviation' => 0,
            'hour_minus_deviation' => 0,
            'day_plus_deviation' => 0,
            'day_minus_deviation' => 0,
        );
        foreach ($tasks as $task) {
            $hourDiff = $task->estimate - $task->consumed;
            $dayDiff = $task->deadline->diffInDays($task->finishedDate, false);

            if ($hourDiff > 0) {
                $result['hour_plus_deviation'] += $hourDiff;
            } else {
                $result['hour_minus_deviation'] += $hourDiff;
            }
            if ($dayDiff < 0) {
                $result['day_plus_deviation'] += $dayDiff;
            } else {
                $result['day_minus_deviation'] += $dayDiff;
            }
        }
        return $result;
    }


    public function getHourPlusDeviation()
    {
        $hourDiff = $this->estimate - $this->consumed;
        if ($hourDiff > 0) {
            return $hourDiff;
        } else {
            return 0;
        }

    }

    public function getHourMinusDeviation()
    {
        $hourDiff = $this->estimate - $this->consumed;
        if ($hourDiff < 0) {
            return $hourDiff;
        } else {
            return 0;
        }

    }

    public function getDayPlusDeviation()
    {
        $dayDiff = $this->deadline->diffInDays($this->finishedDate, false);

        if ($dayDiff < 0) {
            return $dayDiff;
        } else {
            return 0;
        }
    }

    public function getDayMinusDeviation()
    {
        $dayDiff = $this->deadline->diffInDays($this->finishedDate, false);

        if ($dayDiff > 0) {
            return $dayDiff;
        } else {
            return 0;
        }
    }

}