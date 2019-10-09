<?php
require __DIR__ . '/../init.php';
require PATH_INIT . '/router.php';

use Phalcon\Mvc\Application;
use Phalcon\Di;
use Phalcon\Mvc\Dispatcher;
use Phalcon\Events\Manager as EventsManager;
use Phalcon\Events\Event;

/** @var Di $di */
$di->set('dispatcher', function () {
    $dispatcher = new Dispatcher();
    $dispatcher->setDefaultNamespace('SupBoard\Controller');

//    $eventsManager = new EventsManager();
//    $eventsManager->attach('beforeException', function (Event $event, $dispatcher) {});

    return $dispatcher;
});

$application = new Application($di);
echo $application->handle()->getContent();