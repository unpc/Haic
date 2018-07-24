<?php

namespace Gini\ORM;

class Perm extends Object
{
    public $code        = 'string:50';
    public $name        = 'string:100';
    public $weight      = 'int';


    protected static $db_index = [
        'code',
        'name',
        'weight'
    ];
}
