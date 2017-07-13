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
 * @module     core/modal_save_cancel
 * @class      modal_save_cancel
 * @package    core
 * @copyright  2016 Ryan Wyllie <ryan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
define(['jquery', 'core/notification', 'core/custom_interaction_events', 'core/modal', 'core/modal_events'],
        function($, Notification, CustomEvents, Modal, ModalEvents) {

    var SELECTORS = {
        YES_BUTTON: '[data-action="yes"]',
        NO_BUTTON: '[data-action="no"]',
    };

    /**
     * Constructor for the Modal.
     *
     * @param {object} root The root jQuery element for the modal
     */
    var ModalConfirm = function(root) {
        Modal.call(this, root);

        if (!this.getFooter().find(SELECTORS.YES_BUTTON).length) {
            Notification.exception({message: 'No "yes" button found'});
        }

        if (!this.getFooter().find(SELECTORS.NO_BUTTON).length) {
            Notification.exception({message: 'No "no" button found'});
        }
    };

    ModalConfirm.prototype = Object.create(Modal.prototype);
    ModalConfirm.prototype.constructor = ModalConfirm;

    /**
     * Override parent implementation to prevent changing the footer content.
     */
    ModalConfirm.prototype.setFooter = function() {
        Notification.exception({message: 'Can not change the footer of a confirm modal'});
        return;
    };

    /**
     * Set up all of the event handling for the modal.
     *
     * @method registerEventListeners
     */
    ModalConfirm.prototype.registerEventListeners = function() {
        // Apply parent event listeners.
        Modal.prototype.registerEventListeners.call(this);

        this.getModal().on(CustomEvents.events.activate, SELECTORS.YES_BUTTON, function(e, data) {
            var yesEvent = $.Event(ModalEvents.yes);
            this.getRoot().trigger(yesEvent, this);

            if (!yesEvent.isDefaultPrevented()) {
                this.hide();
                data.originalEvent.preventDefault();
            }
        }.bind(this));

        this.getModal().on(CustomEvents.events.activate, SELECTORS.NO_BUTTON, function(e, data) {
            var noEvent = $.Event(ModalEvents.no);
            this.getRoot().trigger(noEvent, this);

            if (!noEvent.isDefaultPrevented()) {
                this.hide();
                data.originalEvent.preventDefault();
            }
        }.bind(this));
    };

    return ModalConfirm;
});
