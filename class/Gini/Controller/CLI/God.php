<?php

namespace Gini\Controller\CLI;

class God extends \Gini\Controller\CLI
{
    public function __index($args)
    {
        echo "Available commands:\n";
        echo "  gini God addUser\n";
    }

    public function actionAddUser($args)
    {
        if (0 == count($args)) {
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

    public function actionUpdatePoints()
    {
        $points = (array)\Gini\Config::get('point');
        foreach ($points as $key => $value) {
            $identity = "#".$key;
            $point = a('point')->whose('identity')->is($identity);
            if (!$point->id) {
                $point->identity = $identity;
            }
            $point->title = $value;
            echo $point->save() ? '.' : 'x';
        }
    }
}
