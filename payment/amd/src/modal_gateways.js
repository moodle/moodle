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
 * Contain the logic for the gateways modal: A modal with proceed and cancel buttons.
 *
 * @module     core_payment/modal_gateways
 * @package    core_payment
 * @copyright  2020 Shamim Rezaie <shamim@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define([
    'jquery',
    'core/notification',
    'core/custom_interaction_events',
    'core/modal',
    'core/modal_events',
    'core_payment/events',
    'core/modal_registry'
],
function(
    $,
    Notification,
    CustomEvents,
    Modal,
    ModalEvents,
    PaymentEvents,
    ModalRegistry
) {

    var registered = false;
    var SELECTORS = {
        PROCEED_BUTTON: '[data-action="proceed"]',
        CANCEL_BUTTON: '[data-action="cancel"]',
    };

    /**
     * Constructor for the Modal.
     *
     * @param {object} root The root jQuery element for the modal
     */
    var ModalGateways = function(root) {
        Modal.call(this, root);
    };

    ModalGateways.TYPE = 'core_payment-modal_gateways';
    ModalGateways.prototype = Object.create(Modal.prototype);
    ModalGateways.prototype.constructor = ModalGateways;

    /**
     * Set up all of the event handling for the modal.
     *
     * @method registerEventListeners
     */
    ModalGateways.prototype.registerEventListeners = function() {
        // Apply parent event listeners.
        Modal.prototype.registerEventListeners.call(this);

        this.getModal().on(CustomEvents.events.activate, SELECTORS.PROCEED_BUTTON, function(e, data) {
            var proceedEvent = $.Event(PaymentEvents.proceed);
            this.getRoot().trigger(proceedEvent, this);

            if (!proceedEvent.isDefaultPrevented()) {
                this.hide();
                data.originalEvent.preventDefault();
            }
        }.bind(this));

        this.getModal().on(CustomEvents.events.activate, SELECTORS.CANCEL_BUTTON, function(e, data) {
            var cancelEvent = $.Event(ModalEvents.cancel);
            this.getRoot().trigger(cancelEvent, this);

            if (!cancelEvent.isDefaultPrevented()) {
                this.hide();
                data.originalEvent.preventDefault();
            }
        }.bind(this));
    };

    // Automatically register with the modal registry the first time this module is imported so that you can create modals
    // of this type using the modal factory.
    if (!registered) {
        ModalRegistry.register(ModalGateways.TYPE, ModalGateways, 'core_payment/modal_gateways');
        registered = true;
    }

    return ModalGateways;
});
