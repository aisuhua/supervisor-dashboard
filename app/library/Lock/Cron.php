<?php
namespace SupBoard\Lock;

class Cron extends File
{
    protected $filename = PATH_APP . '/lock/cron.lock';

    public function __construct()
    {
        parent::__construct($this->filename);
    }
}