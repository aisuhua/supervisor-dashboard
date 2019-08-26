<?php
use Phalcon\Forms\Form;
use Phalcon\Forms\Element\Text;
use Phalcon\Forms\Element\Select;
use Phalcon\Forms\Element\Hidden;
use Phalcon\Forms\Element\Password;
use Phalcon\Validation\Validator\PresenceOf;
use Phalcon\Validation\Validator\Between;
use Phalcon\Validation\Validator\Regex;
use Phalcon\Validation\Validator\Numericality;
use Phalcon\Validation\Validator\InclusionIn;

class ProgramForm extends Form
{
    public function initialize($entity = null, $options = null)
    {
        // id
        if (isset($options['edit']) && $options['edit'])
        {
            $id = new Hidden('id');
            $this->add($id);
        }

        // server_id
        $server_id = new Hidden('server_id');

        $server_id->addValidators([
            new PresenceOf([
                'message' => '缺少分组 ID'
            ])
        ]);

        $this->add($server_id);

        // program
        $program = new Text('program', [
            'class' => 'form-control',
            'autocomplete' => 'off',
            'value' => ''
        ]);

        $program->addValidators([
            new PresenceOf([
                'message' => '程序名不能为空'
            ])
        ]);

        $this->add($program);

        // command
        $command = new Text('command', [
            'class' => 'form-control',
            'autocomplete' => 'off',
            'value' => ''
        ]);

        $command->addValidators([
            new PresenceOf([
                'message' => '命令不能为空'
            ])
        ]);

        $this->add($command);

        // process_name
        $process_name = new Text('process_name', [
            'class' => 'form-control',
            'autocomplete' => 'off',
            'value' => '%(program_name)s_%(process_num)s'
        ]);

        $process_name->addValidators([
            new PresenceOf([
                'message' => '进程名不能为空'
            ])
        ]);

        $this->add($process_name);

        // numprocs
        $numprocs = new Text('numprocs', [
            'class' => 'form-control',
            'autocomplete' => 'off',
            'value' => 1
        ]);

        $numprocs->addValidators([
            new PresenceOf([
                'message' => '进程数不能为空'
            ]),
            new Numericality([
                'message' => '进程数必须是数字'
            ]),
            new Between(
                [
                    "minimum" => 1,
                    "maximum" => 256,
                    "message" => "进程数范围为1～256",
                ]
            )
        ]);

        $this->add($numprocs);

        // numprocs_start
        $numprocs_start = new Text('numprocs_start', [
            'class' => 'form-control',
            'autocomplete' => 'off',
            'value' => 0
        ]);

        $numprocs_start->addValidators([
            new PresenceOf([
                'message' => '进程下标起始值不能为空'
            ]),
            new Numericality([
                'message' => '进程下标起始值必须是数字'
            ])
        ]);

        $this->add($numprocs_start);

        // directory
        $directory = new Text('directory', [
            'class' => 'form-control',
            'autocomplete' => 'off',
            'value' => '%(here)s'
        ]);

        $this->add($directory);

        // autostart
        $autostart = new Select(
            'autostart',
            [
                'true' => 'true',
                'false' => 'false',
            ],
            [
                'class' => 'form-control'
            ]
        );

        $autostart->addValidators([
            new InclusionIn([
                "domain"  => ['true', 'false'],
                'message' => '自动启动的值只能是 true 或者 false'
            ])
        ]);

        $this->add($autostart);

        // startretries
        $startretries = new Text('startretries', [
            'class' => 'form-control',
            'autocomplete' => 'off',
            'value' => 20
        ]);

        $startretries->addValidators([
            new Numericality([
                'message' => '启动重试次数必须是数字'
            ])
        ]);

        $this->add($startretries);

        // autorestart
        $autorestart = new Select(
            'autorestart',
            [
                'true' => 'true',
                'false' => 'false',
            ],
            [
                'class' => 'form-control'
            ]
        );

        $autorestart->addValidators([
            new InclusionIn([
                "domain"  => ['true', 'false'],
                'message' => '自动重启的值只能是 true 或者 false'
            ])
        ]);

        $this->add($autorestart);

        // redirect_stderr
        $redirect_stderr = new Select(
            'redirect_stderr',
            [
                'true' => 'true',
                'false' => 'false',
            ],
            [
                'class' => 'form-control'
            ]
        );

        $redirect_stderr->addValidators([
            new InclusionIn([
                "domain"  => ['true', 'false'],
                'message' => '错误重定向的值只能是 true 或者 false'
            ])
        ]);

        $this->add($redirect_stderr);

        // stdout_logfile
        $stdout_logfile = new Text('stdout_logfile', [
            'class' => 'form-control',
            'autocomplete' => 'off',
            'value' => 'AUTO'
        ]);

        $this->add($stdout_logfile);

        // stdout_logfile_backups
        $stdout_logfile_backups = new Text('stdout_logfile_backups', [
            'class' => 'form-control',
            'autocomplete' => 'off',
            'value' => 0
        ]);

        $stdout_logfile_backups->addValidators([
            new Numericality([
                'message' => '标准输出日志备份数量必须是数字'
            ]),
            new Between(
                [
                    "minimum" => 0,
                    "maximum" => 256,
                    "message" => "准输出日志备份数量范围为1～256",
                ]
            )
        ]);

        $this->add($stdout_logfile_backups);

        // program
        $stdout_logfile_maxbytes = new Text('stdout_logfile_maxbytes', [
            'class' => 'form-control',
            'autocomplete' => 'off',
            'value' => '1M'
        ]);

        $this->add($stdout_logfile_maxbytes);
    }
}