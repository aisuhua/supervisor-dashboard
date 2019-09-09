<?php
use Phalcon\Cli\Task;

class CronTask extends Task
{
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

                    // 更新执行时间
                    $cron->last_time = $current_time;
                    $cron->save();

                    // 写执行日志
                    $cronLog = new CronLog();
                    $cronLog->cron_id = $cron->id;
                    $cronLog->server_id = $cron->server_id;
                    $cronLog->program = $program;
                    $cronLog->command = $cron->command;
                    $cronLog->start_time = time();
                    $cronLog->create();

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
                try
                {
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
                            strpos($e->getMessage(), 'BAD_NAME:') !== false
                        )
                        {
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
                        $cronLog->status = CronLog::STATUS_FINISHED;
                    }
                    elseif (in_array($info['statename'], ['BACKOFF', 'FATAL', 'UNKNOWN', 'STOPPED']))
                    {
                        $cronLog->status = CronLog::STATUS_FAILED;
                    }

                    // 进程退出时间
                    $cronLog->end_time = $info['stop'];
                    // 读取进程日志并写入数据库
                    $cronLog->log = $supervisor->tailProcessStdoutLog($process_name, 0, 16 * 1024 * 1024)[0];
                    $cronLog->save();

                    // 清理进程日志，以释放该任务所占用的日志文件
                    $result = $supervisor->clearProcessLogs($process_name);
                    $result = $supervisor->clearProcessLogs($process_name);
                    var_dump($result);

                    // 删除进程
                    $this->removeCron($server, $cronLog->program);

                    print_cli("{$cronLog->program} 已执行完成");
                }
                catch (Exception $e)
                {
                    print_cli($e->getMessage());
                }
            }

            sleep(1);
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
        $ini .= "autostart=false" . PHP_EOL;
        $ini .= "startretries=0" . PHP_EOL;
        $ini .= "autorestart=false" . PHP_EOL;
        $ini .= "redirect_stderr=true" . PHP_EOL;
        $ini .= "stdout_logfile=/var/log/supervisor/demo.log" . PHP_EOL;
        $ini .= "stdout_logfile_backups=0" . PHP_EOL;
        $ini .= "stdout_logfile_maxbytes=512MB" . PHP_EOL;

        return $ini;
    }

    /**
     * 删除定时任务
     *
     * @param Server $server
     * @param $program
     * @return bool
     */
    private function removeCron(Server $server, $program)
    {
        $supervisor = new Supervisor(
            $server->id,
            $server->ip,
            $server->username,
            $server->password,
            $server->port
        );

        $conf_path = '/etc/supervisor/conf.d/cron.conf';
        $uri = "http://{$server->ip}:{$server->sync_conf_port}";

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
                print_cli("配置文件为空，跳过");
                return true;
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
                print_cli("不存在 {$key} 的配置项，跳过");
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

            $supervisor->reloadConfig();
            $supervisor->removeProcessGroup($program);
        }
        catch (Exception $e)
        {
            print_cli($e->getMessage());

            if (!$config_lock->unlock())
            {
                echo '配置解锁失败', PHP_EOL;
            }

            return false;
        }

        if (!$config_lock->unlock())
        {
            echo '配置解锁失败', PHP_EOL;
            return false;
        }

        return true;
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

        try
        {
            $config_lock = new ConfigLock();
            if (!$config_lock->lock())
            {
                print_cli('配置锁定失败');
                return false;
            }

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
                    print_cli("{$key} 配置项已经存在，跳过");
                    return true;
                }

                $origin = build_ini_string($parsed);
                $ini = trim($origin) . PHP_EOL . $ini;
            }

            $write = SupervisorSyncConf::write($uri, $conf_path, $ini);
            if (!$write['state'])
            {
                print_cli("配置写入出错：{$write['message']}");
                return false;
            }

            $supervisor->reloadConfig();
            $supervisor->addProcessGroup($program);
            $supervisor->startProcessGroup($program, false);

            if (!$config_lock->unlock())
            {
                echo '配置解锁失败', PHP_EOL;
                return false;
            }

            return true;
        }
        catch (Exception $e)
        {
//            print_cli(
//                get_class($e), ': ', $e->getMessage(),
//                ' in File:', $e->getFile(),
//                ' on Line: ', $e->getLine(),
//                "\n Trace: ", $e->getTraceAsString()
//            );
            print_cli($e->getMessage());

            if (!$config_lock->unlock())
            {
                echo '配置解锁失败', PHP_EOL;
            }

            return false;
        }
    }
}