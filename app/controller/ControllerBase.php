<?php
use Phalcon\Mvc\Controller;
use Phalcon\Mvc\View;

class ControllerBase extends Controller
{
    protected $isPjax = false;

    public function isPjax()
    {
        return $this->isPjax;
    }

    public function beforeExecuteRoute()
    {
        if(isset($_SERVER["HTTP_X_PJAX"]))
        {
            $this->isPjax = true;

            //302跳转是由浏览器自动发起，此时将不会带上 _pjax 参数
            //因此，利用此特征来有选择性地修改url地址
            if(!$this->request->get('_pjax'))
            {
                $this->response->setHeader('X-PJAX-URL', $_SERVER['REQUEST_URI']);
            }

            $this->view->setRenderLevel(View::LEVEL_BEFORE_TEMPLATE);
        }
    }

    public function afterExecuteRoute()
    {
        if (!$this->isPjax())
        {
            $result = $this
                ->modelsManager
                ->createBuilder()
                ->from(['g' => ServerGroup::class])
                ->leftJoin(Server::class, "s.server_group_id = g.id", 's')
                ->columns([
                    'g.id as server_group_id',
                    'g.name as server_group_name',
                    's.id as server_id',
                    's.ip as server_ip',
                    's.port as server_port',
                ])
                ->orderBy('g.sort DESC, s.sort DESC, s.create_time ASC')
                ->getQuery()
                ->execute();

            $menus = [];
            $menu_servers = [];
            if ($result->count() > 0)
            {
                foreach ($result as $item)
                {
                    $menus[$item['server_group_id']] = $item['server_group_name'];

                    if (!empty($item['server_id']))
                    {
                        !empty($menu_servers[$item['server_group_id']]) ?: $menu_servers[$item['server_group_id']] = [];
                        $menu_servers[$item['server_group_id']][] = [
                            'id' => $item['server_id'],
                            'ip' => $item['server_ip'],
                            'port' => $item['server_port'],
                        ];
                    }
                }
            }

            $this->view->menus = $menus;
            $this->view->menu_servers = $menu_servers;
        }
    }
}
