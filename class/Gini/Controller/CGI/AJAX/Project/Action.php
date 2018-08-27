<?php

namespace Gini\Controller\CGI\AJAX\Project;

class Action extends \Gini\Controller\CGI
{
    public function actionAddDispatcher($id=0)
    {
        $me = _G('ME');
        $form = $this->form();
        $project = a('project', $id);
        if (!$project->id) {
            $this->redirect('error/404');
        }
        if (!$me->isAllowedTo('指派派件员', $project)) {
            $this->redirect('error/401');
        }
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $validator = new \Gini\CGI\Validator;

            try {
                $validator
                    ->validate('name', $form['name'] && $form['id'], T('请选择用户!'))
                    ->done();
                $project->dispatcher = a('user', (int)$form['id']);
                $project->save();

                $log = a('log');
                $log->user = $me;
                $log->project = $project;
                $log->action = \Gini\ORM\Log::ACTION_DISPATCH;
                $log->description = sprintf('%s 指派派件员为 %s。', $me->name, $project->dispatcher->name);
                $log->save();

                return \Gini\IoC::construct('\Gini\CGI\Response\HTML', '<script data-ajax="true">window.location.reload();</script>');
            } catch (\Gini\CGI\Validator\Exception $e) {
                $form['_errors'] = $validator->errors();
            }
        }

        return \Gini\IoC::construct('\Gini\CGI\Response\HTML', V('projects/actions/add-dispatcher-modal', [
            'form' => $form,
            'project' => $project
        ]));
    }

    public function actionAddAssessor($id=0)
    {
        $me = _G('ME');
        $form = $this->form();
        $project = a('project', $id);
        if (!$project->id) {
            $this->redirect('error/404');
        }
        if (!$me->isAllowedTo('指派估价员', $project)) {
            $this->redirect('error/401');
        }
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $validator = new \Gini\CGI\Validator;

            try {
                $validator
                    ->validate('name', $form['name'] && $form['id'], T('请选择用户!'))
                    ->done();
                $project->assessor = a('user', (int)$form['id']);
                $project->save();

                $log = a('log');
                $log->user = $me;
                $log->project = $project;
                $log->action = \Gini\ORM\Log::ACTION_ASSESSOR;
                $log->description = sprintf('%s 指派估价师为 %s。', $me->name, $project->assessor->name);
                $log->save();

                return \Gini\IoC::construct('\Gini\CGI\Response\HTML', '<script data-ajax="true">window.location.reload();</script>');
            } catch (\Gini\CGI\Validator\Exception $e) {
                $form['_errors'] = $validator->errors();
            }
        }

        return \Gini\IoC::construct('\Gini\CGI\Response\HTML', V('projects/actions/add-assessor-modal', [
            'form' => $form,
            'project' => $project
        ]));
    }

    public function actionAddSurveyor($id=0)
    {
        $me = _G('ME');
        $form = $this->form();
        $project = a('project', $id);
        if (!$project->id) {
            $this->redirect('error/404');
        }
        if (!$me->isAllowedTo('指派查勘员', $project)) {
            $this->redirect('error/401');
        }
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $validator = new \Gini\CGI\Validator;

            try {
                $validator
                    ->validate('name', $form['name'] && $form['id'], T('请选择用户!'))
                    ->done();
                $project->surveyor = a('user', (int)$form['id']);
                $project->save();

                $log = a('log');
                $log->user = $me;
                $log->project = $project;
                $log->action = \Gini\ORM\Log::ACTION_SURVEYOR;
                $log->description = sprintf('%s 指派查勘员为 %s。', $me->name, $project->surveyor->name);
                $log->save();

                return \Gini\IoC::construct('\Gini\CGI\Response\HTML', '<script data-ajax="true">window.location.reload();</script>');
            } catch (\Gini\CGI\Validator\Exception $e) {
                $form['_errors'] = $validator->errors();
            }
        }

        return \Gini\IoC::construct('\Gini\CGI\Response\HTML', V('projects/actions/add-surveyor-modal', [
            'form' => $form,
            'project' => $project
        ]));
    }
}
