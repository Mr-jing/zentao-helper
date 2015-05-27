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


$app->get(ROUTE_BASE . '/deviations/{id:\d+}/{name:\w+}', array(
    'as' => 'deviations',
    'uses' => 'App\Http\Controllers\PageController@getDeviations',
));

$app->get(ROUTE_BASE . '/reactivated/{id:\d+}/{name:\w+}', array(
    'as' => 'reactivated',
    'uses' => 'App\Http\Controllers\PageController@getReactivated',
));
