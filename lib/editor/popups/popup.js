function __dlg_onclose() {
    if (!document.all) {
        opener.Dialog._return(null);
    }
};

function __dlg_init() {
    if (!document.all) {
        // init dialogArguments, as IE gets it
        window.dialogArguments = opener.Dialog._arguments;
        window.sizeToContent();
        window.sizeToContent(); // for reasons beyond understanding,
                    // only if we call it twice we get the
                    // correct size.
        window.addEventListener("unload", __dlg_onclose, true);
        // center on parent
        var px1 = opener.screenX;
        var px2 = opener.screenX + opener.outerWidth;
        var py1 = opener.screenY;
        var py2 = opener.screenY + opener.outerHeight;
        var x = (px2 - px1 - window.outerWidth) / 2;
        var y = (py2 - py1 - window.outerHeight) / 2;
        var body = document.body;
        window.innerHeight = body.offsetHeight + 10;
        window.innerWidth = body.offsetWidth + 10;
        //window.moveTo(((screen.width - window.innerWidth) / 2), ((screen.height - window.innerHeight)/2));
        //window.resizeTo(window.innerWidth, window.innerHeight);
        window.focus();
    } else {
        var body = document.body;
        window.dialogWidth = body.offsetWidth + "px";
        window.dialogHeight = body.offsetHeight + 50 + "px";
        window.resizeTo(window.dialogWidth, window.dialogHeight);
    }
};

// closes the dialog and passes the return info upper.
function __dlg_close(val) {
    if (document.all) { // IE
        window.returnValue = val;
    } else {
        opener.Dialog._return(val);
    }
    window.close();
};
