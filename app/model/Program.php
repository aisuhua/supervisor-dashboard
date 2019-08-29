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

//    public function validation()
//    {
//        $validator = new Validation();
//
//        $validator->add(
//            ['server_id', 'program'],
//            new Uniqueness(
//                [
//                    'message' => '该程序名称已存在，换个名字试试吧~',
//                ]
//            )
//        );
//
//        return $this->validate($validator);
//    }
}