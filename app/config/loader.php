<?php

$loader = new \Phalcon\Loader();

$loader->registerDirs(
    [
        $config->application->controllerDir,
        $config->application->modelDir,
        $config->application->libraryDir,
        $config->application->taskDir,
    ]
)->register();
