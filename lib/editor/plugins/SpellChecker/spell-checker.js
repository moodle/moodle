// Spell Checker Plugin for HTMLArea-3.0
// Implementation by Mihai Bazon.  Sponsored by www.americanbible.org
//
// htmlArea v3.0 - Copyright (c) 2002 interactivetools.com, inc.
// This notice MUST stay intact for use (see license.txt).
//
// A free WYSIWYG editor replacement for <textarea> fields.
// For full source code and docs, visit http://www.interactivetools.com/
//
// Version 3.0 developed by Mihai Bazon for InteractiveTools.
//	     http://students.infoiasi.ro/~mishoo
//
// $Id$

function SpellChecker(editor) {
	this.editor = editor;

	var cfg = editor.config;
	var tt = SpellChecker.I18N;
	var bl = SpellChecker.btnList;
	var self = this;

	// register the toolbar buttons provided by this plugin
	var toolbar = [];
	for (var i in bl) {
		var btn = bl[i];
		if (!btn) {
			toolbar.push("separator");
		} else {
			var id = "SC-" + btn[0];
			cfg.registerButton(id, tt[id], "plugins/SpellChecker/img/" + btn[0] + ".gif", false,
					   function(editor, id) {
						   // dispatch button press event
						   self.buttonPress(editor, id);
					   }, btn[1]);
			toolbar.push(id);
		}
	}

	for (var i in toolbar) {
		cfg.toolbar[0].push(toolbar[i]);
	}
};

SpellChecker.btnList = [
	null, // separator
	["spell-check"]
	];

SpellChecker.prototype.buttonPress = function(editor, id) {
	switch (id) {
	    case "SC-spell-check":
		SpellChecker.editor = editor;
		SpellChecker.init = true;
		var uiurl = editor.config.editorURL + "plugins/SpellChecker/spell-check-ui.html";
		var win;
		if (HTMLArea.is_ie) {
			win = window.open(uiurl, "SC_spell_checker",
					  "toolbar=no,location=no,directories=no,status=no,menubar=no," +
					  "scrollbars=no,resizable=yes,width=600,height=400");
		} else {
			win = window.open(uiurl, "SC_spell_checker",
					  "toolbar=no,menubar=no,personalbar=no,width=600,height=400," +
					  "scrollbars=no,resizable=yes");
		}
		win.focus();
		break;
	}
};

// this needs to be global, it's accessed from spell-check-ui.html
SpellChecker.editor = null;
