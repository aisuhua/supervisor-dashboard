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

    public function initialize()
    {
        $this->keepSnapshots(true);
    }

    public function beforeUpdate()
    {

    }

    public function beforeCreate()
    {
        $this->create_time = time();
    }

    public function beforeSave()
    {
        $this->numprocs ?: $this->numprocs = 1;
        $this->numprocs_start ?: $this->numprocs_start = 0;
        $this->process_name ?: $this->process_name = '%(program_name)s_%(process_num)s';
        $this->user ?: $this->user = 'www-data';
        $this->directory ?: $this->directory = '%(here)s';
        $this->autostart ?: $this->autostart = 'true';
        $this->startretries ?: $this->startretries = 20;
        $this->autorestart ?: $this->autorestart = 'true';
        $this->redirect_stderr ?: $this->redirect_stderr = 'true';
        $this->stdout_logfile ?: $this->stdout_logfile = 'AUTO';
        $this->stdout_logfile_backups ?: $this->stdout_logfile_backups = 0;
        $this->stdout_logfile_maxbytes ?: $this->stdout_logfile_maxbytes = '1MB';

        $this->update_time = time();
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

    public static function applyDefaultValue(&$value)
    {
        !empty($value['numprocs']) ?: $value['numprocs'] = 1;
        !empty($value['numprocs_start']) ?: $value['numprocs_start'] = 0;
        !empty($value['process_name']) ?: $value['process_name'] = '%(program_name)s_%(process_num)s';
        !empty($value['user']) ?: $value['user'] = 'www-data';
        !empty($value['directory']) ?: $value['directory'] = '%(here)s';
        !empty($value['autostart']) ?: $value['autostart'] = 'true';
        !empty($value['startretries']) ?: $value['startretries'] = 20;
        !empty($value['autorestart']) ?: $value['autorestart'] = 'true';
        !empty($value['redirect_stderr']) ?: $value['redirect_stderr'] = 'true';
        !empty($value['stdout_logfile']) ?: $value['stdout_logfile'] = 'AUTO';
        !empty($value['stdout_logfile_backups']) ?: $value['stdout_logfile_backups'] = 0;
        !empty($value['stdout_logfile_maxbytes']) ?: $value['stdout_logfile_maxbytes'] = '1MB';
    }

    public static function getIniTemplate()
    {
        $ini = '';
        $ini .= "[program:cat]" . PHP_EOL;
        $ini .= "command=/bin/cat" . PHP_EOL;
        $ini .= "numprocs=1" . PHP_EOL;
        $ini .= "numprocs_start=0" . PHP_EOL;
        $ini .= "process_name=%(program_name)s_%(process_num)s" . PHP_EOL;
        $ini .= "user=www-data" . PHP_EOL;
        $ini .= "directory=%(here)s" . PHP_EOL;
        $ini .= "autostart=true" . PHP_EOL;
        $ini .= "startretries=20" . PHP_EOL;
        $ini .= "autorestart=true" . PHP_EOL;
        $ini .= "redirect_stderr=true" . PHP_EOL;
        $ini .= "stdout_logfile=AUTO" . PHP_EOL;
        $ini .= "stdout_logfile_backups=0" . PHP_EOL;
        $ini .= "stdout_logfile_maxbytes=1MB";

        return $ini;
    }
}