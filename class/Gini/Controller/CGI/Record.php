<?php

namespace Gini\Controller\CGI;

use \Gini\Model\Help;

class Record extends Layout\God {
    
    function __index() {
        $form = $this->form();
        $step = 10;
        $projects = those('project')->whose('archive_time')->isGreaterThan('0000-00-00 00:00:00');
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            //获取post参数 并校验
            $form = $this->form('post');
            $projects = $projects->whose('number')->contains($form['number']);
        }
        $pagination = Help::pagination($projects, $form['st'], $step);
        $this->view->body = V('records/index', [
            'projects' => $projects,
            'form' => $form
        ]);
    }

}
