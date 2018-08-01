<?php

namespace Gini\Controller\CGI;

class Template extends Layout\God {
    
    public function __index() 
    {
        $form = $this->form();
        $step = 10;
        $templates = those('template');
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $form = $this->form('post');
            $templates = $templates->whose('title')->contains($form['title']);
        }
        $pagination = \Gini\Model\Help::pagination($templates, $form['st'], $step);
        $this->view->body = V('template/list', [
            'templates' => $templates,
            'pagination' => $pagination,
            'form' => $form
        ]);
    }

    public function actionEdit($id=0)
    {
        $me = _G('ME');
        $template = a('template', $id);
        if (!$template->id) {
            $this->redirect('error/404');
        }

        $form = $this->form();

        $this->view->body = V('template/edit', [
            'template' => $template,
            'form' => $form
        ]);
    }
    

}
