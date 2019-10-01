<?php

use Phalcon\Mvc\View;
use Phalcon\Mvc\Url as UrlResolver;
use Phalcon\Mvc\View\Engine\Volt as VoltEngine;
use Phalcon\Mvc\Model\Metadata\Memory as MetaDataAdapter;
use Phalcon\Session\Adapter\Files as SessionAdapter;
use Phalcon\Flash\Session as FlashSession;
use Phalcon\Flash\Direct as FlashDirect;
use Phalcon\Events\Manager as EventsManager;
use Phalcon\Logger\Adapter\File as FileLogger;
use Phalcon\Db\Adapter\Pdo\Mysql;
use Phalcon\Events\Event;
use Phalcon\Di;
use SupBoard\Supervisor\SupAgent;
use SupBoard\Model\Server;
use SupBoard\Supervisor\Supervisor;

/** @var Di $di */
$di->setShared('url', function () {
    $url = new UrlResolver();
    $url->setBaseUri('/');

    return $url;
});

/**
 * Setting up the view component
 */
$di->setShared('view', function () {
    $view = new View();
    $view->setDI($this);
    $view->setViewsDir(PATH_APP . '/view/');

    $view->registerEngines([
        '.volt' => function ($view) {
            $volt = new VoltEngine($view, $this);

            $volt->setOptions([
                'compiledPath' => PATH_CACHE . '/volt/',
                'compileAlways' => DEBUG ? true : false
            ]);

            return $volt;
        }
    ]);

    return $view;
});

/**
 * 日志服务
 */
$di->setShared('logger', function($filename = null) {
    $filename = empty($filename) ? 'default.log' : $filename;
    $logger = new FileLogger(PATH_LOG . '/' . $filename);

    return $logger;
});

/**
 * Database connection is created based in the parameters defined in the configuration file
 */
$di->setShared('db', function () {
    $connection = new Mysql([
        'host' => $GLOBALS['db']['host'],
        'username' => $GLOBALS['db']['username'],
        'password' => $GLOBALS['db']['password'],
        'dbname' => $GLOBALS['db']['dbname'],
        'charset' => $GLOBALS['db']['charset'],
    ]);

    $em = new EventsManager();
    $di = $this;

    $em->attach('db', function (Event $event, Mysql $connection) use ($di) {
        if ($event->getType() == 'beforeQuery')
        {
            $variables = $connection->getSQLVariables();
            $string = $connection->getSQLStatement();

            if ($variables)
            {
                if (is_array(current($variables)))
                {
                    $string .= ' [' . join(',', current($variables)) . ']';
                }
                else
                {
                    $string .= ' [' . join(',', $variables) . ']';
                }
            }

            /** @var Di $di */
            $di->get('logger', ['db.log'])->debug($string);
        }
    });

    $connection->setEventsManager($em);

    return $connection;
});


$di->setShared('modelsMetadata', function () {
    return new MetaDataAdapter();
});

$di->setShared('session', function () {
    $session = new SessionAdapter();
    $session->start();

    return $session;
});

$di->set('flashSession', function () {
    return new FlashSession([
        'error'   => 'alert alert-danger pnotify fade',
        'success' => 'alert alert-success pnotify fade',
        'notice'  => 'alert alert-info pnotify fade',
        'warning' => 'alert alert-warning pnotify fade'
    ]);
});

$di->set('flash', function () {
    $flash = new FlashDirect([
        'error'   => 'alert alert-danger pnotify fade',
        'success' => 'alert alert-success pnotify fade',
        'notice'  => 'alert alert-info pnotify fade',
        'warning' => 'alert alert-warning pnotify fade'
    ]);

    return $flash;
});

$di->set('supervisor', function ($name, $ip, $port, $username = null, $password = null) {
    return new Supervisor($name, $ip, $username, $password, $port);
});

$di->setShared('supAgent', function (Server $server) {
    return new SupAgent($server);
});


