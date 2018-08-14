<?php

namespace Gini\Controller\CGI\AJAX;

class Template extends \Gini\Controller\CGI
{
    public function actionAdd()
    {
        $me = _G('ME');
        $form = $this->form();

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $validator = new \Gini\CGI\Validator;

            try {
                $validator
                    ->validate('title', $form['title'], T('模板名称不能为空!'))
                    ->done();
                $template = a('template');
                $template->user = $me;
                $template->ctime = date('Y-m-d H:i:s');
                $template->title = $form['title'];
                $template->content = $form['content'];
                $template->save();

                return \Gini\IoC::construct('\Gini\CGI\Response\HTML', '<script data-ajax="true">window.location.reload();</script>');
            } catch (\Gini\CGI\Validator\Exception $e) {
                $form['_errors'] = $validator->errors();
            }
        }

        return \Gini\IoC::construct('\Gini\CGI\Response\HTML', V('template/add-template-modal', [
            'form' => $form
        ]));
    }

    public function actionEdit($id=0)
    {
        $me = _G('ME');
        $template = a('template', $id);
        if (!$template->id) {
            $this->redirect('error/404');
        }

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $form = $this->form();

            $validator = new \Gini\CGI\Validator;

            $template->content = $form['content'];
            $template->save();

            return \Gini\IoC::construct('\Gini\CGI\Response\HTML', '<script data-ajax="true">window.location.reload();</script>');
        }
    }

    public function actionDelete($id=0, $refresh=false)
    {
        $me = _G('ME');
        $template = a('template', $id);
        if (!$template->id) {
            $this->redirect('error/404');
        }

        //remove this template
        $template->delete();

        if ($refresh) {
            return \Gini\IoC::construct('\Gini\CGI\Response\HTML', '<script data-ajax="true">window.location.href="'.URL('/template').'"</script>');
        }

        return \Gini\IoC::construct('\Gini\CGI\Response\HTML', V('template/delete-template-success', [
            'template' => $template
        ]));
    }

    public function actionGetTemplates()
    {
        $me = _G('ME');
        $form = $this->form();
        $objects = [];

        try {
            $templates = those('template')->whose('title')->contains(H($form['query']));
            foreach ($templates as $key => $template) {
                $objects[$key] = [
                    'name' => $template->title,
                    'id' => $template->id
                ];
            }
        } catch (\Gini\RPC\Exception $e) {
            $objects = [];
        }

        return \Gini\IoC::construct('\Gini\CGI\Response\JSON', $objects);
    }
}
