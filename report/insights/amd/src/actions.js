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
 * Module to manage report insights actions that are executed using AJAX.
 *
 * @package    report_insights
 * @copyright  2017 David Monllao {@link http://www.davidmonllao.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * This module manages prediction actions that require AJAX requests.
 *
 * @module report_insights/actions
 */
define(['jquery', 'core/str', 'core/ajax', 'core/notification', 'core/url', 'core/modal_factory', 'core/modal_events'],
        function($, Str, Ajax, Notification, Url, ModalFactory, ModalEvents) {

    return {

        /**
         * Attach on click handlers for bulk actions.
         *
         * @param {String} rootNode
         * @access public
         */
        initBulk: function(rootNode) {

            /**
             * Executes the provided action.
             *
             * @param  {Array}  predictionIds
             * @param  {Array}  predictionContainers
             * @param  {String} actionName
             * @return {Promise}
             */
            var executeAction = function(predictionIds, predictionContainers, actionName) {

                return Ajax.call([
                    {
                        methodname: 'report_insights_action_executed',
                        args: {
                            predictionids: predictionIds,
                            actionname: actionName
                        }
                    }
                ])[0].then(function() {
                    // Remove the selected elements from the list.

                    var tableNode = false;
                    predictionContainers.forEach(function(el) {
                        if (tableNode === false) {
                            tableNode = el.closest('table');
                        }
                        el.remove();
                    });

                    if (tableNode.find('tbody > tr').length === 0) {
                        let params = {
                            contextid: tableNode.closest('div.insight-container').data('context-id'),
                            modelid: tableNode.closest('div.insight-container').data('model-id')
                        };
                        window.location.assign(Url.relativeUrl("report/insights/insights.php", params, false));
                    }
                    return;
                }).catch(Notification.exception);
            };

            $(rootNode + ' [data-bulk-actionname]').on('click', function(e) {
                e.preventDefault();
                var action = $(e.currentTarget);
                var actionName = action.data('bulk-actionname');
                var actionVisibleName = action.text().trim();

                var predictionIds = [];
                var predictionContainers = [];

                $('.insights-list input[data-togglegroup^="insight-bulk-action-"][data-toggle="slave"]:checked').each(function() {
                    var container = $(this).closest('tr[data-prediction-id]');
                    predictionContainers.push(container);
                    predictionIds.push(container.data('prediction-id'));
                });

                if (predictionIds.length === 0) {
                    // No items selected message.
                    return this;
                }

                var strings = [];
                Str.get_strings([{
                    key: 'confirmbulkaction',
                    component: 'report_insights',
                    param: {
                        action: actionVisibleName,
                        nitems: predictionIds.length
                    }
                }, {
                    key: 'confirm',
                    component: 'moodle'
                }]
                ).then(function(strs) {
                    strings = strs;
                    return ModalFactory.create({
                        type: ModalFactory.types.SAVE_CANCEL,
                        title: actionVisibleName,
                        body: strings[0],
                    });
                }).then(function(modal) {
                    modal.setSaveButtonText(strings[1]);
                    modal.show();
                    modal.getRoot().on(ModalEvents.save, function() {
                        // The action is now confirmed, sending an action for it.
                        return executeAction(predictionIds, predictionContainers, actionName);
                    });

                    return modal;
                }).catch(Notification.exception);

                return this;
            });
        },
    };
});
