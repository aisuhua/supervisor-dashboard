<?php
use Phalcon\Cli\Task;

class CronTask extends Task
{
    public function dispatcherAction()
    {
        $server_id = 41;

        /** @var Server $server */
        $server = Server::findFirst($server_id);

        $supervisor = new Supervisor(
            $server->id,
            $server->ip,
            $server->username,
            $server->password,
            $server->port
        );

        $process_name = 'sys_cron_cat:sys_cron_cat_0';

        try
        {
            $info = $supervisor->getProcessInfo($process_name);

            if ($info['statename'] != 'RUNNING')
            {
                //$supervisor->startProcess($process_name, true);
                print_cli($process_name, " started,", " server_id: {$server->id}");

                // 更新本次执行时间
                $cron = Cron\CronExpression::factory('1 * * * *');
                echo $cron->getPreviousRunDate()->format('Y-m-d H:i:s'), PHP_EOL;
                echo $cron->getNextRunDate()->format('Y-m-d H:i:s'), PHP_EOL;
            }
        }
        catch (Exception $e)
        {
            print_cli(
                get_class($e), ': ', $e->getMessage(),
                ' in File:', $e->getFile(),
                ' on Line: ', $e->getLine()
            );
        }
    }
}