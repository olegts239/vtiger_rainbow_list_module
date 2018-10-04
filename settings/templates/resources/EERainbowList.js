/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is: EntExt
 * The Initial Developer of the Original Code is EntExt.
 * All Rights Reserved.
 * If you have any questions or comments, please email: devel@entext.com
 ************************************************************************************/

jQuery.Class("EERainbowList_Js",{},{

    records : null,

    registerEventsForListView : function() {
        if(!this.validListData()) return;

        var aDeferred = jQuery.Deferred();
        var viewName = app.getViewName();
        var thisInstance = this;
        var records = [];
        var params = {};

        jQuery('.listViewEntries').each(function() {
            records.push(jQuery(this).attr('data-id'));
        });

        if(!records.length) return;

        params['module'] = 'EERainbowList';
        params['action'] = 'ListRowsColors';
        if(viewName == 'List') {
            params['current_module'] = app.getModuleName();
        }
        if(viewName == 'Detail') {
            params['current_module'] = jQuery('.relatedModuleName').val();
        }
        params['records'] = records;
        AppConnector.request(params).then(
            function(data) {
                if(data.success) {
                    if(data.result) {
                        thisInstance.records = data.result;
                        thisInstance.setBackgroundColor();
                    }
                }
                aDeferred.resolve(data);
            },

            function(error) {
                aDeferred.reject(error);
            }
        );
        return aDeferred.promise();
    },

    validListData : function() {
        var viewName = app.getViewName();

        // List view
        if(viewName == 'List') {
            if(jQuery('#listViewContents .listViewEntriesTable tr.listViewEntries').length > 0) {
                this.listViewContainer = jQuery('#listViewContents');
                return true;
            }
        }

        // Related list view
        if(viewName == 'Detail') {
            if(jQuery('.relatedContents .listViewEntriesTable tr.listViewEntries').length > 0) {
                this.listViewContainer = jQuery('.relatedContainer');
                return true;
            }
        }

        return false;
    },

    setBackgroundColor : function() {
        for(var index in this.records) {
            var color = this.records[index];
            jQuery('.listViewEntriesTable').find('tr[data-id='+index+']').css('background-color', color);
        }
    },

    registerEvents : function() {
        this.registerEventsForListView();
    }
});

jQuery(document).ready(function() {
    var eeRainbowListInstance  =  new EERainbowList_Js();
    eeRainbowListInstance.registerEvents();
    app.listenPostAjaxReady(function() {
        var eeRainbowListInstance  =  new EERainbowList_Js();
        eeRainbowListInstance.registerEvents();
    });
});