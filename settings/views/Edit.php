<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is: EntExt
 * The Initial Developer of the Original Code is EntExt.
 * All Rights Reserved.
 * If you have any questions or comments, please email: devel@entext.com
 ************************************************************************************/

class Settings_EERainbowList_Edit_View extends Settings_Vtiger_Index_View {

    public function process(Vtiger_Request $request) {
        $mode = $request->getMode();
        if ($mode) {
            $this->$mode($request);
        } else {
            $this->step1($request);
        }
    }

    public function preProcess(Vtiger_Request $request) {
        parent::preProcess($request);
        $viewer = $this->getViewer($request);

        $recordId = $request->get('record');
        $viewer->assign('RECORDID', $recordId);
        if($recordId) {
            $rainbowModel = Settings_EERainbowList_Record_Model::getInstance($recordId);
            $viewer->assign('RAINBOW_MODEL', $rainbowModel);
        }
        $viewer->assign('RECORD_MODE', $request->getMode());
        $viewer->view('EditHeader.tpl', $request->getModule(false));
    }

    public function step1(Vtiger_Request $request) {
        $currentUser = Users_Record_Model::getCurrentUserModel();
        $viewer = $this->getViewer($request);
        $moduleName = $request->getModule();
        $qualifiedModuleName = $request->getModule(false);

        $recordId = $request->get('record');
        if ($recordId) {
            $rainbowModel = Settings_EERainbowList_Record_Model::getInstance($recordId);
            $viewer->assign('RECORDID', $recordId);
            $viewer->assign('MODULE_MODEL', $rainbowModel->getModule());
            $viewer->assign('MODE', 'edit');
        } else {
            $rainbowModel = Settings_EERainbowList_Record_Model::getCleanInstance($moduleName);
            $selectedModule = $request->get('source_module');
            if(!empty($selectedModule)) {
                $viewer->assign('SELECTED_MODULE', $selectedModule);
            }
        }

        $viewer->assign('RAINBOW_MODEL', $rainbowModel);
        $viewer->assign('ALL_MODULES', Settings_EERainbowList_Module_Model::getSupportedModules());

        $viewer->assign('MODULE', $moduleName);
        $viewer->assign('QUALIFIED_MODULE', $qualifiedModuleName);
        $viewer->assign('CURRENT_USER', $currentUser);
        $admin = Users::getActiveAdminUser();
        $viewer->assign('ACTIVE_ADMIN', $admin);

        $viewer->view('Step1.tpl', $qualifiedModuleName);
    }

    public function step2(Vtiger_Request $request) {

        $viewer = $this->getViewer($request);
        $moduleName = $request->getModule();
        $qualifiedModuleName = $request->getModule(false);

        $recordId = $request->get('record');

        if ($recordId) {
            $rainbowModel = Settings_EERainbowList_Record_Model::getInstance($recordId);
            $selectedModule = $rainbowModel->getModule();
            $selectedModuleName = $selectedModule->getName();
        } else {
            $selectedModuleName = $request->get('module_name');
            $selectedModule = Vtiger_Module_Model::getInstance($selectedModuleName);
            $rainbowModel = Settings_EERainbowList_Record_Model::getCleanInstance($selectedModuleName);

        }
        $rainbowModel->set('module_name', $selectedModuleName);
        $rainbowModel->set('summary', $request->get('summary'));
        $rainbowModel->set('color', $request->get('color'));

        //Added to support advance filters
        $recordStructureInstance = Settings_EERainbowList_RecordStructure_Model::getInstanceForRainbowListModule($rainbowModel,
            Settings_EERainbowList_RecordStructure_Model::RECORD_STRUCTURE_MODE_FILTER);

        $viewer->assign('RECORD_STRUCTURE_MODEL', $recordStructureInstance);
        $recordStructure = $recordStructureInstance->getStructure();
        if(in_array($selectedModuleName,  getInventoryModules())){
            $itemsBlock = "LBL_ITEM_DETAILS";
            unset($recordStructure[$itemsBlock]);
        }
        $viewer->assign('RECORD_STRUCTURE', $recordStructure);

        $viewer->assign('RAINBOW_MODEL', $rainbowModel);

        $viewer->assign('MODULE_MODEL', $selectedModule);
        $viewer->assign('SELECTED_MODULE_NAME', $selectedModuleName);

        $dateFilters = Vtiger_Field_Model::getDateFilterTypes();
        foreach($dateFilters as $comparatorKey => $comparatorInfo) {
            $comparatorInfo['startdate'] = DateTimeField::convertToUserFormat($comparatorInfo['startdate']);
            $comparatorInfo['enddate'] = DateTimeField::convertToUserFormat($comparatorInfo['enddate']);
            $comparatorInfo['label'] = vtranslate($comparatorInfo['label'], $qualifiedModuleName);
            $dateFilters[$comparatorKey] = $comparatorInfo;
        }
        $viewer->assign('DATE_FILTERS', $dateFilters);
        $viewer->assign('ADVANCED_FILTER_OPTIONS', Settings_EERainbowList_Field_Model::getAdvancedFilterOptions());
        $viewer->assign('ADVANCED_FILTER_OPTIONS_BY_TYPE', Settings_EERainbowList_Field_Model::getAdvancedFilterOpsByFieldType());
        $viewer->assign('COLUMNNAME_API', 'getWorkFlowFilterColumnName');

        $viewer->assign('FIELD_EXPRESSIONS', Settings_EERainbowList_Module_Model::getExpressions());
        $viewer->assign('META_VARIABLES', Settings_EERainbowList_Module_Model::getMetaVariables());

        $viewer->assign('ADVANCE_CRITERIA', $rainbowModel->transformToAdvancedFilterCondition());

        $viewer->assign('IS_FILTER_SAVED_NEW', true);
        $viewer->assign('MODULE', $moduleName);
        $viewer->assign('QUALIFIED_MODULE', $qualifiedModuleName);

        $viewer->view('Step2.tpl', $qualifiedModuleName);
    }

    public function getHeaderScripts(Vtiger_Request $request) {
        $headerScriptInstances = parent::getHeaderScripts($request);
        $moduleName = $request->getModule();

        $jsFileNames = array(
            'modules.Settings.Vtiger.resources.Edit',
            "modules.Settings.$moduleName.resources.Edit",
            "modules.Settings.$moduleName.resources.Edit1",
            "modules.Settings.$moduleName.resources.Edit2",
            "modules.Settings.$moduleName.resources.Edit3",
            "modules.Settings.$moduleName.resources.AdvanceFilter",
            "modules.Settings.$moduleName.resources.libraries.jscolor.jscolor",
            '~libraries/jquery/ckeditor/ckeditor.js',
            "modules.Vtiger.resources.CkEditor",
            '~libraries/jquery/jquery.datepick.package-4.1.0/jquery.datepick.js',
        );

        $jsScriptInstances = $this->checkAndConvertJsScripts($jsFileNames);
        $headerScriptInstances = array_merge($headerScriptInstances, $jsScriptInstances);
        return $headerScriptInstances;
    }

    function getHeaderCss(Vtiger_Request $request) {
        $headerCssInstances = parent::getHeaderCss($request);
        $cssFileNames = array(
            '~libraries/jquery/jquery.datepick.package-4.1.0/jquery.datepick.css',
        );
        $cssInstances = $this->checkAndConvertCssStyles($cssFileNames);
        $headerCssInstances = array_merge($cssInstances, $headerCssInstances);
        return $headerCssInstances;
    }
}