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

// internationalization file was already loaded in parent ;-)
var SpellChecker = window.opener.SpellChecker;
var i18n = SpellChecker.I18N;

var is_ie = window.opener.HTMLArea.is_ie;
var editor = SpellChecker.editor;
var frame = null;
var currentElement = null;
var wrongWords = null;
var modified = false;
var allWords = {};

function makeCleanDoc(leaveFixed) {
	// document.getElementById("status").innerHTML = 'Please wait: rendering valid HTML';
	for (var i in wrongWords) {
		var el = wrongWords[i];
		if (!(leaveFixed && /HA-spellcheck-fixed/.test(el.className))) {
			el.parentNode.insertBefore(el.firstChild, el);
			el.parentNode.removeChild(el.nextSibling);
			el.parentNode.removeChild(el);
		} else {
			el.className = "HA-spellcheck-fixed";
			el.parentNode.removeChild(el.nextSibling);
		}
	}
	// we should use innerHTML here, but IE6's implementation fucks up the
	// HTML to such extent that our poor Perl parser doesn't understand it
	// anymore.
	return window.opener.HTMLArea.getHTML(frame.contentWindow.document.body, leaveFixed);
};

function recheckClicked() {
	document.getElementById("status").innerHTML = i18n["Please wait: changing dictionary to"] + ': "' + document.getElementById("f_dictionary").value + '".';
	var field = document.getElementById("f_content");
	field.value = makeCleanDoc(true);
	field.form.submit();
};

function saveClicked() {
	if (modified) {
		editor.setHTML(makeCleanDoc(false));
	}
	window.close();
	return false;
};

function cancelClicked() {
	var ok = true;
	if (modified) {
		ok = confirm(i18n["QUIT_CONFIRMATION"]);
	}
	if (ok) {
		window.close();
	}
	return false;
};

function replaceWord(el) {
	var replacement = document.getElementById("v_replacement").value;
	modified = (el.innerHTML != replacement);
	if (el) {
		el.className = el.className.replace(/\s*HA-spellcheck-(hover|fixed)\s*/g, " ");
	}
	el.className += " HA-spellcheck-fixed";
	el.__msh_fixed = true;
	if (!modified) {
		return false;
	}
	el.innerHTML = replacement;
};

function replaceClicked() {
	replaceWord(currentElement);
	var start = currentElement.__msh_id;
	var index = start;
	do {
		++index;
		if (index == wrongWords.length) {
			index = 0;
		}
	} while ((index != start) && wrongWords[index].__msh_fixed);
	if (index == start) {
		index = 0;
		alert(i18n["Finished list of mispelled words"]);
	}
	wrongWords[index].onclick();
	return false;
};

function replaceAllClicked() {
	var replacement = document.getElementById("v_replacement").value;
	var ok = true;
	var spans = allWords[currentElement.__msh_origWord];
	if (spans.length == 0) {
		alert("An impossible condition just happened.  Call FBI.  ;-)");
	} else if (spans.length == 1) {
		replaceClicked();
		return false;
	}
	/*
	var message = "The word \"" + currentElement.__msh_origWord + "\" occurs " + spans.length + " times.\n";
	if (replacement == currentElement.__msh_origWord) {
		ok = confirm(message + "Ignore all occurrences?");
	} else {
		ok = confirm(message + "Replace all occurrences with \"" + replacement + "\"?");
	}
	*/
	if (ok) {
		for (var i in spans) {
			if (spans[i] != currentElement) {
				replaceWord(spans[i]);
			}
		}
		// replace current element the last, so that we jump to the next word ;-)
		replaceClicked();
	}
	return false;
};

function ignoreClicked() {
	document.getElementById("v_replacement").value = currentElement.__msh_origWord;
	replaceClicked();
	return false;
};

function ignoreAllClicked() {
	document.getElementById("v_replacement").value = currentElement.__msh_origWord;
	replaceAllClicked();
	return false;
};

function learnClicked() {
	alert("Not [yet] implemented");
	return false;
};

function internationalizeWindow() {
	var types = ["div", "span", "button"];
	for (var i in types) {
		var tag = types[i];
		var els = document.getElementsByTagName(tag);
		for (var j = els.length; --j >= 0;) {
			var el = els[j];
			if (el.childNodes.length == 1 && /\S/.test(el.innerHTML)) {
				var txt = el.innerHTML;
				if (typeof i18n[txt] != "undefined") {
					el.innerHTML = i18n[txt];
				}
			}
		}
	}
};

function initDocument() {
	internationalizeWindow();
	modified = false;
	frame = document.getElementById("i_framecontent");
	var field = document.getElementById("f_content");
	field.value = editor.getHTML();
	field.form.submit();
	document.getElementById("f_init").value = "0";

	// assign some global event handlers

	var select = document.getElementById("v_suggestions");
	select.onchange = function() {
		document.getElementById("v_replacement").value = this.value;
	};
	if (is_ie) {
		select.attachEvent("ondblclick", replaceClicked);
	} else {
		select.addEventListener("dblclick", replaceClicked, true);
	}

	document.getElementById("b_replace").onclick = replaceClicked;
	// document.getElementById("b_learn").onclick = learnClicked;
	document.getElementById("b_replall").onclick = replaceAllClicked;
	document.getElementById("b_ignore").onclick = ignoreClicked;
	document.getElementById("b_ignall").onclick = ignoreAllClicked;
	document.getElementById("b_recheck").onclick = recheckClicked;

	document.getElementById("b_ok").onclick = saveClicked;
	document.getElementById("b_cancel").onclick = cancelClicked;

	select = document.getElementById("v_dictionaries");
	select.onchange = function() {
		document.getElementById("f_dictionary").value = this.value;
	};
};

function wordClicked() {
	if (currentElement) {
		var a = allWords[currentElement.__msh_origWord];
		currentElement.className = currentElement.className.replace(/\s*HA-spellcheck-current\s*/g, " ");
		for (var i in a) {
			var el = a[i];
			if (el != currentElement) {
				el.className = el.className.replace(/\s*HA-spellcheck-same\s*/g, " ");
			}
		}
	}
	currentElement = this;
	this.className += " HA-spellcheck-current";
	var a = allWords[currentElement.__msh_origWord];
	for (var i in a) {
		var el = a[i];
		if (el != currentElement) {
			el.className += " HA-spellcheck-same";
		}
	}
	document.getElementById("b_replall").disabled = (a.length <= 1);
	document.getElementById("b_ignall").disabled = (a.length <= 1);
	var txt;
	if (a.length == 1) {
		txt = "one occurrence";
	} else if (a.length == 2) {
		txt = "two occurrences";
	} else {
		txt = a.length + " occurrences";
	}
	document.getElementById("statusbar").innerHTML = "Found " + txt +
		' for word "<b>' + currentElement.__msh_origWord + '</b>"';
	var select = document.getElementById("v_suggestions");
	for (var i = select.length; --i >= 0;) {
		select.remove(i);
	}
	var suggestions;
	suggestions = this.nextSibling.firstChild.data.split(/,/);
	for (var i = 0; i < suggestions.length; ++i) {
		var txt = suggestions[i];
		var option = document.createElement("option");
		option.value = txt;
		option.appendChild(document.createTextNode(txt));
		select.appendChild(option);
	}
	document.getElementById("v_currentWord").innerHTML = this.__msh_origWord;
	if (suggestions.length > 0) {
		select.selectedIndex = 0;
		select.onchange();
	} else {
		document.getElementById("v_replacement").value = this.innerHTML;
	}
	return false;
};

function wordMouseOver() {
	this.className += " HA-spellcheck-hover";
};

function wordMouseOut() {
	this.className = this.className.replace(/\s*HA-spellcheck-hover\s*/g, " ");
};

function finishedSpellChecking() {
	// initialization of global variables
	currentElement = null;
	wrongWords = null;
	allWords = {};

	document.getElementById("status").innerHTML = "HTMLArea Spell Checker (<a href='readme-tech.html' target='_blank' title='Technical information'>info</a>)";
	var doc = frame.contentWindow.document;
        var spans = doc.getElementsByTagName("span");
        var sps = [];
	var id = 0;
        for (var i = 0; i < spans.length; ++i) {
                var el = spans[i];
                if (/HA-spellcheck-error/.test(el.className)) {
                        sps.push(el);
			el.onclick = wordClicked;
			el.onmouseover = wordMouseOver;
			el.onmouseout = wordMouseOut;
			el.__msh_id = id++;
			var txt = (el.__msh_origWord = el.firstChild.data);
			el.__msh_fixed = false;
			if (typeof allWords[txt] == "undefined") {
				allWords[txt] = [el];
			} else {
				allWords[txt].push(el);
			}
                }
        }
	wrongWords = sps;
	if (sps.length == 0) {
		if (!modified) {
			alert(i18n["NO_ERRORS_CLOSING"]);
			window.close();
		} else {
			alert(i18n["NO_ERRORS"]);
		}
		return false;
	}
	(currentElement = sps[0]).onclick();
	var as = doc.getElementsByTagName("a");
	for (var i = as.length; --i >= 0;) {
		var a = as[i];
		a.onclick = function() {
			if (confirm(i18n["CONFIRM_LINK_CLICK"] + ":\n" +
				    this.href + "\n" + i18n["I will open it in a new page."])) {
				window.open(this.href);
			}
			return false;
		};
	}
	var dicts = doc.getElementById("HA-spellcheck-dictionaries");
	if (dicts) {
		dicts.parentNode.removeChild(dicts);
		dicts = dicts.innerHTML.split(/,/);
		var select = document.getElementById("v_dictionaries");
		for (var i = select.length; --i >= 0;) {
			select.remove(i);
		}
		for (var i = 0; i < dicts.length; ++i) {
			var txt = dicts[i];
			var option = document.createElement("option");
			option.value = txt;
			option.appendChild(document.createTextNode(txt));
			select.appendChild(option);
		}
	}
};
