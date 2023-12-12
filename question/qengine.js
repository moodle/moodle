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
 * JavaScript required by the question engine.
 *
 * @package    moodlecore
 * @subpackage questionengine
 * @copyright  2008 The Open University
 * @deprecated since Moodle 4.0
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


M.core_scroll_manager = M.core_scroll_manager || {};

// TODO Remove the scroll manager and deprecation layer in 4.6 MDL-76685.
/* eslint-disable */
var loadedPromise = new Promise(function(resolve) {
    require(['core/scroll_manager'], function(ScrollManager) {
        var transitionLayer = {};

        var deprecatedNotice = function(functionName, newFunctionName) {
            window.console.error(
                "The " + functionName + " function has been deprecated. " +
                "Please use core/scroll_manager::" + newFunctionName + "() instead"
            );
        };

        transitionLayer.save_scroll_pos = function(Y, element) {
            deprecatedNotice('save_scroll_pos', 'saveScrollPos');
            ScrollManager.saveScrollPos(element);
        };

        transitionLayer.scroll_to_saved_pos = function() {
            deprecatedNotice('scroll_to_saved_pos', 'scrollToSavedPosition');
            ScrollManager.scrollToSavedPosition();
        };

        M.core_scroll_manager = transitionLayer;

        resolve(transitionLayer);
    });
});

var callPromisedFunction = function(functionName, args) {
    loadedPromise.then(function(transitionLayer) {
        transitionLayer[functionName].apply(null, args);
    });
};

if (!M.core_scroll_manager.save_scroll_pos) {
    // Note: This object is short lived.
    // It only lives until the new scroll manager is loaded, at which point it is replaced.

    /**
     * In the form that contains the element, set the value of the form field with
     * name scrollpos to the current scroll position. If there is no element with
     * that name, it creates a hidden form field with that name within the form.
     * @deprecated since Moodle 4.0
     * @see core/scroll_manager
     * @param element the element in the form. Should be something that can be
     *      passed to Y.one.
     */
    M.core_scroll_manager.save_scroll_pos = function(Y, element) {
        callPromisedFunction(M.core_scroll_manager.save_scroll_pos, [Y, element]);
    };

    /**
     * Event handler that can be used on a link. Assumes that the link already
     * contains at least one URL parameter.
     * @deprecated since Moodle 4.0
     * @see core/scroll_manager
     */
    M.core_scroll_manager.save_scroll_action = function() {
        Y.log("The scroll_to_saved_pos function has been deprecated. " +
            "Please use initLinksScrollPos in core/scroll_manager instead.", 'moodle-core-notification', 'warn');
    };

    /**
     * If there is a parameter like scrollpos=123 in the URL, scroll to that saved position.
     * @deprecated since Moodle 4.0
     * @see core/scroll_manager
     * @todo Final deprecation on Moodle 4.4 MDL-72438
     */
    M.core_scroll_manager.scroll_to_saved_pos = function(Y) {
        callPromisedFunction(M.core_scroll_manager.scroll_to_saved_pos, Y);
    };
}
/* eslint-enable */

M.core_question_engine = M.core_question_engine || {};

/**
 * Flag used by M.core_question_engine.prevent_repeat_submission.
 */
M.core_question_engine.questionformalreadysubmitted = false;

/**
 * Initialise a question submit button. This saves the scroll position and
 * sets the fragment on the form submit URL so the page reloads in the right place.
 * @deprecated since Moodle 4.0
 * @see core_question/question_engine
 * @param button the id of the button in the HTML.
 */
M.core_question_engine.init_submit_button = function(Y, button) {
    Y.log("The core_question_engine.init_submit_button function has been deprecated. " +
        "Please use initSubmitButton in core_question/question_engine instead.", 'moodle-core-notification', 'warn');

    require(['core_form/submit'], function(submit) {
        submit.init(button);
    });
    var totalQuestionsInPage = document.querySelectorAll('div.que').length;
    var buttonel = document.getElementById(button);
    var outeruniqueid = buttonel.closest('.que').id;
    Y.on('click', function(e) {
        M.core_scroll_manager.save_scroll_pos(Y, button);
        if (totalQuestionsInPage > 1) {
            // Only change the form action if the page have more than one question.
            buttonel.form.action = buttonel.form.action + '#' + outeruniqueid;
        }
    }, buttonel);
}

/**
 * Initialise a form that contains questions printed using print_question.
 * This has the effect of:
 * 1. Turning off browser autocomlete.
 * 2. Stopping enter from submitting the form (or toggling the next flag) unless
 *    keyboard focus is on the submit button or the flag.
 * 3. Removes any '.questionflagsavebutton's, since we have JavaScript to toggle
 *    the flags using ajax.
 * 4. Scroll to the position indicated by scrollpos= in the URL, if it is there.
 * 5. Prevent the user from repeatedly submitting the form.
 * @param Y the Yahoo object. Needs to have the DOM and Event modules loaded.
 * @param form something that can be passed to Y.one, to find the form element.
 * @deprecated since Moodle 4.0
 * @see core_question/question_engine
 * @todo Final deprecation on Moodle 4.4 MDL-72438
 */
M.core_question_engine.init_form = function(Y, form) {
    Y.log("The core_question_engine.init_form function has been deprecated. " +
        "Please use init_form in core_question/question_engine instead.", 'moodle-core-notification', 'warn');

    Y.one(form).setAttribute('autocomplete', 'off');

    Y.on('submit', M.core_question_engine.prevent_repeat_submission, form, form, Y);

    Y.on('key', function (e) {
        if (!e.target.test('a') && !e.target.test('input[type=submit]') &&
                !e.target.test('input[type=img]') && !e.target.test('textarea') && !e.target.test('[contenteditable=true]')) {
            e.preventDefault();
        }
    }, form, 'press:13');

    Y.one(form).all('.questionflagsavebutton').remove();

    M.core_scroll_manager.scroll_to_saved_pos(Y);
}

/**
 * Event handler to stop a question form being submitted more than once.
 * @param e the form submit event.
 * @param form the form element.
 * @deprecated since Moodle 4.0
 * @see core_question/question_engine
 * @todo Final deprecation on Moodle 4.4 MDL-72438
 */
M.core_question_engine.prevent_repeat_submission = function(e, Y) {
    Y.log("The prevent_repeat_submission function has been deprecated. " +
        "Please use preventRepeatSubmission in core_question/question_engine instead.", 'moodle-core-notification', 'warn');

    if (M.core_question_engine.questionformalreadysubmitted) {
        e.halt();
        return;
    }

    setTimeout(function() {
        Y.all('input[type=submit]').set('disabled', true);
    }, 0);
    M.core_question_engine.questionformalreadysubmitted = true;
}
