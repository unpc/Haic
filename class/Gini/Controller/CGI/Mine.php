<?php

namespace Gini\Controller\CGI;

class Mine extends Layout\God {
    
    function __index($id=0) {
        $form = $this->form();
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $group = [];
            $group['name'] = $form['name'];
            $group['address'] = $form['address'];
            $group['owner'] = $form['owner'];
            $group['level'] = $form['level'];
            $group['number'] = $form['number'];
            Hub('template.group', $group);
        }
        $this->view->body = V('mine/index', [
            'form' => $form
        ]);
    }

}
