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
            ->where('finishedBy', '!=', '')
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
//        return $this->select('finishedBy', 'estimate', 'consumed', 'deadline', 'finishedDate')
        return $this
            ->where('finishedBy', $account)
            ->where('project', $projectId)
            ->get();
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

}