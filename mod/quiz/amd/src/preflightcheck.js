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
 * This class manages the confirmation pop-up (also called the pre-flight check)
 * that is sometimes shown when a use clicks the start attempt button.
 *
 * This is also responsible for opening the pop-up window, if the quiz requires to be in one.
 *
 * @module    mod_quiz/preflightcheck
 * @copyright 2016 The Open University
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since     3.1
 */
define(['jquery', 'core/yui', 'core_form/changechecker'], function($, Y, FormChangeChecker) {

    /**
     * @alias module:mod_quiz/preflightcheck
     */
    var t = {
        confirmDialogue: null,

        /**
         * Initialise the start attempt button.
         *
         * @param {String} startButton the id of the start attempt button that we will be enhancing.
         * @param {String} confirmationTitle the title of the dialogue.
         * @param {String} confirmationForm selector for the confirmation form to show in the dialogue.
         * @param {String} popupoptions If not null, the quiz should be launced in a pop-up.
         */
        init: function(startButton, confirmationTitle, confirmationForm, popupoptions) {
            var finalStartButton = startButton;

            Y.use('moodle-core-notification', function() {
                if (Y.one(confirmationForm)) {
                    t.confirmDialogue = new M.core.dialogue({
                        headerContent: confirmationTitle,
                        bodyContent: Y.one(confirmationForm),
                        draggable: true,
                        visible: false,
                        center: true,
                        modal: true,
                        width: null,
                        extraClasses: ['mod_quiz_preflight_popup']
                    });

                    Y.one(startButton).on('click', t.displayDialogue);
                    Y.one('#id_cancel').on('click', t.hideDialogue);

                    finalStartButton = t.confirmDialogue.get('boundingBox').one('[name="submitbutton"]');
                }

                if (popupoptions) {
                    Y.one(finalStartButton).on('click', t.launchQuizPopup, t, popupoptions);
                }
            });
        },

        /**
         * Display the dialogue.
         * @param {Y.EventFacade} e the event being responded to, if any.
         */
        displayDialogue: function(e) {
            if (e) {
                e.halt();
            }
            t.confirmDialogue.show();
        },

        /**
         * Hide the dialogue.
         * @param {Y.EventFacade} e the event being responded to, if any.
         */
        hideDialogue: function(e) {
            if (e) {
                e.halt();
            }
            t.confirmDialogue.hide(e);
        },

        /**
         * Event handler for the quiz start attempt button.
         * @param {Event} e the event being responded to
         * @param {Object} popupoptions
         */
        launchQuizPopup: function(e, popupoptions) {
            e.halt();
            Y.use('io-form', function() {
                var form = e.target.ancestor('form');

                FormChangeChecker.resetFormDirtyState(form.getDOMNode());
                window.openpopup(e, {
                    url: form.get('action') + '?' + Y.IO.stringify(form).replace(/\bcancel=/, 'x='),
                    windowname: 'quizpopup',
                    options: popupoptions,
                    fullscreen: true,
                });
            });
        }
    };

    return t;
});
