<?php
use Phalcon\Mvc\View;

class ProcessController extends ControllerSupervisorBase
{
    public function indexAction()
    {
        $processes = [];
        $callback = function () use (&$processes)
        {
            $processes = $this->supervisor->getAllProcessInfo();
        };

        $this->setCallback($callback);
        $this->invoke();

        $process_groups = array_unique(array_column($processes, 'group'));
        $process_warnings = array_filter($processes, function($process) {
            return $process['statename'] != 'RUNNING';
        });

        $local_processes = Process::find([
            "server_id = {$this->server->id}"
        ])->toArray();

        $key_parts = array_column($local_processes, null, 'program');

        $process_group_merge = [];
        foreach ($process_groups as $key => $process_group)
        {
            $process_group_merge[$key] = ['program' => $process_group];
            if (isset($key_parts[$process_group]))
            {
                $process_group_merge[$key] += $key_parts[$process_group];
            }
        }

        $this->view->processes = $processes;
        $this->view->process_groups = $process_group_merge;
        $this->view->process_warnings = $process_warnings;
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

    public function editAction($id)
    {
        /** @var Process $process */
        $process = Process::findFirst($id);

        if ($this->request->isPost())
        {
            if ($this->request->getPost('mode') == 'ini')
            {
                $this->dispatcher->forward([
                    'action' => 'editIni'
                ]);

                return;
            }

            $form = new ProcessForm($process, [
                'edit' => true
            ]);

            if (!$form->isValid($this->request->getPost()))
            {
                foreach ($form->getMessages() as $message)
                {
                    $this->flash->error($message->getMessage());
                }
            }
            else
            {
                if (!$process->update())
                {
                    $this->flash->error($process->getMessages());
                }
                else
                {
                    $this->flash->success("修改成功");
                    $form->clear();
                    $this->view->reload_config = true;
                }
            }
        }

        $this->view->process = $process;
        $this->view->form = new ProcessForm($process, [
            'edit' => true
        ]);
        $this->view->ini = $process->getIni();
    }

    public function editIniAction($id)
    {
        /** @var Process $process */
        $process = Process::findFirst($id);

        if ($this->request->isPost())
        {
            $server_id = $this->request->getPost('server_id', 'int');
            $ini = $this->request->getPost('ini');

            $parsed = parse_ini_string($ini, true, INI_SCANNER_RAW);
            if ($parsed === false)
            {
                $this->flash->error('配置解析错误');
            }
            else
            {
                $key = trim(key($parsed));
                $value = current($parsed);

                if (!preg_match("/^program:[a-zA-Z0-9_\-]{0,255}$/", $key, $matches))
                {
                    $this->flash->error('配置格式不对');
                }
                else
                {
                    $value['program'] = explode(':', $key)[1];
                    $value['server_id'] = $server_id;

                    $form = new ProcessForm($process, [
                        'edit' => true
                    ]);

                    if (!$form->isValid($value))
                    {
                        foreach ($form->getMessages() as $message)
                        {
                            $this->flash->error($message->getMessage());
                        }
                    }
                    else
                    {
                        if (!$process->update())
                        {
                            $this->flash->error($process->getMessages());
                        }
                        else
                        {
                            unset($ini);
                            $form->clear();
                            $this->flash->success("修改成功");
                            $this->view->reload_config = true;
                        }
                    }
                }
            }
        }

        $this->view->pick('process/edit');
        $this->view->mode = 'ini';

        // 重新查询一次防止更新失败被污染
        $process = Process::findFirst($id);
        $this->view->process = $process;
        $this->view->form = new ProcessForm($process, [
            'edit' => true
        ]);
        $this->view->ini = isset($ini) ? $ini : $process->getIni();
    }

    public function iniAction()
    {
        if ($this->request->isPost())
        {
            $ini = $this->request->getPost('ini', 'trim');
            $server_id = $this->server->id;

            // 检查程序名是否重复
            if (preg_match_all('/\[program:[a-zA-Z0-9_\-]{1,255}\]/', $ini, $matches))
            {
                foreach (array_count_values($matches[0]) as $k => $v)
                {
                    if ($v > 1)
                    {
                        $this->flash->error("{$k} 程序名重复");
                        goto end;
                    }
                }
            }

            $parsed = parse_ini_string($ini, true, INI_SCANNER_RAW);
            if ($parsed === false)
            {
                $this->flash->error('配置解析错误');
                goto end;
            }

            $form = new ProcessForm();
            $filtered = [];

            // 获取所有表字段
            $process = new Process();
            $metadata = $process->getModelsMetaData();

            $attributes = $metadata->getAttributes($process);
            $white_list = array_diff($attributes, ['id', 'server_id', 'program', 'create_time', 'update_time']);

            foreach ($parsed as $key => $value)
            {
                if (!preg_match("/^program:[a-zA-Z0-9_\-]{0,255}$/", trim($key), $matches))
                {
                    $this->flash->error("[{$key}] 配置格式不对");
                    goto end;
                }

                // 丢弃白名单之外的字段
                $value = array_filter($value, function($key) use ($white_list) {
                    return in_array($key, $white_list);
                }, ARRAY_FILTER_USE_KEY);

                $value['program'] = explode(':', trim($key))[1];
                $value['server_id'] = $server_id;

                // 验证配置文件是否填写正确
                if (!$form->isValid($value))
                {
                    foreach ($form->getMessages() as $message)
                    {
                        $this->flash->error("[$key] " . $message->getMessage());
                        goto end;
                    }
                }
                $form->clear();

                // 使用默认值填充配置文件没有写的字段
                Process::applyDefaultValue($value);
                // Sort by key
                ksort($value);
                $filtered[] = $value;
            }

            try
            {
                $this->db->begin();

                $sql = "DELETE FROM process WHERE server_id = {$server_id}";
                $success = $this->db->execute($sql);

                if (!$success)
                {
                    $this->db->rollback();
                    $this->flash->error('配置删除失败');
                    goto end;
                }

                if (!empty($filtered))
                {
                    $placeholders = [];
                    $values = [];
                    $fields = array_keys($filtered[0]);
                    $field_sql = '`' . implode('`, `', $fields) . '`';

                    foreach ($filtered as $item)
                    {
                        $placeholders[] = '(' . substr(str_repeat('?, ', count($fields)), 0, -2) . ')';
                        $values = array_merge($values, array_values($item));
                    }

                    $placeholder_sql = implode(',', $placeholders);
                    $sql = "INSERT INTO `process` ({$field_sql}) VALUES {$placeholder_sql}";
                    $success = $this->db->execute($sql, $values);

                    if (!$success)
                    {
                        $this->db->rollback();
                        $this->flash->error('配置插入失败');
                        goto end;
                    }
                }

                unset($ini);
                $this->db->commit();
                $this->flash->success('修改成功');
                $this->view->reload_config = true;
            }
            catch (Exception $e)
            {
                $this->db->rollback();
                $this->flash->error("修改失败：{$e->getMessage()}");
            }
        }

        // goto here
        end:

        if (!isset($ini))
        {
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
        }

        $this->view->ini = $ini;
    }

    public function createAction()
    {
        $form = new ProcessForm(null);

        if ($this->request->isPost())
        {
            if ($this->request->getPost('mode') == 'ini')
            {
                $this->dispatcher->forward([
                    'action' => 'createIni'
                ]);

                return;
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

                if (!preg_match("/^program:[a-zA-Z0-9_\-]{0,255}$/", $key, $matches))
                {
                    $this->flash->error('配置解析错误');
                }
                else
                {
                    $value['program'] = explode(':', $key)[1];
                    $value['server_id'] = $server_id;

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
                }
            }

            $form->clear();
        }

        $this->view->pick('process/create');
        $this->view->mode = 'ini';
        $this->view->form = $form;
        $this->view->ini = isset($ini) ? $ini : Process::getIniTemplate();
    }

    public function deleteAction($id)
    {
        $result = [];

        $process = Process::findFirst($id);

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
        $result['message'] = self::formatMessage($process->program . ' 正在删除');
        $result['reload_config'] = true;

        return $this->response->setJsonContent($result);
    }
}

