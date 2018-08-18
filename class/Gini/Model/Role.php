<?php

namespace Gini\Model;

class Role
{
    public static function userACL($e, $user, $action, $object, $when, $where)
    {
        switch ($action) {
            case '添加':
                if ($user->access('添加用户')) {
                    $e->abort();
                    return true;
                }
                break;
            case '修改密码':
                if ($user->id == $object->id || $user->access('修改用户密码')) {
                    $e->abort();
                    return true;
                }
                break;
            case '修改基本信息':
                if ($user->id == $object->id || $user->access('修改用户基本信息')) {
                    $e->abort();
                    return true;
                }
                break;
            case '修改角色':
                if ($user->access('修改用户角色')) {
                    $e->abort();
                    return true;
                }
                break;
            case '删除用户':
                if ($user->access('删除用户')) {
                    $e->abort();
                    return true;
                }
                break;
            default:
                $e->pass();
                return false;
                break;
        }
    }

    public static function roleACL($e, $user, $action, $object, $when, $where)
    {
        switch ($action) {
            case '查看':
                if ($user->access('查看权限管理模块')) {
                    $e->abort();
                    return true;
                }
                break;
            case '添加':
                if ($user->access('添加角色')) {
                    $e->abort();
                    return true;
                }
                break;
            case '修改':
                if ($object->id && $user->access('修改角色权限')) {
                    $e->abort();
                    return true;
                }
                break;
            case '删除':
                if ($object->id && $user->access('删除角色')) {
                    $e->abort();
                    return true;
                }
                break;
            default:
                $e->pass();
                break;
        }
    }
}
