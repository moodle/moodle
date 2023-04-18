define(['jquery', 'core/notification', 'core/custom_interaction_events', 'core/modal', 'core/modal_registry'],
        function($, Notification, CustomEvents, Modal, ModalRegistry) {
    var registered = false;
    var SELECTORS = {
        SAVE_BUTTON: '[data-action="save"]',
    };

    /**
     * Constructor for the Modal.
     *
     * @param {object} root The root jQuery element for the modal
     */
    var ModalEnrolUsers = function(root) {
        Modal.call(this, root);

        if (!this.getFooter().find(SELECTORS.SAVE_BUTTON).length) {
            Notification.exception({message: 'No save button found'});
        }

    };

    ModalEnrolUsers.TYPE = 'local_qubitscourse-enrolusers';
    ModalEnrolUsers.prototype = Object.create(Modal.prototype);
    ModalEnrolUsers.prototype.constructor = ModalEnrolUsers;

    /**
     * Set up all of the event handling for the modal.
     *
     * @method registerEventListeners
     */
    ModalEnrolUsers.prototype.registerEventListeners = function() {
        // Apply parent event listeners.
        Modal.prototype.registerEventListeners.call(this);
        this.getModal().on(CustomEvents.events.activate, SELECTORS.SAVE_BUTTON, function(e, data) {
            console.log("Enrol users - Save button Kamesh Clicked >>>");
            // Add your logic for when the login button is clicked. This could include the form validation,
            // loading animations, error handling etc.
        }.bind(this));
        
    };

    ModalEnrolUsers.prototype.setLarge = function() {
        Modal.prototype.setLarge.call(this);
        this.getModal().addClass('modal-lg');
    };

    // Automatically register with the modal registry the first time this module is imported so that you can create modals
    // of this type using the modal factory.
    if (!registered) {
        ModalRegistry.register(ModalEnrolUsers.TYPE, ModalEnrolUsers, 'local_qubitscourse/modal_enrolusers');
        registered = true;
    }

    return ModalEnrolUsers;
});