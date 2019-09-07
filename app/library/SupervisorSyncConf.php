<?php
class SupervisorSyncConf
{
    private static $key = 'Mx#d7Xp%ks7m3R1g&XmoUw%9qQ74ehor';

    /**
     * 读取配置
     *
     * @param $uri
     * @param $file_path
     *
     * @return array
     */
    public static function read($uri, $file_path)
    {
        $url = $uri . "/read?file_path={$file_path}";
        return json_decode(curl_get($url), true);
    }

    /**
     * 写入配置
     *
     * @param $uri
     * @param $file_path
     * @param $content
     *
     * @return array
     */
    public static function write($uri, $file_path, $content)
    {
        $url = $uri . '/write';
        $data = [];
        $data['file_path'] = $file_path;
        $data['content'] = $content;
        $data['timestamp'] = (string) time();

        $data['token'] = strtoupper(md5(
            $data['file_path'] . ':' . $data['content'] . ':' . $data['timestamp'] . ':' . self::$key
        ));

        return json_decode(curl_post($url, json_encode($data)), true);
    }
}