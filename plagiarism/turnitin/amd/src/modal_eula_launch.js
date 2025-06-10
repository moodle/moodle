/**
 * Javascript controller for Eula launcher
 *
 * @copyright Turnitin
 * @author 2019 David Winn <dwinn@turnitin.com>
 * @module plagiarism_turnitin/modal_eula_launch
 */

define(
    [
        'jquery',
        'core/ajax',
        'core/notification',
        'core/custom_interaction_events',
        'core/modal',
        'core/modal_registry',
        'core/modal_events',
        'plagiarism_turnitin/eula_event_listener'
    ],
    function($, Ajax, Notification, CustomEvents, Modal, ModalRegistry, ModalEvents, EulaEventListener) {

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
        var ModalEulaLaunch = function(root) {
            Modal.call(this, root);
        };

        ModalEulaLaunch.TYPE = 'plagiarism_turnitin-modal_eula_launch';
        ModalEulaLaunch.TEMPLATE = 'plagiarism_turnitin/modal_eula_launch';
        ModalEulaLaunch.prototype = Object.create(Modal.prototype);
        ModalEulaLaunch.prototype.constructor = ModalEulaLaunch;

        /**
         * Set up all of the event handling for the modal.
         *
         * @method registerEventListeners
         */
        ModalEulaLaunch.prototype.registerEventListeners = function() {
            // Apply parent event listeners.
            Modal.prototype.registerEventListeners.call(this);

            processEula();

            // On clicking the X, then hide the modal.
            this.getModal().on(CustomEvents.events.activate, SELECTORS.HIDE_BUTTON, function(e, data) {
                var cancelEvent = $.Event(ModalEvents.cancel);
                this.getRoot().trigger(cancelEvent, this);

                if (!cancelEvent.isDefaultPrevented()) {
                    this.hide();
                    data.originalEvent.preventDefault();
                }
            }.bind(this));
        };

        // Get the rubrics belonging to a user from Turnitin and refresh menu accordingly.
        /**
         * Method for processing the EULA.
         */
        function processEula() {
            EulaEventListener.attach();
        }

        // Automatically register with the modal registry the first time this module is imported so that
        // you can create modals of this type using the modal factory.
        if (!registered) {
            ModalRegistry.register(ModalEulaLaunch.TYPE, ModalEulaLaunch, 'plagiarism_turnitin/modal_eula_launch');
            registered = true;
        }

        return ModalEulaLaunch;
    }
);