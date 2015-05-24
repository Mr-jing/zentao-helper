<?php namespace App\Http\Controllers;

use App\Bug;
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

    public function getIndex()
    {
        $total = array_extract_key($this->bugs->total(2)->toArray(), 'resolvedBy');
        $severeTotal = array_extract_key($this->bugs->severeTotal(2)->toArray(), 'resolvedBy');
        $reactivatedTotal = array_extract_key($this->bugs->reactivatedTotal(2)->toArray(), 'resolvedBy');
        $runningHours = array_extract_key($this->tasks->runningHours(1)->toArray(), 'finishedBy');

        var_dump($total, $severeTotal, $reactivatedTotal, $runningHours);

        $users = array_unique(array_merge(
            array_keys($total),
            array_keys($severeTotal),
            array_keys($reactivatedTotal),
            array_keys($runningHours)
        ));
        var_dump($users);

        $result = array();
        foreach ($users as $user) {
            $result[$user] = array(
                'estimate_sum' => isset($runningHours[$user]['estimate_sum']) ? $runningHours[$user]['estimate_sum'] : 0,
                'consumed_sum' => isset($runningHours[$user]['consumed_sum']) ? $runningHours[$user]['consumed_sum'] : 0,
                'all_bug_total' => isset($total[$user]['total']) ? $total[$user]['total'] : 0,
                'severe_bug_total' => isset($severeTotal[$user]['total']) ? $severeTotal[$user]['total'] : 0,
                'reactivated_bug_total' => isset($reactivatedTotal[$user]['total']) ? $reactivatedTotal[$user]['total'] : 0,
            );
        }
        var_dump($result);

        var_dump(DB::getQueryLog());
        return 'hi';
    }
}
