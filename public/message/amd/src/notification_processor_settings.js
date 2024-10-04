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
 * @copyright  2016 Ryan Wyllie <ryan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


import $ from 'jquery';
import * as Ajax from 'core/ajax';
import * as Str from 'core/str';
import * as Notification from 'core/notification';
import * as CustomEvents from 'core/custom_interaction_events';
import Modal from 'core/modal';
import * as Fragment from 'core/fragment';

const SELECTORS = {
    SAVE_BUTTON: '[data-action="save"]',
    CANCEL_BUTTON: '[data-action="cancel"]',
    PROCESSOR: '[data-processor-name]',
    PREFERENCE_ROW: '[data-region="preference-row"]',
};

export default class NotificationProcessorSettings extends Modal {
    static TYPE = 'core_message-notification_processor_settings';
    static TEMPLATE = 'core/modal_save_cancel';

    /**
     * Constructor for the Modal.
     *
     * @class
     * @param {object} root The root jQuery element for the modal.
     */
    constructor(root) {
        super(root);
        this.name = null;
        this.userId = null;
        this.contextId = null;
        this.element = null;
        this.saveButton = this.getFooter().find(SELECTORS.SAVE_BUTTON);
        this.cancelButton = this.getFooter().find(SELECTORS.CANCEL_BUTTON);
    }

    /**
     * Set the userid to the given value.
     *
     * @method setUserId
     * @param {int} id The notification userid
     */
    setUserId(id) {
        this.userId = id;
    }

    /**
     * Retrieve the current userid, if any.
     *
     * @method getUserId
     * @return {int|null} The notification userid
     */
    getUserId() {
        return this.userId;
    }

    /**
     * Set the object to the given value.
     *
     * @method setElement
     * @param {object} element The notification node element.
     */
    setElement(element) {
        this.element = element;
    }

    /**
     * Retrieve the current element, if any.
     *
     * @method getElement
     * @return {object|null} The notification node element.
     */
    getElement() {
        return this.element;
    }

    /**
     * Set the name to the given value.
     *
     * @method setName
     * @param {string} name The notification name.
     */
    setName(name) {
        this.name = name;
    }

    /**
     * Retrieve the current name, if any.
     *
     * @method getName
     * @return {string|null} The notification name.
     */
    getName() {
        return this.name;
    }
    /**
     * Set the context id to the given value.
     *
     * @method setContextId
     * @param {Number} id The notification context id
     */
    setContextId(id) {
        this.contextId = id;
    }

    /**
     * Retrieve the current context id, if any.
     *
     * @method getContextId
     * @return {Number|null} The notification context id
     */
    getContextId() {
        return this.contextId;
    }

    /**
     * Get the form element from the modal.
     *
     * @method getForm
     * @return {object}
     */
    getForm() {
        return this.getBody().find('form');
    }

    /**
     * Disable the buttons in the footer.
     *
     * @method disableButtons
     */
    disableButtons() {
        this.saveButton.prop('disabled', true);
        this.cancelButton.prop('disabled', true);
    }

    /**
     * Enable the buttons in the footer.
     *
     * @method enableButtons
     */
    enableButtons() {
        this.saveButton.prop('disabled', false);
        this.cancelButton.prop('disabled', false);
    }

    /**
     * Load the title for the modal to the appropriate value
     * depending on message outputs.
     *
     * @method loadTitleContent
     * @return {object} A promise resolved with the new title text.
     */
    loadTitleContent() {
        this.titlePromise = Str.get_string('processorsettings', 'message');
        this.setTitle(this.titlePromise);

        return this.titlePromise;
    }

    /**
     * Load the body for the modal to the appropriate value
     * depending on message outputs.
     *
     * @method loadBodyContent
     * @return {object} A promise resolved with the fragment html and js from
     */
    loadBodyContent() {
        this.disableButtons();

        const args = {
            userid: this.getUserId(),
            type: this.getName(),
        };

        this.bodyPromise = Fragment.loadFragment('message', 'processor_settings', this.getContextId(), args);
        this.setBody(this.bodyPromise);

        this.bodyPromise.then(() => {
            this.enableButtons();
            return;
        })
        .catch(Notification.exception);

        return this.bodyPromise;
    }

    /**
     * Load both the title and body content.
     *
     * @method loadAllContent
     * @return {object} promise
     */
    loadAllContent() {
        return $.when(this.loadTitleContent(), this.loadBodyContent());
    }

    /**
     * Load the modal content before showing it. This
     * is to allow us to re-use the same modal for creating and
     * editing different message outputs within the page.
     *
     * @method show
     */
    show() {
        this.loadAllContent();
        super.show(this);
    }

    /**
     * Clear the notification from the modal when it's closed so
     * that it is loaded fresh next time it's displayed.
     *
     * @method hide
     */
    hide() {
        super.hide(this);
        this.setContextId(null);
        this.setName(null);
        this.setUserId(null);
    }

    /**
     * Checks if the processor has been configured. If so then remove the unconfigured
     * status from the interface.
     *
     * @method updateConfiguredStatus
     * @return {Promise|boolean}
     */
    updateConfiguredStatus() {
        const processorHeader = $(this.getElement()).closest(SELECTORS.PROCESSOR);

        if (!processorHeader.hasClass('unconfigured')) {
            return false;
        }

        const processorName = processorHeader.attr('data-processor-name');
        const request = {
            methodname: 'core_message_get_message_processor',
            args: {
                name: processorName,
                userid: this.userId,
            },
        };

        return Ajax.call([request])[0]
            .then((result) => {
                // Check if the user has figured configuring the processor.
                if (result.userconfigured) {
                    // If they have then we can enable the settings.
                    const notifications = $(SELECTORS.PREFERENCE_ROW + ' [data-processor-name="' + processorName + '"]');
                    processorHeader.removeClass('unconfigured');
                    notifications.removeClass('disabled');
                }
                return result;
            });
    }

    /**
     * Set up all of the event handling for the modal.
     *
     * @method registerEventListeners
     */
    registerEventListeners() {
        // Apply parent event listeners.
        super.registerEventListeners(this);

        // When the user clicks the save button we trigger the form submission.
        this.getModal().on(CustomEvents.events.activate, SELECTORS.SAVE_BUTTON, (e, data) => {
            this.getForm().submit();
            data.originalEvent.preventDefault();
        });

        this.getModal().on('mpp:formsubmitted', (e) => {
            this.hide();
            this.updateConfiguredStatus();
            e.stopPropagation();
        });

        this.getModal().on(CustomEvents.events.activate, SELECTORS.CANCEL_BUTTON, (e, data) => {
            this.hide();
            data.originalEvent.preventDefault();
            e.stopPropagation();
        });
    }
}

NotificationProcessorSettings.registerModalType();
