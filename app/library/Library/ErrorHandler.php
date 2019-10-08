<?php
namespace SupBoard\Library;

use PHalcon\Di;
use Phalcon\Logger;

class ErrorHandler
{
    public static function init()
    {
        set_error_handler(__CLASS__ .'::errorHandler', E_ALL);
        set_exception_handler(__CLASS__ . '::exceptionHandler');
        register_shutdown_function(__CLASS__ . '::shutdownHandler');
    }

    public static function errorHandler($errno, $errmsg, $filename, $linenum)
    {
        $data = [];
        $data['errno'] = $errno;
        $data['errtype'] = self::getErrorType($errno);
        $data['errmsg'] = $errmsg;
        $data['filename'] = $filename;
        $data['linenum'] = $linenum;
        $data['backtrace'] = json_encode(debug_backtrace());

        self::handle($data);
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

        self::handle($data);
    }

    public static function shutdownHandler()
    {
        if(!is_null($e = error_get_last()))
        {
            self::errorHandler($e['type'], $e['message'], $e['file'], $e['line']);
        }
    }

    protected static function handle(array &$data)
    {
        $di = Di::getDefault();
        $data['client_ip'] = get_client_ip();

        // 将错误日志记录到文件
        $log_file =  'php_error_' . PHP_SAPI . '.log';
        $di->get('logger', [$log_file])->log(self::getLogType($data['errno']), var_export($data, true));

         // 以 json 格式记录，可用于 ES或阿里云等日志系统
         // $data['time'] = time();
         // file_put_contents($log_file, json_encode($data) . PHP_EOL, FILE_APPEND);

        // Cli 模式下将错误输出到标准错误输出
        if (PHP_SAPI === 'cli')
        {
            $di->get('streamLogger')->log(self::getLogType($data['errno']), var_export($data, true));
            return true;
        }

        // 处理 500、400 等业务跳转
        echo 500, PHP_EOL;
    }

    protected static function getErrorType($code)
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
