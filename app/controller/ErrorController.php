<?php
namespace SupBoard\Controller;

use Phalcon\Mvc\View;

class ErrorController extends ControllerBase
{
    public function indexAction()
    {
        if ($this->request->isAjax() && !$this->isPjax())
        {
            $this->response->setJsonContent([
                'state' => 0,
                'message' => '服务异常，请稍后重试'
            ])->send();

            exit();
        }

        $this->view->disableLevel(View::LEVEL_MAIN_LAYOUT);
    }
}