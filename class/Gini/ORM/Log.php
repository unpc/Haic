<?php

namespace Gini\ORM;

class Log extends Object
{
    public $user	    = 'object:user';
    public $project 	= 'object:project';
    public $action      = 'int';
    public $ctime       = 'datetime';
    public $description = 'string:*';

    protected static $db_index = [
        'user', 'project', 'ctime'
    ];
    
    public function save()
    {
        if ($this->ctime == '0000-00-00 00:00:00' || !$this->ctime) {
            $this->ctime = date('Y-m-d H:i:s');
        }
        return parent::save();
    }

    const ACTION_ADD = 1;
    const ACTION_EDIT = 2;
    const ACTION_DELETE = 3;
    const ACTION_DISPATCH = 4;
    const ACTION_ASSESSOR = 5;
    const ACTION_SURVEYOR = 6;
    const ACTION_APPROVAL = 7;
    const ACTION_CLONE = 8;
    const ACTION_CREATE_REPORT = 9;
    const ACTION_PRINT_REPORT = 10;
    const ACTION_ARCHIVE = 11;

    public static $actions = [
        self::ACTION_ADD => '创建',
        self::ACTION_EDIT => '编辑',
        self::ACTION_DELETE => '删除',
        self::ACTION_DISPATCH => '指派派件员',
        self::ACTION_ASSESSOR => '指派估价师',
        self::ACTION_SURVEYOR => '指派查勘员',
        self::ACTION_APPROVAL => '提交审核',
        self::ACTION_CLONE => '克隆数据',
        self::ACTION_CREATE_REPORT => '生成报告',
        self::ACTION_PRINT_REPORT => '打印报告',
        self::ACTION_ARCHIVE => '归档项目'
    ];
}
