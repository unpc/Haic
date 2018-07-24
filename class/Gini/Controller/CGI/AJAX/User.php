<?php

namespace Gini\Controller\CGI\AJAX;

class User extends \Gini\Controller\CGI {

    public function actionGetUsers()
    {
        $me = _G('ME');
        $form = $this->form();
        $objects = [];

        try {
            $users = those('user')->whose('name')->contains(H($form['query']));
            foreach ($users as $key => $user) {
                $objects[$key] = [
                    'name' => $user->name,
                    'id' => $user->id
                ];
            }
        } catch (\Gini\RPC\Exception $e) {
            $objects = [];
        }

        return \Gini\IoC::construct('\Gini\CGI\Response\JSON', $objects);
    }

	public function actionAdd() {
        $me = _G('ME');
        $form = $this->form();
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {

            $validator = new \Gini\CGI\Validator;

            try {

                $username = \Gini\Auth::makeUserName(H($form['username']), H($form['backend']));

                $exist_user = a('user')
                            ->whose('username')
                            ->is($username);

                $validator
                    ->validate('name', $form['name'], T('姓名不能为空!'))
                    ->validate('username', $form['username'], T('登录名不能为空!'))
                    ->validate('username', !$exist_user->id, T('账号在系统中已存在!'))
                    ->validate('password', $form['password'], T('密码不能为空!'))
                    ->done();

                $user = a('user');
                $user->name = H($form['name']);
                $user->username = $username;
                $user->email = H($form['email']);
                $user->phone = H($form['phone']);
                $user->save();

				$auth = \Gini\IoC::construct('\Gini\Auth', $username);
        		$auth->create(H($form['password'] ?: '123456'));

                return \Gini\IoC::construct('\Gini\CGI\Response\HTML', '<script data-ajax="true">window.location.reload();</script>');
            }
            catch (\Gini\CGI\Validator\Exception $e) {
                $form['_errors'] = $validator->errors();
            }

        }

        return \Gini\IoC::construct('\Gini\CGI\Response\HTML', V('users/add-user-modal', [
            'form' => $form
        ]));
    }

    public function actionEdit($id=0) 
    {
        $me = _G('ME');
        $user = a('user', $id);
        if (!$user->id) {
            $this->redirect('error/404');
        }

        $form = $this->form();
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $validator = new \Gini\CGI\Validator;

            try {
                $validator
                    ->validate('name', $form['name'], T('姓名不能为空!'))
                    ->done();

                $user->name = $form['name'];
                $user->phone = $form['phone'];
                $user->email = $form['email'];
                $user->number = $form['number'];
                $user->save();

                return \Gini\IoC::construct('\Gini\CGI\Response\HTML', '<script data-ajax="true">window.location.reload();</script>');
            }
            catch (\Gini\CGI\Validator\Exception $e) {
                $form['_errors'] = $validator->errors();
            }

        }

        return \Gini\IoC::construct('\Gini\CGI\Response\HTML', V('users/edit-user-modal', [
            'form' => $form, 
            'user' => $user
        ]));

    }

    public function actionEditPwd($id=0) 
    {
        $me = _G('ME');
        $user = a('user', $id);
        if (!$user->id) {
            $this->redirect('error/404');
        }

        $form = $this->form();
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $validator = new \Gini\CGI\Validator;

            try {
                $validator
                    ->validate('password', $form['password'], T('密码不能为空!'))
                    ->done();

                $username = $user->username;

                $auth = \Gini\IoC::construct('\Gini\Auth', $username);
                $auth->changePassword(H($form['password'] ?: 'Az123456'));

                return \Gini\IoC::construct('\Gini\CGI\Response\HTML', '<script data-ajax="true">window.location.reload();</script>');
            }
            catch (\Gini\CGI\Validator\Exception $e) {
                $form['_errors'] = $validator->errors();
            }

        }

        return \Gini\IoC::construct('\Gini\CGI\Response\HTML', V('users/edit-user-password-modal', [
            'form' => $form, 
            'user' => $user
        ]));

    }

    public function actionEditRole($id=0) 
    {
        $me = _G('ME');
        $user = a('user', $id);
        if (!$user->id) {
            $this->redirect('error/404');
        }

        $form = $this->form();
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {

            // 删除之前的关联数据
            $user->roles()->deleteAll();
            
            // 关联目前角色数据
            foreach ((array)$form['role'] as $key => $val) {
                if ($val == 'on') {
                    $r = a('user/role');
                    $r->user = $user;
                    $r->role = a('role', $key);
                    $r->save();
                }
            }

            return \Gini\IoC::construct('\Gini\CGI\Response\HTML', '<script data-ajax="true">window.location.reload();</script>');
        }

        return \Gini\IoC::construct('\Gini\CGI\Response\HTML', V('users/edit-user-role-modal', [
            'form' => $form, 
            'user' => $user
        ]));

    }

    function actionDelete($id=0) 
    {
        $me = _G('ME');
        $user = a('user', $id);
        if (!$user->id) {
            $this->redirect('error/404');
        }

        //remove this user
        $user->delete();

        return \Gini\IoC::construct('\Gini\CGI\Response\HTML', V('users/delete-user-success', [
            'user' => $user
        ]));

    }
}
