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

    public function actionData()
    {
        $me = _G('ME');
        if (!$me->isAllowedTo('修改', 'template')) {
            $this->redirect('error/401');
        }

        $form = $this->form();

        $points = those('point');

        if ($form['title']) {
            $points = $points->whose('title')->contains(H($form['title']))
                ->orWhose('identity')->contains(H($form['title']));
        }

        $step = 10;

        $pagination = \Gini\Model\Help::pagination($points, $form['st'], $step, '', ['title' => $form['title']]);

        $this->view->body = V('template/data-template', [
            'me' => $me,
            'form' => $form,
            'points' => $points,
            'pagination' => $pagination
        ]);
    }

    public function actionPoint($id=0)
    {
        $me = _G('ME');
        if (!$me->isAllowedTo('修改', 'template')) {
            $this->redirect('error/401');
        }

        $point = a('point', $id);

        $form = $this->form();

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            if ($form['submit']) {
                $validator = new \Gini\CGI\Validator;

                try {
                    $validator
                        ->validate('title', $form['title'], T('数据名称不能为空!'))
                        ->validate('identity', $form['identity'], T('数据标示不能为空!'))
                        ->done();
                    $point->user = $me;
                    $point->ctime = date('Y-m-d H:i:s');
                    $point->title = $form['title'];
                    $point->content = $form['content'];
                    $point->identity = '#'.$form['identity'];
                    $point->save();

                } catch (\Gini\CGI\Validator\Exception $e) {
                    $form['_errors'] = $validator->errors();
                }

                $this->redirect('/template/data');
            }
        }

        $this->view->body = V('point/add-point', [
            'me' => $me,
            'form' => $form,
            'point' => $point
        ]);
    }
}