<?php
use Phalcon\Cli\Task;

class DemoTask extends Task
{
    public function mainAction()
    {

        // Create a new instance of Jobby
        $jobby = new Jobby\Jobby();

        // Every job has a name
        $jobby->add('CommandExample', [

            // Run a shell command
            'command'  => 'ls -la',
            'debug' => true,

            // Ordinary crontab schedule format is supported.
            // This schedule runs every hour.
            'schedule' => '* * * * *',

        ]);

        $jobby->run();
    }
}