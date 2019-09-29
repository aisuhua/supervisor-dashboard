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

$router->handle();