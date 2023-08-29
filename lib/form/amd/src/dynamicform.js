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
 * Display an embedded form, it is only loaded and reloaded inside its container
 *
 *
 * @module     core_form/dynamicform
 * @copyright  2019 Marina Glancy
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * See also https://docs.moodle.org/dev/Modal_and_AJAX_forms
 *
 * @example
 *    import DynamicForm from 'core_form/dynamicform';
 *
 *    const dynamicForm = new DynamicForm(document.querySelector('#mycontainer', 'pluginname\\form\\formname');
 *    dynamicForm.addEventListener(dynamicForm.events.FORM_SUBMITTED, e => {
 *        e.preventDefault();
 *        window.console.log(e.detail);
 *        dynamicForm.container.innerHTML = 'Thank you, your form is submitted!';
 *    });
 *    dynamicForm.load();
 *
 */

import * as FormChangeChecker from 'core_form/changechecker';
import * as FormEvents from 'core_form/events';
import Ajax from 'core/ajax';
import Fragment from 'core/fragment';
import Notification from 'core/notification';
import Pending from 'core/pending';
import Templates from 'core/templates';
import {getStrings} from 'core/str';
import {serialize} from './util';

/**
 * @class core_form/dynamicform
 */
export default class DynamicForm {

    /**
     * Various events that can be observed.
     *
     * @type {Object}
     */
    events = {
        // Form was successfully submitted - the response is passed to the event listener.
        // Cancellable (in order to prevent default behavior to clear the container).
        FORM_SUBMITTED: 'core_form_dynamicform_formsubmitted',
        // Cancel button was pressed.
        // Cancellable (in order to prevent default behavior to clear the container).
        FORM_CANCELLED: 'core_form_dynamicform_formcancelled',
        // User attempted to submit the form but there was client-side validation error.
        CLIENT_VALIDATION_ERROR: 'core_form_dynamicform_clientvalidationerror',
        // User attempted to submit the form but server returned validation error.
        SERVER_VALIDATION_ERROR: 'core_form_dynamicform_validationerror',
        // Error occurred while performing request to the server.
        // Cancellable (by default calls Notification.exception).
        ERROR: 'core_form_dynamicform_error',
        // Right after user pressed no-submit button,
        // listen to this event if you want to add JS validation or processing for no-submit button.
        // Cancellable.
        NOSUBMIT_BUTTON_PRESSED: 'core_form_dynamicform_nosubmitbutton',
        // Right after user pressed submit button,
        // listen to this event if you want to add additional JS validation or confirmation dialog.
        // Cancellable.
        SUBMIT_BUTTON_PRESSED: 'core_form_dynamicform_submitbutton',
        // Right after user pressed cancel button,
        // listen to this event if you want to add confirmation dialog.
        // Cancellable.
        CANCEL_BUTTON_PRESSED: 'core_form_dynamicform_cancelbutton',
    };

    /**
     * Constructor
     *
     * Creates an instance
     *
     * @param {Element} container - the parent element for the form
     * @param {string} formClass full name of the php class that extends \core_form\modal , must be in autoloaded location
     */
    constructor(container, formClass) {
        this.formClass = formClass;
        this.container = container;

        // Ensure strings required for shortforms are always available.
        getStrings([
            {key: 'collapseall', component: 'moodle'},
            {key: 'expandall', component: 'moodle'}
        ]).catch(Notification.exception);

        // Register delegated events handlers in vanilla JS.
        this.container.addEventListener('click', e => {
            if (e.target.matches('form input[type=submit][data-cancel]')) {
                e.preventDefault();
                const event = this.trigger(this.events.CANCEL_BUTTON_PRESSED, e.target);
                if (!event.defaultPrevented) {
                    this.processCancelButton();
                }
            } else if (e.target.matches('form input[type=submit][data-no-submit="1"]')) {
                e.preventDefault();
                const event = this.trigger(this.events.NOSUBMIT_BUTTON_PRESSED, e.target);
                if (!event.defaultPrevented) {
                    this.processNoSubmitButton(e.target);
                }
            }
        });

        this.container.addEventListener('submit', e => {
            if (e.target.matches('form')) {
                e.preventDefault();
                const event = this.trigger(this.events.SUBMIT_BUTTON_PRESSED);
                if (!event.defaultPrevented) {
                    this.submitFormAjax();
                }
            }
        });
    }

    /**
     * Loads the form via AJAX and shows it inside a given container
     *
     * @param {Object} args
     * @return {Promise}
     * @public
     */
    load(args = null) {
        const formData = serialize(args || {});
        const pendingPromise = new Pending('core_form/dynamicform:load');
        return this.getBody(formData)
        .then((resp) => this.updateForm(resp))
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
        this.container.dispatchEvent(e);
        return e;
    }

    /**
     * Add listener for an event
     *
     * @param {array} args
     * @example:
     *    const dynamicForm = new DynamicForm(...);
     *    dynamicForm.addEventListener(dynamicForm.events.FORM_SUBMITTED, e => {
     *        e.preventDefault();
     *        window.console.log(e.detail);
     *        dynamicForm.container.innerHTML = 'Thank you, your form is submitted!';
     *    });
     */
    addEventListener(...args) {
        this.container.addEventListener(...args);
    }

    /**
     * Get form body
     *
     * @param {String} formDataString form data in format of a query string
     * @private
     * @return {Promise}
     */
    getBody(formDataString) {
        return Ajax.call([{
            methodname: 'core_form_dynamic_form',
            args: {
                formdata: formDataString,
                form: this.formClass,
            }
        }])[0]
        .then(response => {
            return {html: response.html, js: Fragment.processCollectedJavascript(response.javascript)};
        });
    }

    /**
     * On form submit
     *
     * @param {*} response Response received from the form's "process" method
     */
    onSubmitSuccess(response) {
        const event = this.trigger(this.events.FORM_SUBMITTED, response);
        if (event.defaultPrevented) {
            return;
        }

        // Default implementation is to remove the form. Event listener should either remove or reload the form
        // since its contents is no longer correct. For example, if an element was created as a result of
        // form submission, the "id" in the form would be still zero. Also the server-side validation
        // errors from the previous submission may still be present.
        this.container.innerHTML = '';
    }

    /**
     * On exception during form processing
     *
     * @private
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
     * Click on a "submit" button that is marked in the form as registerNoSubmitButton()
     *
     * @method submitButtonPressed
     * @param {Element} button that was pressed
     * @fires event:formSubmittedByJavascript
     */
    processNoSubmitButton(button) {
        const pendingPromise = new Pending('core_form/dynamicform:nosubmit');
        const form = this.getFormNode();
        const formData = new URLSearchParams([...(new FormData(form)).entries()]);
        formData.append(button.getAttribute('name'), button.getAttribute('value'));

        FormEvents.notifyFormSubmittedByJavascript(form, true);

        // Add the button name to the form data and submit it.
        this.disableButtons();

        this.getBody(formData.toString())
        .then(resp => this.updateForm(resp))
        .then(pendingPromise.resolve)
        .catch(exception => this.onSubmitError(exception));
    }

    /**
     * Get the form node from the Dialogue.
     *
     * @returns {HTMLFormElement}
     */
    getFormNode() {
        return this.container.querySelector('form');
    }

    /**
     * Notifies listeners that form dirty state should be reset.
     *
     * @fires event:formSubmittedByJavascript
     */
    notifyResetFormChanges() {
        FormEvents.notifyFormSubmittedByJavascript(this.getFormNode(), true);
        FormChangeChecker.resetFormDirtyState(this.getFormNode());
    }

    /**
     * Click on a "cancel" button
     */
    processCancelButton() {
        // Notify listeners that the form is about to be submitted (this will reset atto autosave).
        this.notifyResetFormChanges();

        const event = this.trigger(this.events.FORM_CANCELLED);
        if (!event.defaultPrevented) {
            // By default removes the form from the DOM.
            this.container.innerHTML = '';
        }
    }

    /**
     * Update form contents
     *
     * @param {object} param
     * @param {string} param.html
     * @param {string} param.js
     * @returns {Promise}
     */
    updateForm({html, js}) {
        return Templates.replaceNodeContents(this.container, html, js);
    }

    /**
     * Validate form elements
     * @return {Boolean} Whether client-side validation has passed, false if there are errors
     * @fires event:formSubmittedByJavascript
     */
    validateElements() {
        // Notify listeners that the form is about to be submitted (this will reset atto autosave).
        FormEvents.notifyFormSubmittedByJavascript(this.getFormNode());

        // Now the change events have run, see if there are any "invalid" form fields.
        const invalid = [...this.container.querySelectorAll('[aria-invalid="true"], .error')];

        // If we found invalid fields, focus on the first one and do not submit via ajax.
        if (invalid.length) {
            invalid[0].focus();
            return false;
        }

        return true;
    }

    /**
     * Disable buttons during form submission
     */
    disableButtons() {
        this.container.querySelectorAll('form input[type="submit"]')
            .forEach(el => el.setAttribute('disabled', true));
    }

    /**
     * Enable buttons after form submission (on validation error)
     */
    enableButtons() {
        this.container.querySelectorAll('form input[type="submit"]')
            .forEach(el => el.removeAttribute('disabled'));
    }

    /**
     * Submit the form via AJAX call to the core_form_dynamic_form WS
     */
    async submitFormAjax() {
        // If we found invalid fields, focus on the first one and do not submit via ajax.
        if (!(await this.validateElements())) {
            this.trigger(this.events.CLIENT_VALIDATION_ERROR, null, false);
            return;
        }
        this.disableButtons();

        // Convert all the form elements values to a serialised string.
        const form = this.container.querySelector('form');
        const formData = new URLSearchParams([...(new FormData(form)).entries()]);

        // Now we can continue...
        Ajax.call([{
            methodname: 'core_form_dynamic_form',
            args: {
                formdata: formData.toString(),
                form: this.formClass
            }
        }])[0]
        .then((response) => {
            if (!response.submitted) {
                // Form was not submitted, it could be either because validation failed or because no-submit button was pressed.
                this.updateForm({html: response.html, js: Fragment.processCollectedJavascript(response.javascript)});
                this.enableButtons();
                this.trigger(this.events.SERVER_VALIDATION_ERROR, null, false);
            } else {
                // Form was submitted properly.
                const data = JSON.parse(response.data);
                this.enableButtons();
                this.notifyResetFormChanges();
                this.onSubmitSuccess(data);
            }
            return null;
        })
        .catch(exception => this.onSubmitError(exception));
    }
}
