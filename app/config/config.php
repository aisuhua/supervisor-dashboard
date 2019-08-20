<?php
use Phalcon\Config;

return new Config([
    'database' => [
        'adapter' => 'Mysql',
        'host' => '192.168.1.229',
        'username' => 'root',
        'password' => 'suhua123',
        'dbname' => 'supervisor_new',
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
        'baseUri' => '/'
    ],
    'volt' => [
        'cacheDir' => APP_PATH . '/cache/volt/'
    ],

]);
