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
    <div class="rainbowContents" style="padding-left: 3%;padding-right: 3%">
        <form name="EditRainbow" action="index.php" method="post" id="rainbow_step1" class="form-horizontal">
            <input type="hidden" name="module" value="EERainbowList">
            <input type="hidden" name="view" value="Edit">
            <input type="hidden" name="mode" value="Step2" />
            <input type="hidden" name="parent" value="Settings" />
            <input type="hidden" class="step" value="1" />
            <input type="hidden" name="record" value="{$RECORDID}" />

            <div class="padding1per" style="border:1px solid #ccc;">
                <label>
                    <strong>{vtranslate('LBL_ENTER_BASIC_COLOR_DETAILS', $QUALIFIED_MODULE)}</strong>
                </label>
                <br>
                <div class="control-group">
                    <div class="control-label">
                        {vtranslate('LBL_SELECT_MODULE', $QUALIFIED_MODULE)}
                    </div>
                    <div class="controls">
                        {if $MODE eq 'edit'}
                            <input type='text' disabled='disabled' value="{vtranslate($MODULE_MODEL->getName(), $MODULE_MODEL->getName())}" >
                            <input type='hidden' name='module_name' value="{$MODULE_MODEL->get('name')}" >
                        {else}
                            <select class="chzn-select" id="moduleName" name="module_name" required="true" data-placeholder="Select Module...">
                                {foreach from=$ALL_MODULES key=TABID item=MODULE_MODEL}
                                    <option value="{$MODULE_MODEL->getName()}" {if $SELECTED_MODULE == $MODULE_MODEL->getName()} selected {/if}>
										{if $MODULE_MODEL->getName() eq 'Calendar'}
											{vtranslate('LBL_TASK', $MODULE_MODEL->getName())}
										{else}
											{vtranslate($MODULE_MODEL->getName(), $MODULE_MODEL->getName())}
										{/if}
									</option>
                                {/foreach}
                            </select>
                        {/if}
                    </div>
                </div>
                <div class="control-group">
                    <div class="control-label">
                        {vtranslate('LBL_DESCRIPTION', $QUALIFIED_MODULE)}<span class="redColor"> *</span>
                    </div>
                    <div class="controls">
                        <input type="text" name="summary" class="span5" data-validation-engine='validate[required]' value="{$RAINBOW_MODEL->get('summary')}" id="summary" />
                    </div>
                </div>
                <div class="control-group">
                    <div class="control-label">
                        {vtranslate('LBL_COLOR', $QUALIFIED_MODULE)}<span class="redColor"> *</span>
                    </div>
                    <div class="controls">
                        <input type="text" name="color" class="span5 jscolor { hash: true }" data-validation-engine='validate[required]' value="{$RAINBOW_MODEL->get('color')}" id="color" />
                    </div>
                </div>

        </div>
        <br>

        <div class="pull-right">
            <button class="btn btn-success" type="submit" disabled="disabled"><strong>{vtranslate('LBL_NEXT', $QUALIFIED_MODULE)}</strong></button>
            <a class="cancelLink" type="reset" onclick="javascript:window.history.back();">{vtranslate('LBL_CANCEL', $QUALIFIED_MODULE)}</a>
        </div>
        <div class="clearfix"></div>
    </form>
</div>
{/strip}