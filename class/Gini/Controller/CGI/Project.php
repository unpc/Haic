<?php

namespace Gini\Controller\CGI;

class Project extends Layout\God {
    
    function __index() {
        $form = $this->form();
        $step = 10;
        $projects = those('project');
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            //获取post参数 并校验
            $form = $this->form('post');
            $projects = $projects->whose('number')->contains($form['number']);
        }
        $pagination = \Gini\Model\Help::pagination($projects, $form['st'], $step);
        $this->view->body = V('projects/index', [
            'projects' => $projects,
            'form' => $form
        ]);
    }

    public function actionProfile($id=0, $sub='user', $subCat='base')
    {
        $me = _G('ME');
        $project = a('project', $id);
        if (!$project->id) $this->redirect('error/401');
        $this->view->body = V('projects/profile', [
            'project' => $project,
            'sub' => $sub,
            'subCat' => $subCat,
            'form' => $form
        ]);
    }

    public function actionDownloadAttachment($id=0, $path='')
    {
        $me = _G('ME');
        $project = a('project', $id);
        if (!$project->id) {
            $this->redirect('error/404');
        }

        if ($path) {
            $fullPath = $project->filePath($path);
            if (is_file($fullPath)) {
                $mime_type = \Gini\File::mimeType($fullPath);
                header("Content-Type: $mime_type");
                header('Accept-Ranges: bytes');
                header('Accept-Length:'.filesize($fullPath));
                header("Content-Disposition: attachment; filename=\"$path\"");
                ob_clean();
                echo file_get_contents($fullPath);
                exit;
            }
        }
    }

    public function actionApproval($id=0)
    {
        $me = _G('ME');
        $project = a('project', $id);
        if (!$project->id) $this->redirect('error/401');
        $this->view->body = V('projects/approval', [
            'project' => $project,
            'form' => $form
        ]);
    }

}
