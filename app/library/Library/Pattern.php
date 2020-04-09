<?php
namespace SupBoard\Library;

class Pattern
{
    public static function command()
    {
        $cmd = [
            'php',
            '/usr/bin/php',
            'node',
            '/usr/bin/node',
            'python',
            'python2',
            'python3',
            '/usr/bin/python',
            '/usr/bin/python2',
            '/usr/bin/python3',
            'bash',
            '/bin/bash',
            'java',
            '/usr/bin/java',
            'aria2c',
            '/usr/bin/aria2c',
            '/www',
        ];

        $cmd = array_map(function($item) {
            return str_replace('/', '\/', $item);
        }, $cmd);
        $cmd_str = implode('|', $cmd);

        return "/^({$cmd_str})((?!\.\.)(?!&&).){0,255}$/";
    }
}
