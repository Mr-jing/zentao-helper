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
