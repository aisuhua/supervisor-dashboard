<?php
use Phalcon\Mvc\Controller;
use Phalcon\Mvc\View;

class ControllerBase extends Controller
{
    protected $isPjax = false;
    protected $renderPjax = false;

    public function beforeExecuteRoute()
    {
        if(isset($_SERVER["HTTP_X_PJAX"]))
        {
            $this->isPjax = true;
            $this->renderPjax = true;

            //302跳转是由浏览器自动发起，此时将不会带上 _pjax 参数
            //因此，利用此特征来有选择性地修改url地址
            if(!$this->request->get('_pjax'))
            {
                $this->response->setHeader('X-PJAX-URL', $_SERVER['REQUEST_URI']);
            }
        }
    }

    public function afterExecuteRoute()
    {
        if($this->isPjax() && $this->renderPjax === true)
        {
            $this->view->setRenderLevel(View::LEVEL_ACTION_VIEW);
        }
    }

    public function isPjax()
    {
        return $this->isPjax;
    }

    public function disablePjax()
    {
        $this->renderPjax = false;
    }
}
