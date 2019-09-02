<?php
use Phalcon\Mvc\Model;
use Phalcon\Validation;
use Phalcon\Validation\Validator\Uniqueness;
use Phalcon\Validation\Validator\PresenceOf;

class Program extends Model
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

    public function beforeSave()
    {
        $this->process_name ?: $this->process_name = '%(program_name)s_%(process_num)s';
        $this->numprocs ?: $this->numprocs = 1;
        $this->numprocs_start ?: $this->numprocs_start = 0;
        $this->user ?: $this->user = 'www-data';
        $this->directory ?: $this->directory = '%(here)s';
        $this->autostart ?: $this->autostart = 'true';
        $this->startretries ?: $this->startretries = 20;
        $this->autorestart ?: $this->autorestart = 'true';
        $this->redirect_stderr ?: $this->redirect_stderr = 'true';
        $this->stdout_logfile ?: $this->stdout_logfile = 'AUTO';
        $this->stdout_logfile_backups ?: $this->stdout_logfile_backups = 0;
        $this->stdout_logfile_maxbytes ?: $this->stdout_logfile_maxbytes = '1M';

        $this->update_time = time();
    }

    public function beforeCreate()
    {
        $this->create_time = time();
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

    public static function formatIniConfig($programs)
    {
        $ini = '';
        foreach ($programs as $program)
        {
            /** @var Program $program */
            $ini .= "[program:{$program->program}]" . PHP_EOL;
            $ini .= "command={$program->command}" . PHP_EOL;
            $ini .= "process_name={$program->process_name}" . PHP_EOL;
            $ini .= "numprocs={$program->numprocs}" . PHP_EOL;
            $ini .= "numprocs_start={$program->numprocs_start}" . PHP_EOL;
            $ini .= "user={$program->user}" . PHP_EOL;
            $ini .= "directory={$program->directory}" . PHP_EOL;
            $ini .= "autostart={$program->autostart}" . PHP_EOL;
            $ini .= "startretries={$program->startretries}" . PHP_EOL;
            $ini .= "autorestart={$program->autorestart}" . PHP_EOL;
            $ini .= "redirect_stderr={$program->redirect_stderr}" . PHP_EOL;
            $ini .= "stdout_logfile={$program->stdout_logfile}" . PHP_EOL;
            $ini .= "stdout_logfile_backups={$program->stdout_logfile_backups}" . PHP_EOL;
            $ini .= "stdout_logfile_maxbytes={$program->stdout_logfile_maxbytes}" . PHP_EOL;
        }

        return $ini;
    }
}