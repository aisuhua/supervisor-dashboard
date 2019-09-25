<?php
require __DIR__ . '/../init.php';
require PATH_INIT . '/router.php';

use Phalcon\Mvc\Application;
use Phalcon\Di;
use Phalcon\Mvc\Dispatcher;

/** @var Di $di */
$di->set('dispatcher', function () {
    $dispatcher = new Dispatcher();
    $dispatcher->setDefaultNamespace('SupBoard\Controller');
    return $dispatcher;
});

$application = new Application($di);
echo $application->handle()->getContent();