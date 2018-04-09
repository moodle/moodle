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
 * AMD module for model actions confirmation.
 *
 * @module     tool_analytics/model
 * @copyright  2017 David Monllao
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
define(['jquery', 'core/str', 'core/log', 'core/notification', 'core/modal_factory', 'core/modal_events'],
    function($, Str, log, Notification, ModalFactory, ModalEvents) {

    /**
     * List of actions that require confirmation and confirmation message.
     */
    var actionsList = {
        clear: {
            title: {
                key: 'clearpredictions',
                component: 'tool_analytics'
            }, body: {
                key: 'clearmodelpredictions',
                component: 'tool_analytics'
            }

        }
    };

    /**
     * Returns the model name.
     *
     * @param {Object} actionItem The action item DOM node.
     * @return {String}
     */
    var getModelName = function(actionItem) {
        return $(actionItem.closest('tr')[0]).find('span.target-name').text();
    };

    /** @alias module:tool_analytics/model */
    return {

        /**
         * Displays a confirm modal window before executing the action.
         *
         * @param {String} actionId
         * @param {String} actionType
         */
        confirmAction: function(actionId, actionType) {
            $('[data-action-id="' + actionId + '"]').on('click', function(ev) {
                ev.preventDefault();

                var a = $(ev.currentTarget);

                if (typeof actionsList[actionType] === "undefined") {
                    log.error('Action "' + actionType + '" is not allowed.');
                    return;
                }

                var reqStrings = [
                    actionsList[actionType].title,
                    actionsList[actionType].body
                ];
                reqStrings[1].param = getModelName(a);

                var stringsPromise = Str.get_strings(reqStrings);
                var modalPromise = ModalFactory.create({type: ModalFactory.types.SAVE_CANCEL});

                $.when(stringsPromise, modalPromise).then(function(strings, modal) {
                    modal.setTitle(strings[0]);
                    modal.setBody(strings[1]);
                    modal.setSaveButtonText(strings[0]);
                    modal.getRoot().on(ModalEvents.save, function() {
                        window.location.href = a.attr('href');
                    });
                    modal.show();
                    return modal;
                }).fail(Notification.exception);
            });
        }
    };
});
