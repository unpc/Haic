<?php

namespace Gini\Controller\CGI;

class Record extends Layout\God {
    
    function __index() {
        $form = $this->form();
        $this->view->body = V('records/index', [
            'form' => $form
        ]);
    }

}
