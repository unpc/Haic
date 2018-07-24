<?php

namespace Gini\Controller\CGI;

use Gini\Model\Alert;

class Login extends Layout\Login {

    function __index() {
        if (\Gini\Auth::isLoggedIn()) {
            $this->redirect('/');
        }
        $form = $this->form();
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $validator = new \Gini\CGI\Validator;

            try {

                $validator
                    ->validate('AuthToken', $form['AuthToken'], T('登录名不能为空!'))
                    ->validate('AuthCode', $form['AuthCode'], T('密码不能为空!'))
                    // ->validate('AuthCode', preg_match('/(?=^.{8,24}$)(?=(?:.*?\d){1})(?=.*[a-z])(?=(?:.*?[A-Z]){1})(?!.*\s)[0-9a-zA-Z!@#.,$%*()_+^&]*$/', $form['AuthCode']), T('请输入8 ~ 24位含大写字母、数字混合密码!'))
                    ->done();
                $username = \Gini\Auth::normalize($form['AuthToken']);
                /**
                 * 生成一个 Auth 来验证用户
                 */
                $auth = \Gini\IoC::construct('\Gini\Auth', $username);
                $password = $form['AuthCode'];
                if ($auth->verify($password)) {

                    \Gini\Auth::login($username);

                    if ($form['remember-me']) {
                        // \Gini\Module\God::rememberMe();
                    }

                    $this->redirect('/');
                }

                $form['_errors']['*'] = T('用户名/密码错误!');
            }
            catch (\Gini\CGI\Validator\Exception $e) {
                $form['_errors'] = $validator->errors();
            }

            foreach ($form['_errors'] as $message) {
                Alert::setMessage($message, Alert::TYPE_ERROR);
            }
        }

        $this->view->title = '用户登录';
        $this->view->body = V('login/body', [
            'form' => $form
        ]);

    }


}
