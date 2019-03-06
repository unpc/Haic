<?php

namespace Gini\Controller\CGI\AJAX;

use Gini\Model\Alert;

class Approval extends \Gini\Controller\CGI
{

    public function actionCreate($id=0)
    {
        $me = _G('ME');
        $project = a('project', $id);
        if (!$project->id) {
            $this->redirect('error/404');
        }
        $approval = a('approval')->whose('project')->is($project);
        $approval->status = \Gini\ORM\Approval::APPROVAL_FIRST;
        $approval->save();
        $current_status = \Gini\ORM\Approval::$APPROVAL_STATUS[$approval->status];

        $log = a('log');
        $log->user = $me;
        $log->project = $project;
        $log->action = \Gini\ORM\Log::ACTION_APPROVAL;
        $log->description = strtr('%user 提交审核! ', ['%user' => $me->name]);
        $log->save();

        return \Gini\IoC::construct('\Gini\CGI\Response\HTML', '<script data-ajax="true">window.location.reload();</script>');
    }

    public function actionPass($id=0)
    {
        $me = _G('ME');
        $approval = a('approval', $id);
        if (!$approval->id) {
            $this->redirect('error/404');
        }
        $canAction = false;
        switch($approval->status) {
            case \Gini\ORM\Approval::APPROVAL_FIRST:
                if ($me->isAllowedTo('一审', $approval)) {
                    $canAction = true;
                }
                break;
            case \Gini\ORM\Approval::APPROVAL_SECOND:
                if ($me->isAllowedTo('二审', $approval)) {
                    $canAction = true;
                }
                break;
            case \Gini\ORM\Approval::APPROVAL_BILLING:
                if ($me->isAllowedTo('登记', $approval)) {
                    $canAction = true;
                }
                break;
            case \Gini\ORM\Approval::APPROVAL_PASS:
                if ($me->isAllowedTo('终审', $approval)) {
                    $canAction = true;
                }
                break;
            default:
                break;
        }
        if (!$canAction) {
            $this->redirect('error/401');
        }

        $approval->status = $approval->status + 1;
        $approval->save();
        $log = a('log');
        $log->user = $me;
        $log->project = $approval->project;
        $log->action = \Gini\ORM\Log::ACTION_APPROVAL;
        $log->description = strtr("%user 审核项目通过 => [%approval%]", [
            '%user' => $me->name,
            '%approval%' => \Gini\ORM\Approval::$APPROVAL_STATUS[$approval->status - 1]
        ]);
        $log->save();

        return \Gini\IoC::construct('\Gini\CGI\Response\HTML', '<script data-ajax="true">window.location.reload();</script>');
    }

    public function actionReply($id=0)
    {
        $me = _G('ME');
        $approval = a('approval', $id);
        if (!$approval->id) {
            $this->redirect('error/404');
        }
        $canAction = false;
        switch($approval->status) {
            case \Gini\ORM\Approval::APPROVAL_FIRST:
                if ($me->isAllowedTo('一审', $approval)) {
                    $canAction = true;
                }
                break;
            case \Gini\ORM\Approval::APPROVAL_SECOND:
                if ($me->isAllowedTo('二审', $approval)) {
                    $canAction = true;
                }
                break;
            case \Gini\ORM\Approval::APPROVAL_BILLING:
                if ($me->isAllowedTo('登记', $approval)) {
                    $canAction = true;
                }
                break;
            case \Gini\ORM\Approval::APPROVAL_PASS:
                if ($me->isAllowedTo('终审', $approval)) {
                    $canAction = true;
                }
                break;
            default:
                break;
        }
        if (!$canAction) {
            $this->redirect('error/401');
        }

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $form = $this->form();

            $validator = new \Gini\CGI\Validator;

            try {
                $validator
                    ->validate('reason', $form['reason'], T('驳回理由不能为空!'))
                    ->done();
                
                $reason = (array)$approval->reason;
                array_unshift($reason, $form['reason']);
                $approval->reason = $reason;
                $approval->status = $approval->status - 1;
                $approval->save();
                $log = a('log');
                $log->user = $me;
                $log->project = $approval->project;
                $log->action = \Gini\ORM\Log::ACTION_APPROVAL;
                $log->description = strtr("%user 项目驳回。[%approval]", [
                    '%user' => $me->name,
                    '%approval%' => \Gini\ORM\Approval::APPROVAL_STATUS[$approval->status + 1]
                ]);
                $log->save();

                return \Gini\IoC::construct('\Gini\CGI\Response\HTML', '<script data-ajax="true">window.location.reload();</script>');
            } catch (\Gini\CGI\Validator\Exception $e) {
                $form['_errors'] = $validator->errors();
            }
        }


        return \Gini\IoC::construct('\Gini\CGI\Response\HTML', V('approval/reply', [
            'approval' => $approval,
            'form' => $form
        ]));
    }
}
