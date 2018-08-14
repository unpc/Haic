<?php

namespace Gini\ORM;

class Template extends Object
{
    public $user	    = 'object:user';
    public $title 	    = 'string:100';
    public $ctime       = 'datetime';
    public $content     = 'string:*';

    protected static $db_index = [
        'user', 'title', 'ctime'
    ];
    
    public function save()
    {
        if ($this->ctime == '0000-00-00 00:00:00' || !$this->ctime) {
            $this->ctime = date('Y-m-d H:i:s');
        }
        return parent::save();
    }
}
