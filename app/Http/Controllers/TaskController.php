<?php namespace App\Http\Controllers;

use App\Task;

class TaskController extends Controller
{
    protected $tasks;

    public function __construct(Task $tasks)
    {
        $this->tasks = $tasks;
    }


    public function deviations()
    {
        $project = \Request::input('project', null);
        $status = \Request::input('status', null);
        $finishedBy = \Request::input('finishedBy', null);
        $start = \Request::input('start', null);
        $end = \Request::input('end', null);

        $validator = \Validator::make(
            \Request::all(),
            array(
                'project' => 'integer',
                'status' => 'in:' . implode(',', Task::$status),
                'start' => 'date_format:Y-m-d',
                'end' => 'date_format:Y-m-d',
            )
        );
        if ($validator->fails()) {
            return current($validator->messages()->all());
        }
        if ($start && $end && strtotime($start) > strtotime($end)
        ) {
            return '起始时间必须小于截至时间';
        }
//        dd(\Request::all());

        $query = $this->tasks;

        if ($project) {
            $query = $query->where('project', $project);
        }
        if ($status) {
            $query = $query->where('status', $status);
        }
        if (is_string($finishedBy)) {
            $query = $query->where('finishedBy', $finishedBy);
        }
        if ($start) {
            $query = $query->where(function ($q) use ($start) {
                $q->where('estStarted', '>=', $start)
                    ->orWhere('realStarted', '>=', $start);
            });
        }
        if ($end) {
            $query = $query->where(function ($q) use ($end) {
                $q->where('estStarted', '<=', $end)
                    ->orWhere('realStarted', '<=', $end);
            });
        }

        $tasks = $query->get();

        // 总的统计数据
        $total = array(
            'estimate_sum' => $tasks->sum('estimate'),
            'consumed_sum' => $tasks->sum('consumed'),
            'hour_plus_deviation' => abs($tasks->sum(function ($task) {
                return $task->getHourPlusDeviation();
            })),
            'hour_minus_deviation' => abs($tasks->sum(function ($task) {
                return $task->getHourMinusDeviation();
            })),
            'day_plus_deviation' => abs($tasks->sum(function ($task) {
                return $task->getDayPlusDeviation();
            })),
            'day_minus_deviation' => abs($tasks->sum(function ($task) {
                return $task->getDayMinusDeviation();
            }))
        );

        return view('deviations2', array(
            'tasks' => $tasks,
            'total' => $total,
        ));
    }

}
