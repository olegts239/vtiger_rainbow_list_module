<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is: EntExt
 * The Initial Developer of the Original Code is EntExt.
 * All Rights Reserved.
 * If you have any questions or comments, please email: devel@entext.com
 ************************************************************************************/

class Settings_EERainbowList_Save_Action extends Settings_Vtiger_Basic_Action {

    public function process(Vtiger_Request $request) {
        $recordId = $request->get('record');
        $summary = $request->get('summary');
        $moduleName = $request->get('module_name');
        $conditions = $request->get('conditions');
        $color = $request->get('color');

        if($recordId) {
            $rainbowModel = Settings_EERainbowList_Record_Model::getInstance($recordId);
        } else {
            $rainbowModel = Settings_EERainbowList_Record_Model::getCleanInstance($moduleName);
        }

        $rainbowModel->set('summary', $summary);
        $rainbowModel->set('module_name', $moduleName);
        $rainbowModel->set('conditions', $conditions);
        $rainbowModel->transformAdvanceFilterToFilter();
        $rainbowModel->set('color', $color);
        $rainbowModel->save();

        $response = new Vtiger_Response();
        $response->setResult(array('id' => $rainbowModel->get('rainbowlist_id')));
        $response->emit();
    }

    public function validateRequest(Vtiger_Request $request) {
        $request->validateWriteAccess();
    }
}