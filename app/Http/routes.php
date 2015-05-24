<?php

define('ROUTE_BASE', 'zentao-helper/public');

$app->get(ROUTE_BASE . '/index', array(
    'as' => 'home',
    'uses' => 'App\Http\Controllers\PageController@getIndex',
));
