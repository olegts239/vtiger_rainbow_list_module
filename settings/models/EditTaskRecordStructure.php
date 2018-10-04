<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is: EntExt
 * The Initial Developer of the Original Code is EntExt.
 * All Rights Reserved.
 * If you have any questions or comments, please email: devel@entext.com
 ************************************************************************************/

class Settings_EERainbowList_EditTaskRecordStructure_Model extends Settings_EERainbowList_RecordStructure_Model {

    /**
     * Function to get the values in structured format
     *
     * @return array <array> - values in structure array('block'=>array(fieldinfo));
     */
    public function getStructure() {
        if(!empty($this->structuredValues)) {
            return $this->structuredValues;
        }

        $recordModel = $this->getRainbowListModel();
        $recordId = $recordModel->getId();

        $values = array();

        $baseModuleModel = $moduleModel = $this->getModule();
        $blockModelList = $moduleModel->getBlocks();

        $additionalFieldModel = new Vtiger_Field_Model();
        $additionalFieldModel->set('workflow_columnname', 'spDetailViewLink')
            ->set('workflow_columnlabel', vtranslate('LBL_DETAIL_VIEW_LINK',$moduleModel->getName()) );
        $values['SP_LINKS_BLOCK'] = array();
        $values['SP_LINKS_BLOCK'][] = $additionalFieldModel;

        foreach($blockModelList as $blockLabel=>$blockModel) {
            $fieldModelList = $blockModel->getFields();
            if (!empty ($fieldModelList)) {
                $values[$blockLabel] = array();
                foreach($fieldModelList as $fieldName=>$fieldModel) {
                    if($fieldModel->isViewable()) {
                        if($moduleModel->getName()=="Documents" && $fieldName=="filename"){
                            continue;
                        }
                        if (in_array($moduleModel->getName(), array('Calendar', 'Events')) && $fieldModel->getDisplayType() == 3) {
                            /* Restricting the following fields(Event module fields) for "Calendar" module
                             * time_start, time_end, eventstatus, activitytype,	visibility, duration_hours,
                             * duration_minutes, reminder_time, recurringtype, notime
                             */
                            continue;
                        }
                        if(!empty($recordId)) {
                            //Set the fieldModel with the valuetype for the client side.
                            $fieldValueType = $recordModel->getFieldFilterValueType($fieldName);
                            $fieldInfo = $fieldModel->getFieldInfo();
                            $fieldInfo['workflow_valuetype'] = $fieldValueType;
                            $fieldModel->setFieldInfo($fieldInfo);
                        }

                        switch($fieldModel->getFieldDataType()) {
                            case 'date'		:	if (($moduleModel->getName() === 'Events' && in_array($fieldName, array('date_start', 'due_date'))) ||
                                ($moduleModel->getName() === 'Calendar' && $fieldName === 'date_start')) {
                                $fieldName = $fieldName .' ($(general : (__VtigerMeta__) usertimezone))';
                            } else {
                                $fieldName = $fieldName .' ($_DATE_FORMAT_)';
                            }
                                break;
                            case 'datetime'	:	$fieldName = $fieldName .' ($(general : (__VtigerMeta__) usertimezone))';	break;
                            default			:	$fieldName;
                        }

                        // This will be used during editing task like email, sms etc
                        $fieldModel->set('workflow_columnname', $fieldName)->set('workflow_columnlabel', vtranslate($fieldModel->get('label'), $moduleModel->getName()));
                        // This is used to identify the field belongs to source module of workflow
                        $fieldModel->set('workflow_sourcemodule_field', true);
                        $values[$blockLabel][$fieldName] = clone $fieldModel;
                    }
                }
            }
        }

        if ($moduleModel->isCommentEnabled()) {
            $commentFieldModelsList = Settings_EERainbowList_Field_Model::getCommentFieldsListForTasks($moduleModel);

            $labelName = vtranslate($moduleModel->getSingularLabelKey(), $moduleModel->getName()). ' ' .vtranslate('LBL_COMMENTS', $moduleModel->getName());
            foreach ($commentFieldModelsList as $commentFieldName => $commentFieldModel) {
                switch($commentFieldModel->getFieldDataType()) {
                    case 'date'		:	$commentFieldName = $commentFieldName .' ($_DATE_FORMAT_)';		break;
                    case 'datetime'	:	$commentFieldName = $commentFieldName .' ($(general : (__VtigerMeta__) usertimezone)_)';	break;
                    default			:	$commentFieldName;
                }
                $commentFieldModel->set('workflow_columnname', $commentFieldName)
                    ->set('workflow_columnlabel', vtranslate($commentFieldModel->get('label'), $moduleModel->getName()))
                    ->set('workflow_sourcemodule_field', true);

                $values[$labelName][$commentFieldName] = $commentFieldModel;
            }
        }

        //All the reference fields should also be sent
        $fields = $moduleModel->getFieldsByType(array('reference', 'owner', 'multireference'));
        foreach($fields as $parentFieldName => $field) {
            $type = $field->getFieldDataType();
            $referenceModules = $field->getReferenceList();
            if($type == 'owner') $referenceModules = array('Users');
            foreach($referenceModules as $refModule) {
                $moduleModel = Vtiger_Module_Model::getInstance($refModule);
                $blockModelList = $moduleModel->getBlocks();
                foreach($blockModelList as $blockLabel=>$blockModel) {
                    $fieldModelList = $blockModel->getFields();
                    if (!empty ($fieldModelList)) {
                        foreach($fieldModelList as $fieldName=>$fieldModel) {
                            if($fieldModel->isViewable()) {
                                $name = "($parentFieldName : ($refModule) $fieldName)";
                                $label = vtranslate($field->get('label'), $baseModuleModel->getName()).' : ('.vtranslate($refModule, $refModule).') '.vtranslate($fieldModel->get('label'), $refModule);

                                switch($fieldModel->getFieldDataType()) {
                                    case 'date'		:	if (($moduleModel->getName() === 'Events' && in_array($fieldName, array('date_start', 'due_date'))) ||
                                        ($moduleModel->getName() === 'Calendar' && $fieldName === 'date_start')) {
                                        $rainbowColumnName = $name .' ($(general : (__VtigerMeta__) usertimezone))';
                                    } else {
                                        $rainbowColumnName = $name .' ($_DATE_FORMAT_)';
                                    }
                                        break;
                                    case 'datetime'	:	$rainbowColumnName = $name .' ($(general : (__VtigerMeta__) usertimezone))';	break;
                                    default			:	$rainbowColumnName = $name;
                                }

                                $fieldModel->set('workflow_columnname', $rainbowColumnName)->set('workflow_columnlabel', $label);
                                if(!empty($recordId)) {
                                    $fieldValueType = $recordModel->getFieldFilterValueType($name);
                                    $fieldInfo = $fieldModel->getFieldInfo();
                                    $fieldInfo['workflow_valuetype'] = $fieldValueType;
                                    $fieldModel->setFieldInfo($fieldInfo);
                                }
                                $values[$field->get('label')][$name] = clone $fieldModel;
                            }
                        }
                    }
                }

                $commentFieldModelsList = array();
                if ($moduleModel->isCommentEnabled()) {
                    $labelName = vtranslate($moduleModel->getSingularLabelKey(), $moduleModel->getName()). ' ' .vtranslate('LBL_COMMENTS', $moduleModel->getName());

                    $commentFieldModelsList = Settings_EERainbowList_Field_Model::getCommentFieldsListForTasks($moduleModel);
                    foreach ($commentFieldModelsList as $commentFieldName => $commentFieldModel) {
                        $name = "($parentFieldName : ($refModule) $commentFieldName)";
                        $label = vtranslate($field->get('label'), $baseModuleModel->getName()).' : ('.
                            vtranslate($refModule, $refModule).') '.
                            vtranslate($commentFieldModel->get('label'), $refModule);

                        $commentFieldModel->set('workflow_columnname', $name)->set('workflow_columnlabel', $label);
                        $values[$labelName][$name] = $commentFieldModel;
                    }
                }
            }
        }
        $this->structuredValues = $values;
        return $values;
    }
}