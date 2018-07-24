<?php

namespace Gini\Controller\CGI\AJAX;

class Building extends \Gini\Controller\CGI 
{

    public function actionGetOwnershipView($id=0)
    {
        $me = _G('ME');
        $building = a('building', $id);
        if (!$building->id) {
            $this->redirect('error/404');
        }

        $form = $this->form();
        $type = $form['type'] ?: $building->ownership_cert;
        if ($type) {
            if ($type != $building->ownership_cert) $building->ownership = [];
            return \Gini\IoC::construct('\Gini\CGI\Response\HTML', V("buildings/ownership/{$type}", [
                'building' => $building
            ]));
        }
    }

    public function actionAddOwnership($id=0)
    {
        $me = _G('ME');
        $project = a('project', $id);
        if (!$project->id) {
            $this->redirect('error/404');
        }

        $form = $this->form();
        $building = $project->building;
        $add = FALSE;
        if (!$building->id) {
            $building = a('building');
            $add = TRUE;
        }

        $ownership = (array)$form;
        unset($ownership['ownership_cert']);

        $building->ownership_cert = (int)$form['ownership_cert'];
        $building->ownership = $ownership;
        $building->save();

        if ($add && $building->id) {
            $project->building = $building;
            $project->save();
        }
    }

}
