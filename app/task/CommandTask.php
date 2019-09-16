<?php
use Phalcon\Cli\Task;

class CommandTask extends Task
{
    // 最大内存占用
    const MAX_MEMORY = 52428800;

    public function checkRunStateAction()
    {
        print_cli('starting...');

        while (true)
        {
            $commands = Command::find([
                "status = " . Command::STATUS_STARTED
            ]);

            if ($commands->count() == 0)
            {
                sleep(1);
                continue;
            }

            $start_time = time();

            /** @var Command $command */
            foreach ($commands as $command)
            {
                try
                {
                    $server = $command->getServer();
                    $supervisor = new Supervisor(
                        $server->id,
                        $server->ip,
                        $server->username,
                        $server->password,
                        $server->port
                    );

                    $program = $command->getProgramName();
                    $process_name = Command::makeProcessName($command->id);

                    try
                    {
                        // 极端的情况下，定时任务所对应的进程不存在
                        $info = $supervisor->getProcessInfo($process_name);
                    }
                    catch (Zend\XmlRpc\Client\Exception\FaultException $e)
                    {
                        // 若定时任务所对应的进程不存在，则将该任务标志为不确定
                        if ($e->getCode() == XmlRpc::BAD_NAME)
                        {
                            print_cli("{$process_name} 进程已经不存在，无法获取进程状态，跳过并继续往下执行");
                            $command->status = CronLog::STATUS_UNKNOWN;
                            $command->save();

                            continue;
                        }

                        print_cli("{$process_name} 进程信息获取失败，直接退出");
                        throw $e;
                    }


                    if (in_array($info['statename'], ['STARTING', 'RUNNING', 'STOPPING']))
                    {
                        // 已启动，正在运行，则跳过
                        continue;
                    }
                    elseif ($info['statename'] == 'EXITED')
                    {
                        if ($info['exitstatus'] == 0)
                        {
                            // 正常退出
                            $command->status = CronLog::STATUS_FINISHED;
                        }
                        else
                        {
                            // 异常退出则标志为失败
                            $command->status = CronLog::STATUS_FAILED;

                            print_cli("{$program} 异常退出，exitstatus: {$info['exitstatus']}, spawnerr: {$info['spawnerr']}");
                        }
                    }
                    elseif ($info['statename'] == 'STOPPED')
                    {
                        // 被中断执行
                        $command->status = CronLog::STATUS_STOPPED;

                        print_cli("{$program} 被中断执行");
                    }
                    elseif (in_array($info['statename'], ['BACKOFF', 'FATAL', 'UNKNOWN']))
                    {
                        // 异常退出
                        $command->status = CronLog::STATUS_FAILED;

                        print_cli("{$program} {$info['statename']}");
                    }

                    // 进程退出时间
                    $command->end_time = $info['stop'];
                    // 读取进程日志并写入数据库
                    $command->log = $supervisor->tailProcessStdoutLog($process_name, 0, 8 * 1024 * 1024)[0];

                    // 删除进程
                    if (!$this->removeCommand($server, $program, $info['stdout_logfile']))
                    {
                        print_cli("{$program} 进程删除失败");
                        continue;
                    }

                    // 保存最终进程状态
                    $command->save();

                    print_cli("{$program} 已执行完成");
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

            // 如果超过最大内存使用限制，则自动退出脚本
            if (($memory = memory_get_usage(true)) > self::MAX_MEMORY)
            {
                print_cli("内存占用 " . size_format($memory) ." 已超过 " . size_format(self::MAX_MEMORY) ." 最大限制，自动退出");
                break;
            }

            // 每次循环至少间隔 1 秒
            if (time() - $start_time < 1)
            {
                sleep(1);
            }
        }
    }

    /**
     * 删除命令进程
     *
     * @param Server $server
     * @param $program
     * @param $log_file
     * @return bool
     */
    private function removeCommand(Server $server, $program, $log_file = null)
    {
        $supervisor = new Supervisor(
            $server->id,
            $server->ip,
            $server->username,
            $server->password,
            $server->port
        );

        $uri = $server->getSupervisorUri();

        $commandLock = new CommandLock();
        if (!$commandLock->lock())
        {
            print_cli('锁定失败');
            return false;
        }

        try
        {
            $read = SupervisorSyncConf::read($uri, $server->command_conf);
            $is_empty_file = strpos($read['message'], 'no such file or directory');

            if (!$read['state'] && $is_empty_file === false)
            {
                print_cli("配置读取出错：{$read['message']}");
                return false;
            }

            if (empty($read['content']))
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

            $write = SupervisorSyncConf::write($uri, $server->command_conf, $ini);
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
                if ($e->getCode() == XmlRpc::BAD_NAME)
                {
                    print_cli("{$program} 不存在，无法删除，忽略错误并继续往下执行");
                }
                elseif($e->getCode() == XmlRpc::STILL_RUNNING)
                {
                    print_cli("{$program} 还在运行，无法删除");
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

            // 配置读写完成后即可解锁
            if (!$commandLock->unlock())
            {
                print_cli('解锁失败，忽略错误并继续往下执行');
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

            if (!$commandLock->unlock())
            {
                print_cli('解锁失败，忽略错误并继续往下执行');
            }

            return false;
        }
    }
}