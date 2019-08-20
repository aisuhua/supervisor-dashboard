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

/**
 * Shared configuration service
 */
$di->setShared('config', function () {
    return include APP_PATH . "/config/config.php";
});

/**
 * The URL component is used to generate all kind of urls in the application
 */
$di->setShared('url', function () {
    $config = $this->getConfig();

    $url = new UrlResolver();
    $url->setBaseUri($config->application->baseUri);

    return $url;
});

/**
 * Setting up the view component
 */
$di->setShared('view', function () {
    $config = $this->getConfig();

    $view = new View();
    $view->setDI($this);
    $view->setViewsDir($config->application->viewDir);

    $view->registerEngines([
        '.volt' => function ($view) {
            $config = $this->getConfig();

            $volt = new VoltEngine($view, $this);

            $volt->setOptions([
                'compiledPath' => $config->volt->cacheDir
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
    $config = $this->getConfig();
    $filename = empty($filename) ? 'default.log' : $filename;
    $logger = new FileLogger($config->logger->logDir . $filename);

    return $logger;
});

/**
 * Database connection is created based in the parameters defined in the configuration file
 */
$di->setShared('db', function () {
    $config = $this->getConfig();

    $class = 'Phalcon\Db\Adapter\Pdo\\' . $config->database->adapter;
    $params = [
        'host' => $config->database->host,
        'username' => $config->database->username,
        'password' => $config->database->password,
        'dbname' => $config->database->dbname,
        'charset' => $config->database->charset
    ];

    $connection = new $class($params);

    $em = new EventsManager();
    $di = $this;

    $em->attach(
        'db',
        function ($event, $connection) use ($di)
        {
            if ($event->getType() == 'beforeQuery')
            {
                $variables = $connection->getSQLVariables();
                $string    = $connection->getSQLStatement();

                if ($variables)
                {
                    $string .= ' [' . join(',', $variables) . ']';
                }

                $di->get('logger', ['db.log'])->debug($string);
            }
        }
    );

    $connection->setEventsManager($em);

    return $connection;
});


/**
 * If the configuration specify the use of metadata adapter use it or use memory otherwise
 */
$di->setShared('modelsMetadata', function () {
    return new MetaDataAdapter();
});

/**
 * Start the session the first time some component request the session service
 */
$di->setShared('session', function () {
    $session = new SessionAdapter();
    $session->start();

    return $session;
});

/**
 * Register the session flash service with the Twitter Bootstrap classes
 */
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

//    $flashBootstrap->setAutoescape(false);
//    $flashBootstrap->setAutomaticHtml(false);
    return $flash;
});



