<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is: EntExt
 * The Initial Developer of the Original Code is EntExt.
 * All Rights Reserved.
 * If you have any questions or comments, please email: devel@entext.com
 ************************************************************************************/
require_once 'modules/com_vtiger_workflow/include.inc';
require_once 'modules/com_vtiger_workflow/expression_engine/VTExpressionsManager.inc';

class Settings_EERainbowList_Record_Model extends Settings_Vtiger_Record_Model {

	public function getId() {
		return $this->get('rainbowlist_id');
	}

	public function getName() {
		return $this->get('summary');
	}

	public function get($key) {
		return parent::get($key);
	}

	public function getEditViewUrl() {
		return 'index.php?module=EERainbowList&parent=Settings&view=Edit&record='.$this->getId();
	}

	public function getModule() {
		return $this->module;
	}

	public function setModule($moduleName) {
		$this->module = Vtiger_Module_Model::getInstance($moduleName);
		return $this;
	}


	public function save() {
		$db = PearDatabase::getInstance();

        $rainbowListId = $this->getId();

        $moduleName = $this->get('module_name');
        $summary = $this->get('summary');
        $conditions = Zend_Json::encode($this->get('conditions'));
        $color = $this->get('color');


        if($rainbowListId){
            $db->pquery("update vtiger_ee_rainbowlist set module_name = ?, summary = ?,
              conditions = ?, color = ?  where rainbowlist_id = ?", array($moduleName, $summary, $conditions, $color, $rainbowListId));
        } else {
            $rainbowListId = $db->getUniqueID("vtiger_ee_rainbowlist");
            $db->pquery("insert into vtiger_ee_rainbowlist(rainbowlist_id, module_name, summary, conditions, color)
                    values (?,?,?,?,?)", array($rainbowListId, $moduleName, $summary, $conditions, $color));
        }

        $this->set('rainbowlist_id', $rainbowListId);
	}

	public function delete() {
        $db = PearDatabase::getInstance();
        $query = 'DELETE FROM vtiger_ee_rainbowlist WHERE rainbowlist_id = ?' ;
        $params = array($this->getId());
        $db->pquery($query, $params);
	}


    /**
     * Function to get the list view actions for the record
     * @return array <Array> - Associate array of Vtiger_Link_Model instances
     */
	public function getRecordLinks() {

		$links = array();

		$recordLinks = array(
			array(
				'linktype' => 'LISTVIEWRECORD',
				'linklabel' => 'LBL_EDIT_RECORD',
				'linkurl' => $this->getEditViewUrl(),
				'linkicon' => 'icon-pencil'
			),
			array(
				'linktype' => 'LISTVIEWRECORD',
				'linklabel' => 'LBL_DELETE_RECORD',
				'linkurl' => 'javascript:Vtiger_List_Js.deleteRecord('.$this->getId().');',
				'linkicon' => 'icon-trash'
			)
		);
		foreach($recordLinks as $recordLink) {
			$links[] = Vtiger_Link_Model::getInstanceFromValues($recordLink);
		}

		return $links;
	}

	public static function getInstance($rainbowId) {
        $db = PearDatabase::getInstance();
        $instance = new self;
        $sql = 'SELECT * FROM vtiger_ee_rainbowlist WHERE rainbowlist_id = ?';
        $params = array($rainbowId);
        $result = $db->pquery($sql, $params);

        if($db->num_rows($result) > 0) {
            $row = $db->raw_query_result_rowdata($result,0);
            $instance->setData($row);
            $instance->set('conditions', Zend_Json::decode($row['conditions']));
            $instance->setModule($instance->get('module_name'));
        }

        return $instance;
	}

	public static function getCleanInstance($moduleName) {
        $instance = new self;
        $instance->setModule($moduleName);
        return $instance;
	}

    public static function getInstancesByModule($moduleName) {
        $db = PearDatabase::getInstance();
        $modelList = array();
        $sql = 'SELECT * FROM vtiger_ee_rainbowlist WHERE module_name = ?';
        $params = array($moduleName);
        $result = $db->pquery($sql, $params);
        $num_rows = $db->num_rows($result);
        for($i = 0; $i < $num_rows; $i++) {
            $rainbowId = $db->query_result($result, $i, 'rainbowlist_id');
            $modelList[] = self::getInstance($rainbowId);
        }
        return $modelList;
    }

    /**
     * Functions transforms rainbow filter to advanced filter
     * @return array <Array>
     */
	function transformToAdvancedFilterCondition() {
		$conditions = $this->get('conditions');
		$transformedConditions = array();

		if(!empty($conditions)) {
			foreach($conditions as $index => $info) {
				if(!($info['groupid'])) {
					$firstGroup[] = array('columnname' => $info['fieldname'], 'comparator' => $info['operation'], 'value' => $info['value'],
						'column_condition' => $info['joincondition'], 'valuetype' => $info['valuetype'], 'groupid' => $info['groupid']);
				} else {
					$secondGroup[] = array('columnname' => $info['fieldname'], 'comparator' => $info['operation'], 'value' => $info['value'],
						'column_condition' => $info['joincondition'], 'valuetype' => $info['valuetype'], 'groupid' => $info['groupid']);
				}
			}
		}
		$transformedConditions[1] = array('columns'=>$firstGroup);
		$transformedConditions[2] = array('columns'=>$secondGroup);
		return $transformedConditions;
	}

    /**
     * Function returns value type of the field filter
     * @param $fieldName
     * @return bool <String>
     */
	function getFieldFilterValueType($fieldName) {
		$conditions = $this->get('conditions');
		if(!empty($conditions) && is_array($conditions)) {
			foreach($conditions as $filter) {
				if($fieldName == $filter['fieldname']) {
					return $filter['valuetype'];
				}
			}
		}
		return false;
	}

	/**
	 * Function transforms Advance filter to rainbow conditions
	 */
	function transformAdvanceFilterToFilter() {
		$conditions = $this->get('conditions');
		$wfCondition = array();

		if(!empty($conditions)) {
			foreach($conditions as $index => $condition) {
				$columns = $condition['columns'];
				if($index == '1' && empty($columns)) {
					$wfCondition[] = array('fieldname'=>'', 'operation'=>'', 'value'=>'', 'valuetype'=>'',
						'joincondition'=>'', 'groupid'=>'0');
				}
				if(!empty($columns) && is_array($columns)) {
					foreach($columns as $column) {
						$wfCondition[] = array('fieldname'=>$column['columnname'], 'operation'=>$column['comparator'],
							'value'=>$column['value'], 'valuetype'=>$column['valuetype'], 'joincondition'=>$column['column_condition'],
							'groupjoin'=>$condition['condition'], 'groupid'=>$column['groupid']);
					}
				}
			}
		}
		$this->set('conditions', $wfCondition);
	}
}