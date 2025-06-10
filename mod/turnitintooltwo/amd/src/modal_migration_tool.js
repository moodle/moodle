/**
 * Javascript controller for the migration tool modal.
 *
 * @package   turnitintooltwo
 * @copyright Turnitin
 * @author 2019 David Winn <dwinn@turnitin.com>
 * @module mod_turnitintooltwo/migration_tool
 */

define([
        'jquery',
        'core/notification',
        'core/custom_interaction_events',
        'core/modal',
        'core/modal_registry',
        'core/modal_events',
        'core/config',
        'mod_turnitintooltwo/migration_tool_migrate'
    ],
    function($, Notification, CustomEvents, Modal, ModalRegistry, ModalEvents, Config, MigrationToolMigrate) {

        var registered = false;
        var SELECTORS = {
            MIGRATE_BUTTON: '[data-action="migrate-assignment"]',
            CANCEL_BUTTON: '[data-action="cancel"]'
        };

        /**
         * Constructor for the Modal.
         *
         * @param {object} root The root jQuery element for the modal
         */
        var ModalMigrationTool = function(root) {
            Modal.call(this, root);
        };

        ModalMigrationTool.TYPE = 'mod_turnitintooltwo-migration_tool';
        ModalMigrationTool.prototype = Object.create(Modal.prototype);
        ModalMigrationTool.prototype.constructor = ModalMigrationTool;

        /**
         * Set up all of the event handling for the modal.
         *
         * @method registerEventListeners
         */
        ModalMigrationTool.prototype.registerEventListeners = function() {
            // Apply parent event listeners.
            Modal.prototype.registerEventListeners.call(this);

            // Fired during a manual migration when clicking to migrate the assignment. Initiate the migration process.
            this.getModal().on(CustomEvents.events.activate, SELECTORS.MIGRATE_BUTTON, function() {
                $('.asktomigrate').hide();
                $('.migrating').show();

                MigrationToolMigrate.migration_tool_migrate(
                    $("#migrate_type").data("courseid"),
                    $("#migrate_type").data("turnitintoolid")
                );
            }.bind(this));

            // On cancel, then hide the modal.
            this.getModal().on(CustomEvents.events.activate, SELECTORS.CANCEL_BUTTON, function(e, data) {

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
            ModalRegistry.register(ModalMigrationTool.TYPE, ModalMigrationTool, 'mod_turnitintooltwo/modal_migration_tool');
            registered = true;
        }

        return ModalMigrationTool;
    }
);