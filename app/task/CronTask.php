<?php
use Phalcon\Cli\Task;

class CronTask extends Task
{
    // 每个定时任务保留的日志记录数
    const MAX_LOG_KEEP = 60;

    public function checkPerMinuteAction()
    {
        while (true)
        {
            if (((int) date('s')) != 0)
            {
                sleep(1);
                continue;
            }

            $cron_list = Cron::find('status = ' . Cron::STATUS_ACTIVE);
            if (empty($cron_list))
            {
                sleep(1);
                continue;
            }

            $start_time = time();
            $now = new DateTime();
            $current_datetime = $now->format('YmdHi');
            $current_time = (new DateTime($current_datetime))->format('U');

            /** @var Cron $cron */
            foreach ($cron_list as $cron)
            {
                try
                {
                    $cronExpression = Cron\CronExpression::factory($cron->time);

                    if (!$cronExpression->isDue($now) || $current_time <= $cron->last_time)
                    {
                        continue;
                    }

                    $server = $cron->getServer();
                    $program = 'sys_cron_' . $cron->id . '_' . $current_datetime;

                    print_cli("{$program} is starting");

                    $ini = $this->makeIni($program, $cron->command, $cron->user);

                    if (!$this->addCron($server, $program, $ini))
                    {
                        print_cli("{$program} 配置添加失败");
                        continue;
                    }

                    // 写执行日志
                    $cronLog = new CronLog();
                    $cronLog->cron_id = $cron->id;
                    $cronLog->server_id = $cron->server_id;
                    $cronLog->program = $program;
                    $cronLog->command = $cron->command;
                    $cronLog->start_time = time();
                    $cronLog->create();

                    // 更新执行时间
                    $cron->last_time = $current_time;
                    $cron->save();

                    print_cli("{$program} has started");
                }
                catch (Exception $e)
                {
                    print_cli($e->getMessage());
                }
            }

            $cost_time = time() - $start_time;
            print_cli("cost_time: {$cost_time}s");

            if ($cost_time >= 30)
            {
                print_cli("任务启动时间过长，请优化");
            }

            // 防止当前分钟启动多次检测
            if ($cost_time < 1)
            {
                sleep(1);
            }
        }
    }

    public function checkPerDayAction()
    {
        $cron_list = Cron::find('status = ' . Cron::STATUS_ACTIVE);

        $now = new DateTime();
        $current_datetime = $now->format('YmdHi');
        $current_time = (new DateTime($current_datetime))->format('U');

        /** @var Cron $cron */
        foreach ($cron_list as $cron)
        {
            try
            {
                $cronExpression = Cron\CronExpression::factory($cron->time);

                $last_time = $cronExpression->getPreviousRunDate()->format('U');
                $next_time = $cronExpression->getNextRunDate($cronExpression->getPreviousRunDate())
                    ->format('U');

                // 任务周期少于一天不过检查
                if ($next_time - $last_time < 86400)
                {
                    continue;
                }

                // 只启动那些因关机等原因导致没有在约定时间启动的任务，类似 anacron 的功能
                $delay = (int) $next_time - (int) $last_time + 3600;
                if (($cron->last_time > 0 && $current_time - $cron->last_time < $delay) ||
                    ($cron->last_time == 0 && $current_time - $cron->update_time < $delay) ||
                    $current_time == $cron->last_time
                )
                {
                    continue;
                }

                $server = $cron->getServer();
                $program = 'sys_cron_' . $cron->id . '_' . $current_datetime;

                print_cli("{$program} is starting");

                // 同步配置
                $ini = $this->makeIni($program, $cron->command, $cron->user);

                if (!$this->addCron($server, $program, $ini))
                {
                    print_cli("{$program} 配置添加失败");
                    continue;
                }

                // 更新执行时间
                $cron->last_time = $current_time;
                $cron->save();

                print_cli("{$program} has started");
            }
            catch (Exception $e)
            {
                print_cli($e->getMessage());
            }
        }
    }

    public function checkRunStateAction()
    {
        while (true)
        {
            $cronLogs = CronLog::find([
                "status = " . CronLog::STATUS_INI . ' OR status = ' . CronLog::STATUS_STARTED
            ]);

            if ($cronLogs->count() == 0)
            {
                sleep(1);
                continue;
            }

            /** @var CronLog $cronLog */
            foreach ($cronLogs as $cronLog)
            {
                try {
                    $server = $cronLog->getServer();
                    $supervisor = new Supervisor(
                        $server->id,
                        $server->ip,
                        $server->username,
                        $server->password,
                        $server->port
                    );

                    $process_name = $cronLog->program . ':' . $cronLog->program . '_0';

                    try
                    {
                        // 极端的情况下，定时任务所对应的进程不存在
                        $info = $supervisor->getProcessInfo($process_name);
                    }
                    catch (Exception $e)
                    {
                        print_cli($e->getMessage());

                        // 若定时任务所对应的进程不存在，则将该任务标志为不确定
                        if ($e instanceof Zend\XmlRpc\Client\Exception\FaultException &&
                            $e->getCode() == XmlRpc::BAD_NAME)
                        {
                            print_cli("{$process_name} 进程已经不存在，无法获取进程状态，跳过并继续往下执行");
                            $cronLog->status = CronLog::STATUS_UNKNOWN;
                            $cronLog->save();

                            continue;
                        }

                        throw $e;
                    }

                    if (in_array($info['statename'], ['STARTING', 'RUNNING', 'STOPPING']))
                    {
                        // 已启动，正在运行，则跳过
                        $cronLog->status = CronLog::STATUS_STARTED;
                        $cronLog->save();

                        continue;
                    }

                    if ($info['statename'] == 'EXITED')
                    {
                        // 正常退出
                        $cronLog->status = CronLog::STATUS_FINISHED;
                    }
                    elseif (in_array($info['statename'], ['BACKOFF', 'FATAL', 'UNKNOWN']))
                    {
                        // 异常退出
                        $cronLog->status = CronLog::STATUS_FAILED;
                    }
                    elseif ($info['statename'] == 'STOPPED')
                    {
                        // 被中断执行
                        $cronLog->status = CronLog::STATUS_STOPPED;
                    }

                    // 进程退出时间
                    $cronLog->end_time = $info['stop'];
                    // 读取进程日志并写入数据库
                    $cronLog->log = $supervisor->tailProcessStdoutLog($process_name, 0, 16 * 1024 * 1024)[0];

                    // 删除进程
                    if (!$this->removeCron($server, $cronLog->program, $info['stdout_logfile']))
                    {
                        print_cli("{$cronLog->program} 进程删除失败");
                        continue;
                    }

                    // 保存最终进程状态
                    $cronLog->save();

                    // 每个任务只保存最新的 60 份日志
                    $offset = self::MAX_LOG_KEEP - 1;
                    $sql = "select id from cron_log where cron_id = {$cronLog->cron_id} order by id desc limit {$offset}, 1";
                    $one = $this->db->fetchOne($sql);
                    if ($one)
                    {
                        $sql = "DELETE FROM cron_log where cron_id = {$cronLog->cron_id} and id < {$one['id']}";
                        $this->db->execute($sql);
                    }

                    print_cli("{$cronLog->program} 已执行完成");
                }
                catch (Exception $e)
                {
                    print_cli(
                        get_class($e), ': ', $e->getMessage(),
                        ' in File:', $e->getFile(),
                        ' on Line: ', $e->getLine(),
                        "\n Trace: ", $e->getTraceAsString()
                    );
                }
            }

            sleep(1);
        }
    }

    /**
     * 每个小时启动清理僵死进程
     */
    public function clearDefunctProcessAction()
    {

    }

    /**
     * 添加定时任务
     *
     * @param Server $server
     * @param $program
     * @param $ini
     * @return bool
     */
    private function addCron(Server $server, $program, $ini)
    {
        $supervisor = new Supervisor(
            $server->id,
            $server->ip,
            $server->username,
            $server->password,
            $server->port
        );

        $uri = $server->getSupervisorUri();
        $conf_path = $server->getCronConfPath();

        $config_lock = new ConfigLock();
        if (!$config_lock->lock())
        {
            print_cli('配置锁定失败');
            return false;
        }

        try
        {
            $read = SupervisorSyncConf::read($uri, $conf_path);
            $is_empty_file = strpos($read['message'], 'no such file or directory');

            if (!$read['state'] && $is_empty_file === false)
            {
                print_cli("配置读取出错：{$read['message']}");
                return false;
            }

            // 如果配置不为空
            if (!empty($read['content']))
            {
                $parsed = parse_ini_string($read['content'], true, INI_SCANNER_RAW);
                if ($parsed === false)
                {
                    print_cli("配置解析出错：{$read['content']}");
                    return false;
                }

                $key = "program:{$program}";

                // 现有配置项已经有该进程的信息
                if (isset($parsed[$key]))
                {
                    print_cli("{$key} 配置项已经存在，跳过添加步骤");
                }
                else
                {
                    $origin = build_ini_string($parsed);
                    $ini = trim($origin) . PHP_EOL . $ini;

                    $write = SupervisorSyncConf::write($uri, $conf_path, $ini);
                    if (!$write['state'])
                    {
                        print_cli("配置写入出错：{$write['message']}");
                        return false;
                    }
                }
            }

            $supervisor->reloadConfig();

            try
            {
                $supervisor->addProcessGroup($program);
            }
            catch (Zend\XmlRpc\Client\Exception\FaultException $e)
            {
                print_cli($e->getMessage());

                if ($e->getCode() != XmlRpc::ALREADY_ADDED)
                {
                    throw $e;
                }

                print_cli("{$program} 进程组已经存在，忽略错误信息并进入下一步");
            }

            $supervisor->startProcessGroup($program, false);

            if (!$config_lock->unlock())
            {
                echo '配置解锁失败', PHP_EOL;
            }

            return true;
        }
        catch (Exception $e)
        {
            print_cli(
                get_class($e), ': ', $e->getMessage(),
                ' in File:', $e->getFile(),
                ' on Line: ', $e->getLine(),
                "\n Trace: ", $e->getTraceAsString()
            );

            if (!$config_lock->unlock())
            {
                echo '配置解锁失败', PHP_EOL;
            }

            return false;
        }
    }

    /**
     * 删除定时任务
     *
     * @param Server $server
     * @param $program
     * @param $log_file
     * @return bool
     */
    private function removeCron(Server $server, $program, $log_file = null)
    {
        $supervisor = new Supervisor(
            $server->id,
            $server->ip,
            $server->username,
            $server->password,
            $server->port
        );

        $uri = $server->getSupervisorUri();
        $conf_path = $server->getCronConfPath();

        $config_lock = new ConfigLock();
        if (!$config_lock->lock())
        {
            print_cli('配置锁定失败');
            return false;
        }

        try
        {
            $read = SupervisorSyncConf::read($uri, $conf_path);
            $is_empty_file = strpos($read['message'], 'no such file or directory');

            if (!$read['state'] && $is_empty_file === false)
            {
                print_cli("配置读取出错：{$read['message']}");
                return false;
            }

            if (!$read['content'])
            {
                print_cli("配置文件为空，直接跳到 reload 配置步骤");
                goto reload;
            }

            $parsed = parse_ini_string($read['content'], true, INI_SCANNER_RAW);
            if ($parsed === false)
            {
                print_cli("配置解析出错：{$read['content']}");
                return false;
            }

            $key = "program:{$program}";

            if (!isset($parsed[$key]))
            {
                print_cli("不存在 {$key} 的配置项，无需删除并跳过");
                return true;
            }

            unset($parsed[$key]);
            $ini = build_ini_string($parsed);

            $write = SupervisorSyncConf::write($uri, $conf_path, $ini);
            if (!$write['state'])
            {
                print_cli("配置写入出错：{$write['message']}");
                return false;
            }

            // 重载配置步骤
            reload:

            $supervisor->reloadConfig();

            try
            {
                $supervisor->removeProcessGroup($program);
            }
            catch (Zend\XmlRpc\Client\Exception\FaultException $e)
            {
                print_cli($e->getMessage());

                if ($e->getCode() == XmlRpc::BAD_NAME)
                {
                    print_cli("{$program} 进程组已经不存在，无法删除，跳过并继续往下执行");
                }
                elseif($e->getCode() == XmlRpc::STILL_RUNNING)
                {
                    print_cli("{$program} 进程组还在运行，无法删除");
                    throw $e;
                }
                else
                {
                    throw $e;
                }
            }

            // 清理进程日志，以释放该任务所占用的日志文件
            // clearProcessLogs 方法不能清理已经退出的进程日志
            if ($log_file)
            {
                $deleted = SupervisorSyncConf::delete($uri, $log_file);
                if (!$deleted['state'])
                {
                    print_cli("日志文件删除失败：{$log_file}");
                    return false;
                }
            }

            if (!$config_lock->unlock())
            {
                print_cli('配置解锁失败');
            }

            return true;
        }
        catch (Exception $e)
        {
            print_cli(
                get_class($e), ': ', $e->getMessage(),
                ' in File:', $e->getFile(),
                ' on Line: ', $e->getLine(),
                "\n Trace: ", $e->getTraceAsString()
            );

            if (!$config_lock->unlock())
            {
                print_cli('配置解锁失败');
            }

            return false;
        }
    }

    private function makeIni($program, $command, $user)
    {
        $ini = '';
        $ini .= "[program:{$program}]" . PHP_EOL;
        $ini .= "command={$command}" . PHP_EOL;
        $ini .= "process_name=%(program_name)s_%(process_num)s" . PHP_EOL;
        $ini .= "numprocs=1" . PHP_EOL;
        $ini .= "numprocs_start=0" . PHP_EOL;
        $ini .= "user={$user}" . PHP_EOL;
        $ini .= "directory=%(here)s" . PHP_EOL;
        $ini .= "startsecs=1" . PHP_EOL;
        $ini .= "autostart=false" . PHP_EOL;
        $ini .= "startretries=0" . PHP_EOL;
        $ini .= "autorestart=false" . PHP_EOL;
        $ini .= "redirect_stderr=true" . PHP_EOL;
        $ini .= "stdout_logfile=AUTO" . PHP_EOL;
        $ini .= "stdout_logfile_backups=0" . PHP_EOL;
        $ini .= "stdout_logfile_maxbytes=16MB" . PHP_EOL;

        return $ini;
    }
}