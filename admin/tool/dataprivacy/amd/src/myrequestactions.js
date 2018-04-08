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
 * AMD module to enable users to manage their own data requests.
 *
 * @module     tool_dataprivacy/myrequestactions
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
    'core/templates'],
function($, Ajax, Notification, Str, ModalFactory, ModalEvents, Templates) {

    /**
     * List of action selectors.
     *
     * @type {{CANCEL_REQUEST: string}}
     * @type {{CONTACT_DPO: string}}
     */
    var ACTIONS = {
        CANCEL_REQUEST: '[data-action="cancel"]',
        CONTACT_DPO: '[data-action="contactdpo"]',
    };

    /**
     * MyRequestActions class.
     */
    var MyRequestActions = function() {
        this.registerEvents();
    };

    /**
     * Register event listeners.
     */
    MyRequestActions.prototype.registerEvents = function() {
        $(ACTIONS.CANCEL_REQUEST).click(function(e) {
            e.preventDefault();

            var requestId = $(this).data('requestid');
            var stringkeys = [
                {
                    key: 'cancelrequest',
                    component: 'tool_dataprivacy'
                },
                {
                    key: 'cancelrequestconfirmation',
                    component: 'tool_dataprivacy'
                }
            ];

            Str.get_strings(stringkeys).then(function(langStrings) {
                var title = langStrings[0];
                var confirmMessage = langStrings[1];
                return ModalFactory.create({
                    title: title,
                    body: confirmMessage,
                    type: ModalFactory.types.SAVE_CANCEL
                }).then(function(modal) {
                    modal.setSaveButtonText(title);

                    // Handle save event.
                    modal.getRoot().on(ModalEvents.save, function() {
                        // Cancel the request.
                        var params = {
                            'requestid': requestId
                        };

                        var request = {
                            methodname: 'tool_dataprivacy_cancel_data_request',
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
                });
            }).done(function(modal) {
                // Show the modal!
                modal.show();

            }).fail(Notification.exception);
        });

        $(ACTIONS.CONTACT_DPO).click(function(e) {
            e.preventDefault();

            var replyToEmail = $(this).data('replytoemail');

            var keys = [
                {
                    key: 'contactdataprotectionofficer',
                    component: 'tool_dataprivacy'
                },
                {
                    key: 'send',
                    component: 'tool_dataprivacy'
                },
            ];

            var sendButtonText = '';
            Str.get_strings(keys).then(function(langStrings) {
                var modalTitle = langStrings[0];
                sendButtonText = langStrings[1];
                var context = {
                    'replytoemail': replyToEmail
                };
                return ModalFactory.create({
                    title: modalTitle,
                    body: Templates.render('tool_dataprivacy/contact_dpo', context),
                    type: ModalFactory.types.SAVE_CANCEL,
                    large: true
                });
            }).done(function(modal) {
                modal.setSaveButtonText(sendButtonText);

                // Handle send event.
                modal.getRoot().on(ModalEvents.save, function(e) {
                    var message = $('#message').val().trim();
                    if (message.length === 0) {
                        e.preventDefault();
                        // Show validation error when the message is empty.
                        $('[data-region="messageinput"]').addClass('has-danger notifyproblem');
                        $('#id_error_message').removeAttr('hidden');
                    } else {
                        // Send the message.
                        sendMessageToDPO(message);
                    }
                });

                // Handle hidden event.
                modal.getRoot().on(ModalEvents.hidden, function() {
                    // Destroy when hidden.
                    modal.destroy();
                });

                // Show the modal!
                modal.show();
            }).fail(Notification.exception);
        });
    };

    /**
     * Send message to the Data Protection Officer.
     *
     * @param {String} message The message to send.
     */
    function sendMessageToDPO(message) {
        var request = {
            methodname: 'tool_dataprivacy_contact_dpo',
            args: {
                message: message
            }
        };

        var requestType = 'success';
        Ajax.call([request])[0].then(function(data) {
            if (data.result) {
                return Str.get_string('requestsubmitted', 'tool_dataprivacy');
            }
            requestType = 'error';
            return data.warnings.join('<br>');

        }).done(function(message) {
            Notification.addNotification({
                message: message,
                type: requestType
            });

        }).fail(Notification.exception);
    }

    return /** @alias module:tool_dataprivacy/myrequestactions */ {
        // Public variables and functions.

        /**
         * Initialise the unified user filter.
         *
         * @method init
         * @return {MyRequestActions}
         */
        'init': function() {
            return new MyRequestActions();
        }
    };
});
