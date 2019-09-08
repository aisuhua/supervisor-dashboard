<?php
use Phalcon\Cli\Task;

class CronTask extends Task
{
    public function dispatcherAction()
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

            /** @var Cron $cron */
            foreach ($cron_list as $cron)
            {
                try
                {
                    $cronExpression = Cron\CronExpression::factory($cron->time);
                    $last_time = $cronExpression->getPreviousRunDate()->format('U');
                    $current_time = $cronExpression->getNextRunDate($cronExpression->getPreviousRunDate())->format('U');
                    $next_time = $cronExpression->getNextRunDate()->format('U');

//                    echo date('Y-m-d H:i:s'), PHP_EOL;
//                    echo date('Y-m-d H:i', $last_time), PHP_EOL;
//                    echo date('Y-m-d H:i', $current_time), PHP_EOL;
//                    echo date('Y-m-d H:i', $next_time), PHP_EOL;

                    // 针对从来没有运行过的
//                    $pre_time = Cron\CronExpression::factory($cron->time)
//                        ->getPreviousRunDate()
//                        ->format('U');
//                    $create_time = strtotime(date('Y-m-d H:i'));

//                    if (($cron->last_time > 0 && !$cronExpression->isDue() && $cron->last_time >= $last_time) ||
//                        ($cron->last_time > 0 && $cronExpression->isDue() && $cron->last_time > $last_time) ||
//                        ($cron->last_time == 0 && $pre_time <= $create_time)
//                    )
//                    {
//                        continue;
//                    }

                    if (($cron->last_time > 0 && !$cronExpression->isDue() && $cron->last_time >= $last_time) ||
                        ($cron->last_time > 0 && $cronExpression->isDue() && $cron->last_time > $last_time)
                    )
                    {
                        continue;
                    }

                    // 如果时间没有到或者已经执行过则跳过  21 22 23
//                    if (!$cronExpression->isDue() &&
//                        ($cron->last_time > 0 && $cron->last_time > $last_time)
//                    )
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

                    $now = time();
                    $datetime = date('YmdHi', $now);
                    $program = 'sys_cron_' . $cron->id . '_' . $datetime;

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
                    $cron->last_time = strtotime(date('Y-m-d H:i'));
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

            $cost_time = $start_time - time();
            if ($cost_time < 15)
            {
                sleep(15 - $cost_time);
            }
        }
    }
}