<?php
use Phalcon\Mvc\View;

class SupervisorConfigController extends ControllerSupervisorBase
{
    public function indexAction()
    {
        $form = new ProgramForm(null);

        $programs = Program::find([
            'server_id = :server_id:',
            'bind' => [
                'server_id' => $this->server->id
            ],
            'order' => 'program asc, id asc'
        ]);

        $this->view->programs = $programs;
        $this->view->form = $form;
    }

    public function createAction()
    {
        $result = [];
        $form = new ProgramForm(null);

        if ($this->request->isPost())
        {
            $program = new Program();
            $form->setEntity($program);

            if (!$form->isValid($this->request->getPost()))
            {
                foreach ($form->getMessages() as $message)
                {
                    $result['state'] = 0;
                    $result['message'] = $message->getMessage();

                    return $this->response->setJsonContent($result);
                }
            }

            if (!$program->create())
            {
                foreach ($program->getMessages() as $message)
                {
                    $result['state'] = 0;
                    $result['message'] = $message->getMessage();

                    return $this->response->setJsonContent($result);
                }
            }

            $this->flashSession->success("配置添加成功");
            $form->clear();

            $result['state'] = 1;
            $result['message'] = '配置添加成功';
            $result['data'] = $program->toArray();

            return $this->response->setJsonContent($result);
        }
    }

    public function editAction($server_id, $id)
    {
        $result = [];

        $program = Program::findFirst($id);
        if (!$program)
        {
            $result['state'] = 0;
            $result['message'] = '不存在该分组';

            return $this->response->setJsonContent($result);
        }

        if ($this->request->isPost())
        {
            $form = new ProgramForm($program, [
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

            if (!$program->save())
            {
                foreach ($program->getMessages() as $message)
                {
                    $result['state'] = 0;
                    $result['message'] = $message->getMessage();

                    return $this->response->setJsonContent($result);
                }
            }

            $form->clear();

            $result['state'] = 1;
            $result['message'] = '配置修改成功';
            $result['data'] = $program->toArray();

            return $this->response->setJsonContent($result);
        }
    }

    public function deleteAction()
    {
        $result = [];

        // ids 支持数组或者以逗号分割两种形式
        $ids = $this->request->getPost('ids');

        if (is_array($ids))
        {
            $id_arr = array_filter($ids, function($item) {
                return is_numeric($item);
            });
        }
        else
        {
            $id_arr = array_filter(explode(',', trim($ids)), function($item) {
                return is_numeric($item);
            });
        }

        if (empty($id_arr))
        {
            $result['state'] = 0;
            $result['message'] = "ids 参数不能为空";

            return $this->response->setJsonContent($result);
        }

        $phql = "DELETE FROM Program WHERE id IN ({ids:array-int})";
        $model = $this->modelsManager->executeQuery(
            $phql,
            ['ids' => $id_arr]
        );

        if (!$model->success())
        {
            $messages = $model->getMessages();
            foreach ($messages as $message)
            {
                $result['state'] = 0;
                $result['message'] = $message->getMessage();

                return $this->response->setJsonContent($result);
            }
        }

        $result['state'] = 1;
        $result['message'] = '删除成功';

        return $this->response->setJsonContent($result);
    }

    public function iniModeAction()
    {
        if ($this->request->isPost())
        {
            $ini = $this->request->getPost('ini', 'trim', '');

            $ini_parsed = parse_ini_string($ini, true, INI_SCANNER_RAW);
            if ($ini_parsed === false)
            {
                $result['state'] = 0;
                $result['message'] = "配置文件格式不对";

                return $this->response->setJsonContent($result);
            }

            $form = new ProgramForm();
            $filtered = [];

            foreach ($ini_parsed as $key => $value)
            {
                if (!preg_match("/^program:[a-zA-Z0-9_\-]{1,255}$/", trim($key), $matches))
                {
                    $result['state'] = 0;
                    $result['message'] = "配置文件格式不对：{$key}";

                    return $this->response->setJsonContent($result);
                }

                $value['program'] = explode(':', trim($key))[1];
                $value['server_id'] = $this->server->id;

                // 验证配置文件是否填写正确
                if (!$form->isValid($value))
                {
                    foreach ($form->getMessages() as $message)
                    {
                        $result['state'] = 0;
                        $result['message'] = "[$key] " . $message->getMessage();

                        return $this->response->setJsonContent($result);
                    }
                }
                $form->clear();

                // 使用默认值填充配置文件没有写的字段
                $value['process_name'] ?: $value['process_name'] = '%(program_name)s_%(process_num)s';
                $value['numprocs'] ?: $value['numprocs'] = 1;
                $value['numprocs_start'] ?: $value['numprocs_start'] = 0;
                $value['user'] ?:  $value['user'] = 'www-data';
                $value['directory'] ?: $value['directory'] = '%(here)s';
                $value['autostart'] ?: $value['autostart'] = 'true';
                $value['startretries'] ?: $value['startretries'] = 20;
                $value['autorestart'] ?: $value['autorestart'] = 'true';
                $value['redirect_stderr'] ?: $value['redirect_stderr'] = 'true';
                $value['stdout_logfile'] ?: $value['stdout_logfile'] = 'AUTO';
                $value['stdout_logfile_backups'] ?: $value['stdout_logfile_backups'] = 0;
                $value['stdout_logfile_maxbytes'] ?: $value['stdout_logfile_maxbytes'] = '1M';

                $filtered[] = $value;
            }

            try
            {
                $this->db->begin();

                $sql = "DELETE FROM program WHERE server_id = {$this->server->id}";
                $success = $this->db->execute($sql);

                if (!$success)
                {
                    $this->db->rollback();

                    $result['state'] = 0;
                    $result['message'] = "配置删除失败";

                    return $this->response->setJsonContent($result);
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
                    $sql = "INSERT INTO `program` ({$field_sql}) VALUES {$placeholder_sql}";
                    $success = $this->db->execute($sql, $values);

                    if (!$success)
                    {
                        $this->db->rollback();

                        $result['state'] = 0;
                        $result['message'] = "配置插入失败";

                        return $this->response->setJsonContent($result);
                    }
                }

                $this->db->commit();
            }
            catch (Exception $e)
            {
                $this->db->rollback();

                $result['state'] = 0;
                $result['message'] = "修改失败，原因如下：" . $e->getMessage();

                return $this->response->setJsonContent($result);
            }

            $this->flashSession->success('修改成功');

            $result['state'] = 1;
            $result['message'] = "修改成功";

            return $this->response->setJsonContent($result);
        }

        $programs = Program::find([
            'server_id = :server_id:',
            'bind' => [
                'server_id' => $this->server->id
            ],
            'order' => 'program asc, id asc'
        ]);

        $ini = '';
        foreach ($programs as $program)
        {
            /** @var Program $program */
            $ini .= "[program:{$program->program}]" . PHP_EOL;
            $ini .= "command={$program->command}" . PHP_EOL;
            $ini .= "process_name={$program->process_name}" . PHP_EOL;
            $ini .= "numprocs={$program->numprocs}" . PHP_EOL;
            $ini .= "numprocs_start={$program->numprocs_start}" . PHP_EOL;
            $ini .= "user={$program->user}" . PHP_EOL;
            $ini .= "directory={$program->directory}" . PHP_EOL;
            $ini .= "autostart={$program->autostart}" . PHP_EOL;
            $ini .= "startretries={$program->startretries}" . PHP_EOL;
            $ini .= "autorestart={$program->autorestart}" . PHP_EOL;
            $ini .= "redirect_stderr={$program->redirect_stderr}" . PHP_EOL;
            $ini .= "stdout_logfile={$program->stdout_logfile}" . PHP_EOL;
            $ini .= "stdout_logfile_backups={$program->stdout_logfile_backups}" . PHP_EOL;
            $ini .= "stdout_logfile_maxbytes={$program->stdout_logfile_maxbytes}" . PHP_EOL;
        }

        $this->view->programs = $programs;
        $this->view->ini = trim($ini);
        $this->view->setRenderLevel(View::LEVEL_ACTION_VIEW);
    }

    public function loadServerAction()
    {

    }
}