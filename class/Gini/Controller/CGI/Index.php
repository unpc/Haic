<?php

namespace Gini\Controller\CGI;

class Index extends Layout\God
{
    public function __index()
    {
        $this->redirect('/user');
    }

    public function actionLogOut()
    {
        \Gini\Auth::logout();
        $this->redirect('/login');
    }
}
