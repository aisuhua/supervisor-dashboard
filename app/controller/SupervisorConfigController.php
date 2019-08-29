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

            $raw = [];
            foreach ($ini_parsed as $key => $value)
            {
                if (!preg_match("/^program:[a-zA-Z0-9_\-]{1,255}$/", trim($key), $matches))
                {
                    continue;
                }

                $program_name = explode(':', trim($key))[1];
                $raw[] = [
                    'server_id' => $this->server->id,
                    'program' => $this->request->getPost('program', ['trim', 'string'], ''),
                    'command' => $this->request->getPost('command', ['trim', 'string'], ''),
                    'process_name' => $this->request->getPost('process_name', ['trim', 'string'], ''),
                    'numprocs' => $this->request->getPost('numprocs', ['int'], 1),
                    'numprocs_start' => $this->request->getPost('numprocs_start', ['int'], 0),
                    'user' => $this->request->getPost('user', ['trim', 'string'], ''),
                    'directory' => $this->request->getPost('directory', ['trim', 'string'], '%(here)s'),
                    'autostart' => $this->request->getPost('autostart', ['trim', 'string'], 'true'),
                    'startretries' => $this->request->getPost('startretries', 'int', 20),
                    'autorestart' => $this->request->getPost('autostart', ['trim', 'string'], 'true'),
                    'redirect_stderr' => $this->request->getPost('redirect_stderr', ['trim', 'string'], 'true'),
                    'stdout_logfile' => $this->request->getPost('stdout_logfile', ['trim', 'string'], 'AUTO'),
                    'stdout_logfile_backups' => $this->request->getPost('stdout_logfile_backups', 'int', 0),
                    'stdout_logfile_maxbytes' => $this->request->getPost('stdout_logfile_maxbytes', ['trim', 'string'], '1M'),
                ];
            }

            exit;
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