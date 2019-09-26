<?php
namespace SupBoard\Lock;

class Process extends File
{
    protected $filename = PATH_APP . '/lock/process.lock';

    public function __construct()
    {
        parent::__construct($this->filename);
    }
}