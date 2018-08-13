<?php

namespace Gini\Controller\CGI;

use \Gini\Model\Alert;
use \Gini\Model\Help;
use \Gini\ORM\Approval;

class Project extends Layout\God {
    
    function __index() {
        $form = $this->form();
        $step = 10;
        $projects = those('project')->whose('archive_time')->is('0000-00-00 00:00:00');
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            //获取post参数 并校验
            $form = $this->form('post');
            $projects = $projects->whose('number')->contains($form['number']);
        }

        $projects = $projects->orderBy('archive_time', 'desc');
        
        $pagination = Help::pagination($projects, $form['st'], $step);
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
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $approval = a('approval')->whose('project')->is($project);
            if (!$approval->id) {
                $approval = a('approval');
                $approval->project = $project;
                $approval->ctime = date('Y-m-d H:i:s');
                $approval->save();
            }

            $validator = new \Gini\CGI\Validator;

            $form = $this->form();

            try {

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
                    ->validate('bus_source', $form['bus_source'], T('业务来源不能为空!'))
                    ->validate('bus_contact', $form['bus_contact'], T('业务联系人不能为空!'))
                    ->validate('project_type', $form['project_type'], T('项目类型不能为空!'))
                    ->validate('bank', $form['bank'], T('贷款银行不能为空!'))
                    ->validate('type', $form['type'], T('评估类别不能为空!'))
                    ->validate('amount', $form['amount'], T('估价结果（万元）不能为空!'))
                    ->validate('pages', $form['pages'], T('档案总页数不能为空!'))
                    ->validate('archived_time', $form['archived_time'], T('归档时间不能为空!'))
                    ->validate('function', $form['function'], T('评估方法不能为空!'))
                    ->validate('explor_option', $form['explor_option'], T('实地查勘人意见不能为空!'))
                    ->validate('appraisal_option', $form['appraisal_option'], T('评估思路不能为空!'))
                    ->done();

                    $approval->info = (array)$form;
                    $approval->status = Approval::APPROVAL_FIRST;
                    if ($approval->save()) {
                        $current_status = Approval::$APPROVAL_STATUS[$approval->status];
                        Alert::setMessage("提交审核成功至{$current_status}!", Alert::TYPE_OK);
                    }
                    else {
                        Alert::setMessage("提交审核失败!", Alert::TYPE_ERROR);
                    }
            }
            catch (\Gini\CGI\Validator\Exception $e) {
                $form['_errors'] = $validator->errors();
            }
        }

        $this->view->body = V('projects/approval', [
            'project' => $project,
            'form' => $form
        ]);
    }

    public function actionPrint($id=0, $tid=0)
    {
        $me = _G('ME');
        $project = a('project', $id);
        $template = a('template', $tid);
        if (!$project->id) $this->redirect('error/404');
        if (!$template->id) $this->redirect('error/404');

        $this->view = V('projects/print', [
            'project' => $project,
            'template' => $template
        ]);
    }

}
