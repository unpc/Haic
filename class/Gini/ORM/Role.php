<?php

namespace Gini\ORM;

class Role extends Object
{
    public $code  = 'string:50';
    public $name  = 'string:100';

    protected static $db_index = [
        'code',
        'unique:name'
    ];
}
