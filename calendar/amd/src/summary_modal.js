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
 * @package    core_calendar
 * @copyright  2017 Simey Lameze <simey@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
define(['jquery', 'core/notification', 'core/custom_interaction_events', 'core/modal', 'core/modal_registry'],
    function($, Notification, CustomEvents, Modal, ModalRegistry) {

    var registered = false;
    var SELECTORS = {
        EDIT_BUTTON: '[data-action="edit"]',
        DELETE_BUTTON: '[data-action="delete"]',
        EVENT_LINK: '[data-action="event-link"]'
    };

    /**
     * Constructor for the Modal.
     *
     * @param {object} root The root jQuery element for the modal
     */
    var ModalEventSummary = function(root) {
        Modal.call(this, root);

        if (!this.getFooter().find(SELECTORS.EDIT_BUTTON).length) {
            Notification.exception({message: 'No edit button found'});
        }

        if (!this.getFooter().find(SELECTORS.DELETE_BUTTON).length) {
            Notification.exception({message: 'No delete button found'});
        }
    };

    ModalEventSummary.TYPE = 'core_calendar-event_summary';
    ModalEventSummary.prototype = Object.create(Modal.prototype);
    ModalEventSummary.prototype.constructor = ModalEventSummary;

    // Automatically register with the modal registry the first time this module is imported so that you can create modals
    // of this type using the modal factory.
    if (!registered) {
        ModalRegistry.register(ModalEventSummary.TYPE, ModalEventSummary, 'core_calendar/event_summary_modal');
        registered = true;
    }

    return ModalEventSummary;

});