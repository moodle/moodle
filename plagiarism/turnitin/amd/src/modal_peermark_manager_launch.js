/**
 * Javascript controller for the Peermark Manager launcher
 *
 * @copyright Turnitin
 * @author 2019 David Winn <dwinn@turnitin.com>
 * @module plagiarism_turnitin/modal_peermark_manager_launch
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
        var ModalPeermarkManagerLaunch = function(root) {
            Modal.call(this, root);
        };

        ModalPeermarkManagerLaunch.TYPE = 'plagiarism_turnitin-modal_peermark_manager_launch';
        ModalPeermarkManagerLaunch.TEMPLATE = 'plagiarism_turnitin/modal_peermark_manager_launch';
        ModalPeermarkManagerLaunch.prototype = Object.create(Modal.prototype);
        ModalPeermarkManagerLaunch.prototype.constructor = ModalPeermarkManagerLaunch;

        /**
         * Set up all of the event handling for the modal.
         *
         * @method registerEventListeners
         */
        ModalPeermarkManagerLaunch.prototype.registerEventListeners = function() {
            // Apply parent event listeners.
            Modal.prototype.registerEventListeners.call(this);

            // On cancel, then hide the modal.
            this.getModal().on(CustomEvents.events.activate, SELECTORS.HIDE_BUTTON, function(e, data) {
                var cancelEvent = $.Event(ModalEvents.cancel);
                this.getRoot().trigger(cancelEvent, this);

                if (!cancelEvent.isDefaultPrevented()) {
                    this.hide();
                    data.originalEvent.preventDefault();

                    refreshPeermarkAssignments();
                }
            }.bind(this));

            // On clicking outside the modal, refresh the Peermark assignments.
            this.getRoot().click(function(e) {
                if (!$(e.target).closest(SELECTORS.MODAL).length) {
                    refreshPeermarkAssignments();
                }
            }.bind(this));
        };

        /**
         * Method to refresh peermark assignments.
         */
        function refreshPeermarkAssignments() {
            $.ajax({
                type: "POST",
                url: M.cfg.wwwroot + "/plagiarism/turnitin/ajax.php",
                dataType: "json",
                data: {
                    action: "refresh_peermark_assignments",
                    cmid: $('input[name="coursemodule"]').val(),
                    sesskey: M.cfg.sesskey
                }
            });
        }

        // Automatically register with the modal registry the first time this module is imported so that
        // you can create modals of this type using the modal factory.
        if (!registered) {
            ModalRegistry.register(ModalPeermarkManagerLaunch.TYPE,
                ModalPeermarkManagerLaunch,
                'plagiarism_turnitin/modal_peermark_manager_launch');
            registered = true;
        }

        return ModalPeermarkManagerLaunch;
    }
);