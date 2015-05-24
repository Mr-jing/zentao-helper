<?php

define('ROUTE_BASE', 'zentao-helper/public');

$app->get(ROUTE_BASE . '/index', function () use ($app) {
    return $app->welcome();
});
