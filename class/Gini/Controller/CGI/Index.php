<?php

namespace Gini\Controller\CGI;

class Index extends Layout\God {
    
    function __index() {
        $this->redirect('/user');
    }

    function actionLogOut() {
        \Gini\Auth::logout();
        $this->redirect('/login');
    }

}
