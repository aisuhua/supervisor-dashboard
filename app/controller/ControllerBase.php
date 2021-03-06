<?php
namespace SupBoard\Controller;

use Phalcon\Mvc\Controller;
use Phalcon\Mvc\View;
use SupBoard\Model\ServerGroup;
use SupBoard\Model\Server;

class ControllerBase extends Controller
{
    protected $is_pjax = false;

    public function isPjax()
    {
        return $this->is_pjax;
    }

    public function beforeExecuteRoute()
    {
        if(isset($_SERVER["HTTP_X_PJAX"]))
        {
            $this->is_pjax = true;

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
        if (!$this->isPjax() && !$this->request->isAjax())
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
                ->orderBy('g.sort DESC, s.ip ASC')
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
