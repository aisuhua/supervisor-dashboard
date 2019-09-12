<?php
class Tool
{
    /**
     * http://www.thecave.info/php-ping-script-to-check-remote-server-or-website/
     * @param $host
     * @return bool
     */
    public static function ping($host)
    {
        exec(sprintf('ping -c 1 -W 5 %s', escapeshellarg($host)), $res, $rval);
        return $rval === 0;
    }

    public static function commandPattern()
    {
        $cmd = [
            'php',
            '/usr/bin/php',
            'node',
            '/usr/bin/node',
            'python',
            'python3',
            '/usr/bin/python',
            'bash',
            '/bin/bash',
            'java',
            '/usr/bin/java',
            'aria2c',
            '/usr/bin/aria2c',
            '/www/disasterctl/disasterctl',
            '/www/timer_rbmq/timerrbmq',
            '/www/web/timer_rbmq/timerrbmq',
            '/www/web/msg.115.com/crond/apns_worker/apns_worker',
            '/www/web/msg.yun.115.com/crond/apns_worker/apns_worker',
            '/www/web/home-imgload/home-imgload1604',
            '/www/web/home-unread/home-unread1604'
        ];

        $cmd = array_map(function($item) {
            return str_replace('/', '\/', $item);
        }, $cmd);

        $cmd_str = implode('|', $cmd);

        return "/(^({$cmd_str})\s+[0-9a-zA-Z\._\-\s=:\/%()]{1,255}$)|(^\/bin\/cat$)|(^\/bin\/echo\s?\w*?$)|(date)/";
    }
}