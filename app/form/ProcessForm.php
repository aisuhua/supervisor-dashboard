<?php
namespace SupBoard\Form;

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
use SupBoard\Tool\Tool;

class ProcessForm extends Form
{
    public function initialize($entity = null, $options = null)
    {
        // id
        $id = new Hidden('id');
        $this->add($id);

        // server_id
        $server_id = new Hidden('server_id');

        $server_id->setFilters([
            'int'
        ]);

        $server_id->addValidators([
            new PresenceOf([
                'message' => '缺少分组 ID'
            ])
        ]);

        $this->add($server_id);

        // program
        $program = new Text('program', [
            'class' => 'form-control',
            'value' => ''
        ]);

        $program->setFilters([
            'string',
            'trim'
        ]);

        $program->addValidators([
            new PresenceOf([
                'message' => '程序名不能为空'
            ]),
            new Regex(
                [
                    "pattern" => '/^[0-9A-Za-z\.\-_]+$/',
                    "message" => "程序名只能包含数字、字母、下划线、英文句号 . 和 - 横杆字符"
                ]
            )
        ]);

        $this->add($program);

        // command
        $command = new Text('command', [
            'class' => 'form-control',
            'value' => ''
        ]);

        $command->setFilters([
            'string',
            'trim'
        ]);

        $command->addValidators([
            new PresenceOf([
                'message' => '命令不能为空'
            ]),
            new Regex(
                [
                    "pattern" => Tool::commandPattern(),
                    "message" => "命令格式不正确"
                ]
            )
        ]);

        $this->add($command);

        // process_name
        $process_name = new Text('process_name', [
            'class' => 'form-control',
            'value' => '%(program_name)s_%(process_num)s'
        ]);

        $process_name->setFilters([
            'string',
            'trim'
        ]);

        $this->add($process_name);

        // numprocs
        $numprocs = new Text('numprocs', [
            'class' => 'form-control',
            'value' => 1
        ]);

        $numprocs->setFilters([
            'int'
        ]);

        $numprocs->addValidators([
            new Numericality([
                'message' => '进程数必须是数字',
                'allowEmpty' => true
            ]),
            new Between(
                [
                    "minimum" => 1,
                    "maximum" => 256,
                    "message" => "进程数范围为1～256",
                    'allowEmpty' => true
                ]
            )
        ]);

        $this->add($numprocs);

        // numprocs_start
        $numprocs_start = new Text('numprocs_start', [
            'class' => 'form-control',
            'value' => 0
        ]);

        $numprocs_start->setFilters([
            'int'
        ]);

        $numprocs_start->addValidators([
            new Numericality([
                'message' => '进程下标起始值必须是数字',
                'allowEmpty' => true
            ])
        ]);

        $this->add($numprocs_start);

        // user
        $user = new Text('user', [
            'class' => 'form-control',
            'value' => 'www-data'
        ]);

        $user->setFilters([
            'string',
            'trim'
        ]);

        $this->add($user);

        // stopwaitsecs
        $stopwaitsecs = new Text('stopwaitsecs', [
            'class' => 'form-control',
            'value' => 10
        ]);

        $stopwaitsecs->setFilters([
            'string',
            'trim'
        ]);

        $stopwaitsecs->addValidators([
            new Numericality([
                'message' => '停止等待秒数必须是数字',
                'allowEmpty' => true
            ]),
        ]);

        $this->add($stopwaitsecs);

        // directory
        $directory = new Text('directory', [
            'class' => 'form-control',
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

        $autostart->setFilters([
            'string',
            'trim'
        ]);

        $autostart->addValidators([
            new InclusionIn([
                "domain"  => ['true', 'false'],
                'message' => '自动启动的值只能是 true 或者 false',
                'allowEmpty' => true
            ])
        ]);

        $this->add($autostart);

        // startretries
        $startretries = new Text('startretries', [
            'class' => 'form-control',
            'value' => 20
        ]);

        $startretries->setFilters([
            'int'
        ]);

        $startretries->addValidators([
            new Numericality([
                'message' => '启动重试次数必须是数字',
                'allowEmpty' => true
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

        $autorestart->setFilters([
            'string',
            'trim'
        ]);

        $autorestart->addValidators([
            new InclusionIn([
                "domain"  => ['true', 'false'],
                'message' => '自动重启的值只能是 true 或者 false',
                'allowEmpty' => true
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

        $redirect_stderr->setFilters([
            'string',
            'trim'
        ]);

        $redirect_stderr->addValidators([
            new InclusionIn([
                "domain"  => ['true', 'false'],
                'message' => '错误重定向的值只能是 true 或者 false',
                'allowEmpty' => true
            ])
        ]);

        $this->add($redirect_stderr);

        // stdout_logfile
        $stdout_logfile = new Text('stdout_logfile', [
            'class' => 'form-control',
            'value' => 'AUTO'
        ]);

        $stdout_logfile->setFilters([
            'string',
            'trim'
        ]);

        $this->add($stdout_logfile);

        // stdout_logfile_backups
        $stdout_logfile_backups = new Text('stdout_logfile_backups', [
            'class' => 'form-control',
            'value' => 0
        ]);

        $stdout_logfile_backups->setFilters([
            'int'
        ]);

        $stdout_logfile_backups->addValidators([
            new Numericality([
                'message' => '标准输出日志备份数量必须是数字',
                'allowEmpty' => true
            ]),
            new Between(
                [
                    "minimum" => 0,
                    "maximum" => 256,
                    "message" => "准输出日志备份数量范围为1～256",
                    'allowEmpty' => true
                ]
            )
        ]);

        $this->add($stdout_logfile_backups);

        // stdout_logfile_maxbytes
        $stdout_logfile_maxbytes = new Text('stdout_logfile_maxbytes', [
            'class' => 'form-control',
            'value' => '1MB'
        ]);

        $stdout_logfile_maxbytes->setFilters([
            'string',
            'trim'
        ]);

        $stdout_logfile_maxbytes->addValidators([
            new Regex(
                [
                    "pattern" => '/^\d+MB$/',
                    "message" => "标准输出日志的最大字节数不正确",
                    'allowEmpty' => true
                ]
            )
        ]);

        $this->add($stdout_logfile_maxbytes);
    }
}