/**
 * Javascript controller for Quickmark launcher
 *
 * @copyright Turnitin
 * @author 2019 David Winn <dwinn@turnitin.com>
 * @module plagiarism_turnitin/modal_quickmark_launch
 */

define(
    [
        'jquery',
        'core/ajax',
        'core/notification',
        'core/custom_interaction_events',
        'core/modal',
        'core/modal_registry',
        'core/modal_events'
    ],
    function($, Ajax, Notification, CustomEvents, Modal, ModalRegistry, ModalEvents) {

        var registered = false;
        var SELECTORS = {
            HIDE_BUTTON: '[data-action="hide"]',
            MODAL: '[data-region="modal"]'
        };

        /**
         * Constructor for the Modal.
         *
         * @param {object} root The root jQuery element for the modal
         */
        var ModalQuickmarkLaunch = function(root) {
            Modal.call(this, root);
        };

        ModalQuickmarkLaunch.TYPE = 'plagiarism_turnitin-modal_quickmark_launch';
        ModalQuickmarkLaunch.TEMPLATE = 'plagiarism_turnitin/modal_quickmark_launch';
        ModalQuickmarkLaunch.prototype = Object.create(Modal.prototype);
        ModalQuickmarkLaunch.prototype.constructor = ModalQuickmarkLaunch;

        /**
         * Set up all of the event handling for the modal.
         *
         * @method registerEventListeners
         */
        ModalQuickmarkLaunch.prototype.registerEventListeners = function() {
            // Apply parent event listeners.
            Modal.prototype.registerEventListeners.call(this);

            // On cancel, then hide the modal.
            this.getModal().on(CustomEvents.events.activate, SELECTORS.HIDE_BUTTON, function(e, data) {
                var cancelEvent = $.Event(ModalEvents.cancel);
                this.getRoot().trigger(cancelEvent, this);

                if (!cancelEvent.isDefaultPrevented()) {
                    this.hide();
                    data.originalEvent.preventDefault();
                }
            }.bind(this));
        };

        // Automatically register with the modal registry the first time this module is imported so that
        // you can create modals of this type using the modal factory.
        if (!registered) {
            ModalRegistry.register(ModalQuickmarkLaunch.TYPE, ModalQuickmarkLaunch, 'plagiarism_turnitin/modal_quickmark_launch');
            registered = true;
        }

        return ModalQuickmarkLaunch;
    }
);