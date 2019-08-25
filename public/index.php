<?php
use Phalcon\Di\FactoryDefault;

error_reporting(E_ALL);
ini_set('display_errors', 1);
set_time_limit(120);

define('BASE_PATH', dirname(__DIR__));
define('APP_PATH', BASE_PATH . '/app');

//try {

    /**
     * The FactoryDefault Dependency Injector automatically registers
     * the services that provide a full stack framework.
     */
    $di = new FactoryDefault();

    /**
     * Handle routes
     */
    include APP_PATH . '/config/router.php';

    /**
     * Read services
     */
    include APP_PATH . '/config/services.php';

    /**
     * Get config service for use in inline setup below
     */
    $config = $di->getConfig();

    /**
     * Include Autoloader
     */
    include APP_PATH . '/config/loader.php';

    /**
     * Include vendor
     */
    include BASE_PATH . '/vendor/autoload.php';

    /**
     * Handle the request
     */
    $application = new \Phalcon\Mvc\Application($di);

    echo $application->handle()->getContent();

//} catch (\Exception $e) {
//    echo $e->getMessage() . '<br>';
//    echo '<pre>' . $e->getTraceAsString() . '</pre>';
//}
