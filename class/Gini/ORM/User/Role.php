<?php

namespace Gini\ORM\User;

class Role extends \Gini\ORM\Object
{
    public $user    = 'object:user';
    public $role    = 'object:role';

    protected static $db_index = [
        'user',
        'role'
    ];

}
