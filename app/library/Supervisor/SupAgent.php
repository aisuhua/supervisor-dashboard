<?php
namespace SupBoard\Supervisor;

class SupAgent
{
    protected $host;
    protected $port;
    protected $base_uri;

    public function __construct($host, $port)
    {
        $this->host = $host;
        $this->port = $port;
    }

    protected function getBaseUri()
    {
        return $this->host . ':8000';
    }

    protected function getUrl($api_uri)
    {
        return self::getBaseUri() . $api_uri;
    }

    public function tailCronLog($log_id, $file_size = 1048576)
    {
        $api_url = self::getUrl("/cron-log/log/{$log_id}/{$file_size}");
        return curl_get($api_url, [], 3);
    }
}