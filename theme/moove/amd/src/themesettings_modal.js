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
 * Theme settings modal js.
 *
 * @package    theme_moove
 * @copyright  2020 Willian Mano - http://conecti.me
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
define(['jquery', 'core/notification', 'core/custom_interaction_events', 'core/modal', 'core/modal_registry', 'core/ajax'],
    function(jQuery, Notification, CustomEvents, Modal, ModalRegistry, Ajax) {

        var registered = false;
        var SELECTORS = {
            SAVE_BUTTON: '[data-action="save"]',
            CANCEL_BUTTON: '[data-action="cancel"]'
        };

        /**
         * Constructor for the Modal.
         *
         * @param {object} root The root jQuery element for the modal
         */
        var ThemeSettingsModal = function(root) {
            Modal.call(this, root);

            var request = Ajax.call([{
                methodname: 'theme_moove_getthemesettings',
                args: {}
            }]);

            request[0].done(function(result) {
                document.getElementById('fonttype').value = result.fonttype;

                if (result.enableaccessibilitytoolbar) {
                    document.getElementById('enableaccessibilitytoolbar').checked = true;
                }
            });
        };

        ThemeSettingsModal.TYPE = 'theme_moove-themesettings_modal';
        ThemeSettingsModal.prototype = Object.create(Modal.prototype);
        ThemeSettingsModal.prototype.constructor = ThemeSettingsModal;

        /**
         * Set up all of the event handling for the modal.
         *
         * @method registerEventListeners
         */
        ThemeSettingsModal.prototype.registerEventListeners = function() {
            // Apply parent event listeners.
            Modal.prototype.registerEventListeners.call(this);

            this.getModal().on(CustomEvents.events.activate, SELECTORS.SAVE_BUTTON, function() {
                var request = Ajax.call([{
                    methodname: 'theme_moove_savethemesettings',
                    args: {
                        formdata: this.getFormData()
                    }
                }]);

                request[0].done(function() {
                    document.location.reload(true);
                }.bind(this)).fail(function(error){
                    var message = error.message;

                    if (!message) {
                        message = error.error;
                    }

                    Notification.addNotification({
                        message: message,
                        type: 'error'
                    });

                    this.hide();

                    this.destroy();
                }.bind(this));
            }.bind(this));

            this.getModal().on(CustomEvents.events.activate, SELECTORS.CANCEL_BUTTON, function() {
                this.hide();
                this.destroy();
            }.bind(this));
        };

        /**
         * Get the serialised form data.
         *
         * @method getFormData
         * @return {string} serialised form data
         */
        ThemeSettingsModal.prototype.getFormData = function() {
            return this.getForm().serialize();
        };

        /**
         * Get the form element from the modal.
         *
         * @method getForm
         * @return {object}
         */
        ThemeSettingsModal.prototype.getForm = function() {
            return this.getBody().find('form');
        };

        // Automatically register with the modal registry the first time this module is imported so that you can create modals
        // of this type using the modal factory.
        if (!registered) {
            ModalRegistry.register(ThemeSettingsModal.TYPE, ThemeSettingsModal, 'theme_moove/themesettings_modal');
            registered = true;
        }

        return ThemeSettingsModal;
    });