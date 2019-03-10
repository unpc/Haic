<?php

namespace Gini\Controller\CGI;

use Gini\Model\Alert;
use Gini\Model\Help;
use Gini\ORM\Approval;

use PhpOffice\PhpWord\Settings as WordSettings;
use PhpOffice\Common\XMLWriter;
use PhpOffice\PhpWord\Writer\Word2007\Element\Container;

class Project extends Layout\God
{
    public function __index($type = 1)
    {
        if (!_G('ME')->isAllowedTo('查看', 'project')) {
            $this->redirect('error/401');
        }
        $form = $this->form();
        $step = 10;
        $projects = those('project')
                        ->whose('archive_time')->is('0000-00-00 00:00:00')
                        ->andWhose('type')->is($type);

        if ('POST' == $_SERVER['REQUEST_METHOD']) {
            //获取post参数 并校验
            $form = $this->form('post');
            if ($form['number']) {
                $projects = $projects->whose('number')->contains($form['number']);
            }
            if ($form['title']) {
                $projects = $projects->whose('title')->contains($form['title']);
            }
            if ($form['source_from']) {
                $projects = $projects->whose('source_from')->contains($form['source_from']);
            }
            if ($form['bank_from']) {
                $projects = $projects->whose('bank_from')->contains($form['bank_from']);
            }
        }

        $projects = $projects->orderBy('archive_time', 'desc');

        $pagination = Help::pagination($projects, $form['st'], $step);
        $this->view->body = V('projects/index', [
            'type' => $type,
            'projects' => $projects,
            'form' => $form,
            'pagination' => $pagination,
        ]);
    }

    public function actionProfile($id = 0, $sub = 'user', $subCat = 'base')
    {
        $me = _G('ME');
        $project = a('project', $id);
        if (!$project->id) {
            $this->redirect('error/401');
        }
        if (!$me->isAllowedTo('查看', $project)) {
            $this->redirect('error/401');
        }
        $this->view->body = V('projects/profile', [
            'project' => $project,
            'sub' => $sub,
            'subCat' => $subCat,
            'form' => $form,
        ]);
    }

    public function actionDownloadAttachment($id = 0, $path = '')
    {
        $me = _G('ME');
        $project = a('project', $id);
        if (!$project->id) {
            $this->redirect('error/404');
        }
        if (!$me->isAllowedTo('修改', $project)) {
            $this->redirect('error/401');
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

    public function actionApproval($id = 0)
    {
        $me = _G('ME');
        $project = a('project', $id);
        if (!$project->id) {
            $this->redirect('error/401');
        }
        if ('POST' == $_SERVER['REQUEST_METHOD']) {
            $approval = a('approval')->whose('project')->is($project);
            if (!$approval->id) {
                $approval = a('approval');
                $approval->project = $project;
                $approval->ctime = date('Y-m-d H:i:s');
                $approval->save();
            }

            $validator = new \Gini\CGI\Validator();

            $form = $this->form();

            try {   
                if (!$project->isPreparation) {
                    $validator
                        ->validate('user_name', $form['user_name'], T('估价委托人不可为空!'))
                        ->validate('report_no', $form['report_no'], T('报告编号不能为空!'))
                        ->validate('owner', $form['owner'], T('房屋所有权人不能为空!'))
                        ->validate('number', $form['number'], T('项目编号不能为空!'))
                        ->validate('address', $form['address'], T('项目坐落不能为空!'))
                        ->validate('title', $form['title'], T('项目名称不能为空!'))
                        ->validate('contact', $form['contact'], T('联系人不能为空!'))
                        ->validate('contact_phone', $form['contact_phone'], T('联系电话不能为空!'))
                        ->validate('case_name', $form['case_name'], T('项目案名不能为空!'))
                        ->validate('stment_date', $form['stment_date'], T('委托日期不能为空!'))
                        ->validate('explor_date', $form['explor_date'], T('价值时点不能为空!'))
                        ->validate('explor_user', $form['explor_user'], T('领勘人不能为空!'))
                        ->validate('area', $form['area'], T('建筑面积不能为空!'))
                        ->validate('acreage', $form['acreage'], T('土地面积不能为空!'))
                        ->validate('target', $form['target'], T('估价目的不能为空!'))
                        ->validate('structure', $form['structure'], T('建筑结构不能为空!'))
                        ->validate('year', $form['year'], T('建筑年代不能为空!'))
                        ->validate('land_source', $form['land_source'], T('土地来源不能为空!'))
                        ->validate('source_from', $form['source_from'], T('业务来源不能为空!'))
                        ->validate('source_contact', $form['source_contact'], T('业务联系人不能为空!'))
                        ->validate('project_type', $form['project_type'], T('项目类型不能为空!'))
                        ->validate('bank_from', $form['bank_from'], T('贷款银行不能为空!'))
                        ->validate('type', $form['type'], T('评估类别不能为空!'))
                        ->validate('amount', $form['amount'], T('估价结果（万元）不能为空!'))
                        ->validate('pages', $form['pages'], T('档案总页数不能为空!'))
                        ->validate('archived_time', $form['archived_time'], T('归档时间不能为空!'))
                        ->validate('function', $form['function'], T('评估方法不能为空!'))
                        ->validate('explor_option', $form['explor_option'], T('实地查勘人意见不能为空!'))
                        ->validate('appraisal_option', $form['appraisal_option'], T('评估思路不能为空!'))
                        ->done();
                }

                $approval->info = (array) $form;
                if ($approval->save()) {
                    Alert::setMessage("审核信息保存成功!", Alert::TYPE_OK);
                    $log = a('log');
                    $log->user = $me;
                    $log->project = $project;
                    $log->action = \Gini\ORM\Log::ACTION_APPROVAL;
                    $log->description = strtr('%user 保存审核表信息! ', ['%user' => $me->name]);
                    $log->save();
                }
                else {
                    Alert::setMessage('审核信息保存失败!', Alert::TYPE_ERROR);
                }
            } catch (\Gini\CGI\Validator\Exception $e) {
                $form['_errors'] = $validator->errors();
            }
        }

        $this->view->body = V('projects/approval', [
            'project' => $project,
            'form' => $form,
        ]);
    }

    public function actionPrint($id = 0, $tid = 0)
    {
        $me = _G('ME');
        $project = a('project', $id);
        $template = a('template', $tid);
        if (!$project->id) {
            $this->redirect('error/404');
        }
        if (!$template->id) {
            $this->redirect('error/404');
        }
        if (!$me->isAllowedTo('打印报告', $project)) {
            $this->redirect('error/401');
        }

        $this->view = V('projects/print', [
            'project' => $project,
            'template' => $template,
        ]);
    }

    public function actionTemplate($id = 0)
    {
        $me = _G('ME');
        $project = a('project', $id);
        if (!$project->id) {
            $this->redirect('error/404');
        }
        if (!$project->template->id) {
            $this->redirect('error/404');
        }
        if (!$me->isAllowedTo('打印报告', $project)) {
            $this->redirect('error/401');
        }
        $t = $project->template;
        $fullPath = $t->filePath($t->attachments('docx')[0]);
        if (!$fullPath || !$t->attachments('docx')[0]) {
            $this->redirect('error/404');
        }

        $word = \PhpOffice\PhpWord\IOFactory::load($fullPath);
        $word->save($t->filePath('template.html'), 'HTML');

        $content = file_get_contents($t->filePath('template.html'));
        $content = strtr((string)$content, $project->getTemplateData());
        file_put_contents($t->filePath('template.html'), $content);

        $this->view->body = V('template/before-show', [
            //'view' => \Gini\IoC::construct('\Gini\VIEW\PHTML', $t->filePath('template.html'), []),
            'url' => URL("/data/template/{$t->id}/template.html"),
            'project' => $project,
        ]);
    }

    public function actionPreeval($id = 0)
    {
        $me = _G('ME');
        $project = a('project', $id);
        if (!$project->id) {
            $this->redirect('error/404');
        }
        if (!$project->preeval->id) {
            $this->redirect('error/404');
        }
        if (!$me->isAllowedTo('打印报告', $project)) {
            $this->redirect('error/401');
        }
        $t = $project->preeval;
        $fullPath = $t->filePath($t->attachments('docx')[0]);
        if (!$fullPath || !$t->attachments('docx')[0]) {
            $this->redirect('error/404');
        }

        $word = \PhpOffice\PhpWord\IOFactory::load($fullPath);
        $word->save($t->filePath('template.phtml'), 'HTML');

        $content = file_get_contents($t->filePath('template.phtml'));
        $content = strtr((string)$content, $project->getTemplateData());
        file_put_contents($t->filePath('template.html'), $content);

        $this->view->body = V('template/before-show', [
            // 'view' => \Gini\IoC::construct('\Gini\VIEW\PHTML', $t->filePath('template.phtml'), []),
            'url' => URL("/data/template/{$t->id}/template.html"),
            'project' => $project,
        ]);
    }

    public function actionDownloadTemplate($id = 0)
    {
        $me = _G('ME');
        $project = a('project', $id);
        if (!$project->id) {
            $this->redirect('error/404');
        }
        if (!$project->template->id) {
            $this->redirect('error/404');
        }
        if (!$me->isAllowedTo('打印报告', $project)) {
            $this->redirect('error/401');
        }
        $t = $project->template;
        $fullPath = $t->filePath($t->attachments('docx')[0]);
        if (!$fullPath || !$t->attachments('docx')[0]) {
            $this->redirect('error/404');
        }

        $templ = new \PhpOffice\PhpWord\TemplateProcessor($fullPath);
        $variables = $templ->getVariables();
        foreach ($project->getTableData() as $name => $data) {
            while (1) {
                $variables = $templ->getVariables();
                if (in_array($name, $variables)) {
                    $templ->cloneRow($name, count($data));
                    $keys = (array)explode('.', $name);
                    $preKey = $keys[0];
                    foreach ($data as $n => $v) {
                        $num = $n + 1;
                        foreach ((array)$v as $key => $value) {
                            $templ->setValue("$preKey.$key#$num", $value);
                        }
                    }
                }
                else {
                    break;
                }
            }
        }
        foreach ($project->getTemplateData() as $k => $v) {
            if (in_array($k, $variables)) {
                $templ->setValue($k, $v);
            }
        }
        foreach ($project->getTableListData() as $name => $data) {
            foreach ((array)$data as $k => $v) {
                if (!is_array($v)) $v = [$v];
                if (count($v) == 1) {
                    $replace = "$name.$k";
                    if (in_array($replace, $variables)) {
                        $templ->setValue($replace, current($v));
                    }
                }
                else {
                    foreach ((array)$v as $num => $value) {
                        $real_num = $num + 1;
                        $replace = "$name.$k.$real_num";
                        if (in_array($replace, $variables)) {
                            $templ->setValue($replace, $value?:'');
                        }
                    }
                }
            }
        }

        $title = "Report({$project->number}).docx";
        header("Cache-Control: public");     
        header("Content-Description: File Transfer");     
        header('Content-Disposition: attachment; filename="' . $title . '"');  
        header("Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document");     
        header("Content-Transfer-Encoding: binary");
        ob_clean();
        flush();
        $templ->saveAs('php://output');
        exit();
    }

    public function actionDownloadPreeval($id = 0)
    {
        $me = _G('ME');
        $project = a('project', $id);
        if (!$project->id) {
            $this->redirect('error/404');
        }
        if (!$project->preeval->id) {
            $this->redirect('error/404');
        }
        if (!$me->isAllowedTo('打印报告', $project)) {
            $this->redirect('error/401');
        }
        $t = $project->preeval;
        $fullPath = $t->filePath($t->attachments('docx')[0]);
        if (!$fullPath || !$t->attachments('docx')[0]) {
            $this->redirect('error/404');
        }

        $templ = new \PhpOffice\PhpWord\TemplateProcessor($fullPath);
        $variables = $templ->getVariables();
        foreach ($project->getTableData() as $name => $data) {
            while (1) {
                $variables = $templ->getVariables();
                if (in_array($name, $variables)) {
                    $templ->cloneRow($name, count($data));
                    $keys = (array)explode('.', $name);
                    $preKey = $keys[0];
                    foreach ($data as $n => $v) {
                        $num = $n + 1;
                        foreach ((array)$v as $key => $value) {
                            $templ->setValue("$preKey.$key#$num", $value);
                        }
                    }
                }
                else {
                    break;
                }
            }
        }
        foreach ($project->getTemplateData() as $k => $v) {
            if (in_array($k, $variables)) {
                $templ->setValue($k, $v);
            }
        }
        foreach ($project->getTableListData() as $name => $data) {
            foreach ((array)$data as $k => $v) {
                if (!is_array($v)) $v = [$v];
                if (count($v) == 1) {
                    $replace = "$name.$k";
                    if (in_array($replace, $variables)) {
                        $templ->setValue($replace, current($v));
                    }
                }
                else {
                    foreach ((array)$v as $num => $value) {
                        $real_num = $num + 1;
                        $replace = "$name.$k.$real_num";
                        if (in_array($replace, $variables)) {
                            $templ->setValue($replace, $value?:'');
                        }
                    }
                }
            }
        }

        header('Content-Description: File Transfer');
        header('Content-Disposition: attachment; filename="'.$project->title."预评.docx".'"');
        header('Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document');
        header('Content-Transfer-Encoding: binary');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Expires: 0');

        ob_clean();
        flush();
        $templ->saveAs('php://output');
        exit();
    }
}
