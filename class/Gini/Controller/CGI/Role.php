<?php

namespace Gini\Controller\CGI;

class Role extends Layout\God
{
    public function __index($id = 0)
    {
        $me = _G('ME');
        $form = $this->form();
        $roles = those('role');
        $role = a('role', $id);
        if (!$role->id) {
            $role = $roles->current();
        }
        if ('POST' == $_SERVER['REQUEST_METHOD']) {
            if ('submit' == $form['submit']) {
                if (!$me->isAllowedTo('修改', $role)) {
                    $this->redirect('error/401');
                }
                $role->perms = $form['perms'];
                $role->save();
            } elseif ('delete' == $form['submit']) {
                if (!$me->isAllowedTo('删除', $role)) {
                    $this->redirect('error/401');
                }
                $role->delete();
                $this->redirect('/role');
            } else {
                if (!$me->isAllowedTo('添加', 'role')) {
                    $this->redirect('error/401');
                }
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
            'form' => $form,
        ]);
    }
}
