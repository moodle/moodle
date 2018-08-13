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
 * @module     tool_dataprivacy/data_request_modal
 * @package    tool_dataprivacy
 * @copyright  2018 Jun Pataleta
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
define(['jquery', 'core/notification', 'core/custom_interaction_events', 'core/modal', 'core/modal_registry',
        'tool_dataprivacy/events'],
    function($, Notification, CustomEvents, Modal, ModalRegistry, DataPrivacyEvents) {

        var registered = false;
        var SELECTORS = {
            APPROVE_BUTTON: '[data-action="approve"]',
            DENY_BUTTON: '[data-action="deny"]',
            COMPLETE_BUTTON: '[data-action="complete"]'
        };

        /**
         * Constructor for the Modal.
         *
         * @param {object} root The root jQuery element for the modal
         */
        var ModalDataRequest = function(root) {
            Modal.call(this, root);
        };

        ModalDataRequest.TYPE = 'tool_dataprivacy-data_request';
        ModalDataRequest.TYPE_ENQUIRY = 'tool_dataprivacy-complete_request';
        ModalDataRequest.prototype = Object.create(Modal.prototype);
        ModalDataRequest.prototype.constructor = ModalDataRequest;

        /**
         * Set up all of the event handling for the modal.
         *
         * @method registerEventListeners
         */
        ModalDataRequest.prototype.registerEventListeners = function() {
            // Apply parent event listeners.
            Modal.prototype.registerEventListeners.call(this);

            this.getModal().on(CustomEvents.events.activate, SELECTORS.APPROVE_BUTTON, function(e, data) {
                var approveEvent = $.Event(DataPrivacyEvents.approve);
                this.getRoot().trigger(approveEvent, this);

                if (!approveEvent.isDefaultPrevented()) {
                    this.hide();
                    data.originalEvent.preventDefault();
                }
            }.bind(this));

            this.getModal().on(CustomEvents.events.activate, SELECTORS.DENY_BUTTON, function(e, data) {
                var denyEvent = $.Event(DataPrivacyEvents.deny);
                this.getRoot().trigger(denyEvent, this);

                if (!denyEvent.isDefaultPrevented()) {
                    this.hide();
                    data.originalEvent.preventDefault();
                }
            }.bind(this));

            this.getModal().on(CustomEvents.events.activate, SELECTORS.COMPLETE_BUTTON, function(e, data) {
                var completeEvent = $.Event(DataPrivacyEvents.complete);
                this.getRoot().trigger(completeEvent, this);

                if (!completeEvent.isDefaultPrevented()) {
                    this.hide();
                    data.originalEvent.preventDefault();
                }
            }.bind(this));
        };

        // Automatically register with the modal registry the first time this module is imported so that you can create modals
        // of this type using the modal factory.
        if (!registered) {
            ModalRegistry.register(ModalDataRequest.TYPE, ModalDataRequest, 'tool_dataprivacy/data_request_modal');
            ModalRegistry.register(ModalDataRequest.TYPE_ENQUIRY, ModalDataRequest, 'tool_dataprivacy/mark_request_complete_modal');
            registered = true;
        }

        return ModalDataRequest;
    });
