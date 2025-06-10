/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

$.fn.dataTableExt.oApi.fnStandingRedraw = function(oSettings) {
    if(oSettings.oFeatures.bServerSide === false){
        var before = oSettings._iDisplayStart;

        oSettings.oApi._fnReDraw(oSettings);

        // Var iDisplayStart has been reset to zero - so lets change it back.
        oSettings._iDisplayStart = before;
        oSettings.oApi._fnCalculateEnd(oSettings);
    }

    // Draw the 'current' page.
    oSettings.oApi._fnDraw(oSettings);
};

$.fn.dataTableExt.oApi.fnReloadAjax = function (oSettings, sNewSource, fnCallback, bStandingRedraw) {
    if (typeof sNewSource != 'undefined' && sNewSource != null) {
        oSettings.sAjaxSource = sNewSource;
    }
    this.oApi._fnProcessingDisplay( oSettings, true );
    var that = this;
    var iStart = oSettings._iDisplayStart;

    oSettings.fnServerData(oSettings.sAjaxSource, null, function(json) {
        /* Clear the old information from the table */
        that.oApi._fnClearTable( oSettings );

        /* Got the data - add it to the table */
        for (var i = 0; i < json.aaData.length; i++) {
            that.oApi._fnAddData( oSettings, json.aaData[i] );
        }

        oSettings.aiDisplay = oSettings.aiDisplayMaster.slice();
        that.fnDraw(that);

        if (typeof bStandingRedraw != 'undefined' && bStandingRedraw === true) {
            oSettings._iDisplayStart = iStart;
            that.fnDraw(false);
        }

        that.oApi._fnProcessingDisplay(oSettings, false);

        /* Callback user function - for event handlers etc */
        if (typeof fnCallback == 'function' && fnCallback != null) {
            fnCallback(oSettings);
        }
    });
};

$.fn.dataTableExt.oApi.fnSetFilteringDelay = function ( oSettings, iDelay ) {
    var _that = this;
    this.each( function ( i ) {
        $.fn.dataTableExt.iApiIndex = i;
        iDelay  = (iDelay && (/^[0-9]+$/.test(iDelay))) ? iDelay : 250;

        var $this = this, oTimerId;
        var anControl = $( 'input', _that.fnSettings().aanFeatures.f );

        anControl.unbind( 'keyup' ).bind( 'keyup', function() {
            var $$this = $this;
            window.clearTimeout(oTimerId);

            oTimerId = window.setTimeout(function() {
                $.fn.dataTableExt.iApiIndex = i;
                _that.fnFilter( anControl.val() );
            }, iDelay);
        });

        return this;
    } );
    return this;
};