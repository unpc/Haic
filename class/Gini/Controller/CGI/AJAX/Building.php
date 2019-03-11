<?php

namespace Gini\Controller\CGI\AJAX;

class Building extends \Gini\Controller\CGI
{
    public function actionGetOwnershipView($id=0)
    {
        $me = _G('ME');
        $building = a('building', $id);

        $form = $this->form();

        $type = (array)$form['type'];
        if (count($type)) {
            return \Gini\IoC::construct('\Gini\CGI\Response\HTML', V("buildings/ownership/view", [
                'building' => $building,
                'type' => $type
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
        $add = false;
        if (!$building->id) {
            $building = a('building');
            $add = true;
        }

        $building->ownership_cert = (array)$form['ownership_cert'];
        $building->ownership = (array)$form['ownership'];

        /** 权属状况更新需要同步更新到基础信息 */
        

        $building->save();

        if ($add && $building->id) {
            $project->building = $building;
            $project->save();
        }
    }
}
