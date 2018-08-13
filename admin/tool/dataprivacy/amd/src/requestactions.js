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
     * @type {{MARK_COMPLETE: string}}
     */
    var ACTIONS = {
        APPROVE_REQUEST: '[data-action="approve"]',
        DENY_REQUEST: '[data-action="deny"]',
        VIEW_REQUEST: '[data-action="view"]',
        MARK_COMPLETE: '[data-action="complete"]'
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
            $.when(promises[0]).then(function(data) {
                if (data.result) {
                    return data.result;
                }
                // Fail.
                Notification.addNotification({
                    message: data.warnings[0].message,
                    type: 'error'
                });
                return false;

            }).then(function(data) {
                // Determine the type of modal to show.
                var modalType = ModalFactory.types.DEFAULT;
                if (data.approvedeny) {
                    modalType = ModalDataRequest.TYPE;
                } else if (data.canmarkcomplete) {
                    modalType = ModalDataRequest.TYPE_ENQUIRY;
                }
                var body = Templates.render('tool_dataprivacy/request_details', data);
                return ModalFactory.create({
                    title: data.typename,
                    body: body,
                    type: modalType,
                    large: true
                });

            }).then(function(modal) {
                // Handle approve event.
                modal.getRoot().on(DataPrivacyEvents.approve, function() {
                    showConfirmation(DataPrivacyEvents.approve, requestId);
                });

                // Handle deny event.
                modal.getRoot().on(DataPrivacyEvents.deny, function() {
                    showConfirmation(DataPrivacyEvents.deny, requestId);
                });

                // Handle send event.
                modal.getRoot().on(DataPrivacyEvents.complete, function() {
                    var params = {
                        'requestid': requestId
                    };
                    handleSave('tool_dataprivacy_mark_complete', params);
                });

                // Handle hidden event.
                modal.getRoot().on(ModalEvents.hidden, function() {
                    // Destroy when hidden.
                    modal.destroy();
                });

                // Show the modal!
                modal.show();

                return;

            }).catch(Notification.exception);
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

        $(ACTIONS.MARK_COMPLETE).click(function(e) {
            e.preventDefault();
            showConfirmation(DataPrivacyEvents.complete, $(this).data('requestid'));
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
        var params = {
            'requestid': requestId
        };
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
            case DataPrivacyEvents.complete:
                keys = [
                    {
                        key: 'markcomplete',
                        component: 'tool_dataprivacy'
                    },
                    {
                        key: 'confirmcompletion',
                        component: 'tool_dataprivacy'
                    }
                ];
                wsfunction = 'tool_dataprivacy_mark_complete';
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
                handleSave(wsfunction, params);
            });

            // Handle hidden event.
            modal.getRoot().on(ModalEvents.hidden, function() {
                // Destroy when hidden.
                modal.destroy();
            });

            modal.show();

            return;

        }).catch(Notification.exception);
    }

    /**
     * Calls a web service function and reloads the page on success and shows a notification.
     * Displays an error notification, otherwise.
     *
     * @param {String} wsfunction The web service function to call.
     * @param {Object} params The parameters for the web service functoon.
     */
    function handleSave(wsfunction, params) {
        // Confirm the request.
        var request = {
            methodname: wsfunction,
            args: params
        };

        Ajax.call([request])[0].done(function(data) {
            if (data.result) {
                // On success, reload the page so that the data request table will be updated.
                // TODO: Probably in the future, better to reload the table or the target data request via AJAX.
                window.location.reload();
            } else {
                // Add the notification.
                Notification.addNotification({
                    message: data.warnings[0].message,
                    type: 'error'
                });
            }
        }).fail(Notification.exception);
    }

    return RequestActions;
});
