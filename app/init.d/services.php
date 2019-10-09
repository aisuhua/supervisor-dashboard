<?php
/**
 * @var Phalcon\Di $di
 */

use Phalcon\Mvc\View;
use Phalcon\Mvc\Url as UrlResolver;
use Phalcon\Mvc\View\Engine\Volt as VoltEngine;
use Phalcon\Mvc\Model\Metadata\Memory as MemoryMetaData;
use Phalcon\Mvc\Model\MetaData\Files as FileMetaData;
use Phalcon\Session\Adapter\Files as SessionAdapter;
use Phalcon\Flash\Session as FlashSession;
use Phalcon\Flash\Direct as FlashDirect;
use Phalcon\Events\Manager as EventsManager;
use Phalcon\Logger\Adapter\File as FileLogger;
use Phalcon\Logger\Adapter\Stream as StreamLogger;
use Phalcon\Db\Adapter\Pdo\Mysql;
use Phalcon\Events\Event;
use Phalcon\Di;
use SupBoard\Supervisor\SupAgent;
use SupBoard\Model\Server;
use SupBoard\Supervisor\Supervisor;
use Phalcon\Logger\Formatter\Line as FormatterLine;

/**
 * 地址服务
 */
$di->setShared('url', function ()
{
    $url = new UrlResolver();
    $url->setBaseUri('/');

    return $url;
});

/**
 * 视图服务
 */
$di->setShared('view', function ()
{
    $view = new View();
    $view->setDI($this);
    $view->setViewsDir(PATH_APP . '/view/');

    $view->registerEngines([
        '.volt' => function ($view)
        {
            $volt = new VoltEngine($view, $this);

            $volt->setOptions([
                'compiledPath' => PATH_CACHE . '/volt/',
                'compileAlways' => DEBUG_MODE ? true : false
            ]);

            return $volt;
        }
    ]);

    return $view;
});

/**
 * 文件日志服务
 */
$di->setShared('logger', function($filename = null)
{
    $formatter = new FormatterLine(null, 'c');

    $filename = empty($filename) ? 'default.log' : $filename;
    $logger = new FileLogger(PATH_LOG . '/' . $filename);
    $logger->setFormatter($formatter);

    return $logger;
});

/**
 * 标准错误输出日志服务
 */
$di->setShared('streamLogger', function($filename = null)
{
    $formatter = new FormatterLine(null, 'c');
    $logger = new StreamLogger('php://stderr');
    $logger->setFormatter($formatter);

    return $logger;
});

/**
 * 数据库服务
 */
$di->setShared('db', function ()
{
    $connection = new Mysql([
        'host' => $GLOBALS['db']['host'],
        'port' => $GLOBALS['db']['port'],
        'username' => $GLOBALS['db']['username'],
        'password' => $GLOBALS['db']['password'],
        'dbname' => $GLOBALS['db']['dbname'],
        'charset' => $GLOBALS['db']['charset'],
    ]);

    if (!DEBUG_MODE)
    {
        return $connection;
    }

    $eventManager = new EventsManager();
    $di = $this;

    $eventManager->attach('db', function (Event $event, Mysql $connection) use ($di)
    {
        if ($event->getType() == 'beforeQuery')
        {
            $variables = $connection->getSQLVariables();
            $string = $connection->getSQLStatement();
            $context   = $variables ?: [];
            /** @var Di $di */
            $logger = $di->get('logger', ['db.log']);

            if (!empty($context))
            {
                $context = ' ' . var_export_min($context, true);
            }
            else
            {
                $context = '';
            }

            $logger->debug($string . $context);
        }
    });

    $connection->setEventsManager($eventManager);

    return $connection;
});

/**
 * 数据库元数据服务
 */
$di->setShared('modelsMetadata', function ()
{
    if (DEBUG_MODE)
    {
        return new MemoryMetaData();
    }

    return new FileMetaData([
        'metaDataDir' => PATH_CACHE . '/metadata/',
        'lifetime' => 86400
    ]);
});

/**
 * session 服务
 */
$di->setShared('session', function ()
{
    $session = new SessionAdapter();
    $session->start();

    return $session;
});

/**
 * flash session
 */
$di->set('flashSession', function ()
{
    return new FlashSession([
        'error'   => 'alert alert-danger pnotify fade',
        'success' => 'alert alert-success pnotify fade',
        'notice'  => 'alert alert-info pnotify fade',
        'warning' => 'alert alert-warning pnotify fade'
    ]);
});

/**
 * flash
 */
$di->set('flash', function ()
{
    return new FlashDirect([
        'error'   => 'alert alert-danger pnotify fade',
        'success' => 'alert alert-success pnotify fade',
        'notice'  => 'alert alert-info pnotify fade',
        'warning' => 'alert alert-warning pnotify fade'
    ]);
});

/**
 * Supervisor 服务
 */
$di->set('supervisor', function ($name, $ip, $port, $username = null, $password = null)
{
    return new Supervisor($name, $ip, $username, $password, $port);
});

/**
 * Supervisor Agent 服务
 */
$di->setShared('supAgent', function (Server $server)
{
    return new SupAgent($server);
});


