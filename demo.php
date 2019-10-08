<?php
require 'init.php';

/**
 * 错误接管函数
 *
 * @param string $errno
 * @param string $errmsg
 * @param string $filename
 * @param string $linenum
 * @return bool
 */
function debug_error_handler($errno, $errmsg, $filename, $linenum)
{
    $result = array();
    $result['errno'] = $errno;
    $result['errtype'] = get_error_type($errno);
    $result['errmsg'] = $errmsg;
    $result['filename'] = $filename;
    $result['linenum'] = $linenum;
    $result['client_ip'] = get_client_ip();
    $result['backtrace'] = json_encode(debug_backtrace());
    save_error_log($result);

    return true;
}

/**
 * 异常处理
 * @param Exception $e
 * @return bool
 */
function debug_exception_handler($e)
{
    $result = [];
    $result['errno'] = $e->getCode();
    $result['errtype'] = get_class($e);
    $result['errmsg'] = $e->getMessage();
    $result['filename'] = $e->getFile();
    $result['linenum'] = $e->getLine();
    $result['client_ip'] = get_client_ip();
    $result['backtrace'] = json_encode($e->getTrace()) ?: $e->getTraceAsString();
    save_error_log($result);

    return true;
}

/**
 * 捕捉Fatal错误
 */
function debug_shutdown_handler()
{
    if(is_null($e = error_get_last()))
    {
        debug_error_handler($e['type'], $e['message'], $e['file'], $e['line']);
    }
}

function save_error_log(&$err_arr)
{
    if (DEBUG_MODE || PHP_SAPI === 'cli')
    {
        fwrite(STDERR, var_export($err_arr, true));
    }

    $err_arr['time'] = time();
    $log_file = PATH_LOG . '/php_error_' . PHP_SAPI . '_' . date('ym') . '.log';
    file_put_contents($log_file, json_encode($err_arr) . PHP_EOL, FILE_APPEND);

    return true;
}

function get_error_type($code)
{
    switch ($code)
    {
        case 0:
            return 'Uncaught exception';
        case E_ERROR:
            return 'E_ERROR';
        case E_WARNING:
            return 'E_WARNING';
        case E_PARSE:
            return 'E_PARSE';
        case E_NOTICE:
            return 'E_NOTICE';
        case E_CORE_ERROR:
            return 'E_CORE_ERROR';
        case E_CORE_WARNING:
            return 'E_CORE_WARNING';
        case E_COMPILE_ERROR:
            return 'E_COMPILE_ERROR';
        case E_COMPILE_WARNING:
            return 'E_COMPILE_WARNING';
        case E_USER_ERROR:
            return 'E_USER_ERROR';
        case E_USER_WARNING:
            return 'E_USER_WARNING';
        case E_USER_NOTICE:
            return 'E_USER_NOTICE';
        case E_STRICT:
            return 'E_STRICT';
        case E_RECOVERABLE_ERROR:
            return 'E_RECOVERABLE_ERROR';
        case E_DEPRECATED:
            return 'E_DEPRECATED';
        case E_USER_DEPRECATED:
            return 'E_USER_DEPRECATED';
    }
    return $code;
}

set_error_handler('debug_error_handler', E_ALL);
set_exception_handler('debug_exception_handler');
register_shutdown_function('debug_shutdown_handler');

function test()
{
    echo $a;
    fopen('suhua', 'r');
    100 / 0;
    throw new \Exception('I am exception');
}

try
{
    test();

    echo 'suhua', PHP_EOL;

}
catch (\Throwable $e)
{
    echo $e->getMessage(), PHP_EOL;
    exit;
}

