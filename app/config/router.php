<?php

$router = $di->getRouter();

// Define your routes here
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

$router->add(
    '/server/{server_id:[0-9]+}/program',
    [
        'controller' => 'program',
        'action' => 'index'
    ]
);

$router->handle();
