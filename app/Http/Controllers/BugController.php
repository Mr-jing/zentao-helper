<?php namespace App\Http\Controllers;

use App\Bug;

class BugController extends Controller
{
    protected $bugs;

    public function __construct(Bug $bugs)
    {
        $this->bugs = $bugs;
    }


    public function search()
    {
        $openedDateStart = \Request::input('openedDateStart', null);
        $openedDateEnd = \Request::input('openedDateEnd', null);
        $reactivated = \Request::input('reactivated', null);
        $assignedTo = \Request::input('assignedTo', null);
        $resolvedBy = \Request::input('resolvedBy', null);
        $severity = \Request::input('severity', null);
        $resolution = \Request::input('resolution', null);

        $validator = \Validator::make(
            array(
                'openedDateStart' => $openedDateStart,
                'openedDateEnd' => $openedDateEnd,
                'reactivated' => $reactivated,
                'assignedTo' => $assignedTo,
                'resolvedBy' => $resolvedBy,
                'severity' => $severity,
                'resolution' => $resolution,
            ),
            array(
                'openedDateStart' => 'date_format:Y-m-d',
                'openedDateEnd' => 'date_format:Y-m-d',
            )
        );

        if ($validator->fails()) {
            return current($validator->messages()->all());
        }
        if ($openedDateStart &&
            $openedDateEnd &&
            strtotime($openedDateStart) > strtotime($openedDateEnd)
        ) {
            return '起始时间必须小于截至时间';
        }
//        dd(\Request::all());

        $query = $this->bugs;

        if (is_string($assignedTo)) {
            $query = $query->where('assignedTo', $assignedTo);
        }
        if (is_string($resolvedBy)) {
            $query = $query->where('resolvedBy', $resolvedBy);
        }
        if ($reactivated) {
            $query = $query->where('activatedCount', '>', 0);
        }
        if ($severity) {
            $query = $query->whereIn('severity', explode('|', $severity));
        }
        if ($resolution) {
            $query = $query->whereIn('resolution', explode('|', $resolution));
        }
        if ($openedDateStart) {
            $query = $query->where('openedDate', '>=', $openedDateStart);
        }
        if ($openedDateEnd) {
            $query = $query->where('openedDate', '<=', $openedDateEnd);
        }

        return view('bug', array(
            'bugs' => $query->get(),
        ));
    }

}
