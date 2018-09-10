// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * JavaScript library for the quiz module.
 *
 * @package    mod
 * @subpackage questionnaire
 * @copyright
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/*
 * A workaround for MSIE versions < 10 which do not recognize classList. Answer by Paulpro at:
 * http://stackoverflow.com/questions/6787383/what-is-the-solution-to-remove-add-a-class-in-pure-javascript.
 * */

function addClass(el, aclass){
    el.className += ' ' + aclass;
}

function removeClass(el, aclass){
    var elClass = ' ' + el.className + ' ';
    while(elClass.indexOf(' ' + aclass + ' ') != - 1) {
         elClass = elClass.replace(' ' + aclass + ' ', '');
    }
    el.className = elClass;
}
// End classList workaround.

/**
 * Javascript for hiding/displaying children questions on preview page of
 * questionnaire with conditional branching.
 */

function depend(children, choices) {
    children = children.split(',');
    choices = choices.split(',');
    var childrenlength = children.length;
    var choiceslength = choices.length;
    var child = null;
    var choice = null;
    for (var i = 0; i < childrenlength; i++) {
        child = children[i];
        var q = document.getElementById(child);
        if (q) {
            var radios = q.getElementsByTagName('input');
            var radiolength = radios.length;
            var droplists = q.getElementsByTagName('select');
            var droplistlength = droplists.length;
            var textareas = q.getElementsByTagName('textarea');
            var textarealength = textareas.length;
            for (var k = 0; k < choiceslength; k++) {
                var j, m, n;
                choice = choices[k];
                if (child == choice) {
                    // If this browser version accepts classList.
                    if (typeof document !== "undefined" && ("classList" in document.createElement("a"))) {
                        q.classList.add('qn-container');
                        // If this browser version DOES NOT accept classList (e.g. MSIE < 10)
                    } else {
                        addClass(q, 'qn-container');
                    }
                    for (j = 0; j < radiolength; j++) {
                        var radio = radios[j];
                        radio.disabled = false;
                    }
                    for (m = 0; m < droplistlength; m++) {
                        var droplist = droplists[m];
                        droplist.disabled = false;
                    }
                    delete children[i];
                } else if (children[i]){
                    if (typeof document !== "undefined" && ("classList" in document.createElement("a"))) {
                        q.classList.remove('qn-container');
                        q.classList.add('hidedependquestion');
                    } else {
                        removeClass(q, 'qn-container');
                    }
                    addClass(q, 'hidedependquestion');
                    for (j = 0; j < radiolength; j++) {
                        var radio = radios[j];
                        radio.disabled = true;
                        radio.checked = false;
                        radio.value = '';
                    }
                    for (m = 0; m < droplistlength; m++) {
                        var droplist = droplists[m];
                        droplist.selectedIndex = 0;
                        droplist.disabled = true;
                        droplist.checked = false;
                    }
                    for (n = 0; n < textarealength; n++) {
                        var textarea = textareas[n];
                        textarea.value = '';
                    }
                }
            }
        }
    }
}

/* exported dependdrop */

function dependdrop(qId, children) {
    var e = document.getElementById(qId);
    var choice = e.options[e.selectedIndex].value;
    depend(children, choice);
}
// End conditional branching functions.

// When respondent enters text in !other field, corresponding
// radio button OR check box is automatically checked.
/* exported other_check */
function other_check(name) {
    var other = name.split("_");
    var f = document.getElementById("phpesp_response");
    for (var i = 0; i <= f.elements.length; i++) {
        if (f.elements[i].value == "other_" + other[1]) {
            f.elements[i].checked = true;
            break;
        }
    }
}

// Automatically empty an !other text input field if another Radio button is clicked.
/* exported other_check_empty */
function other_check_empty(name, value) {
    var f = document.getElementById("phpesp_response");
    var i;
    for (i = 0; i < f.elements.length; i++) {
        if ((f.elements[i].name == name) && f.elements[i].value.substr(0, 6) == "other_") {
            f.elements[i].checked = true;
            var otherid = f.elements[i].name + "_" + f.elements[i].value.substring(6);
            var other = document.getElementsByName(otherid);
            if (value.substr(0,6) != "other_") {
                other[0].value = "";
            } else {
                other[0].focus();
            }
            var actualbuttons = document.getElementsByName(name);
            for (i = 0; i <= actualbuttons.length; i++) {
                if (actualbuttons[i].value == value) {
                    actualbuttons[i].checked = true;
                    break;
                }
            }
            break;
        }
    }
}

// In a Rate question type of sub-type Order : automatically uncheck a Radio button
// when another radio button in the same column is clicked.
/* exported other_rate_uncheck */
function other_rate_uncheck(name, value) {
    var col_name = name.substr(0, name.indexOf("_"));
    var inputbuttons = document.getElementsByTagName("input");
    for (var i = 0; i <= inputbuttons.length - 1; i++) {
        var button = inputbuttons[i];
        if (button.type == "radio" && button.name != name && button.value == value
                    && button.name.substr(0, name.indexOf("_")) == col_name) {
            button.checked = false;
        }
    }
}

// Empty an !other text input when corresponding Check Box is clicked (supposedly to empty it).
/* exported checkbox_empty */
function checkbox_empty(name) {
    var actualbuttons = document.getElementsByName(name);
    for (var i = 0; i <= actualbuttons.length; i++) {
        if (actualbuttons[i].value.substr(0, 6) == "other_") {
            name = name.substring(0, name.length - 2) + actualbuttons[i].value.substring(5);
            var othertext = document.getElementsByName(name);
            if (othertext[0].value == "" && actualbuttons[i].checked == true) {
                othertext[0].focus();
            } else {
                othertext[0].value = "";
            }
            break;
        }
    }
}


M.mod_questionnaire = M.mod_questionnaire || {};

/* exported Y */
/* exported e */
M.mod_questionnaire.init_attempt_form = function(Y) {
    M.core_formchangechecker.init({formid: 'phpesp_response'});
};

M.mod_questionnaire.init_sendmessage = function(Y) {
    Y.on('click', function(e) {
        Y.all('input.usercheckbox').each(function() {
            this.set('checked', 'checked');
        });
    }, '#checkall');

    Y.on('click', function(e) {
        Y.all('input.usercheckbox').each(function() {
            this.set('checked', '');
        });
    }, '#checknone');

    Y.on('click', function(e) {
        Y.all('input.usercheckbox').each(function() {
            if (this.get('alt') == 0) {
                this.set('checked', 'checked');
            } else {
                this.set('checked', '');
            }
        });
    }, '#checknotstarted');

    Y.on('click', function(e) {
        Y.all('input.usercheckbox').each(function() {
            if (this.get('alt') == 1) {
                this.set('checked', 'checked');
            } else {
                this.set('checked', '');
            }
        });
    }, '#checkstarted');

};