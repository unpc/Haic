<?php

namespace Gini\Controller\CGI\AJAX;

class Project extends \Gini\Controller\CGI
{
    public function actionAdd()
    {
        $me = _G('ME');
        $form = $this->form();
        if (!$me->isAllowedTo('添加', 'project')) {
            $this->redirect('error/401');
        }
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $validator = new \Gini\CGI\Validator;

            try {
                $validator
                    // ->validate('title', $form['title'], T('项目名称不能为空!'))
                    ->validate('number', $form['number'], T('项目编号不能为空!'))
                    ->validate('type', $form['type'], T('请选择项目类型!'))
                    ->validate('user_phone', $form['user_phone'], T('请填写委托人联系方式!'))
                    // ->validate('source_from', $form['source_from'], T('请选择业务来源!'))
                    ->done();
                $project = a('project');
                $project->owner = $me;
                $project->ctime = H($form['ctime']);
                $project->number = $form['number'];
                $project->title = $form['title'];
                $project->type = (int)$form['type'];
                $project->source_description = $form['source_description'];
                $project->source_from = H($form['source_from']);
                $project->source_contact = H($form['source_contact']);
                $project->bank_from = H($form['bank_from']);
                $project->user_name = H($form['user_name']);
                $project->user_phone = H($form['user_phone']);
                $project->target = H($form['target']);
                $project->isPreparation = (int)$form['isPreparation'];
                $project->save();

                if ($project->id) {
                    $log = a('log');
                    $log->user = $me;
                    $log->project = $project;
                    $log->action = \Gini\ORM\Log::ACTION_ADD;
                    $log->description = strtr("%user 创建项目。", ['%user' => $me->name]);
                    $log->save();
                }

                return \Gini\IoC::construct('\Gini\CGI\Response\HTML', '<script data-ajax="true">window.location.reload();</script>');
            } catch (\Gini\CGI\Validator\Exception $e) {
                $form['_errors'] = $validator->errors();
            }
        }

        return \Gini\IoC::construct('\Gini\CGI\Response\HTML', V('projects/add-project-modal', [
            'form' => $form
        ]));
    }

    public function actionEdit($id=0)
    {
        $me = _G('ME');
        $project = a('project', $id);
        if (!$project->id) {
            $this->redirect('error/404');
        }
        if (!$me->isAllowedTo('修改', $project)) {
            $this->redirect('error/401');
        }
        $form = $this->form();

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $validator = new \Gini\CGI\Validator;

            try {
                $validator
                    // ->validate('title', $form['title'], T('项目名称不能为空!'))
                    ->validate('number', $form['number'], T('项目编号不能为空!'))
                    ->validate('type', $form['type'], T('请选择项目类型!'))
                    ->validate('user_phone', $form['user_phone'], T('请填写委托人联系方式!'))
                    // ->validate('source_from', $form['source_from'], T('请选择业务来源!'))
                    ->done();
                $project->ctime = H($form['ctime']);
                $project->number = $form['number'];
                $project->title = $form['title'];
                $project->type = (int)$form['type'];
                $project->source_description = $form['source_description'];
                $project->source_from = H($form['source_from']);
                $project->bank_from = H($form['bank_from']);
                $project->source_contact = H($form['source_contact']);
                $project->user_name = H($form['user_name']);
                $project->user_phone = H($form['user_phone']);
                $project->target = H($form['target']);
                $project->isPreparation = (int)$form['isPreparation'];
                $project->save();

                if ($project->id) {
                    $log = a('log');
                    $log->user = $me;
                    $log->project = $project;
                    $log->action = \Gini\ORM\Log::ACTION_EDIT;
                    $log->description = strtr("%user 修改项目基础信息。", ['%user' => $me->name]);
                    $log->save();
                }

                return \Gini\IoC::construct('\Gini\CGI\Response\HTML', '<script data-ajax="true">window.location.reload();</script>');
            } catch (\Gini\CGI\Validator\Exception $e) {
                $form['_errors'] = $validator->errors();
            }
        }

        return \Gini\IoC::construct('\Gini\CGI\Response\HTML', V('projects/edit-project-modal', [
            'form' => $form,
            'project' => $project
        ]));
    }

    public function actionDelete($id=0)
    {
        $me = _G('ME');
        $project = a('project', $id);
        if (!$project->id) {
            $this->redirect('error/404');
        }
        if (!$me->isAllowedTo('删除', $project)) {
            $this->redirect('error/401');
        }
        //remove this project
        $project->delete();

        return \Gini\IoC::construct('\Gini\CGI\Response\HTML', V('projects/delete-project-success', [
            'project' => $project
        ]));
    }

    public function actionAddUser($id=0)
    {
        $me = _G('ME');
        $project = a('project', $id);
        if (!$project->id) {
            $this->redirect('error/404');
        }
        if (!$me->isAllowedTo('修改', $project)) {
            $this->redirect('error/401');
        }
        $form = $this->form();
        $project->user_name = H($form['user_name']);
        $project->user_address = H($form['user_address']);
        $project->company_titile = H($form['company_titile']);
        $project->company_address = H($form['company_address']);
        $project->save();

        $log = a('log');
        $log->user = $me;
        $log->project = $project;
        $log->action = \Gini\ORM\Log::ACTION_EDIT;
        $log->description = sprintf('%s 修改委托人信息。', $me->name);
        $log->save();
    }

    public function actionAddTarget($id=0)
    {
        $me = _G('ME');
        $project = a('project', $id);
        if (!$project->id) {
            $this->redirect('error/404');
        }
        if (!$me->isAllowedTo('修改', $project)) {
            $this->redirect('error/401');
        }
        $form = $this->form();
        $project->target = $form['target'];
        $project->save();

        $log = a('log');
        $log->user = $me;
        $log->project = $project;
        $log->action = \Gini\ORM\Log::ACTION_EDIT;
        $log->description = sprintf('%s 修改估价目的信息。', $me->name);
        $log->save();
    }

    public function actionAddObject($id=0)
    {
        $me = _G('ME');
        $project = a('project', $id);
        if (!$project->id) {
            $this->redirect('error/404');
        }
        if (!$me->isAllowedTo('修改', $project)) {
            $this->redirect('error/401');
        }
        $form = $this->form();
        $building = $project->building;
        $add = false;
        if (!$building->id) {
            $building = a('building');
            $add = true;
        }

        $building->year = $form['year'];
        $building->property = (int)$form['property'];
        $building->address = $form['address'];
        $building->structure = (int)$form['structure'];
        $building->structure_text = $form['structure_text'];
        $building->total_floor = $form['total_floor'];
        $building->floor = $form['floor'];
        $building->front = (int)$form['front'];
        $building->use = (int)$form['use'];
        $building->use_text = $form['use_text'];
        $building->area = $form['area'];
        $building->type = (int)$form['type'];
        $building->type_text = $form['type_text'];
        $building->height = $form['height'];
        $building->mortgage = (int)$form['mortgage'];
        $building->appurtenance = (array)$form['appurtenance'];
        $building->out_wall = (array)$form['out_wall'];
        $building->out_wall_text = $form['out_wall_text'];
        $building->parlour_door_outside = (int)$form['parlour_door_outside'];
        $building->parlour_door_outside_text = $form['parlour_door_outside_text'];
        $building->parlour_door_inside = (int)$form['parlour_door_inside'];
        $building->parlour_door_inside_text = $form['parlour_door_inside_text'];
        $building->parlour_window_outside = (int)$form['parlour_window_outside'];
        $building->parlour_window_outside_text = $form['parlour_window_outside_text'];
        $building->parlour_window_inside = (int)$form['parlour_window_inside'];
        $building->parlour_window_inside_text = $form['parlour_window_inside_text'];
        $building->parlour_wall = (int)$form['parlour_wall'];
        $building->parlour_wall_text = $form['parlour_wall_text'];
        $building->parlour_platfond = (int)$form['parlour_platfond'];
        $building->parlour_platfond_text = $form['parlour_platfond_text'];
        $building->parlour_floor = (int)$form['parlour_floor'];
        $building->parlour_floor_text = $form['parlour_floor_text'];
        $building->room_wall = (int)$form['room_wall'];
        $building->room_wall_text = $form['room_wall_text'];
        $building->room_platfond = (int)$form['room_platfond'];
        $building->room_platfond_text = $form['room_platfond_text'];
        $building->room_floor = (int)$form['room_floor'];
        $building->room_floor_text = $form['room_floor_text'];
        $building->toilet_wall = (int)$form['toilet_wall'];
        $building->toilet_wall_text = $form['toilet_wall_text'];
        $building->toilet_platfond = (int)$form['toilet_platfond'];
        $building->toilet_platfond_text = $form['toilet_platfond_text'];
        $building->toilet_floor = (int)$form['toilet_floor'];
        $building->toilet_floor_text = $form['toilet_floor_text'];
        $building->toilet_appurtenance = (array)$form['toilet_appurtenance'];
        $building->toilet_appurtenance_text = $form['toilet_appurtenance_text'];
        $building->cook_wall = (int)$form['cook_wall'];
        $building->cook_wall_text = $form['cook_wall_text'];
        $building->cook_platfond = (int)$form['cook_platfond'];
        $building->cook_platfond_text = $form['cook_platfond_text'];
        $building->cook_floor = (int)$form['cook_floor'];
        $building->cook_floor_text = $form['cook_floor_text'];
        $building->cook_appurtenance = (array)$form['cook_appurtenance'];
        $building->cook_appurtenance_text = $form['cook_appurtenance_text'];
        $building->veranda_wall = (int)$form['veranda_wall'];
        $building->veranda_wall_text = $form['veranda_wall_text'];
        $building->veranda_floor = (int)$form['veranda_floor'];
        $building->veranda_floor_text = $form['veranda_floor_text'];
        $building->lighting = (int)$form['lighting'];
        $building->ammeter = (int)$form['ammeter'];
        $building->smoke_detector = (int)$form['smoke_detector'];
        $building->up_down_water = (int)$form['up_down_water'];
        $building->line_tv = (int)$form['line_tv'];
        $building->spray_system = (int)$form['spray_system'];
        $building->heating = (int)$form['heating'];
        $building->telephone_reservation = (int)$form['telephone_reservation'];
        $building->center_water = (int)$form['center_water'];
        $building->gas = (int)$form['gas'];
        $building->lift = (int)$form['lift'];
        $building->hot_water = (int)$form['hot_water'];
        $building->talk_back = (int)$form['talk_back'];
        $building->use_status = (int)$form['use_status'];
        $building->percent_new = $form['percent_new'];
        $building->east_front = $form['east_front'];
        $building->south_front = $form['south_front'];
        $building->west_front = $form['west_front'];
        $building->north_front = $form['north_front'];
        $building->around_appurtenance = $form['around_appurtenance'];
        $building->another_desc = $form['another_desc'];
        $building->plot = $form['plot'];
        $building->save();

        if ($add && $building->id) {
            $project->building = $building;
            $project->save();
        }

        $log = a('log');
        $log->user = $me;
        $log->project = $project;
        $log->action = \Gini\ORM\Log::ACTION_EDIT;
        $log->description = sprintf('%s 修改估价对象基础信息。', $me->name);
        $log->save();
    }

    public function actionAddResult($id=0)
    {
        $me = _G('ME');
        $project = a('project', $id);
        if (!$project->id) {
            $this->redirect('error/404');
        }
        if (!$me->isAllowedTo('修改', $project)) {
            $this->redirect('error/401');
        }
        $form = $this->form();
        $building = $project->building;
        $add = false;
        if (!$building->id) {
            $building = a('building');
            $add = true;
        }
        
        $building->amount = $form['amount'];
        $building->upper_amount = $form['upper_amount'];
        $building->unit = $form['unit'];
        $building->upper_unit = $form['upper_unit'];
        $building->yxsck_amount = $form['yxsck_amount'];
        $building->upper_yxsck_amount = $form['upper_yxsck_amount'];
        $building->dyzq_amount = $form['dyzq_amount'];
        $building->upper_dyzq_amount = $form['upper_dyzq_amount'];
        $building->tqk_amount = $form['tqk_amount'];
        $building->upper_tqk_amount = $form['upper_tqk_amount'];
        $building->fdyxsck_amount = $form['fdyxsck_amount'];
        $building->upper_fdyxsck_amount = $form['upper_fdyxsck_amount'];
        $building->dyjz_amount = $form['dyjz_amount'];
        $building->upper_dyjz_amount = $form['upper_dyjz_amount'];
        $building->dyjz_unit = $form['dyjz_unit'];
        $building->upper_dyjz_unit = $form['upper_dyjz_unit'];
        $building->save();

        if ($add && $building->id) {
            $project->building = $building;
            $project->save();
        }

        $log = a('log');
        $log->user = $me;
        $log->project = $project;
        $log->action = \Gini\ORM\Log::ACTION_EDIT;
        $log->description = sprintf('%s 修改估价结果描述信息。', $me->name);
        $log->save();
    }

    public function actionAddRegister($id=0)
    {
        $me = _G('ME');
        $project = a('project', $id);
        if (!$project->id) {
            $this->redirect('error/404');
        }
        if (!$me->isAllowedTo('修改', $project)) {
            $this->redirect('error/401');
        }
        $form = $this->form();

        $items = [];

        if (count((array)$form['items'])) {
            foreach ((array)$form['items'] as $i => $item) {
                if ($item['name'] && $item['number']) {
                    $items[] = $item;
                }
            }
        }
        $project->registers = $items;
        $project->save();

        $log = a('log');
        $log->user = $me;
        $log->project = $project;
        $log->action = \Gini\ORM\Log::ACTION_EDIT;
        $log->description = sprintf('%s 修改注册房地产估价师信息。', $me->name);
        $log->save();
    }

    public function actionAddOperation($id=0)
    {
        $me = _G('ME');
        $project = a('project', $id);
        if (!$project->id) {
            $this->redirect('error/404');
        }
        if (!$me->isAllowedTo('修改', $project)) {
            $this->redirect('error/401');
        }
        $form = $this->form();

        $edit = false;

        if ($form['operation_date']) {
            $project->operation_date = date('Y-m-d H:i:s', strtotime($form['operation_date']));
            $edit = true;
        }
        if ($form['operation_from']) {
            $project->operation_from = date('Y-m-d H:i:s', strtotime($form['operation_from']));
            $edit = true;
        }
        if ($form['operation_to']) {
            $project->operation_to = date('Y-m-d H:i:s', strtotime($form['operation_to']));
            $edit = true;
        }

        if ($form['operation_dur']) {
            $project->operation_dur = $form['operation_dur'];
            $edit = true;
        }

        $edit && $project->save();

        $log = a('log');
        $log->user = $me;
        $log->project = $project;
        $log->action = \Gini\ORM\Log::ACTION_EDIT;
        $log->description = sprintf('%s 修改估价作业期信息。', $me->name);
        $log->save();
    }

    public function actionAddIncomeReport($id=0)
    {
        $me = _G('ME');
        $project = a('project', $id);
        if (!$project->id) {
            $this->redirect('error/404');
        }
        if (!$me->isAllowedTo('修改', $project)) {
            $this->redirect('error/401');
        }
        $form = $this->form();

        $project->income_table = (array)$form;
        $project->save();

        $log = a('log');
        $log->user = $me;
        $log->project = $project;
        $log->action = \Gini\ORM\Log::ACTION_EDIT;
        $log->description = sprintf('%s 修改技术报告收益法信息。', $me->name);
        $log->save();

        return \Gini\IoC::construct('\Gini\CGI\Response\HTML', '<script data-ajax="true">window.location.reload();</script>');
    }

    public function actionAddReport($id=0)
    {
        $me = _G('ME');
        $project = a('project', $id);
        if (!$project->id) {
            $this->redirect('error/404');
        }
        if (!$me->isAllowedTo('修改', $project)) {
            $this->redirect('error/401');
        }
        $form = $this->form();

        $project->calculat_table = (array)$form;
        $project->save();

        $log = a('log');
        $log->user = $me;
        $log->project = $project;
        $log->action = \Gini\ORM\Log::ACTION_EDIT;
        $log->description = sprintf('%s 修改技术报告比较法信息。', $me->name);
        $log->save();

        // return \Gini\IoC::construct('\Gini\CGI\Response\HTML', '<script data-ajax="true">window.location.reload();</script>');
    }

    public function actionUploadAttachment($id=0)
    {
        $me = _G('ME');
        $project = a('project', $id);
        if (!$project->id) {
            $this->redirect('error/404');
        }
        if (!$me->isAllowedTo('修改', $project)) {
            $this->redirect('error/401');
        }
        $form = $this->form('files');
        $file = $form['input'];

        if ($file) {
            $fileName = current($file['name']);
            $fullPath = $project->filePath($fileName);
            \Gini\File::ensureDir($project->filePath());
            move_uploaded_file(current($file['tmp_name']), $fullPath);
            if (is_file($fullPath)) {
                return \Gini\IoC::construct('\Gini\CGI\Response\JSON', [
                    'ok' => H(T('文件上传成功!')),
                    'file' => $fullPath
                ]);
                $log = a('log');
                $log->user = $me;
                $log->project = $project;
                $log->action = \Gini\ORM\Log::ACTION_EDIT;
                $log->description = sprintf('%s 上传附件信息 [%s]。', $me->name, $fileName);
                $log->save();
            }
        }
        return \Gini\IoC::construct('\Gini\CGI\Response\JSON', [
            'error' => H(T('文件上传失败!')),
            'file' => $fullPath
        ]);
    }

    public function actionDeleteAttachment($id=0)
    {
        $me = _G('ME');
        $project = a('project', $id);
        if (!$project->id) {
            $this->redirect('error/404');
        }
        if (!$me->isAllowedTo('修改', $project)) {
            $this->redirect('error/401');
        }
        $form = $this->form();

        $path = $form['key'];
        if ($path) {
            $fullPath = $project->filePath($path);
            if (is_file($fullPath)) {
                \Gini\File::delete($fullPath);
                return \Gini\IoC::construct('\Gini\CGI\Response\JSON', [
                    'ok' => H(T('文件删除成功!')),
                    'file' => $fullPath
                ]);
                $log = a('log');
                $log->user = $me;
                $log->project = $project;
                $log->action = \Gini\ORM\Log::ACTION_EDIT;
                $log->description = sprintf('%s 上传附件信息 [%s]。', $me->name, $path);
                $log->save();
            }
        }
        return \Gini\IoC::construct('\Gini\CGI\Response\JSON', [
            'error' => H(T('文件删除失败!')),
            'file' => $fullPath
        ]);
    }

    public function actionArchive($id=0)
    {
        $me = _G('ME');
        $project = a('project', $id);
        if (!$project->id) {
            $this->redirect('error/404');
        }
        if (!$me->isAllowedTo('归档', $project)) {
            $this->redirect('error/401');
        }
        $project->archive_time = date('Y-m-d H:i:s');
        $project->save();

        $log = a('log');
        $log->user = $me;
        $log->project = $project;
        $log->action = \Gini\ORM\Log::ACTION_ARCHIVE;
        $log->description = strtr("%user 归档项目。", ['%user' => $me->name]);
        $log->save();

        return \Gini\IoC::construct('\Gini\CGI\Response\HTML', '<script data-ajax="true">window.location.href="'.URL('/project').'"</script>');
    }

    public function actionDescription($id=0)
    {
        $me = _G('ME');
        $project = a('project', $id);
        if (!$project->id) {
            $this->redirect('error/404');
        }

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $form = $this->form();
            $description = a('description', [
                'user' => $me,
                'project' => $project
            ]);
            if (!$description->id) {
                $description = a('description');
                $description->user = $me;
                $description->project = $project;
            }
            $description->description = H($form['description']);
            $description->save();

            return \Gini\IoC::construct('\Gini\CGI\Response\HTML', '<script data-ajax="true">window.location.reload();</script>');
        }
        return \Gini\IoC::construct('\Gini\CGI\Response\HTML', V('projects/add-project-description', [
            'project' => $project,
            'form' => $form
        ]));
    }

    public function actionClone($id=0)
    {
        $me = _G('ME');
        $project = a('project', $id);
        if (!$project->id) {
            $this->redirect('error/404');
        }
        if (!$me->isAllowedTo('克隆项目', $project)) {
            $this->redirect('error/401');
        }
        $form = $this->form();

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $validator = new \Gini\CGI\Validator;

            try {
                $op = a('project', ['number' => $form['number']]);
                $validator
                    ->validate('number', $form['number'], T('项目编号不能为空!'))
                    ->validate('number', $op->id, T('没找到对应的编号的项目进行克隆!'))
                    ->done();
                
                // 克隆信息
                $project->user_name = $op->user_name;
                $project->user_address = $op->user_address;

                $project->target = $op->target;

                $project->registers = $op->registers;

                $project->operation_date = $op->operation_date;
                $project->operation_from = $op->operation_from;
                $project->operation_to = $op->operation_to;

                $project->save();
                
                $data = $op->building->getData();
                $building = $project->building;
                foreach ($data as $key => $v) {
                    if ($key == 'id') {
                        continue;
                    }
                    if ($key == '_extra') {
                        foreach ((array)$v as $kk => $vv) {
                            $building->$kk = $vv;
                        }
                    } else {
                        $building->$key = $v;
                    }
                }
                $building->save();

                $project->building = $building;
                $project->save();

                return \Gini\IoC::construct('\Gini\CGI\Response\HTML', '<script data-ajax="true">window.location.reload();</script>');
            } catch (\Gini\CGI\Validator\Exception $e) {
                $form['_errors'] = $validator->errors();
            }
        }
        return \Gini\IoC::construct('\Gini\CGI\Response\HTML', V('projects/clone-project-description', [
            'project' => $project,
            'form' => $form
        ]));
    }

    public function actionSelectTemplate($id=0)
    {
        $me = _G('ME');
        $project = a('project', $id);
        if (!$project->id) {
            $this->redirect('error/404');
        }
        if (!$me->isAllowedTo('打印报告', $project)) {
            $this->redirect('error/401');
        }
        $form = $this->form();

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $validator = new \Gini\CGI\Validator;

            try {
                $template = a('template', (int)$form['id']);
                $validator
                    ->validate('name', $form['name'], T('报告模板不能为空!'))
                    ->validate('name', $template->id, T('请按照自动提示栏重新选择!'))
                    ->done();
                

                if ($template->id) {
                    $project->template = $template;
                    $project->save();
                    return \Gini\IoC::construct('\Gini\CGI\Response\HTML', '<script data-ajax="true">window.location.reload();</script>');
                }
            } catch (\Gini\CGI\Validator\Exception $e) {
                $form['_errors'] = $validator->errors();
            }
        }

        return \Gini\IoC::construct('\Gini\CGI\Response\HTML', V('projects/before-print-project', [
            'project' => $project,
            'form' => $form
        ]));
    }

    public function actionSelectPreEval($id=0)
    {
        $me = _G('ME');
        $project = a('project', $id);
        if (!$project->id) {
            $this->redirect('error/404');
        }
        if (!$me->isAllowedTo('打印报告', $project)) {
            $this->redirect('error/401');
        }
        $form = $this->form();

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $validator = new \Gini\CGI\Validator;

            try {
                $template = a('template', (int)$form['id']);
                $validator
                    ->validate('name', $form['name'], T('预评模板不能为空!'))
                    ->validate('name', $template->id, T('请按照自动提示栏重新选择!'))
                    ->done();
                

                if ($template->id) {
                    $project->preeval = $template;
                    $project->save();
                    return \Gini\IoC::construct('\Gini\CGI\Response\HTML', '<script data-ajax="true">window.location.reload();</script>');
                }
            } catch (\Gini\CGI\Validator\Exception $e) {
                $form['_errors'] = $validator->errors();
            }
        }

        return \Gini\IoC::construct('\Gini\CGI\Response\HTML', V('projects/before-preeval-project', [
            'project' => $project,
            'form' => $form
        ]));
    }

    public function actionSuppleDesc($id=0)
    {
        $me = _G('ME');
        $project = a('project', $id);
        if (!$project->id) {
            $this->redirect('error/404');
        }
        if (!$me->isAllowedTo('修改', $project)) {
            $this->redirect('error/401');
        }

        $form = $this->form();

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $validator = new \Gini\CGI\Validator;
            try {
                $project->supple_desc = (array)$form;
                $project->hasSuppleDesc = $form['hasSuppleDesc'];
                $project->save();
                return \Gini\IoC::construct('\Gini\CGI\Response\HTML', '<script data-ajax="true">window.location.reload();</script>');
            } catch (\Gini\CGI\Validator\Exception $e) {
                $form['_errors'] = $validator->errors();
            }
        }

        return \Gini\IoC::construct('\Gini\CGI\Response\HTML', V('projects/supple-desc', [
            'project' => $project,
            'form' => $form
        ]));
    }
}
