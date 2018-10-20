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
            Hub('template.group', (array)$form);
        }
        $this->view->body = V('mine/index', [
            'form' => $form,
        ]);
    }
}
