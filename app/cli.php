<?php
use Phalcon\Di\FactoryDefault\Cli as CliDI;
use Phalcon\Cli\Console as ConsoleApp;

error_reporting(E_ALL);

define('BASE_PATH', dirname(__DIR__));
define('APP_PATH', BASE_PATH . '/app');

// 注册服务
$di = new CliDI();
include APP_PATH . '/config/services.php';

// 注册自动加载目录
$config = $di->getConfig();
include APP_PATH . '/config/loader.php';

$console = new ConsoleApp();
$console->setDI($di);

$arguments = [];

foreach ($argv as $k => $arg)
{
    if ($k === 1)
    {
        $arguments['task'] = $arg;
    }
    elseif ($k === 2)
    {
        $arguments['action'] = $arg;
    }
    elseif ($k >= 3)
    {
        $arguments['params'][] = $arg;
    }
}

try
{
    $console->handle($arguments);
}
catch (\Phalcon\Exception $e)
{
    fwrite(STDERR, $e->getMessage() . PHP_EOL);
    exit(1);
}
catch (\Throwable $throwable)
{
    fwrite(STDERR, $throwable->getMessage() . PHP_EOL);
    exit(1);
}
catch (\Exception $exception)
{
    fwrite(STDERR, $exception->getMessage() . PHP_EOL);
    exit(1);
}
