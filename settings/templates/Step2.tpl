{*<!--
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is: EntExt
 * The Initial Developer of the Original Code is EntExt.
 * All Rights Reserved.
 * If you have any questions or comments, please email: devel@entext.com
 ************************************************************************************/
-->*}
{strip}
	<form name="EditRainbow" action="index.php" method="post" id="rainbow_step2" class="form-horizontal" >
		<input type="hidden" name="module" value="EERainbowList" />
		<input type="hidden" name="action" value="Save" />
		<input type="hidden" name="parent" value="Settings" />
		<input type="hidden" class="step" value="2" />
		<input type="hidden" name="summary" value="{$RAINBOW_MODEL->get('summary')}" />
		<input type="hidden" name="record" value="{$RAINBOW_MODEL->getId()}" />
		<input type="hidden" name="module_name" value="{$RAINBOW_MODEL->get('module_name')}" />
        <input type="hidden" name="color" value="{$RAINBOW_MODEL->get('color')}" />
		<input type="hidden" name="conditions" id="advanced_filter" value='' />
        <input type="hidden" id="olderConditions" value='{ZEND_JSON::encode($RAINBOW_MODEL->get('conditions'))}' />
		<div class="row-fluid" style="border:1px solid #ccc;">
				<div id="advanceFilterContainer" {if $IS_FILTER_SAVED_NEW == false} class="zeroOpacity conditionsContainer padding1per" {else} class="conditionsContainer padding1per" {/if}>
					<h5 class="padding-bottom1per"><strong>{vtranslate('LBL_CHOOSE_FILTER_CONDITIONS',$MODULE)}</strong></h5>
					<span class="span10" >
						{include file='AdvanceFilter.tpl'|@vtemplate_path RECORD_STRUCTURE=$RECORD_STRUCTURE}
					</span>
					{include file="FieldExpressions.tpl"|@vtemplate_path:$QUALIFIED_MODULE EXECUTION_CONDITION=$RAINBOW_MODEL->get('execution_condition')}
				</div>
			</div><br>
			<div class="pull-right">
				<button class="btn btn-danger backStep" type="button"><strong>{vtranslate('LBL_BACK', $QUALIFIED_MODULE)}</strong></button>&nbsp;&nbsp;
				<button class="btn btn-success" type="submit"><strong>{vtranslate('LBL_SAVE', $QUALIFIED_MODULE)}</strong></button>
				<a class="cancelLink" type="reset" onclick="javascript:window.history.back();">{vtranslate('LBL_CANCEL', $QUALIFIED_MODULE)}</a>
			</div>
			<br><br>
	</form>
{/strip}