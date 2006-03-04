/**
* $Id$
*
* Though "Dialog" looks like an object, it isn't really an object.  Instead
* it's just namespace for protecting global symbols.
**/

function Dialog(url, width, height, action, init) {
    if (typeof init == "undefined") {
        init = window;  // pass this window object by default
    }
    Dialog._geckoOpenModal(url, width, height, action, init);
};

Dialog._addEvent = function (el, evname, func) {
    if ( document.all ) {
        el.attachEvent("on" + evname, func);
    } else {
        el.addEventListener(evname, func, true);
    }
};

Dialog._removeEvent = function (el, evname, func) {
    if ( document.all ) {
        el.detachEvent("on" + evname, func);
    } else {
        el.removeEventListener(evname, func, true);
    }
};

Dialog._stopEvent = function (ev) {
    if ( document.all ) {
        ev.cancelBubble = true;
        ev.returnValue = false;
    } else {
        ev.preventDefault();
        ev.stopPropagation();
    }
};

Dialog._parentEvent = function(ev) {
    if (Dialog._modal && !Dialog._modal.closed) {
        Dialog._modal.focus();
        Dialog._stopEvent(ev);
    }
};

// should be a function, the return handler of the currently opened dialog.
Dialog._return = null;

// constant, the currently opened dialog
Dialog._modal = null;

// the dialog will read it's args from this variable
Dialog._arguments = null;

Dialog._geckoOpenModal = function(url, width, height, action, init) {

    var file = url.substring(url.lastIndexOf('/') + 1, url.lastIndexOf('.'));
    var x,y;
    x = width;
    y = height;

    var lx = (screen.width - x) / 2;
    var tx = (screen.height - y) / 2;
    var dlg = window.open(url, "ha_dialog", "toolbar=no,menubar=no,personalbar=no, width="+ x +",height="+ y +",scrollbars=no,resizable=no, left="+ lx +", top="+ tx +"");
    Dialog._modal = dlg;
    Dialog._arguments = init;

    // capture some window's events
    function capwin(w) {
        Dialog._addEvent(w, "click", Dialog._parentEvent);
        Dialog._addEvent(w, "mousedown", Dialog._parentEvent);
        Dialog._addEvent(w, "focus", Dialog._parentEvent);
    };
    // release the captured events
    function relwin(w) {
        Dialog._removeEvent(w, "click", Dialog._parentEvent);
        Dialog._removeEvent(w, "mousedown", Dialog._parentEvent);
        Dialog._removeEvent(w, "focus", Dialog._parentEvent);
    };
    capwin(window);
    // capture other frames, note the exception trapping, this is because
    // we are not permitted to add events to frames outside of the current
    // window's domain.
    for (var i = 0; i < window.frames.length; i++) {try { capwin(window.frames[i]); } catch(e) { } };
    // make up a function to be called when the Dialog ends.
    Dialog._return = function (val) {
        if (val && action) {
            action(val);
        }
        relwin(window);
        // capture other frames
        for (var i = 0; i < window.frames.length; i++) { try { relwin(window.frames[i]); } catch(e) { } };
        Dialog._modal = null;
    };
    Dialog._modal.focus();
};
