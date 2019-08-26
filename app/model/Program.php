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

    public function beforeCreate()
    {
        $this->create_time = time();
    }

    public function beforeSave()
    {
        $this->update_time = time();
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