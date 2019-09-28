<?php
namespace SupBoard\Supervisor;

use SupBoard\Exception\Exception;
use SupBoard\Model\Server;

class SupAgent
{
    protected $server_id;
    protected $host;
    protected $port;
    protected $base_uri;

    public function __construct(Server $server)
    {
        $this->host = $server->ip;
        $this->port = $server->agent_port;
        $this->server_id = $server->id;
    }

    public function ping()
    {
        $api_url = self::makeUrl("/state");

        try
        {
            curl_get($api_url);
            return true;
        }
        catch (Exception $e)
        {
            return false;
        }
    }

    public function tailCronLog($log_id, $file_size = 1048576)
    {
        $api_url = self::makeUrl("/cron-log/tail/{$log_id}/{$file_size}");
        return curl_get($api_url, [], 3);
    }

    public function tailCommandLog($log_id, $file_size = 1048576)
    {
        $api_url = self::makeUrl("/command-log/tail/{$log_id}/{$file_size}");
        return curl_get($api_url, [], 3);
    }

    public function processReload()
    {
        $api_url = self::makeUrl("/process/reload/{$this->server_id}");
        return self::handelResult(curl_get($api_url));
    }

    public function commandReload($id)
    {
        $api_url = self::makeUrl("/command/reload/{$this->server_id}/{$id}");
        return self::handelResult(curl_get($api_url));
    }

    protected function makeUrl($uri)
    {
        $time = time();
        $url = $this->host . ':' . $this->port . $uri;
        $auth = md5($url . $time . $GLOBALS['api']['key']);

        return  $url . "?time={$time}&auth=$auth";
    }

    /**
     * @param $result
     * @return mixed
     */
    protected function handelResult($result)
    {
        return json_decode($result, true);
    }
}