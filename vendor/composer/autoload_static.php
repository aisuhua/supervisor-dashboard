<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit1506b007d5b8df55eb36887a561ea6f3
{
    public static $prefixLengthsPsr4 = array (
        'Z' => 
        array (
            'Zend\\XmlRpc\\' => 12,
            'Zend\\Validator\\' => 15,
            'Zend\\Uri\\' => 9,
            'Zend\\Stdlib\\' => 12,
            'Zend\\Server\\' => 12,
            'Zend\\Math\\' => 10,
            'Zend\\Loader\\' => 12,
            'Zend\\Http\\' => 10,
            'Zend\\EventManager\\' => 18,
            'Zend\\Escaper\\' => 13,
            'Zend\\Code\\' => 10,
            'ZendXml\\' => 8,
        ),
        'P' => 
        array (
            'Psr\\Container\\' => 14,
        ),
        'I' => 
        array (
            'Interop\\Container\\' => 18,
        ),
        'C' => 
        array (
            'Cron\\' => 5,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Zend\\XmlRpc\\' => 
        array (
            0 => __DIR__ . '/..' . '/zendframework/zend-xmlrpc/src',
        ),
        'Zend\\Validator\\' => 
        array (
            0 => __DIR__ . '/..' . '/zendframework/zend-validator/src',
        ),
        'Zend\\Uri\\' => 
        array (
            0 => __DIR__ . '/..' . '/zendframework/zend-uri/src',
        ),
        'Zend\\Stdlib\\' => 
        array (
            0 => __DIR__ . '/..' . '/zendframework/zend-stdlib/src',
        ),
        'Zend\\Server\\' => 
        array (
            0 => __DIR__ . '/..' . '/zendframework/zend-server/src',
        ),
        'Zend\\Math\\' => 
        array (
            0 => __DIR__ . '/..' . '/zendframework/zend-math/src',
        ),
        'Zend\\Loader\\' => 
        array (
            0 => __DIR__ . '/..' . '/zendframework/zend-loader/src',
        ),
        'Zend\\Http\\' => 
        array (
            0 => __DIR__ . '/..' . '/zendframework/zend-http/src',
        ),
        'Zend\\EventManager\\' => 
        array (
            0 => __DIR__ . '/..' . '/zendframework/zend-eventmanager/src',
        ),
        'Zend\\Escaper\\' => 
        array (
            0 => __DIR__ . '/..' . '/zendframework/zend-escaper/src',
        ),
        'Zend\\Code\\' => 
        array (
            0 => __DIR__ . '/..' . '/zendframework/zend-code/src',
        ),
        'ZendXml\\' => 
        array (
            0 => __DIR__ . '/..' . '/zendframework/zendxml/src',
        ),
        'Psr\\Container\\' => 
        array (
            0 => __DIR__ . '/..' . '/psr/container/src',
        ),
        'Interop\\Container\\' => 
        array (
            0 => __DIR__ . '/..' . '/container-interop/container-interop/src/Interop/Container',
        ),
        'Cron\\' => 
        array (
            0 => __DIR__ . '/..' . '/dragonmantank/cron-expression/src/Cron',
        ),
    );

    public static $prefixesPsr0 = array (
        'S' => 
        array (
            'Supervisor' => 
            array (
                0 => __DIR__ . '/..' . '/yzalis/supervisor/src',
            ),
        ),
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit1506b007d5b8df55eb36887a561ea6f3::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit1506b007d5b8df55eb36887a561ea6f3::$prefixDirsPsr4;
            $loader->prefixesPsr0 = ComposerStaticInit1506b007d5b8df55eb36887a561ea6f3::$prefixesPsr0;

        }, null, ClassLoader::class);
    }
}
