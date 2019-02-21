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
        $me = _G('ME');

        $db = a('project')->db();

        $num = (int)$db->value(strtr("select count(distinct project.id) from project 
        left join approval on approval.project_id = project.id 
        where (approval.status in (0, 1) or approval.id is null) 
        and (project.assessor_id = %id or project.dispatcher_id = %id or project.surveyor_id = %id)", ['%id' => $me->id]));

        if ($me->access('部门内审核（一审）')) {
            $num += (int)$db->value(strtr("select count(distinct project.id) from project 
                left join approval on approval.project_id = project.id 
                where approval.status = %status", [
                    '%status' => \Gini\ORM\Approval::APPROVAL_FIRST
                ]));
        }

        if ($me->access('审核部审核（二审）')) {
            $num += (int)$db->value(strtr("select count(distinct project.id) from project 
                left join approval on approval.project_id = project.id 
                where approval.status = %status", [
                    '%status' => \Gini\ORM\Approval::APPROVAL_SECOND
                ]));
        }

        if ($me->access('财务审核（登记）')) {
            $num += (int)$db->value(strtr("select count(distinct project.id) from project 
                left join approval on approval.project_id = project.id 
                where approval.status = %status", [
                    '%status' => \Gini\ORM\Approval::APPROVAL_BILLING
                ]));
        }

        if ($me->access('办公室审核（终审）')) {
            $num += (int)$db->value(strtr("select count(distinct project.id) from project 
                left join approval on approval.project_id = project.id 
                where approval.status = %status", [
                    '%status' => \Gini\ORM\Approval::APPROVAL_PASS
                ]));
        }
        
        $this->view->title = '天元房地产评估';
        $this->view->sidebar = V('sidebar', ['selected'=>$args[0], 'num' => $num]);
        $this->view->nav    = V('nav');
        $this->view->footer = V('footer');

        $c = implode('-', $args);
        $this->view->layout_class = "layout-$c";

        return parent::__postAction($action, $params, $response);
    }
}
