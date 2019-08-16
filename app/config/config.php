<?php
use Phalcon\Config;

return new Config([
    'database' => [
        'adapter' => 'Mysql',
        'host' => '192.168.1.229',
        'username' => 'root',
        'password' => 'suhua123',
        'dbname' => 'hack',
        'charset' => 'utf8mb4',
    ],
    'application' => [
        'appDir' => APP_PATH,
        'controllerDir' => APP_PATH . '/controller/',
        'modelDir' => APP_PATH . '/model/',
        'viewDir' => APP_PATH . '/view/',
        'libraryDir' => APP_PATH . '/library/',
        'taskDir' => APP_PATH . '/task/',
        'baseUri' => '/'
    ],
    'volt' => [
        'cacheDir' => APP_PATH . '/cache/volt/'
    ],

]);
