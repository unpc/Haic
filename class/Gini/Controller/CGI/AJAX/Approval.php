<?php

namespace Gini\Controller\CGI\AJAX;

class Approval extends \Gini\Controller\CGI
{
    public function actionPass($id=0)
    {
        $me = _G('ME');
        $approval = a('approval', $id);
        if (!$approval->id) {
            $this->redirect('error/404');
        }

        $approval->status = $approval->status + 1;
        $approval->save();

        return \Gini\IoC::construct('\Gini\CGI\Response\HTML', '<script data-ajax="true">window.location.reload();</script>');
    }

    public function actionReply($id=0)
    {
        $me = _G('ME');
        $approval = a('approval', $id);
        if (!$approval->id) {
            $this->redirect('error/404');
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
