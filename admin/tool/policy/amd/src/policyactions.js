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
 * Policy actions.
 *
 * @module     tool_policy/policyactions
 * @package    tool_policy
 * @copyright  2018 Sara Arjona (sara@moodle.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
define([
    'jquery',
    'core/ajax',
    'core/notification',
    'core/modal_factory',
    'core/modal_events'],
function($, Ajax, Notification, ModalFactory, ModalEvents) {

    /**
     * List of action selectors.
     *
     * @type {{VIEW_POLICY: string}}
     */
    var ACTIONS = {
        VIEW_POLICY: '[data-action="view"]'
    };

    /**
     * PolicyActions class.
     */
    var PolicyActions = function() {
        this.registerEvents();
    };

    /**
     * Register event listeners.
     */
    PolicyActions.prototype.registerEvents = function() {
        $(ACTIONS.VIEW_POLICY).click(function(e) {
            e.preventDefault();

            var versionid = $(this).data('versionid');
            var behalfid = $(this).data('behalfid');

            var params = {
                'versionid': versionid,
                'behalfid': behalfid
            };

            var request = {
                methodname: 'tool_policy_get_policy_version',
                args: params
            };

            var promises = Ajax.call([request]);
            var modalTitle = '';
            var modalType = ModalFactory.types.DEFAULT;
            $.when(promises[0]).then(function(data) {
                if (data.result.policy) {
                    modalTitle = data.result.policy.name;
                    return data.result.policy.content;
                }
                // Fail.
                Notification.addNotification({
                    message: data.warnings[0].message,
                    type: 'error'
                });
                return false;

            }).then(function(html) {
                if (html != false) {
                    return ModalFactory.create({
                        title: modalTitle,
                        body: html,
                        type: modalType,
                        large: true
                    }).then(function(modal) {
                        // Handle hidden event.
                        modal.getRoot().on(ModalEvents.hidden, function() {
                            // Destroy when hidden.
                            modal.destroy();
                        });

                        return modal;
                    });
                }
                return false;
            }).done(function(modal) {
                // Show the modal.
                modal.show();
            }).fail(Notification.exception);
        });

    };

    return /** @alias module:tool_policy/policyactions */ {
        // Public variables and functions.

        /**
         * Initialise the actions helper.
         *
         * @method init
         * @return {PolicyActions}
         */
        'init': function() {
            return new PolicyActions();
        }
    };
});
