<?php

define('ROUTE_BASE', 'zentao-helper/public');

$app->get(ROUTE_BASE . '/project/{id:\d+}', array(
    'as' => 'home',
    'uses' => 'App\Http\Controllers\PageController@getIndex',
));

$app->get(ROUTE_BASE . '/project/{id:\d+}/u/{name:\w+}', array(
    'as' => 'person',
    'uses' => 'App\Http\Controllers\PageController@getShow',
));

// 报表页面
$app->get(ROUTE_BASE . '/statement', array(
    'as' => 'statement',
    'uses' => 'App\Http\Controllers\PageController@getStatement',
));

$app->get(ROUTE_BASE . '/deviations/{id:\d+}/{name:\w+}', array(
    'as' => 'deviations',
    'uses' => 'App\Http\Controllers\PageController@getDeviations',
));

$app->get(ROUTE_BASE . '/reactivated/{id:\d+}/{name:\w+}', array(
    'as' => 'reactivated',
    'uses' => 'App\Http\Controllers\PageController@getReactivated',
));

// bug 搜索页
$app->get(ROUTE_BASE . '/bug/search', array(
    'as' => 'bug_search',
    'uses' => 'App\Http\Controllers\BugController@search',
));

// 任务偏差详情页面
$app->get(ROUTE_BASE . '/task/deviations', array(
    'as' => 'task_deviations',
    'uses' => 'App\Http\Controllers\TaskController@deviations',
));
