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
 * Contain the logic for the save/cancel modal.
 *
 * @module     core_calendar/modal_delete
 * @class      modal_delete
 * @package    core_calendar
 * @copyright  2017 Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
define([
    'jquery',
    'core/notification',
    'core/custom_interaction_events',
    'core/modal',
    'core/modal_events',
    'core/modal_registry',
    'core_calendar/events',
],
function(
    $,
    Notification,
    CustomEvents,
    Modal,
    ModalEvents,
    ModalRegistry,
    CalendarEvents
) {

    var registered = false;
    var SELECTORS = {
        DELETE_ONE_BUTTON: '[data-action="deleteone"]',
        DELETE_ALL_BUTTON: '[data-action="deleteall"]',
        CANCEL_BUTTON: '[data-action="cancel"]',
    };

    /**
     * Constructor for the Modal.
     *
     * @param {object} root The root jQuery element for the modal
     */
    var ModalDelete = function(root) {
        Modal.call(this, root);

        this.setRemoveOnClose(true);
    };

    ModalDelete.TYPE = 'core_calendar-modal_delete';
    ModalDelete.prototype = Object.create(Modal.prototype);
    ModalDelete.prototype.constructor = ModalDelete;

    /**
     * Set up all of the event handling for the modal.
     *
     * @method registerEventListeners
     */
    ModalDelete.prototype.registerEventListeners = function() {
        // Apply parent event listeners.
        Modal.prototype.registerEventListeners.call(this);

        this.getModal().on(CustomEvents.events.activate, SELECTORS.DELETE_ONE_BUTTON, function(e, data) {
            var saveEvent = $.Event(ModalEvents.save);
            this.getRoot().trigger(saveEvent, this);

            if (!saveEvent.isDefaultPrevented()) {
                this.hide();
                data.originalEvent.preventDefault();
            }
        }.bind(this));

        this.getModal().on(CustomEvents.events.activate, SELECTORS.DELETE_ALL_BUTTON, function(e, data) {
            var saveEvent = $.Event(CalendarEvents.deleteAll);
            this.getRoot().trigger(saveEvent, this);

            if (!saveEvent.isDefaultPrevented()) {
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
        ModalRegistry.register(ModalDelete.TYPE, ModalDelete, 'calendar/event_delete_modal');
        registered = true;
    }

    return ModalDelete;
});
