<?php

namespace Gini\Controller\CGI;

class User extends Layout\God
{
    public function __index()
    {
        $form = $this->form();
        $step = 10;
        $users = those('user');
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            //获取post参数 并校验
            $form = $this->form('post');
            $users = $users->whose('name')->contains($form['name']);
        }
        $pagination = \Gini\Model\Help::pagination($users, $form['st'], $step);
        $this->view->body = V('users/index', [
            'users' => $users,
            'pagination' => $pagination,
            'form' => $form
        ]);
    }
}
