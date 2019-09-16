<?php
use Phalcon\Mvc\Model;
use Phalcon\Validation;
use Phalcon\Validation\Validator\Uniqueness;
use Phalcon\Validation\Validator\PresenceOf;

/**
 * Class Command
 *
 * @method Server getServer()
 */
class Command extends Model
{
    public $id;
    public $server_id;
    public $user;
    public $command;
    public $status;
    public $start_time;
    public $end_time;
    public $log;
    public $update_time;
    public $create_time;

    const STATUS_INI = 0; // 初始化状态
    const STATUS_STARTED = 1; // 已启动
    const STATUS_FINISHED = 2; // 已正常完成
    const STATUS_FAILED = -1; // 没有正常退出
    const STATUS_UNKNOWN = -2; // 无法确定进程的执行状态
    const STATUS_STOPPED = -3; // 被中断

    public function initialize()
    {
        $this->belongsTo('server_id', 'Server', 'id', [
            'alias' => 'server',
            'reusable' => false
        ]);
    }

    public function beforeCreate()
    {
        $this->create_time = time();
    }

    public function beforeSave()
    {
        $this->update_time = time();
    }

    /**
     * 获取该命令对应的程序名称
     *
     * @return string
     */
    public function getProgramName()
    {
        return 'sys_command_' . $this->id;
    }

    /**
     * 产生该命令对应的进程名称
     *
     * @return string
     */
    public static function makeProcessName($id)
    {
        return 'sys_command_' . $id . ':' . 'sys_command_' . $id . '_0';
    }

    public function getIni()
    {
        $program = $this->getProgramName();

        $ini = '';
        $ini .= "[program:{$program}]" . PHP_EOL;
        $ini .= "command={$this->command}" . PHP_EOL;
        $ini .= "process_name=%(program_name)s_%(process_num)s" . PHP_EOL;
        $ini .= "numprocs=1" . PHP_EOL;
        $ini .= "numprocs_start=0" . PHP_EOL;
        $ini .= "user={$this->user}" . PHP_EOL;
        $ini .= "directory=%(here)s" . PHP_EOL;
        $ini .= "startsecs=0" . PHP_EOL;
        $ini .= "autostart=false" . PHP_EOL;
        $ini .= "startretries=0" . PHP_EOL;
        $ini .= "autorestart=false" . PHP_EOL;
        $ini .= "redirect_stderr=true" . PHP_EOL;
        $ini .= "stdout_logfile=AUTO" . PHP_EOL;
        $ini .= "stdout_logfile_backups=0" . PHP_EOL;
        $ini .= "stdout_logfile_maxbytes=50MB" . PHP_EOL;

        return $ini;
    }
}