<?php

namespace Gini\ORM;

class User extends Object
{
    public $name = 'string:50';
    public $gender = 'bool';
    public $username = 'string:120';
    public $admin = 'bool';
    public $email = 'string:120';
    public $phone = 'string:120';
    public $ctime = 'datetime';
    public $group = 'array';
    public $number = 'string:50';

    protected static $db_index = [
        'unique:username',
        'ctime', 'number',
    ];

    public function isAllowedTo($action, $object = null, $when = null, $where = null)
    {
        if (null === $object) {
            return \Gini\Event::trigger(
                "user.isAllowedTo[$action]",
                $this,
                $action,
                null,
                $when,
                $where
            );
        }

        $oname = is_string($object) ? $object : $object->name();

        return \Gini\Event::trigger(
            [
                "user.isAllowedTo[$action].$oname",
                "user.isAllowedTo[$action].*",
            ],
            $this,
            $action,
            $object,
            $when,
            $where
        );
    }

    public function icon()
    {
        return new \Model\Icon($this);
    }

    public function save()
    {
        if ('0000-00-00 00:00:00' == $this->ctime || !$this->ctime) {
            $this->ctime = date('Y-m-d H:i:s');
        }

        return parent::save();
    }

    public function roles()
    {
        return those('user/role')->whose('user')->is($this);
    }

    public function access($perm = '')
    {
        $admins = (array) \Gini\Config::get('admin.token');
        list($token, $backend) = \Gini\Auth::parseUserName($this->username);
        if (in_array($token, $admins)) {
            return true;
        }

        if (!_G('PERMS')) {
            $roles = $this->roles();
            $perms = [];
            foreach ($roles as $r) {
                $perms += (array)$r->role->perms;
            }
            _G('PERMS', $perms);
        }
        return array_key_exists($perm, _G('PERMS'));
    }

    public function delete()
    {
        // 删除人员关联角色信息
        $this->roles()->deleteAll();

        return parent::delete();
    }
}
