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
 * Request actions.
 *
 * @module     tool_dataprivacy/requestactions
 * @package    tool_dataprivacy
 * @copyright  2018 Jun Pataleta
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
define([
    'jquery',
    'core/ajax',
    'core/notification',
    'core/str',
    'core/modal_factory',
    'core/modal_events',
    'core/templates',
    'tool_dataprivacy/data_request_modal',
    'tool_dataprivacy/events'],
function($, Ajax, Notification, Str, ModalFactory, ModalEvents, Templates, ModalDataRequest, DataPrivacyEvents) {

    /**
     * List of action selectors.
     *
     * @type {{APPROVE_REQUEST: string}}
     * @type {{DENY_REQUEST: string}}
     * @type {{VIEW_REQUEST: string}}
     */
    var ACTIONS = {
        APPROVE_REQUEST: '[data-action="approve"]',
        DENY_REQUEST: '[data-action="deny"]',
        VIEW_REQUEST: '[data-action="view"]'
    };

    /**
     * RequestActions class.
     */
    var RequestActions = function() {
        this.registerEvents();
    };

    /**
     * Register event listeners.
     */
    RequestActions.prototype.registerEvents = function() {
        $(ACTIONS.VIEW_REQUEST).click(function(e) {
            e.preventDefault();

            var requestId = $(this).data('requestid');

            // Cancel the request.
            var params = {
                'requestid': requestId
            };

            var request = {
                methodname: 'tool_dataprivacy_get_data_request',
                args: params
            };

            var promises = Ajax.call([request]);
            var modalTitle = '';
            var modalType = ModalFactory.types.DEFAULT;
            $.when(promises[0]).then(function(data) {
                if (data.result) {
                    // Check if the status is awaiting approval.
                    if (data.result.status == 2) {
                        modalType = ModalDataRequest.TYPE;
                    }
                    modalTitle = data.result.typename;
                    return Templates.render('tool_dataprivacy/request_details', data.result);
                }
                // Fail.
                Notification.addNotification({
                    message: data.warnings[0].message,
                    type: 'error'
                });
                return false;

            }).then(function(html) {
                return ModalFactory.create({
                    title: modalTitle,
                    body: html,
                    type: modalType,
                    large: true
                }).then(function(modal) {
                    // Handle approve event.
                    modal.getRoot().on(DataPrivacyEvents.approve, function() {
                        showConfirmation(DataPrivacyEvents.approve, requestId);
                    });

                    // Handle deny event.
                    modal.getRoot().on(DataPrivacyEvents.deny, function() {
                        showConfirmation(DataPrivacyEvents.deny, requestId);
                    });

                    // Handle hidden event.
                    modal.getRoot().on(ModalEvents.hidden, function() {
                        // Destroy when hidden.
                        modal.destroy();
                    });

                    return modal;
                });
            }).done(function(modal) {
                // Show the modal!
                modal.show();
            }).fail(Notification.exception);
        });

        $(ACTIONS.APPROVE_REQUEST).click(function(e) {
            e.preventDefault();

            var requestId = $(this).data('requestid');
            showConfirmation(DataPrivacyEvents.approve, requestId);
        });

        $(ACTIONS.DENY_REQUEST).click(function(e) {
            e.preventDefault();

            var requestId = $(this).data('requestid');
            showConfirmation(DataPrivacyEvents.deny, requestId);
        });
    };

    /**
     * Show the confirmation dialogue.
     *
     * @param {String} action The action name.
     * @param {Number} requestId The request ID.
     */
    function showConfirmation(action, requestId) {
        var keys = [];
        var wsfunction = '';
        switch (action) {
            case DataPrivacyEvents.approve:
                keys = [
                    {
                        key: 'approverequest',
                        component: 'tool_dataprivacy'
                    },
                    {
                        key: 'confirmapproval',
                        component: 'tool_dataprivacy'
                    }
                ];
                wsfunction = 'tool_dataprivacy_approve_data_request';
                break;
            case DataPrivacyEvents.deny:
                keys = [
                    {
                        key: 'denyrequest',
                        component: 'tool_dataprivacy'
                    },
                    {
                        key: 'confirmdenial',
                        component: 'tool_dataprivacy'
                    }
                ];
                wsfunction = 'tool_dataprivacy_deny_data_request';
                break;
        }

        var modalTitle = '';
        Str.get_strings(keys).then(function(langStrings) {
            modalTitle = langStrings[0];
            var confirmMessage = langStrings[1];
            return ModalFactory.create({
                title: modalTitle,
                body: confirmMessage,
                type: ModalFactory.types.SAVE_CANCEL
            });
        }).then(function(modal) {
            modal.setSaveButtonText(modalTitle);

            // Handle save event.
            modal.getRoot().on(ModalEvents.save, function() {
                // Confirm the request.
                var params = {
                    'requestid': requestId
                };

                var request = {
                    methodname: wsfunction,
                    args: params
                };

                Ajax.call([request])[0].done(function(data) {
                    if (data.result) {
                        window.location.reload();
                    } else {
                        Notification.addNotification({
                            message: data.warnings[0].message,
                            type: 'error'
                        });
                    }
                }).fail(Notification.exception);
            });

            // Handle hidden event.
            modal.getRoot().on(ModalEvents.hidden, function() {
                // Destroy when hidden.
                modal.destroy();
            });

            return modal;
        }).done(function(modal) {
            modal.show();
        }).fail(Notification.exception);
    }

    return RequestActions;
});
