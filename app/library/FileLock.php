<?php
class FileLock
{
    protected $filename;
    protected $fp;
    protected $locked;

    public function __construct($filename)
    {
        $this->fp = fopen($this->filename, "r+");
        $this->locked = false;
        $this->filename = $filename;
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