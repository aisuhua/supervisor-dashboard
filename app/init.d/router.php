<?php
/**
 * @var Phalcon\Mvc\Router $router;
 * @var Phalcon\Di $di;
 */

$router = $di->get('router');

$router->add('/process', [
    'controller' => 'process-manager',
    'action' => 'index'
]);

$router->add('/process/log', [
    'controller' => 'process-manager',
    'action' => 'log'
]);

$router->add('/process/all', [
    'controller' => 'manager',
    'action' => 'processAll'
]);

$router->add('/process/list', [
    'controller' => 'manager',
    'action' => 'processList'
]);

$router->add('/cron/all', [
    'controller' => 'manager',
    'action' => 'cronAll'
]);

$router->add('/cron/list', [
    'controller' => 'manager',
    'action' => 'cronList'
]);

// 404 Not Found
$router->notFound([
    'controller' => 'error',
    'action' => 'show404'
]);

$router->handle();