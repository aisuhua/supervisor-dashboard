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

/**
 * Supervisor 配置管理
 */
$router->add(
    '/server/{server_id:[0-9]+}/config',
    [
        'controller' => 'supervisor-config',
        'action' => 'index'
    ]
);

$router->add(
    '/server/{server_id:[0-9]+}/config/edit/{id:[0-9]+}',
    [
        'controller' => 'supervisor-config',
        'action' => 'edit'
    ]
);

$router->add(
    '/server/{server_id:[0-9]+}/config/create',
    [
        'controller' => 'supervisor-config',
        'action' => 'create'
    ]
);

$router->add(
    '/server/{server_id:[0-9]+}/config/delete',
    [
        'controller' => 'supervisor-config',
        'action' => 'delete'
    ]
);

$router->add(
    '/server/{server_id:[0-9]+}/config/ini-mode',
    [
        'controller' => 'supervisor-config',
        'action' => 'iniMode'
    ]
);

$router->add(
    '/server/{server_id:[0-9]+}/config/load-server',
    [
        'controller' => 'supervisor-config',
        'action' => 'loadServer'
    ]
);

/**
 * 服務器進程管理
 */

// 进程管理
$router->add(
    '/server/{server_id:[0-9]+}/process',
    [
        'controller' => 'process',
        'action' => 'index'
    ]
);

$router->add(
    '/server/{server_id:[0-9]+}/process/create',
    [
        'controller' => 'process',
        'action' => 'create'
    ]
);

$router->add(
    '/server/{server_id:[0-9]+}/process/edit',
    [
        'controller' => 'process',
        'action' => 'edit'
    ]
);

$router->add(
    '/server/{server_id:[0-9]+}/process/stopall',
    [
        'controller' => 'process',
        'action' => 'stopAll'
    ]
);

$router->add(
    '/server/{server_id:[0-9]+}/process/restartall',
    [
        'controller' => 'process',
        'action' => 'restartAll'
    ]
);

$router->add(
    '/server/{server_id:[0-9]+}/process/{name:[^:]+:[^:]+}/stop',
    [
        'controller' => 'process',
        'action' => 'stop'
    ]
);

$router->add(
    '/server/{server_id:[0-9]+}/process/{name:[^:]+:[^:]+}/start',
    [
        'controller' => 'process',
        'action' => 'start'
    ]
);

$router->add(
    '/server/{server_id:[0-9]+}/process/{name:[^:]+:[^:]+}/restart',
    [
        'controller' => 'process',
        'action' => 'restart'
    ]
);

$router->add(
    '/server/{server_id:[0-9]+}/process/{name:[^:]+:[^:]+}/taillog',
    [
        'controller' => 'process',
        'action' => 'tailLog'
    ]
);

$router->add(
    '/server/{server_id:[0-9]+}/process/{name:[^:]+:[^:]+}/clearlog',
    [
        'controller' => 'process',
        'action' => 'clearLog'
    ]
);

// 进程组管理
$router->add(
    '/server/{server_id:[0-9]+}/process/{name:[^:]+}/stop',
    [
        'controller' => 'process',
        'action' => 'stopGroup'
    ]
);


$router->add(
    '/server/{server_id:[0-9]+}/process/{name:[^:]+}/start',
    [
        'controller' => 'process',
        'action' => 'startGroup'
    ]
);

$router->add(
    '/server/{server_id:[0-9]+}/process/{name:[^:]+}/restart',
    [
        'controller' => 'process',
        'action' => 'restartGroup'
    ]
);

$router->add(
    '/server/{server_id:[0-9]+}/process/reload-config',
    [
        'controller' => 'process',
        'action' => 'reloadConfig'
    ]
);


$router->handle();
