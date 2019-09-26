<?php
namespace SupBoard\Lock;

class Command extends File
{
    protected $filename = PATH_APP . '/lock/command.lock';

    public function __construct()
    {
        parent::__construct($this->filename);
    }
}