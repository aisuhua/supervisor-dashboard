<?php
class Tool
{
    public static function shortName($name)
    {
        return explode(":", $name)[1];
    }

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
}