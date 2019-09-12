<?php
class ProcessLock extends FileLock
{
    protected $filename = APP_PATH . '/lock/process.txt';

    public function __construct()
    {
        parent::__construct($this->filename);
    }
}