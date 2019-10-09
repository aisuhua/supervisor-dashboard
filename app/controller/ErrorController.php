<?php
namespace SupBoard\Controller;

use Phalcon\Mvc\View;
use Phalcon\Mvc\Controller;

class ErrorController extends Controller
{
    public function indexAction()
    {
        if ($this->request->isAjax() && !isset($_SERVER["HTTP_X_PJAX"]))
        {
            $this->response->setJsonContent([
                'state' => 0,
                'message' => '请求失败，请重试'
            ])->send();

            exit();
        }

        $this->view->disableLevel(View::LEVEL_MAIN_LAYOUT);
    }
}