<?php

namespace Gini\Controller\CGI;

class User extends Layout\God
{
    public function __index($sub='normal')
    {
        $form = $this->form();
        $step = 10;
        
        if ($sub == 'normal') {
            $users = those('user')->whose('number')->is('');
        }
        else {
            $users = those('user')->whose('number')->isNot('');
        }

        if ('POST' == $_SERVER['REQUEST_METHOD']) {
            //获取post参数 并校验
            $form = $this->form('post');
            $users = $users->whose('name')->contains($form['name']);
        }
        $pagination = \Gini\Model\Help::pagination($users, $form['st'], $step);
        $this->view->body = V('users/index', [
            'users' => $users,
            'pagination' => $pagination,
            'form' => $form,
            'sub' => $sub
        ]);
    }
}
