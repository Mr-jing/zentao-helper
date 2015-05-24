<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Bug extends Model
{
    protected $table = 'zt_bug';

    public $timestamps = false;

    public function total($projectId)
    {
        return $this->project($projectId)
            ->groupBy('resolvedBy')
            ->get();
    }


    public function severeTotal($projectId)
    {
        return $this->project($projectId)
            ->severity()
            ->groupBy('resolvedBy')
            ->get();
    }

    public function reactivatedTotal($projectId)
    {
        return $this->project($projectId)
            ->reactivated()
            ->groupBy('resolvedBy')
            ->get();
    }


    public function scopeProject($query, $projectId)
    {
        return $query->select(DB::raw('count(*) as total, resolvedBy'))
            ->where('project', $projectId)
            ->where('resolvedBy', '!=', '')
            ->whereIn('resolution', array('fixed', 'notrepro', 'postponed'));
    }


    /**
     * 严重的bug（1级或者2级）
     *
     * @param $query
     * @return mixed
     */
    public function scopeSeverity($query)
    {
        return $query->whereIn('severity', array(1, 2));
    }

    /**
     * 重复激活的bug
     *
     * @param $query
     * @return mixed
     */
    public function scopeReactivated($query)
    {
        return $query->where('activatedCount', '>', 0);
    }


}