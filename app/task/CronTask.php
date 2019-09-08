<?php
use Phalcon\Cli\Task;

class CronTask extends Task
{
    public function checkPerMinuteAction()
    {
        while (true)
        {
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

//                    $last_time = $cronExpression->getPreviousRunDate()->format('U');
//                    $next_time = $cronExpression->getNextRunDate($cronExpression->getPreviousRunDate())
//                        ->format('U');
//                    if (($cron->last_time > 0 && $current_time - $cron->last_time < $next_time - $last_time) ||
//                        ($cron->last_time == 0 && $current_time - $cron->update_time < $next_time - $last_time))
//                    {
//                        continue;
//                    }

                    $server = $cron->getServer();
                    $supervisor = new Supervisor(
                        $server->id,
                        $server->ip,
                        $server->username,
                        $server->password,
                        $server->port
                    );

                    $program = 'sys_cron_' . $cron->id . '_' . $current_datetime;

                    print_cli("{$program} is starting");

                    // 同步配置
                    $conf_path = '/etc/supervisor/conf.d/cron.conf';
                    $uri = "http://{$server->ip}:{$server->sync_conf_port}";

                    $read = SupervisorSyncConf::read($uri, $conf_path);
                    $is_empty_file = strpos($read['message'], 'no such file or directory');

                    if (!$read['state'] && !$is_empty_file)
                    {
                        print_cli("配置读取出错：{$read['message']}");
                        continue;
                    }

                    $ini = '';
                    $ini .= "[program:{$program}]" . PHP_EOL;
                    $ini .= "command={$cron->command}" . PHP_EOL;
                    $ini .= "process_name=%(program_name)s_%(process_num)s" . PHP_EOL;
                    $ini .= "numprocs=1" . PHP_EOL;
                    $ini .= "numprocs_start=0" . PHP_EOL;
                    $ini .= "user={$cron->user}" . PHP_EOL;
                    $ini .= "directory=%(here)s" . PHP_EOL;
                    $ini .= "autostart=false" . PHP_EOL;
                    $ini .= "startretries=0" . PHP_EOL;
                    $ini .= "autorestart=false" . PHP_EOL;
                    $ini .= "redirect_stderr=true" . PHP_EOL;
                    $ini .= "stdout_logfile=AUTO" . PHP_EOL;
                    $ini .= "stdout_logfile_backups=0" . PHP_EOL;
                    $ini .= "stdout_logfile_maxbytes=50MB" . PHP_EOL;

                    $ini = trim($read['content']) . PHP_EOL . $ini;

                    $write = SupervisorSyncConf::write($uri, $conf_path, $ini);
                    if (!$write['state'])
                    {
                        print_cli("配置写入出错：{$write['message']}");
                        continue;
                    }

                    $supervisor->reloadConfig();
                    $supervisor->addProcessGroup($program);
                    $supervisor->startProcessGroup($program, false);

                    // 更新执行时间
                    $cron->last_time = $current_time;
                    $cron->save();

                    // 写日志记录

                    print_cli("{$program} has started");
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

            $cost_time = time() - $start_time;
            print_cli("cost_time: {$cost_time}s");

            if ($cost_time >= 30)
            {
                print_cli("任务启动时间过长，请优化");
            }

            if ($cost_time < 60)
            {
                sleep(60 - $cost_time + 1);
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

                // 只启动那些因关机等原因
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
                $ini = '';
                $ini .= "[program:{$program}]" . PHP_EOL;
                $ini .= "command={$cron->command}" . PHP_EOL;
                $ini .= "process_name=%(program_name)s_%(process_num)s" . PHP_EOL;
                $ini .= "numprocs=1" . PHP_EOL;
                $ini .= "numprocs_start=0" . PHP_EOL;
                $ini .= "user={$cron->user}" . PHP_EOL;
                $ini .= "directory=%(here)s" . PHP_EOL;
                $ini .= "autostart=false" . PHP_EOL;
                $ini .= "startretries=0" . PHP_EOL;
                $ini .= "autorestart=false" . PHP_EOL;
                $ini .= "redirect_stderr=true" . PHP_EOL;
                $ini .= "stdout_logfile=AUTO" . PHP_EOL;
                $ini .= "stdout_logfile_backups=0" . PHP_EOL;
                $ini .= "stdout_logfile_maxbytes=50MB" . PHP_EOL;

                if (!$this->addCron($server, $program, $ini))
                {
                    print_cli("{$program} 配置添加失败");
                    continue;
                }

                // 更新执行时间
                $cron->last_time = $current_time;
                $cron->save();

                // 写日志记录

                print_cli("{$program} has started");
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
    }

    private function addCron(Server $server, $program, $ini)
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

            if (!$read['state'] && !$is_empty_file)
            {
                print_cli("配置读取出错：{$read['message']}");
                return false;
            }

            $ini = trim($read['content']) . PHP_EOL . $ini;

            $write = SupervisorSyncConf::write($uri, $conf_path, $ini);
            if (!$write['state'])
            {
                print_cli("配置写入出错：{$write['message']}");
                return false;
            }

            $supervisor->reloadConfig();
            $supervisor->addProcessGroup($program);
            $supervisor->startProcessGroup($program, false);
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

        if (!$config_lock->unlock())
        {
            echo '配置解锁失败', PHP_EOL;
            return false;
        }

        return true;
    }

    public function lockAction()
    {
        $config_lock = new ConfigLock();
        if (!$config_lock->lock())
        {
            echo '配置锁定失败', PHP_EOL;
            return false;
        }

        echo '111', PHP_EOL;
        sleep(10);
        echo '222', PHP_EOL;

        if (!$config_lock->unlock())
        {
            echo '配置解锁失败', PHP_EOL;
            return false;
        }

        echo 'done', PHP_EOL;
    }
}