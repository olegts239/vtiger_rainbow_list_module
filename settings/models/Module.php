<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is: EntExt
 * The Initial Developer of the Original Code is EntExt.
 * All Rights Reserved.
 * If you have any questions or comments, please email: devel@entext.com
 ************************************************************************************/

class Settings_EERainbowList_Module_Model extends Settings_Vtiger_Module_Model {

	var $baseTable = 'vtiger_ee_rainbowlist';
	var $baseIndex = 'rainbowlist_id';
	var $listFields = array('summary' => 'Summary', 'module_name' => 'Module', 'color' => 'Color');
	var $name = 'EERainbowList';

	static $metaVariables = array(
		'Current Date' => '(general : (__VtigerMeta__) date) ($_DATE_FORMAT_)',
		'Current Time' => '(general : (__VtigerMeta__) time)',
		'System Timezone' => '(general : (__VtigerMeta__) dbtimezone)',
		'User Timezone' => '(general : (__VtigerMeta__) usertimezone)',
		'CRM Detail View URL' => '(general : (__VtigerMeta__) crmdetailviewurl)',
		'Portal Detail View URL' => '(general : (__VtigerMeta__) portaldetailviewurl)',
		'Site Url' => '(general : (__VtigerMeta__) siteurl)',
		'Portal Url' => '(general : (__VtigerMeta__) portalurl)',
		'Record Id' => '(general : (__VtigerMeta__) recordId)',
		'LBL_HELPDESK_SUPPORT_NAME' => '(general : (__VtigerMeta__) supportName)',
		'LBL_HELPDESK_SUPPORT_EMAILID' => '(general : (__VtigerMeta__) supportEmailid)',
	);

    /**
     * Function to get the url for default view of the module
     * @return string <string> - url
     */
	public static function getDefaultUrl() {
		return 'index.php?module=EERainbowList&parent=Settings&view=List';
	}

    /**
     * Function to get the url for create view of the module
     *
     * @return string <string> - url
     */
	public static function getCreateViewUrl() {
		return "javascript:Settings_EERainbowList_List_Js.triggerCreate('index.php?module=EERainbowList&parent=Settings&view=Edit')";
	}

    /**
     * Function to get the url for create record of the module
     *
     * @return string
     */
	public static function getCreateRecordUrl() {
		return 'index.php?module=EERainbowList&parent=Settings&view=Edit';
	}

    /**
     * Get supported modules
     *
     * @return array
     */
	public static function getSupportedModules() {
		$moduleModels = Vtiger_Module_Model::getAll(array(0,2));
		$supportedModuleModels = array();
		foreach($moduleModels as $tabId => $moduleModel) {
			if($moduleModel->isWorkflowSupported() && $moduleModel->getName() != 'Webmails') {
				$supportedModuleModels[$tabId] = $moduleModel;
			}
		}
		return $supportedModuleModels;
	}

    /**
     * @return array
     */
	public static function getExpressions() {
		$db = PearDatabase::getInstance();

		$mem = new VTExpressionsManager($db);
		return $mem->expressionFunctions();
	}

    /**
     * @return array
     */
	public static function getMetaVariables() {
		return self::$metaVariables;
	}

    /**
     * @return array
     */
	public function getListFields() {
		if(!$this->listFieldModels) {
			$fields = $this->listFields;
			$fieldObjects = array();
			foreach($fields as $fieldName => $fieldLabel) {
				if($fieldName == 'module_name' || $fieldName == 'execution_condition') {
					$fieldObjects[$fieldName] = new Vtiger_Base_Model(array('name' => $fieldName, 'label' => $fieldLabel, 'sort'=>false));
				} else {
					$fieldObjects[$fieldName] = new Vtiger_Base_Model(array('name' => $fieldName, 'label' => $fieldLabel));
				}
			}
			$this->listFieldModels = $fieldObjects;
		}
		return $this->listFieldModels;
	}

}
