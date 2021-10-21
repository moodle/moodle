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
 * Display a form in a modal dialogue
 *
 * Example:
 *    import ModalForm from 'core_form/modalform';
 *
 *    const modalForm = new ModalForm({
 *        formClass: 'pluginname\\form\\formname',
 *        modalConfig: {title: 'Here comes the title'},
 *        args: {categoryid: 123},
 *        returnFocus: e.target,
 *    });
 *    modalForm.addEventListener(modalForm.events.FORM_SUBMITTED, (c) => window.console.log(c.detail));
 *    modalForm.show();
 *
 * See also https://docs.moodle.org/dev/Modal_and_AJAX_forms
 *
 * @module     core_form/modalform
 * @copyright  2018 Mitxel Moriana <mitxel@tresipunt.>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import ModalFactory from 'core/modal_factory';
import ModalEvents from 'core/modal_events';
import Ajax from 'core/ajax';
import Notification from 'core/notification';
import Y from 'core/yui';
import Event from 'core/event';
import Fragment from 'core/fragment';
import Pending from 'core/pending';

export default class ModalForm {

    /**
     * Various events that can be observed.
     *
     * @type {Object}
     */
    events = {
        // Form was successfully submitted - the response is passed to the event listener.
        // Cancellable (but it's hardly ever needed to cancel this event).
        FORM_SUBMITTED: 'core_form_modalform_formsubmitted',
        // Cancel button was pressed.
        // Cancellable (but it's hardly ever needed to cancel this event).
        FORM_CANCELLED: 'core_form_modalform_formcancelled',
        // User attempted to submit the form but there was client-side validation error.
        CLIENT_VALIDATION_ERROR: 'core_form_modalform_clientvalidationerror',
        // User attempted to submit the form but server returned validation error.
        SERVER_VALIDATION_ERROR: 'core_form_modalform_validationerror',
        // Error occurred while performing request to the server.
        // Cancellable (by default calls Notification.exception).
        ERROR: 'core_form_modalform_error',
        // Right after user pressed no-submit button,
        // listen to this event if you want to add JS validation or processing for no-submit button.
        // Cancellable.
        NOSUBMIT_BUTTON_PRESSED: 'core_form_modalform_nosubmitbutton',
        // Right after user pressed submit button,
        // listen to this event if you want to add additional JS validation or confirmation dialog.
        // Cancellable.
        SUBMIT_BUTTON_PRESSED: 'core_form_modalform_submitbutton',
        // Right after user pressed cancel button,
        // listen to this event if you want to add confirmation dialog.
        // Cancellable.
        CANCEL_BUTTON_PRESSED: 'core_form_modalform_cancelbutton',
        // Modal was loaded and this.modal is available (but the form content may not be loaded yet).
        LOADED: 'core_form_modalform_loaded',
    };

    /**
     * Constructor
     *
     * Shows the required form inside a modal dialogue
     *
     * @param {Object} config parameters for the form and modal dialogue:
     * @paramy {String} config.formClass PHP class name that handles the form (should extend \core_form\modal )
     * @paramy {Object} config.modalConfig modal config - title, type, etc.
     *              Default: {removeOnClose: true, type: ModalFactory.types.SAVE_CANCEL}
     * @paramy {Object} config.args Arguments for the initial form rendering (for example, id of the edited entity)
     * @paramy {String} config.saveButtonText the text to display on the Modal "Save" button (optional)
     * @paramy {String} config.saveButtonClasses additional CSS classes for the Modal "Save" button
     * @paramy {HTMLElement} config.returnFocus element to return focus to after the dialogue is closed
     */
    constructor(config) {
        this.modal = null;
        this.config = config;
        this.config.modalConfig = {
            removeOnClose: true,
            type: ModalFactory.types.SAVE_CANCEL,
            large: true,
            ...(this.config.modalConfig || {}),
        };
        this.config.args = this.config.args || {};
        this.futureListeners = [];
    }

    /**
     * Initialise the modal and shows it
     *
     * @return {Promise}
     */
    show() {
        const pendingPromise = new Pending('core_form/modalform:init');
        return ModalFactory.create(this.config.modalConfig)
        .then((modal) => {
            this.modal = modal;

            // Retrieve the form and set the modal body. We can not set the body in the modalConfig,
            // we need to make sure that the modal already exists when we render the form. Some form elements
            // such as date_selector inspect the existing elements on the page to find the highest z-index.
            const formParams = new URLSearchParams(Object.entries(this.config.args || {}));
            const bodyContent = this.getBody(formParams.toString());
            this.modal.setBodyContent(bodyContent);
            bodyContent.catch(Notification.exception);

            // After successfull submit, when we press "Cancel" or close the dialogue by clicking on X in the top right corner.
            this.modal.getRoot().on(ModalEvents.hidden, () => {
                this.notifyResetFormChanges()
                .then(() => {
                    this.modal.destroy();
                    // Focus on the element that actually launched the modal.
                    if (this.config.returnFocus) {
                        this.config.returnFocus.focus();
                    }
                    return null;
                })
                .catch(() => null);
            });

            // Add the class to the modal dialogue.
            this.modal.getModal().addClass('modal-form-dialogue');

            // We catch the press on submit buttons in the forms.
            this.modal.getRoot().on('click', 'form input[type=submit][data-no-submit]',
                (e) => {
                    e.preventDefault();
                    const event = this.trigger(this.events.NOSUBMIT_BUTTON_PRESSED, e.target);
                    if (!event.defaultPrevented) {
                        this.processNoSubmitButton(e.target);
                    }
                });

            // We catch the form submit event and use it to submit the form with ajax.
            this.modal.getRoot().on('submit', 'form', (e) => {
                e.preventDefault();
                const event = this.trigger(this.events.SUBMIT_BUTTON_PRESSED);
                if (!event.defaultPrevented) {
                    this.submitFormAjax();
                }
            });

            // Change the text for the save button.
            if (typeof this.config.saveButtonText !== 'undefined' &&
                typeof this.modal.setSaveButtonText !== 'undefined') {
                this.modal.setSaveButtonText(this.config.saveButtonText);
            }
            // Set classes for the save button.
            if (typeof this.config.saveButtonClasses !== 'undefined') {
                this.setSaveButtonClasses(this.config.saveButtonClasses);
            }
            // When Save button is pressed - submit the form.
            this.modal.getRoot().on(ModalEvents.save, (e) => {
                e.preventDefault();
                this.modal.getRoot().find('form').submit();
            });

            // When Cancel button is pressed - allow to intercept.
            this.modal.getRoot().on(ModalEvents.cancel, (e) => {
                const event = this.trigger(this.events.CANCEL_BUTTON_PRESSED);
                if (event.defaultPrevented) {
                    e.preventDefault();
                }
            });
            this.futureListeners.forEach(args => this.modal.getRoot()[0].addEventListener(...args));
            this.futureListeners = [];
            this.trigger(this.events.LOADED, null, false);
            return this.modal.show();
        })
        .then(pendingPromise.resolve);
    }

    /**
     * Triggers a custom event
     *
     * @private
     * @param {String} eventName
     * @param {*} detail
     * @param {Boolean} cancelable
     * @return {CustomEvent<unknown>}
     */
    trigger(eventName, detail = null, cancelable = true) {
        const e = new CustomEvent(eventName, {detail, cancelable});
        this.modal.getRoot()[0].dispatchEvent(e);
        return e;
    }

    /**
     * Add listener for an event
     *
     * @param {array} args
     * @example:
     *    const modalForm = new ModalForm(...);
     *    dynamicForm.addEventListener(modalForm.events.FORM_SUBMITTED, e => {
     *        window.console.log(e.detail);
     *    });
     */
    addEventListener(...args) {
        if (!this.modal) {
            this.futureListeners.push(args);
        } else {
            this.modal.getRoot()[0].addEventListener(...args);
        }
    }

    /**
     * Get form contents (to be used in ModalForm.setBodyContent())
     *
     * @param {String} formDataString form data in format of a query string
     * @method getBody
     * @private
     * @return {Promise}
     */
    getBody(formDataString) {
        const params = {
            formdata: formDataString,
            form: this.config.formClass
        };
        const pendingPromise = new Pending('core_form/modalform:form_body');
        return Ajax.call([{
            methodname: 'core_form_dynamic_form',
            args: params
        }])[0]
        .then(response => {
            pendingPromise.resolve();
            return {html: response.html, js: Fragment.processCollectedJavascript(response.javascript)};
        });
    }

    /**
     * On exception during form processing. Caller may override
     *
     * @param {Object} exception
     */
    onSubmitError(exception) {
        const event = this.trigger(this.events.ERROR, exception);
        if (event.defaultPrevented) {
            return;
        }

        Notification.exception(exception);
    }

    /**
     * Notifies listeners that form dirty state should be reset.
     *
     * @return {Promise<unknown>}
     */
    notifyResetFormChanges() {
        return new Promise(resolve => {
            Y.use('event', 'moodle-core-event', 'moodle-core-formchangechecker', () => {
                // Ensure that modal contains a form element (it may not if the form class threw an early exception).
                const form = this.modal.getRoot().find('form')[0];
                if (form) {
                    Event.notifyFormSubmitAjax(form, true);
                    M.core_formchangechecker.reset_form_dirty_state();
                }
                resolve();
            });
        });
    }

    /**
     * Wrapper for Event.notifyFormSubmitAjax that waits for the module to load
     *
     * We often destroy the form right after calling this function and we need to make sure that it actually
     * completes before it, or otherwise it will try to work with a form that does not exist.
     *
     * @param {Boolean} skipValidation
     * @return {Promise}
     */
    notifyFormSubmitAjax(skipValidation = false) {
        return new Promise(resolve => {
            Y.use('event', 'moodle-core-event', 'moodle-core-formchangechecker', () => {
                Event.notifyFormSubmitAjax(this.modal.getRoot().find('form')[0], skipValidation);
                resolve();
            });
        });
    }

    /**
     * Click on a "submit" button that is marked in the form as registerNoSubmitButton()
     *
     * @param {Element} button button that was pressed
     */
    processNoSubmitButton(button) {
        this.notifyFormSubmitAjax(true)
        .then(() => {
            // Add the button name to the form data and submit it.
            let formData = this.modal.getRoot().find('form').serialize();
            formData = formData + '&' + encodeURIComponent(button.getAttribute('name')) + '=' +
                encodeURIComponent(button.getAttribute('value'));

            const bodyContent = this.getBody(formData);
            this.modal.setBodyContent(bodyContent);
            bodyContent.catch(Notification.exception);

            return null;
        })
        .catch(null);
    }

    /**
     * Validate form elements
     * @return {Promise} promise that returns true if client-side validation has passed, false if there are errors
     */
    validateElements() {
        return this.notifyFormSubmitAjax()
        .then(() => {
            // Now the change events have run, see if there are any "invalid" form fields.
            /** @var {jQuery} list of elements with errors */
            const invalid = this.modal.getRoot().find('[aria-invalid="true"], .error');

            // If we found invalid fields, focus on the first one and do not submit via ajax.
            if (invalid.length) {
                invalid.first().focus();
                return false;
            }

            return true;
        });
    }

    /**
     * Disable buttons during form submission
     */
    disableButtons() {
        this.modal.getFooter().find('[data-action]').attr('disabled', true);
    }

    /**
     * Enable buttons after form submission (on validation error)
     */
    enableButtons() {
        this.modal.getFooter().find('[data-action]').removeAttr('disabled');
    }

    /**
     * Submit the form via AJAX call to the core_form_dynamic_form WS
     */
    async submitFormAjax() {
        // If we found invalid fields, focus on the first one and do not submit via ajax.
        if (!await this.validateElements()) {
            this.trigger(this.events.CLIENT_VALIDATION_ERROR, null, false);
            return;
        }
        this.disableButtons();

        // Convert all the form elements values to a serialised string.
        const formData = this.modal.getRoot().find('form').serialize();

        // Now we can continue...
        Ajax.call([{
            methodname: 'core_form_dynamic_form',
            args: {
                formdata: formData,
                form: this.config.formClass
            }
        }])[0]
        .then((response) => {
            if (!response.submitted) {
                // Form was not submitted because validation failed.
                const promise = new Promise(
                    resolve => resolve({html: response.html, js: Fragment.processCollectedJavascript(response.javascript)}));
                this.modal.setBodyContent(promise);
                this.enableButtons();
                this.trigger(this.events.SERVER_VALIDATION_ERROR);
            } else {
                // Form was submitted properly. Hide the modal and execute callback.
                const data = JSON.parse(response.data);
                return this.notifyResetFormChanges()
                .then(() => {
                    const event = this.trigger(this.events.FORM_SUBMITTED, data);
                    if (!event.defaultPrevented) {
                        this.modal.hide();
                    }
                    return null;
                });
            }
            return null;
        })
        .catch(this.onSubmitError);
    }

    /**
     * Set the classes for the 'save' button.
     *
     * @method setSaveButtonClasses
     * @param {(String)} value The 'save' button classes.
     */
    setSaveButtonClasses(value) {
        const button = this.modal.getFooter().find("[data-action='save']");
        if (!button) {
            throw new Error("Unable to find the 'save' button");
        }
        button.removeClass().addClass(value);
    }
}
