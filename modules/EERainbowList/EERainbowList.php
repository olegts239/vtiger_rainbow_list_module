<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is: EntExt
 * The Initial Developer of the Original Code is EntExt.
 * All Rights Reserved.
 * If you have any questions or comments, please email: devel@entext.com
 ************************************************************************************/
include_once 'include/Zend/Json.php';
require_once('data/CRMEntity.php');
require_once('data/Tracker.php');

class EERainbowList {

    /**
     * Invoked when special actions are performed on the module.
     *
     * @param String $moduleName
     * @param String $event_type
     */
    function vtlib_handler($moduleName, $event_type) {
        global $adb;
        $moduleInstance = Vtiger_Module::getInstance($moduleName);
        if($event_type == 'module.postinstall') {

            $this->createSettingField();

            // Register header js script
            if($moduleInstance) {
                $moduleInstance->addLink('HEADERSCRIPT', 'EERainbowListHeaderScript', 'layouts/vlayout/modules/Settings/EERainbowList/resources/EERainbowList.js');
            }

            // Direct update in module table
            $adb->pquery('INSERT INTO vtiger_ee_rainbowlist_seq value (1)', array());

        } else if($event_type == 'module.disabled') {

            // Unregister header js script
            if($moduleInstance) {
                $moduleInstance->deleteLink('HEADERSCRIPT', 'EERainbowListHeaderScript', 'layouts/vlayout/modules/Settings/EERainbowList/resources/EERainbowList.js');
            }

            // There is no vtlib method so direct DB update
            $adb->pquery('UPDATE vtiger_settings_field SET active = 1 WHERE name = ?',array('Rainbow List'));

        } else if($event_type == 'module.enabled') {

            // Register header js script
            if($moduleInstance) {
                $moduleInstance->addLink('HEADERSCRIPT', 'EERainbowListHeaderScript', 'layouts/vlayout/modules/Settings/EERainbowList/resources/EERainbowList.js');
            }

            // There is no vtlib method so direct DB update
            $adb->pquery('UPDATE vtiger_settings_field SET active = 0 WHERE name = ?',array('Rainbow List'));

        } else if($event_type == 'module.preuninstall') {
            // TODO Handle actions when this module is about to be deleted.
        } else if($event_type == 'module.preupdate') {
            // TODO Handle actions before this module is updated.
        } else if($event_type == 'module.postupdate') {
            // TODO Handle actions after this module is updated.
        }

    }

    /**
     * Create Setting
     * ( There is no vtlib method so direct DB update )
     */
    function createSettingField() {
        global $adb;
        $sql = "set @lastfieldid = (select `id` from `vtiger_settings_field_seq`);";
        $adb->pquery($sql,array());
        $sql = "set @blockid = (select `blockid` from `vtiger_settings_blocks` where `label` = 'LBL_OTHER_SETTINGS');";
        $adb->pquery($sql,array());
        $sql = "set @maxseq = (select max(`sequence`) from `vtiger_settings_field` where `blockid` = @blockid);";
        $adb->pquery($sql,array());
        $sql = "INSERT INTO `vtiger_settings_field` (`fieldid`, `blockid`, `name`, `iconpath`, `description`, `linkto`, `sequence`, `active`) "
            . " VALUES (@lastfieldid+1, @blockid, 'Rainbow List', '', 'LBL_EERAINBOWLIST_SETTINGS_DESCRIPTION', 'index.php?module=EERainbowList&parent=Settings&view=List', @maxseq+1, 0);";
        $adb->pquery($sql,array());
        $sql = "UPDATE `vtiger_settings_field_seq` SET `id` = @lastfieldid+1;";
        $adb->pquery($sql,array());
    }
}

?>
