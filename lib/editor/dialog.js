// Though "Dialog" looks like an object, it isn't really an object.  Instead
// it's just namespace for protecting global symbols.

function Dialog(url, action, init) {
	if (typeof init == "undefined") {
		init = window;	// pass this window object by default
	}
	if (document.all) {	// here we hope that Mozilla will never support document.all
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
		setTimeout(function(){Dialog._modal.focus();}, 1);
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
	var dlg = window.open(url, "ha_dialog",
			      "toolbar=no,menubar=no,personalbar=no,width=10,height=10," +
			      "scrollbars=no,resizable=no");
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
	for (var i = 0; i < window.frames.length; capwin(window.frames[i++]));
	// make up a function to be called when the Dialog ends.
	Dialog._return = function (val) {
		if (val && action) {
			action(val);
		}
		relwin(window);
		// capture other frames
		for (var i = 0; i < window.frames.length; relwin(window.frames[i++]));
		Dialog._modal = null;
	};
};
