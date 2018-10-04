/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is: EntExt
 * The Initial Developer of the Original Code is EntExt.
 * All Rights Reserved.
 * If you have any questions or comments, please email: devel@entext.com
 ************************************************************************************/

Settings_Vtiger_List_Js("Settings_EERainbowList_List_Js", {

    triggerCreate : function(url) {
        var selectedModule = jQuery('#moduleFilter').val();
        if(selectedModule.length > 0) {
            url += '&source_module='+selectedModule
        }
        window.location.href = url;
    }

},{

    registerFilterChangeEvent : function() {
        var thisInstance = this;
        jQuery('#moduleFilter').on('change',function(e){
            jQuery('#pageNumber').val("1");
            jQuery('#pageToJump').val('1');
            jQuery('#orderBy').val('');
            jQuery("#sortOrder").val('');
            var params = {
                module : app.getModuleName(),
                parent : app.getParentModuleName(),
                sourceModule : jQuery(e.currentTarget).val()
            };
            //Make the select all count as empty
            jQuery('#recordsCount').val('');
            //Make total number of pages as empty
            jQuery('#totalPageCount').text("");
            thisInstance.getListViewRecords(params).then(
                function(data){
                    thisInstance.updatePagination();
                }
            );
        });
    },

    /**
     * Function to register the list view row click event
     */
    registerRowClickEvent: function(){
        var listViewContentDiv = this.getListViewContentContainer();
        listViewContentDiv.on('click','.listViewEntries',function(e){
            window.location.href = jQuery(e.currentTarget).find('.icon-pencil').closest('a').attr('href');
        });
    },

    getDefaultParams : function() {
        var pageNumber = jQuery('#pageNumber').val();
        var module = app.getModuleName();
        var parent = app.getParentModuleName();
        return {
            'module': module,
            'parent': parent,
            'page': pageNumber,
            'view': "List",
            sourceModule: jQuery('#moduleFilter').val()
        };
    },

    registerEvents : function() {
        this._super();
        this.registerFilterChangeEvent();
    }
});