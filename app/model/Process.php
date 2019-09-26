<?php
namespace SupBoard\Model;

use Phalcon\Mvc\Model;
use Phalcon\Validation;
use Phalcon\Validation\Validator\Uniqueness;
use Phalcon\Validation\Validator\PresenceOf;

class Process extends Model
{
    public $id;
    public $server_id;
    public $program;
    public $command;
    public $process_name;
    public $numprocs;
    public $numprocs_start;
    public $user;
    public $directory;
    public $autostart;
    public $startretries;
    public $autorestart;
    public $redirect_stderr;
    public $stdout_logfile;
    public $stdout_logfile_backups;
    public $stdout_logfile_maxbytes;
    public $update_time;
    public $create_time;

    CONST DEFAULT = [
        'numprocs' => 1,
        'numprocs_start' => 0,
        'process_name' => '%(program_name)s_%(process_num)s',
        'user' => 'www-data',
        'directory' => '%(here)s',
        'autostart' => 'true',
        'startretries' => 20,
        'autorestart' => 'true',
        'redirect_stderr' => 'true',
        'stdout_logfile' => 'AUTO',
        'stdout_logfile_backups' => 0,
        'stdout_logfile_maxbytes' => '1MB'
    ];

    public function initialize()
    {
        $this->keepSnapshots(true);
    }

    public function beforeCreate()
    {
        $this->create_time = time();
    }

    public function beforeSave()
    {
        $this->update_time = time();

        foreach (self::DEFAULT as $key => $value)
        {
            !empty($this->{$key}) ?: $this->{$key} = $value;
        }
    }

    public static function applyDefault(array $data)
    {
        foreach (self::DEFAULT as $key => $value)
        {
            !empty($data[$key]) ?: $data[$key] = $value;
        }

        return $data;
    }

    public function validation()
    {
        $validator = new Validation();

        $validator->add(
            ['server_id', 'program'],
            new Uniqueness(
                [
                    'message' => '该程序名称已存在，换个名字试试吧~',
                ]
            )
        );

        return $this->validate($validator);
    }

    public function getIni()
    {
        $ini = '';
        $ini .= "[program:{$this->program}]" . PHP_EOL;
        $ini .= "command={$this->command}" . PHP_EOL;
        $ini .= "numprocs={$this->numprocs}" . PHP_EOL;
        $ini .= "numprocs_start={$this->numprocs_start}" . PHP_EOL;
        $ini .= "process_name={$this->process_name}" . PHP_EOL;
        $ini .= "user={$this->user}" . PHP_EOL;
        $ini .= "directory={$this->directory}" . PHP_EOL;
        $ini .= "autostart={$this->autostart}" . PHP_EOL;
        $ini .= "startretries={$this->startretries}" . PHP_EOL;
        $ini .= "autorestart={$this->autorestart}" . PHP_EOL;
        $ini .= "redirect_stderr={$this->redirect_stderr}" . PHP_EOL;
        $ini .= "stdout_logfile={$this->stdout_logfile}" . PHP_EOL;
        $ini .= "stdout_logfile_backups={$this->stdout_logfile_backups}" . PHP_EOL;
        $ini .= "stdout_logfile_maxbytes={$this->stdout_logfile_maxbytes}";

        return $ini;
    }

    public static function getIniTemplate()
    {
        $ini = '';
        $ini .= "[program:cat]" . PHP_EOL;
        $ini .= "command=/bin/cat" . PHP_EOL;

        foreach (self::DEFAULT as $key => $value)
        {
            $ini .= "{$key}={$value}" . PHP_EOL;
        }

        return trim($ini);
    }

}