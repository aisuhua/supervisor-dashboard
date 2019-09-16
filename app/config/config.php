<?php
use Phalcon\Config;

return new Config([
    'database' => [
        'adapter' => 'Mysql',
        'host' => '192.168.1.229',
        'username' => 'root',
        'password' => 'lala123',
        'dbname' => 'supervisor',
        'charset' => 'utf8mb4',
    ],
    'application' => [
        'appDir' => APP_PATH,
        'controllerDir' => APP_PATH . '/controller/',
        'modelDir' => APP_PATH . '/model/',
        'viewDir' => APP_PATH . '/view/',
        'formDir' => APP_PATH . '/form/',
        'libraryDir' => APP_PATH . '/library/',
        'taskDir' => APP_PATH . '/task/',
        'cronDir' => APP_PATH . '/cron/',
        'baseUri' => '/'
    ],
    'volt' => [
        'compiledPath' => APP_PATH . '/cache/volt/',
        'compileAlways' => true
    ],
    'logger' => [
        'logDir' => APP_PATH . '/log/'
    ]

]);
