<?php

namespace Gini\Controller\CGI;

class Template extends Layout\God
{
    public function __index($type=1)
    {
        if (!_G('ME')->isAllowedTo('查看', 'template')) {
            $this->redirect('error/401');
        }
        $form = $this->form();
        $step = 10;
        $templates = those('template')->whose('type')->is($type);
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            if ($form['title']) {
                $templates = $templates->whose('title')->contains($form['title']);
            }
        }
        $pagination = \Gini\Model\Help::pagination($templates, $form['st'], $step);
        $this->view->body = V('template/list', [
            'templates' => $templates,
            'pagination' => $pagination,
            'type' => $type,
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
        if (!$me->isAllowedTo('修改', $template)) {
            $this->redirect('error/401');
        }

        $form = $this->form();

        $this->view->body = V('template/edit', [
            'template' => $template,
            'form' => $form
        ]);
    }
}
