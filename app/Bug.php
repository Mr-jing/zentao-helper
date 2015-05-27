<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Bug extends Model
{
    protected $table = 'zt_bug';

    public $timestamps = false;

    public static $friendlyStatus = array(
        '' => '',
        'active' => '激活',
        'resolved' => '已解决',
        'closed' => '已关闭',
    );


    public static $friendlyResolution = array(
        '' => '',
        'bydesign' => '设计如此',
        'duplicate' => '重复Bug',
        'external' => '外部原因',
        'fixed' => '已解决',
        'notrepro' => '无法重现',
        'postponed' => '延期处理',
        'willnotfix' => '不予解决',
        'tostory' => '转为需求',
    );

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

    public function activatedTotal($projectId)
    {
        return $this->select(DB::raw('count(*) as total, assignedTo'))
            ->where('project', $projectId)
            ->where('status', 'active')
            ->groupBy('assignedTo')
            ->get();
    }

    public function scopeProject($query, $projectId)
    {
        return $query->select(DB::raw('count(*) as total, resolvedBy'))
            ->where('project', $projectId)
            ->where('resolvedBy', '!=', '')
//                ->where('')
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

    public function scopeUserReactivated($query, $account)
    {
        return $query->where('activatedCount', '>', 0)
            ->where(function ($query) use ($account) {
                $query->where('resolvedBy', $account)
                    ->orWhere('assignedTo', $account);
            });
    }

    public function getFriendlyStatus()
    {
        return self::$friendlyStatus[$this->status];
    }

    public function getFriendlyResolution()
    {
        return self::$friendlyResolution[$this->resolution];
    }


}