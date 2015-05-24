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


    public function runningHours($projectId)
    {
        return $this->select(DB::raw('SUM(consumed) as consumed_sum, SUM(estimate) as estimate_sum, finishedBy'))
            ->where('project', $projectId)
            ->where('finishedBy', '!=', '')
            ->groupBy('finishedBy')
            ->get();
    }

}