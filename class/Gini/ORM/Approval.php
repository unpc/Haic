<?php

namespace Gini\ORM;

class Approval extends Object
{
    public $project     = 'object:project';
    public $ctime       = 'datetime';
    public $status      = 'int:1,default:0';
    public $info        = 'array';

    protected static $db_index = [
        'unique:project',
        'status', 
    ];

    const APPROVAL_PENDING = 0;
    const APPROVAL_FIRST = 1;
    const APPROVAL_SECOND = 2;
    const APPROVAL_BILLING = 3;
    const APPROVAL_PASS = 4;
    const APPROVAL_OFFICE_PASS = 5;

    public static $APPROVAL_STATUS = [
        self::APPROVAL_PENDING => '未审核',
        self::APPROVAL_FIRST => '部门审核',
        self::APPROVAL_SECOND => '审核部审核',
        self::APPROVAL_BILLING => '财务审核',
        self::APPROVAL_PASS => '办公室审核',
        self::APPROVAL_OFFICE_PASS => '审核通过'
    ];
}