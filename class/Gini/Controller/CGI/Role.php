<?php

namespace Gini\Controller\CGI;

class Role extends Layout\God {
    
    function __index($id=0) {
        $form = $this->form();
        $roles = those('role');
        $role = a('role', $id);
        if (!$role->id) $role = $roles->current();
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            if (!$role->id) $this->redirect('error/401');
            if ($form['submit']) {
                $role->perms = $form['perms'];
                $role->save();
            }
            else {
                //获取post参数 并校验
                $form = $this->form('post');
                $r = a('role');
                $r->name = $form['name'];
                $r->code = uniqid();
                $r->save();
                $roles = those('role');
            }
        }
        $this->view->body = V('roles/index', [
            'role' => $role,
            'roles' => $roles,
            'form' => $form
        ]);
    }

}
