<?php
use Phalcon\Loader;
use Phalcon\Di\FactoryDefault;
use Phalcon\Di\FactoryDefault\Cli as CliDI;

error_reporting(-1);

// 设置时区
date_default_timezone_set('Asia/Shanghai');

// 是否开启 debug 模式
define('DEBUG_MODE', true);

// 定义项目常量
define('PATH_ROOT', __DIR__);
define('PATH_APP', PATH_ROOT . '/app');
define('PATH_INIT', PATH_APP . '/init.d');
define('PATH_CONFIG', PATH_APP . '/config');
define('PATH_CONFIG_COMMON', PATH_CONFIG . '/common');
define('PATH_CACHE', PATH_APP . '/cache');
define('PATH_LOG', PATH_APP . '/log');
define('PATH_LIBRARY', PATH_APP . '/library');
define('PATH_SUPERVISOR', PATH_ROOT . '/supervisor');
define('PATH_SUPERVISOR_CONFIG', PATH_SUPERVISOR . '/conf.d');
define('PATH_SUPERVISOR_LOG', PATH_SUPERVISOR . '/log');

if (DEBUG_MODE)
{
    ini_set('error_log', PATH_LOG . '/php_error.log');
    ini_set('log_errors', '1');
    ini_set('display_errors', 'On');
}
else
{
    ini_set('error_log', PATH_LOG . '/php_error.log');
    ini_set('log_errors', '1');
    ini_set('display_errors', 'Off');
}

// 判断当前机房
if (is_file("/www/web/IDC_HN1"))
{
    define('IDC_NAME', 'HN1');
    define('PATH_CONFIG_IDC', PATH_CONFIG . '/hn1');
}
else
{
    define('IDC_NAME', 'RC');
    define('PATH_CONFIG_IDC', PATH_CONFIG . '/rc');
}

// 加载公共配置
require PATH_CONFIG_COMMON . '/inc_language.php';

// 加载环境配置
require PATH_CONFIG_IDC . '/inc_config.php';

// 加载 composer 第三方库
require PATH_ROOT . '/vendor/autoload.php';

// 加载库函数
require PATH_LIBRARY . '/lib_func.php';

// 注册默认自动加载的目录
$loader = new Loader();
$loader->registerNamespaces([
    'SupBoard\Controller' => PATH_APP . '/controller/',
    'SupBoard\Model' => PATH_APP . '/model/',
    'SupBoard\Form' => PATH_APP . '/form/',
    'SupBoard' => PATH_APP . '/library/',
]);
$loader->register();

// 根据当前运行模式实例化对应的 DI 容器
/** @var Phalcon\Di $di */
if (PHP_SAPI == 'cli')
{
    $di = new CliDI();
}
else
{
    $di = new FactoryDefault();
}

// 注册公共服务
require PATH_INIT . '/services.php';