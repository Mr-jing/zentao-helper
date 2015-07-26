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
        $resolvedBy = \Request::input('resolvedBy', null);
        $severity = \Request::input('severity', null);

        $validator = \Validator::make(
            array(
                'openedDateStart' => $openedDateStart,
                'openedDateEnd' => $openedDateEnd,
                'reactivated' => $reactivated,
                'resolvedBy' => $resolvedBy,
                'severity' => $severity,
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


        $query = $this->bugs;

        if (is_string($resolvedBy)) {
            $query = $query->where('resolvedBy', $resolvedBy);
        }
        if ($reactivated) {
            $query = $query->where('activatedCount', '>', 0);
        }
        if ($severity) {
            $query = $query->whereIn('severity', explode('|', $severity));
        }
        if ($openedDateStart) {
            $query = $query->where('openedDate', '>=', $openedDateStart);
        }
        if ($openedDateEnd) {
            $query = $query->where('openedDate', '<=', $openedDateEnd);
        }


        echo '<pre>';
        var_dump($openedDateStart);
        var_dump($openedDateEnd);
        var_dump($reactivated);
        var_dump($resolvedBy);
        var_dump($severity);

        var_dump(count($query->get()));
        dd(\DB::getQueryLog());
    }

}
