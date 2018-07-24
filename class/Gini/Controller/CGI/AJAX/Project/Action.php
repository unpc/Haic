<?php

namespace Gini\Controller\CGI\AJAX\Project;

class Action extends \Gini\Controller\CGI {

    public function actionAddDispatcher($id=0)
    {
        $me = _G('ME');
        $form = $this->form();
        $project = a('project', $id);
        if (!$project->id) {
            $this->redirect('error/404');
        }
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {

            $validator = new \Gini\CGI\Validator;

            try {

                $validator
                    ->validate('name', $form['name'] && $form['id'], T('请选择用户!'))
                    ->done();
                $project->dispatcher = a('user', (int)$form['id']);
                $project->save();

                return \Gini\IoC::construct('\Gini\CGI\Response\HTML', '<script data-ajax="true">window.location.reload();</script>');
            }
            catch (\Gini\CGI\Validator\Exception $e) {
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
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {

            $validator = new \Gini\CGI\Validator;

            try {

                $validator
                    ->validate('name', $form['name'] && $form['id'], T('请选择用户!'))
                    ->done();
                $project->assessor = a('user', (int)$form['id']);
                $project->save();

                return \Gini\IoC::construct('\Gini\CGI\Response\HTML', '<script data-ajax="true">window.location.reload();</script>');
            }
            catch (\Gini\CGI\Validator\Exception $e) {
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
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {

            $validator = new \Gini\CGI\Validator;

            try {

                $validator
                    ->validate('name', $form['name'] && $form['id'], T('请选择用户!'))
                    ->done();
                $project->surveyor = a('user', (int)$form['id']);
                $project->save();

                return \Gini\IoC::construct('\Gini\CGI\Response\HTML', '<script data-ajax="true">window.location.reload();</script>');
            }
            catch (\Gini\CGI\Validator\Exception $e) {
                $form['_errors'] = $validator->errors();
            }

        }

        return \Gini\IoC::construct('\Gini\CGI\Response\HTML', V('projects/actions/add-surveyor-modal', [
            'form' => $form,
            'project' => $project
        ]));
    }
}
