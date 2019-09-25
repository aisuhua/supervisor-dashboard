<?php
namespace SupBoard\Controller;

use Phalcon\Mvc\View;

class PublicController extends ControllerBase
{
    public function getRunDatesAction()
    {
        $time = $this->request->get('time', 'trim');

        $str = '';
        if (!$time)
        {
            return $this->response->setContent($str);
        }

        try
        {
            $cron = Cron\CronExpression::factory($time);
            for ($i = 0; $i < 10; $i++)
            {
                $date =  $cron->getNextRunDate(null, $i)->format('Y-m-d H:i:s');
                $str .= "<li>{$date}</li>";
            }

            $str = "<ul>{$str}</ul>";
        }
        catch (Exception $e) {}

        return $this->response->setContent($str);
    }
}

