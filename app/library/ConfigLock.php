<?php
class ConfigLock
{
    protected $filename = APP_PATH . '/lock/config.txt';
    protected $fp;
    protected $locked;

    public function __construct($filename = null)
    {
        $this->fp = fopen($this->filename, "r+");
        $this->locked = false;
        $this->filename = $filename ?: $this->filename;
    }

    public function lock()
    {
        if (!flock($this->fp, LOCK_EX))
        {
            return false;
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
            return false;
        }

        fclose($this->fp);
        $this->locked = false;

        return true;
    }

}