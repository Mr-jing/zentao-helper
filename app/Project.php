<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    protected $table = 'zt_project';

    public $timestamps = false;


    public function userDoneTasks($user)
    {
        return $this->hasMany('App\Task', 'project')
            ->UserDone($user)
            ->get();
    }

    public function userReactivatedBugs($user)
    {
        return $this->hasMany('App\Bug', 'project')
            ->userReactivated($user)
            ->get();
    }

}