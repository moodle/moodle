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
}
/* eslint-enable */

M.core_question_engine = M.core_question_engine || {};

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
