<?php

namespace Gini\ORM;

class Description extends Object
{
    public $user	    = 'object:user';
    public $project 	= 'object:project';
    public $ctime       = 'datetime';
    public $mtime       = 'datetime';
    public $description = 'string:*';

    protected static $db_index = [
        'user', 'project', 'ctime'
    ];
    
    public function save()
    {
        if ($this->ctime == '0000-00-00 00:00:00' || !$this->ctime) {
            $this->ctime = date('Y-m-d H:i:s');
        }
        $this->mtime = date('Y-m-d H:i:s');
        return parent::save();
    }
}
