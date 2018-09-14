// script originates in dialog.php.
// copied for clarification.

// Some functions to retrieve objects.
// @objectID represents the line number.

function getQuizTypeElement() {
    return document.getElementsByName('quizType')[0];
}

function getAnswerElement(objectID) {
    return document.getElementsByName(objectID + '_option')[0];
}

function getThrottleElement(objectID) {
    return document.getElementsByName(objectID + '_throttle')[0];
}

function getCorrectElement(objectID) {
    return document.getElementsByName(objectID + '_correct')[0];
}

function getPercentElement(objectID) {
    return document.getElementsByName(objectID + '_percent')[0];
}

function getFeedbackElement(objectID) {
    return document.getElementsByName(objectID + '_feedback')[0];
}

function getEncodeElement() {
    return document.getElementsByName('output')[0];
}

function getGradeElement() {
        return document.getElementsByName('weighting')[0];
}

// Had to write this one because it was in PHP but not in JS.
function substr(string, position) {
    if (string.length > 0) {
        return string.substring(position, string.length);
    }
}

// Had to write this one because it was in PHP but not in JS.
function strpos(string, word) {
    return string.indexOf(word);
}

function isCode(str) {
    if (str != "") {
        if (((str.charAt(0) == '{') || (str.charAt(1) == '{')) &&
        ((str.charAt(str.length - 2) == '}') || (str.charAt(str.length - 1) == '}'))) {
            // User selected code.
            // This fork looks at the first, second, first before last and last sign.
            // If one of the first two is a { and one of the last two is a }, it will be recognized as code.
            return true;
        } else {
            // User selected text and not code.
            return false;
        }
    } else {
        return false;
    }
}

// Checks if a element is correct and sets colors and checkboxes accordingly.
function checkPercentOnLeave(objectID) {
    var correct_element = getCorrectElement(objectID);
    var percent_element = getPercentElement(objectID);

    if (percent_element.value == '100') {
        setCorrectnessState(objectID, "off");
    } else {
        setCorrectnessState(objectID, "on");
    }
}

function getQuizTypeFromInput() {
    return getQuizTypeElement().options[getQuizTypeElement().options.selectedIndex].value;
}

// Function to extract a string; converted from PHP(?).
// Don't change.
function strstr (haystack, needle, bool) {
    var pos = 0;

    haystack += '';
    pos = haystack.indexOf(needle);

    if (pos == -1) {
        return false;
    } else {
        if (bool) {
            return haystack.substr(0, pos);
        } else {
            return haystack.slice(pos);
        }
    }
}

// Count the answers encapsulated in a string.
function countAnswers() {
    if (typeof(tinyMCEPopup) != 'undefined') {
        var cache = tinyMCEPopup.editor.selection.getContent({format : 'text'});
        return (cache.split("#").length - 1);
    } else {
         return (longstring.split("#").length - 1);
    }
}

// Count the filled inputs to see whether there are vacuous(?) elements.
// which don't have to be walked through when encoding.
function countFilledInputs() {
    var aCounter = countInputRows();
    var empty = false;

    while ((empty == false) && (aCounter > 0)) {
        answer_element = getAnswerElement(aCounter); // document.getElementsByName(aCounter+'_option')[0]; .
        if ((typeof(answer_element) != 'undefined') && (answer_element.value == "")) {
            empty = false;
            aCounter = aCounter - 1;
        } else {;
            return aCounter;
        }
    }
    return aCounter;
}


// Count the number of input rows when determining which will be the next number in line.
function countInputRows() {
    var aTable = document.getElementById("main_table");
    var items = aTable.getElementsByTagName("tr");
    // - 1 because there is a headline.
    return items.length - 1;
}

// ***************************************************************************//.
// ***                  Some moodle internal functions                     ***//.
// ***************************************************************************//.

/*
function __dlg_init(bottom) {
  var body = document.body;
  var body_height = 0;
  if (typeof bottom == "undefined") {
    var div = document.createElement("div");
    body.appendChild(div);
    var pos = getAbsolutePos(div);
    body_height = pos.y;
  } else {
    var pos = getAbsolutePos(bottom);
    body_height = pos.y + bottom.offsetHeight;
  }
  window.dialogArguments = opener.Dialog._arguments;
  document.body.onkeypress = __dlg_close_on_esc;
  window.focus();
};
function getAbsolutePos(el) {
  var r = { x: el.offsetLeft, y: el.offsetTop };
  if (el.offsetParent) {
    var tmp = getAbsolutePos(el.offsetParent);
    r.x += tmp.x;
    r.y += tmp.y;
  }
  return r;
};
function comboSelectValue(c, val) {
  var ops = c.getElementsByTagName("option");
  for (var i = ops.length; --i >= 0;) {
    var op = ops[i];
    op.selected = (op.value == val);
  }
  c.value = val;
};
function __dlg_onclose() {
  opener.Dialog._return(null);
};
function __dlg_init(bottom) {
  var body = document.body;
  var body_height = 0;
  if (typeof bottom == "undefined") {
    var div = document.createElement("div");
    body.appendChild(div);
    var pos = getAbsolutePos(div);
    body_height = pos.y;
 } else {
    var pos = getAbsolutePos(bottom);
    body_height = pos.y + bottom.offsetHeight;
  }
  window.dialogArguments = opener.Dialog._arguments;
  document.body.onkeypress = __dlg_close_on_esc;
  window.focus();
};
function __dlg_translate(i18n) {
  var types = ["span", "option", "td", "button", "div"];
  for (var type in types) {
    var spans = document.getElementsByTagName(types[type]);
    for (var i = spans.length; --i >= 0;) {
      var span = spans[i];
      if (span.firstChild && span.firstChild.data) {
        var txt = i18n[span.firstChild.data];
        if (txt)
          span.firstChild.data = txt;
      }
    }
  }
  var txt = i18n[document.title];
  if (txt)
    document.title = txt;
};
// closes the dialog and passes the return info upper.
function __dlg_close(val) {
  opener.Dialog._return(val);
  window.close();
};
function __dlg_close_on_esc(ev) {
  ev || (ev = window.event);
  if (ev.keyCode == 27) {
    window.close();
    return false;
  }
  return true;
};
*/


// Initialize.
function Init() {
    // __dlg_init(); .
};

function _CloseOnEsc() {
    __dlg_close_on_esc(event);
}

function Init() {
    // __dlg_init();
    getAnswerElement(1).focus();
    getAnswerElement(1).select();
    // document.getElementById('1_option').focus(); .
    // document.getElementById("1_option").select(); .
    document.body.onkeypress = _CloseOnEsc;
    var param = window.dialogArguments;
};

function onOK() {
    var required = {
        "embedcode": "Please insert values and hit 'process'."
    };
    for (var i in required) {
        var el = document.getElementById(i);
        if (!el.value) {
            // alert(required[i]); .
            el.focus();
            return false;
        }
    }
    var fields = ["embedcode"];
    var param = new Object();
    for (var i in fields) {
        var id = fields[i];
        var el = document.getElementById(id);
        param[id] = el.value;
    }
    __dlg_close(param);
    return false;
};

function onCancel() {
    __dlg_close(null);
    return false;
};
