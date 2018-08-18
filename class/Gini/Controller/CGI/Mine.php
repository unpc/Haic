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
            $group = [];
            $group['name'] = $form['name'];
            $group['address'] = $form['address'];
            $group['owner'] = $form['owner'];
            $group['level'] = $form['level'];
            $group['number'] = $form['number'];
            Hub('template.group', $group);
        }
        $this->view->body = V('mine/index', [
            'form' => $form,
        ]);
    }
}
