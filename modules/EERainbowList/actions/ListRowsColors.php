<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is: EntExt
 * The Initial Developer of the Original Code is EntExt.
 * All Rights Reserved.
 * If you have any questions or comments, please email: devel@entext.com
 ************************************************************************************/
require_once 'include/Webservices/State.php';
require_once 'include/Webservices/OperationManager.php';
require_once 'include/Webservices/SessionManager.php';
require_once 'include/Webservices/WebserviceField.php';
require_once 'include/Webservices/EntityMeta.php';
require_once 'include/Webservices/VtigerWebserviceObject.php';
require_once 'include/Webservices/VtigerCRMObject.php';
require_once 'include/Webservices/VtigerCRMObjectMeta.php';
require_once 'include/Webservices/DataTransform.php';
require_once 'include/Webservices/WebServiceError.php';
require_once 'include/Webservices/ModuleTypes.php';
require_once 'include/Webservices/Utils.php';
require_once 'include/Webservices/WebserviceEntityOperation.php';
require_once 'include/Webservices/Retrieve.php';
require_once 'modules/com_vtiger_workflow/VTEntityCache.inc';
require_once 'modules/com_vtiger_workflow/VTJsonCondition.inc';

class EERainbowList_ListRowsColors_Action extends Vtiger_Action_Controller {

    public function checkPermission(Vtiger_Request $request) {

    }

    public function process(Vtiger_Request $request) {
        $currentModule = $request->get('current_module');
        $records = $request->get('records');
        $result = $this->getBackgroundColors($currentModule, $records);
        $response = new Vtiger_Response();
        $response->setResult($result);
        $response->emit();
    }

    private function getBackgroundColors($currentModule, $records) {
        global $current_user;
        $current_user = Users::getActiveAdminUser();
        $entityCache = new VTEntityCache($current_user);
        $jsonCondition = new VTJsonCondition();
        $rainbowModelList = Settings_EERainbowList_Record_Model::getInstancesByModule($currentModule);
        $colors = array();
        foreach($records as $record) {

            // Handle Calendar module
            if($currentModule == 'Calendar') {
                $currentModule = vtws_getCalendarEntityType($record);
                $rainbowModelList = Settings_EERainbowList_Record_Model::getInstancesByModule($currentModule);
            }

            foreach($rainbowModelList as $rainbowModel) {
                $entityId = vtws_getWebserviceEntityId($currentModule, $record);
                $conditions = Zend_Json::encode($rainbowModel->get('conditions'));
                $res = $jsonCondition->evaluate($conditions, $entityCache, $entityId);
                if($res) {
                    $colors[$record] = $rainbowModel->get('color');
                }
            }
        }
        return $colors;
    }
}