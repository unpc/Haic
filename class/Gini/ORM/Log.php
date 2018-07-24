<?php

namespace Gini\ORM;

class Log extends Object
{
    public $object      = 'object:project';
    public $user        = 'object:user';
    public $ctime       = 'datetime';

    protected static $db_index = [
        'ctime'
    ];
}