<?php namespace App\Http\Controllers;

use App\Bug;
use App\Project;
use App\Task;

class PageController extends Controller
{
    protected $bugs;
    protected $tasks;

    public function __construct(Bug $bugs, Task $tasks)
    {
        $this->bugs = $bugs;
        $this->tasks = $tasks;
    }

    public function getIndex($id)
    {
        $projectId = $id;
        $project = Project::findOrFail($projectId);

        $date = \Request::input('date', null);

        $total = array_extract_key($this->bugs->total($projectId, $date)->toArray(), 'resolvedBy');
        $severeTotal = array_extract_key($this->bugs->severeTotal($projectId, $date)->toArray(), 'resolvedBy');
        $reactivatedTotal = array_extract_key($this->bugs->reactivatedTotal($projectId, $date)->toArray(), 'resolvedBy');
        $activatedTotal = array_extract_key($this->bugs->activatedTotal($projectId, $date)->toArray(), 'assignedTo');
        $runningHours = array_extract_key($this->tasks->runningHours($projectId, $date)->toArray(), 'finishedBy');

//        var_dump($activatedTotal, $total, $severeTotal, $reactivatedTotal, $runningHours);

        $users = array_unique(array_merge(
            array_keys($total),
            array_keys($severeTotal),
            array_keys($reactivatedTotal),
            array_keys($activatedTotal),
            array_keys($runningHours)
        ));
//        var_dump($users);

        $result = array();
        foreach ($users as $user) {
            $result[$user] = array(
                'user' => strtolower($user),
                'estimate_sum' => isset($runningHours[$user]['estimate_sum']) ? $runningHours[$user]['estimate_sum'] : 0,
                'consumed_sum' => isset($runningHours[$user]['consumed_sum']) ? $runningHours[$user]['consumed_sum'] : 0,
                'activated_bug_total' => isset($activatedTotal[$user]['total']) ? $activatedTotal[$user]['total'] : 0,
                'all_bug_total' => isset($total[$user]['total']) ? $total[$user]['total'] : 0,
                'severe_bug_total' => isset($severeTotal[$user]['total']) ? $severeTotal[$user]['total'] : 0,
                'reactivated_bug_total' => isset($reactivatedTotal[$user]['total']) ? $reactivatedTotal[$user]['total'] : 0,
            );
            $result[$user] = array_merge($result[$user], $this->tasks->deviation($user, $projectId));
        }

        $records = collect($result)->sortBy('user', SORT_REGULAR, false)->toArray();

        return view('index', array(
            'project' => $project,
            'records' => $records,
        ));
    }


    public function getStatement()
    {
        // 获取参数
        $users = \Request::input('users', null);
        $start = \Request::input('start', null);
        $end = \Request::input('end', null);

        // 验证参数
        $validator = \Validator::make(
            array(
                'users' => $users,
                'start' => $start,
                'end' => $end,
            ),
            array(
                'users' => 'required',
                'start' => 'required|date_format:Y-m-d',
                'end' => 'required|date_format:Y-m-d',
            )
        );
        if ($validator->fails()) {
            return current($validator->messages()->all());
        }
        if (strtotime($start) > strtotime($end)) {
            return '起始时间必须小于截至时间';
        }

        $users = $this->getUsers($users);

        return view('statement', array(
            'users' => $users,
            'start' => $start,
            'end' => $end,
            'rows' => $this->getRows($users, $start, $end),
        ));
    }


    protected function getUsers($users)
    {
        // 初步处理 users
        $users = array_map(function ($user) {
            return trim($user);
        }, explode('|', $users));

        // 所有合法账号
        $accounts = \DB::table('zt_user')->lists('account');

        // 取交集，得到合法的 users
        return array_intersect($accounts, $users);
    }


    protected function getRows($users, $start, $end)
    {
//        $bugs = $this->bugs->where('openedDate', '>=', $start)
//            ->where('openedDate', '<=', $end)
//            ->get();

//        $bugs = $this->bugs->where('openedDate', '>=', $start)
//            ->where('openedDate', '<=', $end)
//            ->where(function ($query) use ($users) {
//                $query->whereIn('assignedTo', $users)
//                    ->orWhereIn('resolvedBy', $users);
//            })
//            ->groupBy('id')
//            ->get();

        // 统计数据
        $rows = array();
        foreach ($users as $user) {
            $bugs = $this->bugs->where('openedDate', '>=', $start)
                ->where('openedDate', '<=', $end)
                ->where(function ($query) use ($user) {
                    $query->where('assignedTo', $user)
                        ->orWhere('resolvedBy', $user);
                })->groupBy('id')->get();


            $tasks = $this->tasks->where('status', 'done')
                ->where('finishedBy', $user)
                ->where(function ($query) use ($start, $end) {
                    $query->where(function ($q1) use ($start, $end) {
                        $q1->where('estStarted', '>=', $start)->where('estStarted', '<=', $end);
                    })->orWhere(function ($q2) use ($start, $end) {
                        $q2->where('realStarted', '>=', $start)->where('realStarted', '<=', $end);
                    });
                })->get();


            $row = array(
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
                })),
                'assigned' => $bugs->where('assignedTo', $user)->count(),
                'resolved' => $bugs->where('resolvedBy', $user)->count(),
                'efficient_resolved' => $bugs->where('resolvedBy', $user)->filter(function ($bug) {
                    return in_array(data_get($bug, 'resolution'), Bug::$efficientResolution);
                })->count(),
                'resolved_severity' => $bugs->where('resolvedBy', $user)->filter(function ($bug) {
                    return in_array(data_get($bug, 'severity'), Bug::$efficientSeverity);
                })->count(),
                'efficient_resolved_severity' => $bugs->where('resolvedBy', $user)->filter(function ($bug) {
                    return in_array(data_get($bug, 'severity'), Bug::$efficientSeverity) &&
                    in_array(data_get($bug, 'resolution'), Bug::$efficientResolution);
                })->count(),
                'resolved_activated' => $bugs->where('resolvedBy', $user)->filter(function ($bug) {
                    return data_get($bug, 'activatedCount') > 0;
                })->count(),
                'efficient_resolved_activated' => $bugs->where('resolvedBy', $user)->filter(function ($bug) {
                    return data_get($bug, 'activatedCount') > 0 &&
                    in_array(data_get($bug, 'resolution'), Bug::$efficientResolution);
                })->count(),
            );

            $rows[$user] = $row;
        }

        return $rows;
    }


    public function getDeviations($id, $name)
    {
        $project = Project::findOrFail($id);
        $tasks = $project->userDoneTasks($name);

        return view('deviations', array(
            'project' => $project,
            'tasks' => $tasks,
        ));
    }


    public function getReactivated($id, $name)
    {
        $project = Project::findOrFail($id);
        $reactivatedBugs = $project->userReactivatedBugs($name);

        return view('reactivated', array(
            'project' => $project,
            'bugs' => $reactivatedBugs,
        ));
    }
}
