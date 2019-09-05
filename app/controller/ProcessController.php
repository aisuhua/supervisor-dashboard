<?php
use Phalcon\Mvc\View;

class ProcessController extends ControllerSupervisorBase
{
    public function indexAction()
    {
        $callback = function ()
        {
            $processes = $this->supervisor->getAllProcessInfo();
            $processGroups = array_unique(array_column($processes, 'group'));
            $process_warnings = array_filter($processes, function($process) {
                return $process['statename'] != 'RUNNING';
            });

            $this->view->processes = $processes;
            $this->view->processGroups = $processGroups;
            $this->view->process_warnings = $process_warnings;
        };

        $this->setCallback($callback);
        $this->invoke();
    }

    public function stopAction()
    {
        $result = [];
        $group = $this->request->get('group', 'string');
        $name = $this->request->get('name', 'string');
        $process_name = $group . ':' . $name;

        $callback = function () use ($process_name) {
            $process = $this->supervisor->getProcessInfo($process_name);
            if ($process['statename'] == 'RUNNING')
            {
                $this->supervisor->stopProcess($process_name, false);
            }
        };
        $this->setCallback($callback);
        $this->invoke();

        $result['state'] = 1;
        $result['message'] = self::formatMessage($name . " 正在停止");

        return $this->response->setJsonContent($result);
    }

    public function startAction()
    {
        $result = [];
        $group = $this->request->get('group', 'string');
        $name = $this->request->get('name', 'string');
        $process_name = $group . ':' . $name;

        $callback = function() use ($process_name)
        {
            $process = $this->supervisor->getProcessInfo($process_name);
            if ($process['statename'] != 'RUNNING')
            {
                $this->supervisor->startProcess($process_name, false);
            }
        };
        $this->setCallback($callback);
        $this->invoke();

        $result['state'] = 1;
        $result['message'] = self::formatMessage($name . " 正在启动");

        return $this->response->setJsonContent($result);
    }

    public function restartAction()
    {
        $result = [];
        $group = $this->request->get('group', 'string');
        $name = $this->request->get('name', 'string');
        $process_name = $group . ':' . $name;

        $result['state'] = 1;
        $result['message'] = self::formatMessage($name . " 正在重启");

        $this->response->setJsonContent($result)->send();

        fastcgi_finish_request();

        $callback = function() use ($process_name)
        {
            $process = $this->supervisor->getProcessInfo($process_name);
            if ($process['statename'] == 'RUNNING')
            {
                $this->supervisor->stopProcess($process_name, true);
            }

            $this->supervisor->startProcess($process_name, false);
        };
        $this->setCallback($callback);
        $this->invoke();
    }

    public function stopGroupAction()
    {
        $result = [];
        $group = $this->request->get('group', 'string');

        $callback = function() use($group)
        {
            $this->supervisor->stopProcessGroup($group, false);
        };
        $this->setCallback($callback);
        $this->invoke();

        $result['state'] = 1;
        $result['message'] = self::formatMessage($group . " 正在停止");

        return $this->response->setJsonContent($result);
    }

    public function startGroupAction()
    {
        $result = [];
        $group = $this->request->get('group', 'string');

        $callback = function() use ($group)
        {
            $this->supervisor->startProcessGroup($group, false);
        };
        $this->setCallback($callback);
        $this->invoke();

        $result['state'] = 1;
        $result['message'] = self::formatMessage($group . " 正在启动");

        return $this->response->setJsonContent($result);
    }

    public function restartGroupAction()
    {
        $result = [];
        $group = $this->request->get('group', 'string');

        $result['state'] = 1;
        $result['message'] = self::formatMessage($group . " 正在重启");

        $this->response->setJsonContent($result)->send();

        fastcgi_finish_request();

        $callback = function() use ($group)
        {
            $this->supervisor->stopProcessGroup($group, true);
            $this->supervisor->startProcessGroup($group, false);
        };
        $this->setCallback($callback);
        $this->invoke();
    }

    public function tailLogAction()
    {
        $group = $this->request->get('group', 'string');
        $name = $this->request->get('name', 'string');
        $process_name = $group . ':' . $name;

        $callback = function() use ($process_name)
        {
            // 只看前面 1M 的日志
            // 注意这里应开启 strip_ansi = true，否则当日志含有 ansi 字符时讲无法查看日志
            $log = $this->supervisor->tailProcessStdoutLog($process_name, 0, 1 * 1024 * 1024);
            $this->view->log = $log;
        };
        $this->setCallback($callback);
        $this->invoke(30);

        $this->view->disableLevel([
            View::LEVEL_LAYOUT => true,
        ]);

        $this->view->setTemplateBefore('tailLog');

        if ($this->isPjax())
        {
            $this->view->setRenderLevel(View::LEVEL_ACTION_VIEW);
        }

        $this->view->group = $group;
        $this->view->name = $name;
    }

    public function clearLogAction()
    {
        $result = [];
        $group = $this->request->get('group', 'string');
        $name = $this->request->get('name', 'string');
        $process_name = $group . ':' . $name;

        $callback = function() use ($process_name)
        {
            $this->supervisor->clearProcessLogs($process_name);
        };
        $this->setCallback($callback);
        $this->invoke();

        $result['state'] = 1;
        $result['message'] = $name . " 日志清理完成";

        return $this->response->setJsonContent($result);
    }

    public function stopAllAction()
    {
        $result = [];
        $result['state'] = 1;
        $result['message'] = self::formatMessage("正在停止所有进程");
        $this->response->setJsonContent($result)->send();

        fastcgi_finish_request();

        $callback = function()
        {
            $this->supervisor->stopAllProcesses(false);
        };
        $this->setCallback($callback);
        $this->invoke();

        $this->flashSession->success("已停止所有进程");

        $this->view->setRenderLevel(
            View::LEVEL_NO_RENDER
        );
    }

    public function restartAllAction()
    {
        $result = [];
        $result['state'] = 1;
        $result['message'] = self::formatMessage("正在重启所有进程");
        $this->response->setJsonContent($result)->send();

        fastcgi_finish_request();

        $callback = function()
        {
            $this->supervisor->stopAllProcesses(true);
            $this->supervisor->startAllProcesses(false);
        };
        $this->setCallback($callback);
        $this->invoke();

        $this->flashSession->success("已重启所有进程");

        $this->view->setRenderLevel(
            View::LEVEL_NO_RENDER
        );
    }

    public function reloadConfigAction()
    {
        $result = [];

        $processes = Process::find([
            'server_id = :server_id:',
            'bind' => [
                'server_id' => $this->server->id
            ],
            'order' => 'program asc, id asc'
        ]);

        $ini_arr = [];
        foreach ($processes as $process)
        {
            /** @var Process $process */
            $ini_arr[] = $process->getIni();
        }

        $ini = implode(PHP_EOL, $ini_arr);

        $url = "http://{$this->server->ip}:{$this->server->sync_conf_port}/write";
        $post_data = [];
        $post_data['file_path'] = $this->server->conf_path;
        $post_data['content'] = $ini;
        $post_data['timestamp'] = (string) time();

        $auth_key = 'Mx#d7Xp%ks7m3R1g&XmoUw%9qQ74ehor';
        $post_data['token'] = strtoupper(md5(
            $post_data['file_path'] . ':' . $post_data['content'] . ':' . $post_data['timestamp'] . ':' . $auth_key
        ));

        $ret = curl_post($url, json_encode($post_data));
        $ret = json_decode($ret, true);

        if (!$ret['state'])
        {
            $result['state'] = 0;
            $result['message'] = $ret['message'];

            return $this->response->setJsonContent($result);
        }

        $added = [];
        $changed = [];
        $removed = [];

        $callback = function() use (&$added, &$changed, &$removed)
        {
            list($added, $changed, $removed) = $this->supervisor->reloadConfig()[0];
        };
        $this->setCallback($callback);
        $this->invoke();

        $messages = [];
        if (!empty($added))
        {
            foreach ($added as $key => $item)
            {
                $messages[] = "{$item} 正在添加";
            }
        }

        if (!empty($removed))
        {
            foreach ($removed as $key => $item)
            {
                $messages[] = "{$item} 正在删除";
            }
        }

        if (!empty($changed))
        {
            foreach ($changed as $key => $item)
            {
                $messages[] = "{$item} 正在更新";
            }
        }

        if (empty($messages))
        {
            $result['state'] = 2;
            $result['message'] = "没有配置需要更新";
        }
        else
        {
            $result['state'] = 1;
            $result['message'] = $this->formatMessage(implode("\n", $messages));
        }

        $this->response->setJsonContent($result)->send();

        fastcgi_finish_request();

        foreach ($added as $group)
        {
            $callback = function() use ($group)
            {
                $this->supervisor->addProcessGroup($group);
            };
            $this->setCallback($callback);
            $this->invoke();
        }

        foreach ($changed as $group)
        {
            $callback = function() use ($group)
            {
                $this->supervisor->stopProcessGroup($group);
                $this->supervisor->removeProcessGroup($group);
                $this->supervisor->addProcessGroup($group);
            };
            $this->setCallback($callback);
            $this->invoke();
        }

        foreach ($removed as $group)
        {
            $callback = function() use ($group)
            {
                $this->supervisor->stopProcessGroup($group);
                $this->supervisor->removeProcessGroup($group);
            };
            $this->setCallback($callback);
            $this->invoke();
        }

        $this->view->setRenderLevel(
            View::LEVEL_NO_RENDER
        );
    }

    public function editAction()
    {
        $result = [];
        $id = $this->request->get('id', 'int', 0);

        $process = Process::findFirst($id);

        if ($this->request->isPost())
        {
            $form = new ProcessForm($process, [
                'edit' => true
            ]);

            if (!$form->isValid($this->request->getPost()))
            {
                foreach ($form->getMessages() as $message)
                {
                    $result['state'] = 0;
                    $result['message'] = $message->getMessage();

                    return $this->response->setJsonContent($result);
                }
            }

            if (!$process->save())
            {
                foreach ($process->getMessages() as $message)
                {
                    $result['state'] = 0;
                    $result['message'] = $message->getMessage();

                    return $this->response->setJsonContent($result);
                }
            }

            $this->flashSession->success("修改成功");
            $form->clear();
        }

//        $this->view->process = $process;
//        $this->view->form = new ProcessForm($process, [
//            'edit' => true
//        ]);
    }

    public function createAction()
    {
        $form = new ProcessForm(null);

        if ($this->request->isPost())
        {
            if ($this->request->getPost('mode') == 'ini')
            {
                return $this->dispatcher->forward([
                    'action' => 'createIni'
                ]);
            }

            $process = new Process();
            $form->bind($this->request->getPost(), $process);

            if (!$form->isValid())
            {
                foreach ($form->getMessages() as $message)
                {
                    $this->flash->error($message->getMessage());
                }
            }
            else
            {
                if (!$process->create())
                {
                    $this->flash->error($process->getMessages());
                }
                else
                {
                    $form->clear();
                    $this->flash->success("添加成功");
                    $this->view->reload_config = true;
                }
            }
        }

        $this->view->form = $form;
        $this->view->ini = Process::getIniTemplate();
    }

    public function createIniAction()
    {
        $form = new ProcessForm(null);

        if ($this->request->isPost())
        {
            $server_id = $this->request->getPost('server_id', 'int');
            $ini = $this->request->getPost('ini');

            $parsed = parse_ini_string($ini, true, INI_SCANNER_RAW);
            if (empty($parsed))
            {
                $this->flash->error('配置解析错误');
            }
            else
            {
                $key = trim(key($parsed));
                $value = current($parsed);

                if (!preg_match("/^program:[a-zA-Z0-9_\-]{1,255}$/", $key, $matches))
                {
                    $this->flash->error('配置解析错误');
                }
                else
                {
                    $value['program'] = explode(':', $key)[1];
                    $value['server_id'] = $server_id;
                    Process::applyDefaultValue($value);

                    $process = new Process();
                    $form->bind($value, $process);

                    if (!$form->isValid())
                    {
                        foreach ($form->getMessages() as $message)
                        {
                            $this->flash->error($message->getMessage());
                        }
                    }
                    else
                    {
                        if (!$process->create())
                        {
                            $this->flash->error($process->getMessages());
                        }
                        else
                        {
                            unset($ini);
                            $this->flash->success("添加成功");
                            $this->view->reload_config = true;
                        }
                    }

                    $form->clear();
                }
            }
        }

        $this->view->pick('process/create');
        $this->view->create_ini = true;
        $this->view->form = $form;
        $this->view->ini = isset($ini) ? $ini : Process::getIniTemplate();
    }

    public function deleteAction()
    {
        $result = [];
        $program = $this->request->get('group', 'string');

        $process = Process::findFirst([
            'server_id = :server_id: AND program = :program:',
            'bind' => [
                'server_id' => $this->server->id,
                'program' => $program
            ]
        ]);

        if (!$process)
        {
            $result['state'] = 0;
            $result['message'] = '不存在该进程配置';

            return $this->response->setJsonContent($result);
        }

        if(!$process->delete())
        {
            foreach ($process->getMessages() as $message)
            {
                $result['state'] = 0;
                $result['message'] = $message->getMessage();

                return $this->response->setJsonContent($result);
            }
        }

        $result['state'] = 1;
        $result['message'] = self::formatMessage($program . ' 正在删除');
        $result['reload_config'] = true;

        return $this->response->setJsonContent($result);
    }
}

