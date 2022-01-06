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
 * A javascript module to handle summary modal.
 *
 * @module     core_calendar/summary_modal
 * @copyright  2017 Simey Lameze <simey@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
define([
    'jquery',
    'core/str',
    'core/notification',
    'core/custom_interaction_events',
    'core/modal',
    'core/modal_registry',
    'core/modal_factory',
    'core/modal_events',
    'core_calendar/repository',
    'core_calendar/events',
    'core_calendar/crud',
],
function(
    $,
    Str,
    Notification,
    CustomEvents,
    Modal,
    ModalRegistry,
    ModalFactory,
    ModalEvents,
    CalendarRepository,
    CalendarEvents,
    CalendarCrud
) {

    var registered = false;
    var SELECTORS = {
        ROOT: "[data-region='summary-modal-container']",
        EDIT_BUTTON: '[data-action="edit"]',
        DELETE_BUTTON: '[data-action="delete"]',
    };

    /**
     * Constructor for the Modal.
     *
     * @param {object} root The root jQuery element for the modal
     */
    var ModalEventSummary = function(root) {
        Modal.call(this, root);
    };

    ModalEventSummary.TYPE = 'core_calendar-event_summary';
    ModalEventSummary.prototype = Object.create(Modal.prototype);
    ModalEventSummary.prototype.constructor = ModalEventSummary;

    /**
     * Get the edit button element from the footer. The button is cached
     * as it's not expected to change.
     *
     * @method getEditButton
     * @return {object} button element
     */
    ModalEventSummary.prototype.getEditButton = function() {
        if (typeof this.editButton == 'undefined') {
            this.editButton = this.getFooter().find(SELECTORS.EDIT_BUTTON);
        }

        return this.editButton;
    };

    /**
     * Get the delete button element from the footer. The button is cached
     * as it's not expected to change.
     *
     * @method getDeleteButton
     * @return {object} button element
     */
    ModalEventSummary.prototype.getDeleteButton = function() {
        if (typeof this.deleteButton == 'undefined') {
            this.deleteButton = this.getFooter().find(SELECTORS.DELETE_BUTTON);
        }

        return this.deleteButton;
    };

    /**
     * Get the id for the event being shown in this modal. This value is
     * not cached because it will change depending on which event is
     * being displayed.
     *
     * @method getEventId
     * @return {int}
     */
    ModalEventSummary.prototype.getEventId = function() {
        return this.getBody().find(SELECTORS.ROOT).attr('data-event-id');
    };

    /**
     * Get the title for the event being shown in this modal. This value is
     * not cached because it will change depending on which event is
     * being displayed.
     *
     * @method getEventTitle
     * @return {String}
     */
    ModalEventSummary.prototype.getEventTitle = function() {
        return this.getBody().find(SELECTORS.ROOT).attr('data-event-title');
    };

    /**
     * Get the number of events in the series for the event being shown in
     * this modal. This value is not cached because it will change
     * depending on which event is being displayed.
     *
     * @method getEventCount
     * @return {int}
     */
    ModalEventSummary.prototype.getEventCount = function() {
        return this.getBody().find(SELECTORS.ROOT).attr('data-event-count');
    };

    /**
     * Get the url for the event being shown in this modal.
     *
     * @method getEventUrl
     * @return {String}
     */
    ModalEventSummary.prototype.getEditUrl = function() {
        return this.getBody().find(SELECTORS.ROOT).attr('data-edit-url');
    };

    /**
     * Is this an action event.
     *
     * @method getEventUrl
     * @return {String}
     */
    ModalEventSummary.prototype.isActionEvent = function() {
        return (this.getBody().find(SELECTORS.ROOT).attr('data-action-event') == 'true');
    };

    /**
     * Set up all of the event handling for the modal.
     *
     * @method registerEventListeners
     */
    ModalEventSummary.prototype.registerEventListeners = function() {
        // Apply parent event listeners.
        Modal.prototype.registerEventListeners.call(this);

        // We have to wait for the modal to finish rendering in order to ensure that
        // the data-event-title property is available to use as the modal title.
        M.util.js_pending('core_calendar/summary_modal:registerEventListeners:bodyRendered');
        this.getRoot().on(ModalEvents.bodyRendered, function() {
            this.getModal().data({
                eventTitle: this.getEventTitle(),
                eventId: this.getEventId(),
                eventCount: this.getEventCount(),
            })
            .attr('data-type', 'event');
            CalendarCrud.registerRemove(this.getModal());
            M.util.js_complete('core_calendar/summary_modal:registerEventListeners:bodyRendered');
        }.bind(this));

        $('body').on(CalendarEvents.deleted, function() {
            // Close the dialogue on delete.
            this.hide();
        }.bind(this));

        CustomEvents.define(this.getEditButton(), [
            CustomEvents.events.activate
        ]);

        this.getEditButton().on(CustomEvents.events.activate, function(e, data) {
            if (this.isActionEvent()) {
                // Action events cannot be edited on the event form and must be redirected to the module UI.
                $('body').trigger(CalendarEvents.editActionEvent, [this.getEditUrl()]);
            } else {
                // When the edit button is clicked we fire an event for the calendar UI to handle.
                // We don't care how the UI chooses to handle it.
                $('body').trigger(CalendarEvents.editEvent, [this.getEventId()]);
            }

            // There is nothing else for us to do so let's hide.
            this.hide();

            // We've handled this event so no need to propagate it.
            e.preventDefault();
            e.stopPropagation();
            data.originalEvent.preventDefault();
            data.originalEvent.stopPropagation();
        }.bind(this));
    };

    // Automatically register with the modal registry the first time this module is imported so that you can create modals
    // of this type using the modal factory.
    if (!registered) {
        ModalRegistry.register(ModalEventSummary.TYPE, ModalEventSummary, 'core_calendar/event_summary_modal');
        registered = true;
    }

    return ModalEventSummary;
});
