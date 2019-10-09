<?php
namespace SupBoard\Library;

use PHalcon\Di;
use Phalcon\Mvc\Dispatcher;
use Phalcon\Logger;

class ErrorHandler
{
    public static function init()
    {
        set_error_handler(__CLASS__ .'::errorHandler', E_ALL);
        set_exception_handler(__CLASS__ . '::exceptionHandler');
        register_shutdown_function(__CLASS__ . '::shutdownHandler');
    }

    public static function shutdownHandler()
    {
        if(!is_null($e = error_get_last()))
        {
            self::errorHandler($e['type'], $e['message'], $e['file'], $e['line']);
        }
    }

    public static function errorHandler($errno, $errmsg, $filename, $linenum)
    {
        $data = [];
        $data['errno'] = $errno;
        $data['errtype'] = self::getErrorType($errno);
        $data['errmsg'] = $errmsg;
        $data['filename'] = $filename;
        $data['linenum'] = $linenum;
        $data['client_ip'] = get_client_ip();
        $data['backtrace'] = json_encode(debug_backtrace());

        $di = Di::getDefault();
        $log_file = 'php_error_' . PHP_SAPI . '.log';
        $log_type = self::getLogType($data['errno']);
        $di->get('logger', [$log_file])->log($log_type, var_export($data, true));

        if (PHP_SAPI === 'cli')
        {
            $di->get('streamLogger')->log($log_type, var_export($data, true));
        }

        switch ($errno)
        {
            case E_WARNING:
            case E_NOTICE:
            case E_CORE_WARNING:
            case E_COMPILE_WARNING:
            case E_USER_WARNING:
            case E_USER_NOTICE:
            case E_STRICT:
            case E_DEPRECATED:
            case E_USER_DEPRECATED:
            case E_ALL:
                break;
            default:
                self::handleResponse();
        }
    }

    public static function exceptionHandler(\Throwable $e)
    {
        $data = [];
        $data['errno'] = $e->getCode();
        $data['errtype'] = get_class($e);
        $data['errmsg'] = $e->getMessage();
        $data['filename'] = $e->getFile();
        $data['linenum'] = $e->getLine();
        $data['backtrace'] = json_encode($e->getTrace()) ?: $e->getTraceAsString();
        $data['client_ip'] = get_client_ip();

        $di = Di::getDefault();
        $log_file =  'php_error_' . PHP_SAPI . '.log';
        $di->get('logger', [$log_file])->error(var_export($data, true));

        if (PHP_SAPI === 'cli')
        {
            $di->get('streamLogger')->error(var_export($data, true));
        }

        self::handleResponse();
    }

    protected static function handleResponse()
    {
        if (PHP_SAPI == 'cli')
        {
            exit(1);
        }

        self::renderErrorPage();
    }

    protected static function renderErrorPage()
    {
        $di = Di::getDefault();

        // 处理 500、400 等业务跳转
        /** @var Dispatcher $dispatcher */
        $dispatcher = $di->get('dispatcher');
        $view = $di->get('view');
        $response = $di->get('response');

        // $dispatcher->setNamespaceName('SupBoard\Controller');
        $dispatcher->setControllerName('error');
        $dispatcher->setActionName('index');

        $view->start();
        $dispatcher->dispatch();
        $view->render('error', 'index', $dispatcher->getParams());
        $view->finish();

        $response->setContent($view->getContent())->send();
    }

    protected static function getErrorType($code)
    {
        switch ($code)
        {
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

    protected static function getLogType($code)
    {
        switch ($code)
        {
            case E_ERROR:
            case E_RECOVERABLE_ERROR:
            case E_CORE_ERROR:
            case E_COMPILE_ERROR:
            case E_USER_ERROR:
            case E_PARSE:
                return Logger::ERROR;
            case E_WARNING:
            case E_USER_WARNING:
            case E_CORE_WARNING:
            case E_COMPILE_WARNING:
                return Logger::WARNING;
            case E_NOTICE:
            case E_USER_NOTICE:
                return Logger::NOTICE;
            case E_STRICT:
            case E_DEPRECATED:
            case E_USER_DEPRECATED:
                return Logger::INFO;
        }

        return Logger::ERROR;
    }
}
