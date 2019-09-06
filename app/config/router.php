<?php

/**
 * @var Phalcon\Mvc\Router $router;
 */
$router = $di->getRouter();

/**
 * 分組服務器管理
 */
$router->add(
    '/server-group/{server_group_id:[0-9]+}/server',
    [
        'controller' => 'server',
        'action' => 'index'
    ]
);

$router->add(
    '/server-group/{server_group_id:[0-9]+}/server/create',
    [
        'controller' => 'server',
        'action' => 'create'
    ]
);

$router->add(
    '/server-group/{server_group_id:[0-9]+}/server/delete',
    [
        'controller' => 'server',
        'action' => 'delete'
    ]
);

$router->add(
    '/server-group/{server_group_id:[0-9]+}/server/edit/{id:[0-9]+}',
    [
        'controller' => 'server',
        'action' => 'edit'
    ]
);

/**
 * 服务器 Supervisor 管理
 */
$router->add(
    '/server/{server_id:[0-9]+}/supervisor/shutdown',
    [
        'controller' => 'supervisor',
        'action' => 'shutdown'
    ]
);

$router->add(
    '/server/{server_id:[0-9]+}/supervisor/restart',
    [
        'controller' => 'supervisor',
        'action' => 'restart'
    ]
);

$router->add(
    '/server/{server_id:[0-9]+}/supervisor/readlog',
    [
        'controller' => 'supervisor',
        'action' => 'readLog'
    ]
);

$router->add(
    '/server/{server_id:[0-9]+}/supervisor/clearlog',
    [
        'controller' => 'supervisor',
        'action' => 'clearLog'
    ]
);

$router->add(
    '/server/{server_id:[0-9]+}/supervisor/status',
    [
        'controller' => 'supervisor',
        'action' => 'status'
    ]
);

$router->handle();
