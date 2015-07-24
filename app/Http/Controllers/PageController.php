<?php namespace App\Http\Controllers;

use App\Bug;
use App\Project;
use App\Task;
use Illuminate\Support\Facades\DB;

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
//        var_dump($result);

        $records = collect($result)->sortBy('user', SORT_REGULAR, false)->toArray();

//        var_dump(DB::getQueryLog());

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
        $end = \Request::input('end', date('Y-m-d'));

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
//        $bugs = $this->bugs->assignedTo(array('king'))->start($start)->end($end)->get();

        // bug 按照创建时间
        // task 按照预计开始时间、实际开始时间中小的那个
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
