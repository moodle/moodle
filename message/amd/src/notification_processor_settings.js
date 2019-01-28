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
 * Load the settings for a message processor.
 *
 * @module     core_message/notification_processor_settings
 * @class      notification_processor_settings
 * @package    message
 * @copyright  2016 Ryan Wyllie <ryan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
define([
        'jquery',
        'core/ajax',
        'core/str',
        'core/notification',
        'core/custom_interaction_events',
        'core/modal',
        'core/modal_registry',
        'core/fragment',
        ],
        function(
            $,
            Ajax,
            Str,
            Notification,
            CustomEvents,
            Modal,
            ModalRegistry,
            Fragment
        ) {

    var registered = false;
    var SELECTORS = {
        SAVE_BUTTON: '[data-action="save"]',
        CANCEL_BUTTON: '[data-action="cancel"]',
        PROCESSOR: '[data-processor-name]',
        PREFERENCE_ROW: '[data-region="preference-row"]',
    };

    /**
     * Constructor for the Modal.
     *
     * @param {object} root The root jQuery element for the modal.
     */
    var NotificationProcessorSettings = function(root) {
        Modal.call(this, root);
        this.name = null;
        this.userId = null;
        this.contextId = null;
        this.element = null;
        this.saveButton = this.getFooter().find(SELECTORS.SAVE_BUTTON);
        this.cancelButton = this.getFooter().find(SELECTORS.CANCEL_BUTTON);
    };

    NotificationProcessorSettings.TYPE = 'core_message-notification_processor_settings';
    NotificationProcessorSettings.prototype = Object.create(Modal.prototype);
    NotificationProcessorSettings.prototype.constructor = NotificationProcessorSettings;

    /**
     * Set the userid to the given value.
     *
     * @method setUserId
     * @param {int} id The notification userid
     */
    NotificationProcessorSettings.prototype.setUserId = function(id) {
        this.userId = id;
    };

    /**
     * Retrieve the current userid, if any.
     *
     * @method getUserId
     * @return {int|null} The notification userid
     */
    NotificationProcessorSettings.prototype.getUserId = function() {
        return this.userId;
    };

    /**
     * Set the object to the given value.
     *
     * @method setElement
     * @param {object} element The notification node element.
     */
    NotificationProcessorSettings.prototype.setElement = function(element) {
        this.element = element;
    };

    /**
     * Retrieve the current element, if any.
     *
     * @method getElement
     * @return {object|null} The notification node element.
     */
    NotificationProcessorSettings.prototype.getElement = function() {
        return this.element;
    };

    /**
     * Set the name to the given value.
     *
     * @method setName
     * @param {string} name The notification name.
     */
    NotificationProcessorSettings.prototype.setName = function(name) {
        this.name = name;
    };

    /**
     * Retrieve the current name, if any.
     *
     * @method getName
     * @return {string|null} The notification name.
     */
    NotificationProcessorSettings.prototype.getName = function() {
        return this.name;
    };
    /**
     * Set the context id to the given value.
     *
     * @method setContextId
     * @param {Number} id The notification context id
     */
    NotificationProcessorSettings.prototype.setContextId = function(id) {
        this.contextId = id;
    };

    /**
     * Retrieve the current context id, if any.
     *
     * @method getContextId
     * @return {Number|null} The notification context id
     */
    NotificationProcessorSettings.prototype.getContextId = function() {
        return this.contextId;
    };

    /**
     * Get the form element from the modal.
     *
     * @method getForm
     * @return {object}
     */
    NotificationProcessorSettings.prototype.getForm = function() {
        return this.getBody().find('form');
    };

    /**
     * Disable the buttons in the footer.
     *
     * @method disableButtons
     */
    NotificationProcessorSettings.prototype.disableButtons = function() {
        this.saveButton.prop('disabled', true);
        this.cancelButton.prop('disabled', true);
    };

    /**
     * Enable the buttons in the footer.
     *
     * @method enableButtons
     */
    NotificationProcessorSettings.prototype.enableButtons = function() {
        this.saveButton.prop('disabled', false);
        this.cancelButton.prop('disabled', false);
    };

    /**
     * Load the title for the modal to the appropriate value
     * depending on message outputs.
     *
     * @method loadTitleContent
     * @return {object} A promise resolved with the new title text.
     */
    NotificationProcessorSettings.prototype.loadTitleContent = function() {
        this.titlePromise = Str.get_string('processorsettings', 'message');
        this.setTitle(this.titlePromise);

        return this.titlePromise;
    };

    /**
     * Load the body for the modal to the appropriate value
     * depending on message outputs.
     *
     * @method loadBodyContent
     * @return {object} A promise resolved with the fragment html and js from
     */
    NotificationProcessorSettings.prototype.loadBodyContent = function() {
        this.disableButtons();

        var args = {
            userid: this.getUserId(),
            type: this.getName(),
        };

        this.bodyPromise = Fragment.loadFragment('message', 'processor_settings', this.getContextId(), args);
        this.setBody(this.bodyPromise);

        this.bodyPromise.then(function() {
            this.enableButtons();
            return;
        }.bind(this))
        .fail(Notification.exception);

        return this.bodyPromise;
    };

    /**
     * Load both the title and body content.
     *
     * @method loadAllContent
     * @return {object} promise
     */
    NotificationProcessorSettings.prototype.loadAllContent = function() {
        return $.when(this.loadTitleContent(), this.loadBodyContent());
    };

    /**
     * Load the modal content before showing it. This
     * is to allow us to re-use the same modal for creating and
     * editing different message outputs within the page.
     *
     * @method show
     */
    NotificationProcessorSettings.prototype.show = function() {
        this.loadAllContent();
        Modal.prototype.show.call(this);
    };

    /**
     * Clear the notification from the modal when it's closed so
     * that it is loaded fresh next time it's displayed.
     *
     * @method hide
     */
    NotificationProcessorSettings.prototype.hide = function() {
        Modal.prototype.hide.call(this);
        this.setContextId(null);
        this.setName(null);
        this.setUserId(null);
    };

    /**
     * Checks if the processor has been configured. If so then remove the unconfigured
     * status from the interface.
     *
     * @method updateConfiguredStatus
     * @return {Promise|boolean}
     */
    NotificationProcessorSettings.prototype.updateConfiguredStatus = function() {
        var processorHeader = $(this.getElement()).closest(SELECTORS.PROCESSOR);

        if (!processorHeader.hasClass('unconfigured')) {
            return false;
        }

        var processorName = processorHeader.attr('data-processor-name');
        var request = {
            methodname: 'core_message_get_message_processor',
            args: {
                name: processorName,
                userid: this.userId,
            },
        };

        return Ajax.call([request])[0]
            .fail(Notification.exception)
            .done(function(result) {
                // Check if the user has figured configuring the processor.
                if (result.userconfigured) {
                    // If they have then we can enable the settings.
                    var notifications = $(SELECTORS.PREFERENCE_ROW + ' [data-processor-name="' + processorName + '"]');
                    processorHeader.removeClass('unconfigured');
                    notifications.removeClass('disabled');
                }
            });
    };

    /**
     * Set up all of the event handling for the modal.
     *
     * @method registerEventListeners
     */
    NotificationProcessorSettings.prototype.registerEventListeners = function() {
        // Apply parent event listeners.
        Modal.prototype.registerEventListeners.call(this);

        // When the user clicks the save button we trigger the form submission.
        this.getModal().on(CustomEvents.events.activate, SELECTORS.SAVE_BUTTON, function(e, data) {
            this.getForm().submit();
            data.originalEvent.preventDefault();
        }.bind(this));

        this.getModal().on('mpp:formsubmitted', function(e) {
            this.hide();
            this.updateConfiguredStatus();
            e.stopPropagation();
        }.bind(this));

        this.getModal().on(CustomEvents.events.activate, SELECTORS.CANCEL_BUTTON, function(e, data) {
            this.hide();
            data.originalEvent.preventDefault();
            e.stopPropagation();
        }.bind(this));
    };

    // Automatically register with the modal registry the first time this module is imported
    // so that you can create modals
    // of this type using the modal factory.
    if (!registered) {
        ModalRegistry.register(
                                NotificationProcessorSettings.TYPE,
                                NotificationProcessorSettings,
                                'core/modal_save_cancel');
        registered = true;
    }

    return NotificationProcessorSettings;
});
