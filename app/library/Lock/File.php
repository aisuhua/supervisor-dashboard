<?php
namespace SupBoard\Lock;

use SupBoard\Exception\Exception;

class File
{
    protected $filename;
    protected $fp;
    protected $locked;

    public function __construct($filename)
    {
        $this->fp = fopen($this->filename, "r+");
        if ($this->fp === false)
        {
            throw new Exception("无法打开文件：{$this->filename}");
        }

        $this->locked = false;
        $this->filename = $filename;
    }

    public function lock()
    {
        if (!flock($this->fp, LOCK_EX))
        {
            throw new Exception("无法获得锁：{$this->filename}");
        }

        $this->locked = true;

        return true;
    }

    public function unlock()
    {
        if (!$this->locked)
        {
            return true;
        }

        if (!flock($this->fp, LOCK_UN))
        {
            throw new Exception("解锁失败：{$this->filename}");
        }

        fclose($this->fp);
        $this->locked = false;

        return true;
    }

}