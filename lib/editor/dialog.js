// $Id$
// Though "Dialog" looks like an object, it isn't really an object.  Instead
// it's just namespace for protecting global symbols.

function Dialog(url, action, init) {
    if (typeof init == "undefined") {
        init = window;  // pass this window object by default
    }
    Dialog._geckoOpenModal(url, action, init);
};

Dialog._parentEvent = function(ev) {
    if (Dialog._modal && !Dialog._modal.closed) {
        Dialog._modal.focus();
        HTMLArea._stopEvent(ev);
    }
};

// should be a function, the return handler of the currently opened dialog.
Dialog._return = null;

// constant, the currently opened dialog
Dialog._modal = null;

// the dialog will read it's args from this variable
Dialog._arguments = null;

Dialog._geckoOpenModal = function(url, action, init) {

    var file = url.substring(url.lastIndexOf('/') + 1, url.lastIndexOf('.'));
    var x,y;
    switch(file) {
        case "insert_image": x = 730; y = 560; break;
        case "dlg_ins_smile": x = 330; y = 320; break;
        case "dlg_ins_char": x = 480; y = 290; break;
        case "select_color": x = 238; y = 195; break;
        case "insert_table": x = 420; y = 250; break;
        case "link_std":     x = 420; y = 230; break;
        case "insert_image_std": x = 450; y = 240; break;
        case "createanchor": x = 300; y = 140; break;
        case "searchandreplace": x = 400; y = 250; break;
        default: x = 50; y = 50;
    }

    var lx = (screen.width - x) / 2;
    var tx = (screen.height - y) / 2;
    var dlg = window.open(url, "ha_dialog", "toolbar=no,menubar=no,personalbar=no, width="+ x +",height="+ y +",scrollbars=no,resizable=no, left="+ lx +", top="+ tx +"");
    Dialog._modal = dlg;
    Dialog._arguments = init;

    // capture some window's events
    function capwin(w) {
        HTMLArea._addEvent(w, "click", Dialog._parentEvent);
        HTMLArea._addEvent(w, "mousedown", Dialog._parentEvent);
        HTMLArea._addEvent(w, "focus", Dialog._parentEvent);
    };
    // release the captured events
    function relwin(w) {
        HTMLArea._removeEvent(w, "click", Dialog._parentEvent);
        HTMLArea._removeEvent(w, "mousedown", Dialog._parentEvent);
        HTMLArea._removeEvent(w, "focus", Dialog._parentEvent);
    };
    capwin(window);
    // capture other frames
    if(document.all) {
        for (var i = 0; i < window.frames.length; capwin(window.frames[i++]));
    }
    // make up a function to be called when the Dialog ends.
    Dialog._return = function (val) {
        relwin(window);
        // capture other frames
        if(document.all) {
            for (var i = 0; i < window.frames.length; relwin(window.frames[i++]));
        }
        if (val && action) {
            action(val);
        }
        Dialog._modal = null;
    };
};
