// $Id$
// Though "Dialog" looks like an object, it isn't really an object.  Instead
// it's just namespace for protecting global symbols.

function Dialog(url, action, init) {
    if (typeof init == "undefined") {
        init = window;  // pass this window object by default
    }
    if (document.all) { // here we hope that Mozilla will never support document.all
        var value =
            showModalDialog(url, init,
            //window.open(url, '_blank',
            "resizable: no; help: no; status: no; scroll: no");
        if (action) {
            action(value);
        }
    } else {
        return Dialog._geckoOpenModal(url, action, init);
    }
};

Dialog._parentEvent = function(ev) {
    if (Dialog._modal && !Dialog._modal.closed) {
        // we get here in Mozilla only, anyway, so we can safely use
        // the DOM version.
        ev.preventDefault();
        ev.stopPropagation();
    }
};

// should be a function, the return handler of the currently opened dialog.
Dialog._return = null;

// constant, the currently opened dialog
Dialog._modal = null;

// the dialog will read it's args from this variable
Dialog._arguments = null;

Dialog._geckoOpenModal = function(url, action, init) {

    if(url.lastIndexOf("insert_image.php") != -1) {
        var x = 730;
        var y = 540;
    } else if (url.lastIndexOf("link_std.php") != -1) {
        var x = 400;
        var y = 180;
    } else if (url.lastIndexOf("dlg_ins_smile.php") != -1) {
        var x = 330;
        var y = 360;
    } else if (url.lastIndexOf("dlg_ins_char.php") != -1) {
        var x = 450;
        var y = 270;
    } else if (url.lastIndexOf("select_color.php") != -1) {
        var x = 238;
        var y = 188;
    } else if (url.lastIndexOf("insert_table.php") != -1) {
        var x = 410;
        var y = 240;
    } else if (url.lastIndexOf("link_std.php") != -1) {
        var x = 420;
        var y = 210;
    } else if (url.lastIndexOf("insert_image_std.php") != -1) {
        var x = 450;
        var y = 230;
    } else {
        var x = 10;
        var y = 10;
    }

    var lx = (screen.width - x) / 2;
    var tx = (screen.height - y) / 2;
    var dlg = window.open(url, "ha_dialog", "toolbar=no,menubar=no,personalbar=no, width="+ x +",height="+ y +",scrollbars=no,resizable=no, left="+ lx +", top="+ tx +"");
    Dialog._modal = dlg;
    Dialog._arguments = init;

    // capture some window's events
    function capwin(w) {
        w.addEventListener("click", Dialog._parentEvent, true);
        w.addEventListener("mousedown", Dialog._parentEvent, true);
        w.addEventListener("focus", Dialog._parentEvent, true);
    };
    // release the captured events
    function relwin(w) {
        w.removeEventListener("focus", Dialog._parentEvent, true);
        w.removeEventListener("mousedown", Dialog._parentEvent, true);
        w.removeEventListener("click", Dialog._parentEvent, true);
    };
    capwin(window);
    // capture other frames
    //for (var i = 0; i < window.frames.length; capwin(window.frames[i++]));
    // make up a function to be called when the Dialog ends.
    Dialog._return = function (val) {
        if (val && action) {
            action(val);
        }
        relwin(window);
        // capture other frames
        //for (var i = 0; i < window.frames.length; relwin(window.frames[i++]));
        Dialog._modal = null;
    };
};
