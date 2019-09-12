<?php
class CronLock extends FileLock
{
    protected $filename = APP_PATH . '/lock/cron.txt';

    public function __construct()
    {
        parent::__construct($this->filename);
    }
}