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
 * Initialise the an add question modal on the quiz page.
 *
 * @module    mod_quiz/add_question_modal_launcher
 * @package   mod_quiz
 * @copyright 2018 Ryan Wyllie <ryan@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
define(
    [
        'jquery',
        'core/notification',
        'core/modal_factory',
    ],
    function(
        $,
        Notification,
        ModalFactory
    ) {

    return {
        /**
         * Create a modal using the modal factory and add listeners to launch the
         * modal when clicked.
         *
         * @param  {string} modalType Which modal to create
         * @param  {string} selector The selectors for the elements that trigger the modal
         * @param  {int} contextId The current context id
         * @param  {function} preShowCallback A callback to execute before the modal is shown
         * @return {promise} Resolved with the modal
         */
        init: function(modalType, selector, contextId, preShowCallback) {
            var body = $('body');

            // Create a question bank modal using the factory.
            // The same modal will be used by all of the add question
            // links that match "selector" on the page. The content
            // of the modal will be changed depending on which link is
            // clicked.
            return ModalFactory.create(
                {
                    type: modalType,
                    large: true,
                    // This callback executes before the modal is shown when the
                    // trigger element is clicked.
                    preShowCallback: function(triggerElement, modal) {
                        triggerElement = $(triggerElement);
                        modal.setContextId(contextId);
                        modal.setAddOnPageId(triggerElement.attr('data-addonpage'));
                        modal.setTitle(triggerElement.attr('data-header'));

                        if (preShowCallback) {
                            preShowCallback(triggerElement, modal);
                        }
                    }
                },
                // Created a deligated listener rather than a single
                // trigger element.
                [body, selector]
            ).fail(Notification.exception);
        }
    };
});
