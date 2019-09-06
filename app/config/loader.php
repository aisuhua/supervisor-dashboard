<?php
/**
 * Include vendor
 */
include BASE_PATH . '/vendor/autoload.php';

/**
 * Other library
 */
include APP_PATH . '/library/function.php';

$loader = new \Phalcon\Loader();

$loader->registerDirs(
    [
        $config->application->controllerDir,
        $config->application->modelDir,
        $config->application->formDir,
        $config->application->libraryDir,
        $config->application->taskDir,
        $config->application->cronDir,
    ]
)->register();
