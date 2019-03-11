<?php

namespace Gini\Controller\CGI;

class Mine extends Layout\God
{
    public function __index($id = 0)
    {
        $me = _G('ME');
        if (!$me->isAllowedTo('查看', 'group')) {
            $this->redirect('error/401');
        }
        $form = $this->form();
        if ('POST' == $_SERVER['REQUEST_METHOD']) {
            if (!$me->isAllowedTo('修改', 'group')) {
                $this->redirect('error/401');
            }
            if (isset($form['added_value'])) {
                if ($form['added_value']) {
                    Hub('template.policy', (array)$form);
                }
                else {
                    Hub('template.policy', (array)\Gini\Config::get('project.policy'));
                }
            }
            else {
                if ($form['name']) {
                    Hub('template.group', (array)$form);
                }
                else {
                    Hub('template.group', (array)\Gini\Config::get('project.group'));
                }
            }
        }
        $this->view->body = V('mine/index', [
            'form' => $form,
        ]);
    }
}
