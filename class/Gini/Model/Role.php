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
                if ($object->id && $object->id == $user->id) {
                    $e->pass();
                    return false;
                }
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

    public static function groupACL($e, $user, $action, $object, $when, $where)
    {
        switch ($action) {
            case '查看':
                if ($user->access('查看机构管理')) {
                    $e->abort();

                    return true;
                }
                break;
            case '修改':
                if ($user->access('修改机构信息')) {
                    $e->abort();

                    return true;
                }
                break;
            default:
                $e->pass();
                break;
        }
    }

    public static function archiveACL($e, $user, $action, $object, $when, $where)
    {
        switch ($action) {
            case '查看档案':
                if ($user->access('查看档案模块')) {
                    $e->abort();

                    return true;
                }
                break;
            case '查看档案项目':
                if ($user->access('查看档案项目内容')) {
                    $e->abort();

                    return true;
                }
                break;
            default:
                $e->pass();
                break;
        }
    }

    public static function templateACL($e, $user, $action, $object, $when, $where)
    {
        switch ($action) {
            case '查看':
                if ($user->access('查看模板信息模块')) {
                    $e->abort();
                    return true;
                }
                break;
            case '添加':
                if ($user->access('添加新模板')) {
                    $e->abort();
                    return true;
                }
                break;
            case '修改':
                if ($object->id && $user->id == $object->user->id) {
                    $e->abort();
                    return true;
                }
                if ($user->access('修改模板信息')) {
                    $e->abort();
                    return true;
                }
                break;
            case '删除':
                if ($object->id && $user->id == $object->user->id) {
                    $e->abort();
                    return true;
                }
                if ($user->access('删除模板信息')) {
                    $e->abort();
                    return true;
                }
                break;
            default:
                $e->pass();
                break;
        }
    }

    public static function projectACL($e, $user, $action, $object, $when, $where)
    {
        if ($action != '查看' && $object->id && $object->archived()) {
            $e->pass();
            return false;
        }
        switch($action) {
            case '查看':
                if ($user->access('查看项目模块')) {
                    $e->abort();
                    return true;
                }
                if ($object->id) {
                    if ($object->owner->id == $user->id) {
                        $e->abort();
                        return true;
                    }
                    if ($object->dispatcher->id == $user->id) {
                        $e->abort();
                        return true;
                    }
                    if ($object->assessor->id == $user->id) {
                        $e->abort();
                        return true;
                    }
                    if ($object->surveyor->id == $user->id) {
                        $e->abort();
                        return true;
                    }
                }
                break;
            case '添加':
                if ($user->access('添加新项目')) {
                    $e->abort();
                    return true;
                }
                break;
            case '修改':
                if ($user->access('修改项目基础信息')) {
                    $e->abort();
                    return true;
                }
                if ($object->id) {
                    if ($object->owner->id == $user->id) {
                        $e->abort();
                        return true;
                    }
                    if ($object->dispatcher->id == $user->id) {
                        $e->abort();
                        return true;
                    }
                    if ($object->assessor->id == $user->id) {
                        $e->abort();
                        return true;
                    }
                    if ($object->surveyor->id == $user->id) {
                        $e->abort();
                        return true;
                    }
                }
                break;
            case '删除':
                if ($object->id && $object->owner->id == $user->id) {
                    $e->abort();
                    return true;
                }
                if ($user->access('删除项目')) {
                    $e->abort();
                    return true;
                }
                break;
            case '指派派件员':
                if ($object->id && $object->type == \Gini\ORM\Project::PUBLIC_BUSINESS) {
                    $e->pass();
                    return false;
                }
                if ($object->isPreparation) {
                    $e->pass();
                    return false;
                }
                if ($object->id && $object->owner->id == $user->id) {
                    $e->abort();
                    return true;
                }
                if ($user->access('指派派件员')) {
                    $e->abort();
                    return true;
                }
                break;
            case '指派估价员':
                if ($object->id && $object->owner->id == $user->id) {
                    $e->abort();
                    return true;
                }
                if ($user->access('指派估价员')) {
                    $e->abort();
                    return true;
                }
                break;
            case '指派查勘员':
                if ($object->id && $object->owner->id == $user->id) {
                    $e->abort();
                    return true;
                }
                if ($user->access('指派查勘员')) {
                    $e->abort();
                    return true;
                }
                break;
            case '克隆项目':
                if ($user->access('克隆其他项目数据')) {
                    $e->abort();
                    return true;
                }
                break;
            case '打印报告':
                if ($object->id && $object->owner->id == $user->id) {
                    $e->abort();
                    return true;
                }
                if ($user->access('打印项目报告')) {
                    $e->abort();
                    return true;
                }
                break;
            case '归档':
                if ($user->access('归档项目')) {
                    $e->abort();
                    return true;
                }
                break;
            case '审核':
                if ($user->access('提交审核')) {
                    $e->abort();
                    return true;
                }
                break;
            default:
                $e->pass();
                break;
        }
    }

    public static function approvalACL($e, $user, $action, $object, $when, $where)
    {
        switch ($action) {
            case '提交':
                if ($object->status == \Gini\ORM\Approval::APPROVAL_PENDING) {
                    if ($user->access('提交审核')) {
                        $e->abort();
                        return true;
                    }
                }
                break;
            case '一审':
                if ($object->status == \Gini\ORM\Approval::APPROVAL_FIRST && $user->access('部门内审核（一审）')) {
                    $e->abort();
                    return true;
                }
                break;
            case '二审':
                if ($object->status == \Gini\ORM\Approval::APPROVAL_SECOND && $user->access('审核部审核（二审）')) {
                    $e->abort();
                    return true;
                }
                break;
            case '登记':
                if ($object->status == \Gini\ORM\Approval::APPROVAL_BILLING && $user->access('财务审核（登记）')) {
                    $e->abort();
                    return true;
                }
                break;
            case '终审':
                if ($object->status == \Gini\ORM\Approval::APPROVAL_PASS && $user->access('办公室审核（终审）')) {
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
