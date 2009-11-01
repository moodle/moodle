<script type="text/javascript">
//<![CDATA[

// If the mouse is clicked outside this element, the edit is CANCELLED (even if the mouse clicks another grade/feedback cell)
// If ctrl-arrow is used, or if [tab] or [enter] are pressed, the edit is RECORDED and the row is updated. The previous element returns to normal

YAHOO.namespace("grader_report");
YAHOO.grader_report.el_being_edited = null;
YAHOO.grader_report.courseid = <?php echo $COURSE->id; ?>;
YAHOO.grader_report.wwwroot = '<?php echo $CFG->wwwroot; ?>';
YAHOO.grader_report.straddfeedback = '<?php echo get_string("addfeedback", "grades"); ?>';
YAHOO.grader_report.strfeedback = '<?php echo get_string("feedback", "grades"); ?>';
YAHOO.grader_report.feedback_trunc_length = <?php echo $report->feedback_trunc_length ?>;
YAHOO.grader_report.decimalpoints = <?php echo $report->getItemsDecimalPoints() ?>;
YAHOO.grader_report.studentsperpage = <?php echo $report->get_pref('studentsperpage'); ?>;
YAHOO.grader_report.showquickfeedback = <?php echo $report->get_pref('showquickfeedback'); ?>;

// Feedback data is cached in a JS array: we don't want to show complete feedback strings in the report, but
// neither do we want to fetch the feedback from PHP each time we click one to edit it
YAHOO.grader_report.feedbacks = <?php echo $report->getFeedbackJsArray(); ?>


/**
 * Given an elementId formatted as grade[cell|value|feedback]_u[$userId]-i[$itemId], returns an object with key-value pairs
 * @param string elId
 * @return object
 */
YAHOO.grader_report.getIdData = function(elId) {
    var re = /grade(value|feedback|cell|scale)_([0-9]*)-i([0-9]*)/;
    var matches = re.exec(elId);
    if (undefined != matches && matches.length > 0) {
        return {type: matches[1], userId: matches[2], itemId: matches[3]};
    } else {
        YAHOO.log("getIdData: Invalid elementId: " + elId, "warn");
        return false;
    }
};

/**
 * Reverse-engineering of getIdData: returns a string based on an object
 * @param object idData
 * @return string
 */
YAHOO.grader_report.getElementId = function(idData) {
    if (undefined != idData.userId && undefined != idData.type && undefined != idData.itemId) {
        return "grade" + idData.type + "_" + idData.userId + "-i" + idData.itemId;
    } else {
        YAHOO.log("getElementId: Invalid elementId: " + idData, "warn");
        return false;
    }
};

/**
 * Interface to the overlib js library. DEPENDENCY ALERT!
 */
YAHOO.grader_report.tooltip = function(e, dataObj) {
    var gr = YAHOO.grader_report;
    if (undefined != dataObj.text) {
        return overlib(dataObj.text, BORDER, 0, FGCLASS, 'feedback', CAPTIONFONTCLASS, 'caption', CAPTION, gr.strfeedback);
    } else {
        return null;
    }
};

/**
 * Sends the record request and un-edits the element
 * @param object editedEl The DOM element being edited, whose value we are trying to save in DB
 * @return array An array of values used to update the row
 */
YAHOO.grader_report.saveField = function(editedEl) {
    var gr = YAHOO.grader_report;
    var idData = gr.getIdData(editedEl.id);

    if (idData.type == 'value') { // Text input
        var newVal = editedEl.firstChild.value;
    } else if (idData.type == 'feedback') { // Textarea
        var newVal = editedEl.firstChild.innerHTML;
    } else if (idData.type == 'scale') { // Select
        var newVal = editedEl.options[editedEl.selectedIndex].value;
    }

    // Don't save if the new value is the same as the old
    if (gr.el_being_edited.value == newVal || (gr.el_being_edited.value == gr.straddfeedback && newVal == '')) {
        YAHOO.log("saveField: Field unchanged, not saving. (" + newVal + ")", "info");
        return false;
    }

    YAHOO.log("saveField: Old value: " + gr.el_being_edited.value + ", new value: " + newVal + ". Saving field...", "info");

    var postData = "id=" + gr.courseid + "&userid=" + idData.userId + "&itemid=" + idData.itemId +
                   "&action=update&newvalue=" + newVal + "&type=" + idData.type;

    var handleSuccess = function(o) {
        try {
            var queryResult = YAHOO.lang.JSON.parse(o.responseText);
        } catch (e) {
            YAHOO.log("saveField: JSON syntax error! " + o.responseText, "error");
        }

        if (queryResult.result == "success") {
            // For a textarea, truncate the feedback to 40 chars and provide a tooltip with the full text
            if (queryResult.gradevalue == null) {
                if (idData.type == 'scale') {
                    editedEl.selectedIndex = 0;
                } else {
                    editedEl.innerHTML = '';
                }
            } else if (idData.type == 'feedback') {
                editedEl.innerHTML = gr.truncateText(queryResult.gradevalue);

                YAHOO.util.Event.addListener(editedEl.id, 'mouseover', gr.tooltip, {text: queryResult.gradevalue}, true);
                YAHOO.util.Event.addListener(editedEl.id, 'mouseout', nd); // See overlib doc for reference
                gr.feedbacks[idData.userId][idData.itemId] = queryResult.gradevalue;
            } else if (idData.type == 'value') {
                editedEl.innerHTML = gr.roundValue(queryResult.gradevalue, idData.itemId);
            }

            YAHOO.util.Dom.addClass(editedEl, "editable");

            // TODO "highlight" the updated element using animation of color (yellow fade-out)

            // Update the row's final grade values
            gr.updateRow(gr.getIdData(editedEl.id).userId, queryResult.row);
        } else {

        }

        // Display message
        gr.displayMessage(queryResult.result, queryResult.message, editedEl);
    }

    var handleFailure = function(o) {
        YAHOO.log("saveField: Failure to call the ajax callbacks page!", "error");
    }

    var uri = gr.wwwroot + '/grade/report/grader/ajax_callbacks.php';
    var callback = {success: handleSuccess, failure: handleFailure};
    var conn = YAHOO.util.Connect.asyncRequest("post", uri, callback, postData);
}; // End of saveField function

/**
 * Displays a message in the message bar above the report, Google-style
 * @param string result "success", "notice" or "error"
 * @param string message
 * @param object An element to highlight
 */
YAHOO.grader_report.displayMessage = function(result, message, elToHighlight) {
    var messageDiv = document.getElementById('grader_report_message');
    // Remove previous message
    // TODO log messages in DB?
    messageDiv.innerHTML = '';

    if (message.length < 1 || !message) {
        return false;
    }

    // Remove all state classes first
    YAHOO.util.Dom.removeClass(messageDiv, 'success');
    YAHOO.util.Dom.removeClass(messageDiv, 'error');
    YAHOO.util.Dom.removeClass(messageDiv, 'notice');

    var attributes = {backgroundColor: { to: '#00F0F0'} };

    // Add result class
    YAHOO.util.Dom.addClass(messageDiv, result);

    messageDiv.innerHTML = message;

    // Highlight given element
    if (result == 'error' && elToHighlight != null) {
        YAHOO.util.Dom.addClass(elToHighlight, 'error');
    }
};

/**
 * Given a userId and an array of string values, updates the innerHTML of each grade cell in the row
 * @param string userId Identifies the correct row
 * @param array An array of values
 *
 */
YAHOO.grader_report.updateRow = function(userId, row) {
    var gr = YAHOO.grader_report;

    // Send update request and update the row
    YAHOO.log("updateRow: Updating row..." + row, "info");

    for (var i in row) {
        if (row[i].finalgrade != null) {
            // Build id string
            var gradevalue = gr.roundValue(row[i].finalgrade, row[i].itemid);

            if (row[i].scale) {
                var idString = "gradescale_";
            } else {
                var idString = "gradevalue_";
            }

            idString += row[i].userid + "-i" + row[i].itemid;

            var elementToUpdate = document.getElementById(idString);

            if (undefined == elementToUpdate) {
                YAHOO.log("updateRow: Element with id " + idString + " does not exist!", "error");
            } else {
                if (row[i].scale) {
                    elementToUpdate.selectedIndex = gradevalue;
                } else {
                    elementToUpdate.innerHTML = gradevalue;
                }

                // Add overridden class where it applies, and remove it where it does not
                // TODO fix the code below, it doesn't currently work. See ajax_callbacks.php for the code building the JSON data
                if (row[i].overridden > 0) {
                    YAHOO.util.Dom.addClass(elementToUpdate.parentNode, "overridden");
                } else if (YAHOO.util.Dom.hasClass(elementToUpdate.parentNode, "overridden")) {
                    YAHOO.util.Dom.removeClass(elementToUpdate.parentNode, "overridden");
                }

                YAHOO.log("updateRow: Updated finalgrade (" + gradevalue + ") of user " + row[i].userid + " for grade item " + row[i].itemid, "info");
            }
        }
    }
};

/**
 * Given a gradevalue or gradefeedback <a> element,
 * @param object element A DOM element
 */
YAHOO.grader_report.openField = function(element) {
    YAHOO.log("openField: Moving to next item: " + element.id, "info");
    var gr = YAHOO.grader_report;
    var idData = gr.getIdData(element.id);
    element.inputId = element.id + "_input";

    // If field is in error, empty it before editing
    if (YAHOO.util.Dom.hasClass(element, 'error')) {
        if (idData.type == 'feedback') {
            element.innerHTML = '';
        } else if (idData.type == 'value') {
            element.value = '';
        }

        YAHOO.util.Dom.removeClass(element, 'error');
    }

    // Show a textarea for feedback, input for grade and leave scale as it is
    if (undefined == idData.type) {
        YAHOO.log("openField: Could not get info from elementId: " + element.id, "warn");
    } else if (idData.type == 'feedback') {
        var original = gr.feedbacks[idData.userId][idData.itemId].toString();
        var tabIndex = element.tabIndex.toString();
        var displayValue = null;

        // If empty feedback, show empty textarea
        if (original == gr.straddfeedback) {
            displayValue = '';
        } else {
            displayValue = original;
        }

        element.innerHTML = '<textarea id="' + element.inputId + '" name="' + idData.type + '">' + displayValue + '</textarea>';
        setTimeout(function() {element.firstChild.focus(); }, 0);
    } else if (idData.type == 'value') {
        var original = element.innerHTML.toString(); // Removes reference to original
        element.innerHTML = '<input onfocus="this.select()" id="' + element.inputId + '" type="text" name="' +
                            idData.type + '" value="' + gr.roundValue(original, idData.itemId) + '" />';
        setTimeout(function() {element.firstChild.focus(); }, 0);
    } else if (idData.type == 'scale') {
        var original = element.options[element.selectedIndex].value;
        setTimeout(function() {element.focus(); }, 0);
    }

    YAHOO.util.Dom.removeClass(element, "editable");

    // Save the element and its original value
    gr.el_being_edited = {elementId: element.id, value: original, tabIndex: tabIndex};
    YAHOO.log("openField: el_being_edited saved as: " + gr.el_being_edited.value, "info");
}

/**
 * Replaces the input, textarea or select inside a gradecell with the value currently held in that
 * input, textarea or select. If the second argument (cancel) is true, replaces the innerHTML with
 * the value held in YAHOO.grader_report.el_being_edited
 *
 * @param object  element DOM element being closed
 * @param boolean forcecancel If true, current value held in input element is dropped in favour of original value in memory
 */
YAHOO.grader_report.closeField = function(element, forcecancel) {
    YAHOO.log("closeField: Closing field: " + element.id, "info");

    var gr = YAHOO.grader_report;
    var idData = gr.getIdData(element.id);

    if (idData.type == 'feedback') {
        var newValue = element.firstChild.value;
        var originalValue = gr.feedbacks[idData.userId][idData.itemId];

        if (!forcecancel && originalValue == newValue) {
            YAHOO.log("closeField: originalValue == newvalue, forcing a cancel", "info");
            forcecancel = true;
            originalValue = gr.truncateText(originalValue);
        }
    } else if (idData.type == 'value') {
        var originalValue = gr.el_being_edited.value;
        var newValue = element.firstChild.value;
    }

    YAHOO.util.Dom.addClass(element, "editable");

    // No need to change HTML for select element
    if (idData.type == 'scale') {
        gr.el_being_edited = null;
        return;
    }

    if (forcecancel || ((newValue == '' && idData.type == 'feedback') && idData.type != 'scale')) {
        // Replace the input by the original value as plain text
        element.innerHTML = gr.truncateText(originalValue);
        YAHOO.log("closeField: Cancelling : Replacing field by original value: " + originalValue, "info");
    } else {
        // For numeric grades, round off to item decimal points
        if (idData.type == 'value') {
            element.innerHTML = gr.roundValue(newValue, idData.itemId);
        } else if (idData.type == 'feedback') {
            element.innerHTML = newValue;
        }
    }

    // Erase the element in memory
    gr.el_being_edited = null;
};

/**
 * Given a string, truncates it if over the limit defined in the report's preferences, adding an ellipsis at the end.
 * TODO improve the regex so that it doesn't truncate halfway through a word
 * @param string text
 * @return string
 */
YAHOO.grader_report.truncateText = function(text) {
    var gr = YAHOO.grader_report;
    var returnString = '';

    returnString = text.substring(0, gr.feedback_trunc_length);

    // Add ... if the string was truncated
    if (returnString.length < text.length) {
        returnString += '...';
    }

    return returnString;
}

/**
 * Given a float value and an itemId, uses that grade_item's decimalpoints preference to round off the value and return it.
 * @param float value
 * @param int itemId
 * @return string
 */
YAHOO.grader_report.roundValue = function(value, itemId) {
    return value;

    // I am ignoring the rest for now, because I am not sure we should round values while in editing mode
    if (value.length == 0) {
        return '';
    }

    var gr = YAHOO.grader_report;
    var decimalpoints = gr.decimalpoints[itemId];
    var gradevalue = Math.round(Number(value) * Math.pow(10, decimalpoints)) / Math.pow(10, decimalpoints);

    // If the value is an integer, add appropriate zeros after the decimal point
    if (gradevalue % 1 == 0 && decimalpoints > 0) {
        gradevalue += '.';
        for (var i = 0; i < decimalpoints; i++) {
            gradevalue += '0';
        }
    }

    return gradevalue;
};

/**
 * Given an element of origin, returns the field to be edited in the given direction.
 * @param object origin_element
 * @param string direction previous|next|left|right|up|down
 * @return object element
 */
YAHOO.grader_report.getField = function(origin_element, direction) {
    var gr = YAHOO.grader_report;
    // get all 'editable' elements
    var haystack = YAHOO.util.Dom.getElementsByClassName('editable');
    var wrapElement = null;

    var feedbackModifier = 1;
    var upDownOffset = 1;
    var idData = gr.getIdData(origin_element.id);

    if (gr.showquickfeedback) {
        feedbackModifier = 2;

        if (idData.type == 'value' || idData.type == 'scale') {
            upDownOffset = gr.studentsperpage;
        }

    }

    if (direction == 'next') {
        var wrapValue = 1;
        var needle = origin_element.tabIndex + 1;

    } else if (direction == 'previous') {
        var wrapValue = haystack.length;
        var needle = origin_element.tabIndex - 1;

    } else if (direction == 'right') {
        var needle = origin_element.tabIndex + gr.studentsperpage * feedbackModifier;
        var wrapValue = null; // TODO implement wrapping when moving right

    } else if (direction == 'left') {
        var needle = origin_element.tabIndex - gr.studentsperpage * feedbackModifier;
        var wrapValue = null; // TODO implement wrapping when moving left

    } else if (direction == 'up') {

        // Jump up from value to feedback: origin + (studentsperpage - 1)
        if (idData.type == 'value' || idData.type == 'scale') {
            var upDownOffset = gr.studentsperpage - 1;

        // Jump up from feedback to value: origin - studentsperpage
        } else if (idData.type == 'feedback') {
            var upDownOffset = -(gr.studentsperpage);
        }

        var needle = origin_element.tabIndex + upDownOffset;
        var wrapValue = haystack.length;

    } else if (direction == 'down') {
        // Jump down from value to feedback: origin + studentsperpage
        if (idData.type == 'value' || idData.type == 'scale') {
            var upDownOffset = gr.studentsperpage;

        // Jump down from feedback to value: origin - (studentsperpage - 1)
        } else if (idData.type == 'feedback') {
            var upDownOffset = -(gr.studentsperpage - 1);
        }
        var needle = origin_element.tabIndex + upDownOffset;
        var wrapValue = 1;
    }

    for (var i = 0; i < haystack.length; i++) {
        if (haystack[i].tabIndex == wrapValue) {
            wrapElement = haystack[i];
        }

        if (haystack[i].tabIndex == needle) {
            return haystack[i];
        }
    }

    // If we haven't returned yet, it means we have reached the end of tabindices: return the wrap element
    if (wrapElement != null) {
        return wrapElement;
    } else { // If no wrap element, just return the element of origin: we are stuck!
        return origin_element;
    }
};


YAHOO.grader_report.init = function() {
    var gr = YAHOO.grader_report;

    // Handle Key presses: Tab and Enter
    this.keyHandler = function(e) {
        var charCode = YAHOO.util.Event.getCharCode(e);

        YAHOO.log("init: Key pressed (" + charCode + "). el_being_edited = " + gr.el_being_edited, "info");
        // Handle keys if editing
        if (gr.el_being_edited !== null) {
            var editedEl = document.getElementById(gr.el_being_edited.elementId);
            var idData = gr.getIdData(editedEl.id);

            // Handle Tab and Shift-Tab for navigation forward/backward
            if (charCode == 9) {
                gr.saveField(editedEl);
                gr.closeField(editedEl, false);

                if (e.shiftKey) {
                    var fieldToOpen = gr.getField(editedEl, 'previous');
                } else {
                    var fieldToOpen = gr.getField(editedEl, 'next');
                }

                gr.openField(fieldToOpen);
            }

            // Handle Enter key press
            if (charCode == 13 && idData.type != 'feedback') { // textareas need [enter]
                // Locate element being edited
                var editedEl = document.getElementById(gr.el_being_edited.elementId);
                gr.saveField(editedEl);
                gr.closeField(editedEl, false);
            }

            // Handle ctrl-arrows
            var arrows = { 37: "left", 38: "up", 39: "right", 40: "down" };

            if (e.ctrlKey && (charCode == 37 || charCode == 38 || charCode == 39 || charCode == 40)) {
                gr.saveField(editedEl);
                gr.closeField(editedEl, false);

                var fieldToOpen = gr.getField(editedEl, arrows[charCode]);
                gr.openField(fieldToOpen);
            }
        }
    }

    // Handle mouse clicks
    this.clickHandler = function(e) {
        var clickTargetElement = YAHOO.util.Event.getTarget(e);


        // Handle a click while a grade value or feedback is being edited
        if (gr.el_being_edited !== null) {
            // idData represents the element being edited, not the clicked element
            var idData = gr.getIdData(gr.el_being_edited.elementId);

            if (idData.type != 'scale') {
                // If clicking in a cell being edited, no action
                // parentNode is the original a to which the element was added as a Child node
                if (gr.el_being_edited.elementId == clickTargetElement.parentNode.id) {
                    YAHOO.log("init: Clicked within the edited element, no action.", "info");
                    return false;
                }

                // Otherwise, we CANCEL the current edit
                var originalTarget = document.getElementById(gr.el_being_edited.elementId);
                YAHOO.log("init: Clicked out of the edited element, cancelling edit.", "info");
                gr.closeField(originalTarget, true);

            } else if (idData.type == 'scale' && gr.el_being_edited.elementId == clickTargetElement.id) {

                // An option has been selected, update the element
                gr.saveField(clickTargetElement);
                // Then open the element to save the new value in el_being_edited
                gr.openField(clickTargetElement);
                return;
            }
        }

        while (clickTargetElement.id != 'grade-report-grader-index') {
            anchor_re = /(gradevalue|gradefeedback|gradescale)(.*) editable/;
            anchor_matches = anchor_re.exec(clickTargetElement.className);

            // YAHOO.log("className = " + clickTargetElement.className, "info");
            var nodeName = clickTargetElement.nodeName.toLowerCase();

            if ((nodeName == 'a' || nodeName == 'select') && anchor_matches) {
                gr.openField(clickTargetElement);
                break;

            // If clicked anywhere else in the cell, default to editing the grade, not the feedback
            } else if (clickTargetElement.nodeName.toLowerCase() == "td" && clickTargetElement.id.match(/gradecell/)) {
                anchors = YAHOO.util.Dom.getElementsByClassName("editable", "a", clickTargetElement);
                if (anchors.length > 0) {
                    clickTargetElement = anchors[0];
                } else {
                    break;
                }
            } else {
                clickTargetElement = clickTargetElement.parentNode;
            }
        }
    }

    YAHOO.util.Event.on("grade-report-grader-index", "click", this.clickHandler);
    YAHOO.util.Event.on(document, "keydown", this.keyHandler, this, true);
};

YAHOO.util.Event.onDOMReady(YAHOO.grader_report.init);

// ]]>
</script>
