<?php

namespace Gini\Controller\CLI;

class God extends \Gini\Controller\CLI {

    function __index($args) 
    {
        echo "Available commands:\n";
        echo "  gini God addUser\n";
    }

    function actionAddUser($args) 
    {
        if (count($args) == 0) {
            die("Usage: gini God addUser [username]\n");
        }

        $username = $args[0];

        echo "Username: $username\n";

        $password = 'Genee83719730';
        $email = 'support@geneegroup.com';

        $name = readline('Name: ');

        $username = \Gini\Auth::normalize($username);

        $user = a('user', ['username' => $username]);
        $user->name = $name;
        $user->username = $username;
        $user->email = $email;
        $user->admin = 1;
        $user->gender = 0;
        $user->save();


        $auth = \Gini\IoC::construct('\Gini\Auth', $username);
        $auth->create($password);

    }

}