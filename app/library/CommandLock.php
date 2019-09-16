<?php
class CommandLock extends FileLock
{
    protected $filename = APP_PATH . '/lock/command.txt';

    public function __construct()
    {
        parent::__construct($this->filename);
    }
}