<?php

namespace Gini\Controller\CGI\Layout;

abstract class God extends \Gini\Controller\CGI\Layout
{
    protected static $layout_name = 'layout/index';
    
    public function __postAction($action, &$params, $response)
    {
        if (!\Gini\Auth::isLoggedIn()) {
            $this->redirect('/login');
        }

        $route = \Gini\CGI::route();
        if ($route) {
            $args = explode('/', $route);
        }
        if (!$route || count($args) == 0) {
            $args = ['God'];
        }


        $this->view->title = '天元房地产评估';
        $this->view->sidebar = V('sidebar', ['selected'=>$args[0]]);
        $this->view->nav    = V('nav');
        $this->view->footer = V('footer');

        $c = implode('-', $args);
        $this->view->layout_class = "layout-$c";

        return parent::__postAction($action, $params, $response);
    }
}
