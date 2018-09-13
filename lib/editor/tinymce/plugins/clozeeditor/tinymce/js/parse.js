// JavaScript Document
// Parse the information from the input string coming from Moodle's HTMLArea
// right into the text fields

var splitstring;
var longstring = "{1:MULTICHOICE:%0%global delivery system#~%100%local management system#~%0%communal drainage system#~%0%state funding system#}";

function loadContent() {
    // Local testing purpose
    // Does not affect online experience

    if (typeof(splitstring) == "undefined") {
        splitstring = longstring;
    }

    // check if user highlighted code or plain text
    if (isCode(splitstring)) {
        // user highlighted quiz code
        for (var i = 1; i <= countAnswers(); i++) {
            // add as many lines as there are answers
            addRow('main_table');
        }

        // Fill all input boxes with code from Moodle's HTMLArea accordingly
        fillBoxes(splitstring);
        encode();
    } else if (isCode(splitstring) == false) {
        // user highlighted text
        // add three rows, standard input
        addRow('main_table');
        addRow('main_table');
        addRow('main_table');

        // fill only the first box, because the user selected text
        fillFirstBoxOnly(splitstring);
    }

    // Toogle throttle column accordingly
    toggleThrottle();
}

// Fill boxes with quiz code coming from Moodle's HTMLArea
function fillBoxes(string) {
    // TODO: should be reforget according to the getELEMENTNAMEElement() style
    var f = document.forms[0];
    var selection;

    // inserted for local testing purposes
    if (typeof(tinyMCEPopup) != 'undefined') {
        selection = tinyMCEPopup.editor.selection.getContent({format : 'text'});
    } else {
        selection = longstring;
    }

    // insert points
    f.weighting.value = getPoints(selection);

    // set test variant
    setTestVariant(getQuizTypeFromString(selection));

    // fill boxes
    var i = 1;
    var defined = true;
    while (defined == true) {
        // loop through all table rows and fill inputs
        if (getAnswerElement(i)) { // Check if element exists
            getAnswerElement(i).value = getAnswer(selection, i);
            getPercentElement(i).value = getPercentage(selection, i);

            // TODO
            if (getPercentage(selection, i) == '100') {
                checkPercentOnLeave(i)
            } else {
                checkPercentOnLeave(i);
            }
            getFeedbackElement(i).value = getComment(selection, i);
            getThrottleElement(i).value = getThrottle(selection, i);
            i = i + 1;
        } else {
            defined = false;
        }
    }
}

function fillFirstBoxOnly(string) {
    // fires when user marked one or more words which should not contain '{' and '}' at first, second, last, or first before last position

    var selection;

    if (typeof(tinyMCEPopup) != "undefined") {
        selection = tinyMCEPopup.editor.selection.getContent({format : 'text'});
    } else {
        // for local testing purpose
        selection = longstring;
    }

    getAnswerElement(1).value = trim(selection);
    getGradeElement().value = "1";
    getCorrectElement(1).checked = true;
    correctnessClick(1);
}

function checkPercentInput(objectID) {
    var alphabet = ['a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z', 'ä', 'ö', 'ü'];
    var element = getAnswerElement(objectID);

    /*
    This is highly experimental and just a mind experiment. Such a check might be implemented in the future
    var noStrings = "";
    for (var c; c < length(element.value); c++) {
    if (lowerCase(element.value[c]) in alphabet) {
      //
    } else {
      noStrings = noStrings + element.value[c];
    }
    }
    element.value = noStrings;
    */

    setPercentTo100(objectID);
}

function setPercentTo100 (objectID) {
    var element = getPercentElement(objectID);

    if (element.value > 100) {
        element.value = 100;
        checkPercentOnLeave(objectID);
    }
}

function addRow(id) {
    counter = countInputRows() + 1;

    var tbody = document.getElementById(id).getElementsByTagName("TBODY")[0];
    var row = document.createElement("TR");
    var td1 = document.createElement("TD");
    var td2 = document.createElement("TD");
    var td3 = document.createElement("TD");
    var td4 = document.createElement("TD");
    var td5 = document.createElement("TD");
    var td6 = document.createElement("TD");

    // first col: Counter
    td1.className = "table_value";
    var label1 = document.createElement("LABEL");
    label1.setAttribute("for", counter + "_option");
    td1.appendChild(label1);
    label1.appendChild(document.createTextNode(counter));

    // second col: Answer
    td2.className = "table_value";
    var input_answer = document.createElement("INPUT");
    td2.appendChild(input_answer);
    input_answer.type = "text";
    input_answer.id = counter + "_option";
    input_answer.name = counter + "_option";
    input_answer.size = 30;
    input_answer.setAttribute("onkeypress", "if (event.keyCode==13) { encode() }" );

    // third col: Throttle Value
    td3.className = "table_value_throttle";
    var input_throttle = document.createElement("INPUT");
    input_throttle.type = "text";
    input_throttle.name = counter + "_throttle";
    if (getQuizTypeElement().value == "NUMERICAL") {
        td3.style.display = "inherit";
    } else {
        td3.style.display = "none";
    }

    td3.appendChild(input_throttle);

    // fourth col: Checkbox
    td4.className = "table_value";
    var input_checkbox = document.createElement("INPUT");
    input_checkbox.type = "checkbox";
    input_checkbox.name = counter + "_correct";
    input_checkbox.setAttribute("onclick", "correctnessClick(" + counter + ")");
    input_checkbox.checked = false;
    td4.appendChild(input_checkbox);

    // fifth col: Percentage Value
    td5.className = "table_value";
    var input_percent = document.createElement("INPUT");
    input_percent.type = "text";
    input_percent.name = counter + "_percent";
    input_percent.value = "0";
    input_percent.size = 4;
    input_percent.maxLength = 4;
    input_percent.setAttribute("onChange", "correctnessClick(" + counter + "); setPercentTo100(" + counter + ")");
    td5.appendChild(input_percent);

    // sixth col: Feedback
    td6.className = "table_value";
    var input_feedback = document.createElement("INPUT");
    input_feedback.type = "text";
    input_feedback.name = counter + "_feedback";
    input_feedback.value = "";
    input_feedback.size = 30;
    td6.appendChild(input_feedback);

    row.appendChild(td1);
    row.appendChild(td2);
    row.appendChild(td3);
    row.appendChild(td4);
    row.appendChild(td5);
    row.appendChild(td6);
    tbody.appendChild(row);
}

// this function will set the correctess status upon clicking a checkbox
function correctnessClick (objectID) {
    var correct_element = getCorrectElement(objectID);
    var percent_element = getPercentElement(objectID);

    if (correct_element.checked == false) {
        setCorrectnessState(objectID, "on");
    } else if (correct_element.checked == true) {
        setCorrectnessState(objectID, "off");
    }
}

// this function sets the color of the percentage input field to grey or white, according to the correctness checkbox
function setCorrectnessState(objectID, state) {
    var correct_element = getCorrectElement(objectID);
    var percent_element = getPercentElement(objectID);

    if (state == "off") {
        // gray out
        percent_element.style.backgroundColor = "EEEEEE";
        percent_element.style.color = "888888";
        percent_element.value = "100";
        percent_element.readOnly = true;
        correct_element.checked = true;

        // TODO: canFocus = false
    } else if (state == "on") {
        // restore color
        percent_element.style.backgroundColor = "white";
        percent_element.style.color = "black";
        if (percent_element.value == "100") {
            // if the value is 100, there should be a 0 so it does not say 100 and is at the same time not checked in the check box
            percent_element.value = "0";
        }
        percent_element.readOnly = false;
        correct_element.checked = false;
    }
}

function setTestVariant(test) {
    // var f = document.forms[0];

    var index = 0;

    if (test == 'SHORTANSWER') {
        index = 0;
    } else if (test == 'SHORTANSWER_C') {
        index = 1;
    } else if (test == 'MULTICHOICE') {
        index = 2;
    } else if (test == 'MULTICHOICE_V') {
        index = 3;
    } else if (test == 'MULTICHOICE_H') {
        index = 4;
    } else if (test == 'NUMERICAL') {
        index = 5;
    }

    // old version
    // f.quiz_type.selectedIndex = index;
    getQuizTypeElement().selectedIndex = index;
}

// this function retrieves points for a given test
function getPoints(string) {
    var temp = string;

    temp = deleteAfterString(temp, ':');
    temp = deleteTillChar(temp, '{');

    // if the user has highlighted a non-break space at the beginning, it has to be omitted
    if (typeof(temp) != "undefined") {
        if (temp.charAt(0) == '{') {
            return temp.charAt(1);
        } else {
            return temp.charAt(0);
        }
    }
}

// this function retrieves the test variant for a given test
function getQuizTypeFromString(string) {
    // jump straight to the test variant, after the first colon
    var temp = string;

    // delete everything before (and including) the first ':'
    temp = deleteTillChar(temp, ':');
    // delete everything after (and including) the second ':'
    temp = deleteAfterString(temp, ':');

    return temp;
}

// ----------------------

if (typeof(tinyMCEPopup) != "undefined") {
    tinyMCEPopup.requireLangPack();
}

var clozeeditorDialog = {
    init : function() {
         // var f = document.forms[0];

         // Get the selected content as text
        splitstring = tinyMCEPopup.editor.selection.getContent({format : 'text'});

         loadContent();

        // stopped working, or never did, so commented it out on 2011-10-14, 1pm
        // f.somearg.value = tinyMCEPopup.getWindowArg('some_custom_arg');
    },

    insert : function() {
         // Insert the contents from the input into the document
         // deprecated
         // tinyMCEPopup.editor.execCommand('mceInsertContent', false, getEncodeElement().value+" ");
        tinyMCEPopup.editor.execCommand('mceInsertContent', false, encode() + " ");

        tinyMCEPopup.close();
    }
   };

if (typeof(tinyMCEPopup) != "undefined") {
        tinyMCEPopup.onInit.add(clozeeditorDialog.init, clozeeditorDialog);
}
