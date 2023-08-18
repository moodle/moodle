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
 * Javascript events for the `core_form` subsystem.
 *
 * @module core_form/events
 * @copyright 2021 Huong Nguyen <huongnv13@gmail.com>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since 3.10
 *
 * @example <caption>Example of listening to a form event.</caption>
 * import {eventTypes as formEventTypes} from 'core_form/events';
 *
 * document.addEventListener(formEventTypes.formSubmittedByJavascript, e => {
 *     window.console.log(e.target); // The form that was submitted.
 *     window.console.log(e.detail.skipValidation); // Whether form validation was skipped.
 * });
 */

import {getString} from 'core/str';
import {dispatchEvent} from 'core/event_dispatcher';

let changesMadeString;

/**
 * Prevent user navigate away when upload progress still running.
 * @param {Event} e The event
 */
const changesMadeCheck = e => {
    if (e) {
        e.returnValue = changesMadeString;
    }
};

/**
 * Events for `core_form`.
 *
 * @constant
 * @property {String} formError See {@link event:core_form/error}
 * @property {String} formFieldValidationFailed See {@link event:core_form/fieldValidationFailed}
 * @property {String} formSubmittedByJavascript See {@link event:core_form/submittedByJavascript}
 * @property {String} uploadChanged See {@link event:core_form/uploadChanged}
 */
export const eventTypes = {
    /**
     * An event triggered when a form contains an error
     *
     * @event formError
     * @type {CustomEvent}
     * @property {HTMLElement} target The form field which errored
     */
    formError: 'core_form/error',

    /**
     * An event triggered when an mform is about to be submitted via javascript.
     *
     * @event core_form/submittedByJavascript
     * @type {CustomEvent}
     * @property {HTMLElement} target The form that was submitted
     * @property {object} detail
     * @property {boolean} detail.skipValidation Whether the form was submitted without validation (i.e. via a Cancel button)
     * @property {boolean} detail.fallbackHandled Whether the legacy YUI event has been handled
     */
    formSubmittedByJavascript: 'core_form/submittedByJavascript',

    /**
     * An event triggered upon form field validation failure.
     *
     * @event core_form/fieldValidationFailed
     * @type {CustomEvent}
     * @property {HTMLElement} target The field that failed validation
     * @property {object} detail
     * @property {String} detail.message The message displayed upon failure
     */
    formFieldValidationFailed: 'core_form/fieldValidationFailed',

    /**
     * An event triggered when an upload is started
     *
     * @event core_form/uploadStarted
     * @type {CustomEvent}
     * @property {HTMLElement} target The location where the upload began
     */
    uploadStarted: 'core_form/uploadStarted',

    /**
     * An event triggered when an upload completes
     *
     * @event core_form/uploadCompleted
     * @type {CustomEvent}
     * @property {HTMLElement} target The location where the upload completed
     */
    uploadCompleted: 'core_form/uploadCompleted',

    /**
     * An event triggered when a file upload field has been changed.
     *
     * @event core_form/uploadChanged
     * @type {CustomEvent}
     * @property {HTMLElement} target The form field which was changed
     */
    uploadChanged: 'core_form/uploadChanged',
};

// These are only imported for legacy.
import jQuery from 'jquery';
import Y from 'core/yui';

/**
 * Trigger an event to indicate that a form field contained an error.
 *
 * @method notifyFormError
 * @param {HTMLElement} field The form field causing the error
 * @returns {CustomEvent}
 * @fires formError
 */
export const notifyFormError = field => dispatchEvent(eventTypes.formError, {}, field);

/**
 * Trigger an event to indiciate that a form was submitted by Javascript.
 *
 * @method
 * @param {HTMLElement} form The form that was submitted
 * @param {Boolean} skipValidation Submit the form without validation. E.g. "Cancel".
 * @param {Boolean} fallbackHandled The legacy YUI event has been handled
 * @returns {CustomEvent}
 * @fires formSubmittedByJavascript
 */
export const notifyFormSubmittedByJavascript = (form, skipValidation = false, fallbackHandled = false) => {
    if (skipValidation) {
        window.skipClientValidation = true;
    }

    const customEvent = dispatchEvent(
        eventTypes.formSubmittedByJavascript,
        {
            skipValidation,
            fallbackHandled,
        },
        form
    );

    if (skipValidation) {
        window.skipClientValidation = false;
    }

    return customEvent;
};

/**
 * Trigger an event to indicate that a form field contained an error.
 *
 * @method notifyFieldValidationFailure
 * @param {HTMLElement} field The field which failed validation
 * @param {String} message The message displayed
 * @returns {CustomEvent}
 * @fires formFieldValidationFailed
 */
export const notifyFieldValidationFailure = (field, message) => dispatchEvent(
    eventTypes.formFieldValidationFailed,
    {
        message,
    },
    field,
    {
        cancelable: true
    }
);

/**
 * Trigger an event to indicate that an upload was started.
 *
 * @method
 * @param {String} elementId The element which was uploaded to
 * @returns {CustomEvent}
 * @fires uploadStarted
 */
export const notifyUploadStarted = async elementId => {
    // Add an additional check for changes made.
    changesMadeString = await getString('changesmadereallygoaway', 'moodle');
    window.addEventListener('beforeunload', changesMadeCheck);

    return dispatchEvent(
        eventTypes.uploadStarted,
        {},
        document.getElementById(elementId),
        {
            bubbles: true,
            cancellable: false,
        }
    );
};

/**
 * Trigger an event to indicate that an upload was completed.
 *
 * @method
 * @param {String} elementId The element which was uploaded to
 * @returns {CustomEvent}
 * @fires uploadCompleted
 */
export const notifyUploadCompleted = elementId => {
    // Remove the additional check for changes made.
    window.removeEventListener('beforeunload', changesMadeCheck);

    return dispatchEvent(
        eventTypes.uploadCompleted,
        {},
        document.getElementById(elementId),
        {
            bubbles: true,
            cancellable: false,
        }
    );
};

/**
 * Trigger upload start event.
 *
 * @method
 * @param {String} elementId
 * @returns {CustomEvent}
 * @fires uploadStarted
 * @deprecated Since Moodle 4.0 See {@link module:core_form/events.notifyUploadStarted notifyUploadStarted}
 */
export const triggerUploadStarted = notifyUploadStarted;

/**
 * Trigger upload complete event.
 *
 * @method
 * @param {String} elementId
 * @returns {CustomEvent}
 * @fires uploadCompleted
 * @deprecated Since Moodle 4.0 See {@link module:core_form/events.notifyUploadCompleted notifyUploadCompleted}
 */
export const triggerUploadCompleted = notifyUploadCompleted;

/**
 * List of the events.
 *
 * @deprecated since Moodle 4.0. See {@link module:core_form/events.eventTypes eventTypes} instead.
 **/
export const types = {
    uploadStarted: 'core_form/uploadStarted',
    uploadCompleted: 'core_form/uploadCompleted',
};

let legacyEventsRegistered = false;
if (!legacyEventsRegistered) {
    // The following event triggers are legacy and will be removed in the future.
    // The following approach provides a backwards-compatability layer for the new events.
    // Code should be updated to make use of native events.
    Y.use('event', 'moodle-core-event', () => {

        // Watch for the new native formError event, and trigger the legacy YUI event.
        document.addEventListener(eventTypes.formError, e => {
            const element = Y.one(e.target);
            const formElement = Y.one(e.target.closest('form'));

            Y.Global.fire(
                M.core.globalEvents.FORM_ERROR,
                {
                    formid: formElement.generateID(),
                    elementid: element.generateID(),
                }
            );
        });

        // Watch for the new native formSubmittedByJavascript event, and trigger the legacy YUI event.
        document.addEventListener(eventTypes.formSubmittedByJavascript, e => {
            if (e.detail.fallbackHandled) {
                // This event was originally generated by a YUI event.
                // Do not generate another as this will recurse.
                return;
            }

            if (e.skipValidation) {
                window.skipClientValidation = true;
            }

            // Trigger the legacy YUI event.
            const form = Y.one(e.target);
            form.fire(
                M.core.event.FORM_SUBMIT_AJAX,
                {
                    currentTarget: form,
                    fallbackHandled: true,
                }
            );

            if (e.skipValidation) {
                window.skipClientValidation = false;
            }
        });
    });

    // Watch for the new native formFieldValidationFailed event, and trigger the legacy jQuery event.
    document.addEventListener(eventTypes.formFieldValidationFailed, e => {
        // Note: The "core_form-field-validation" event is hard-coded in core/event.
        // This is not included to prevent cyclic module dependencies.
        const legacyEvent = jQuery.Event("core_form-field-validation");

        jQuery(e.target).trigger(legacyEvent, e.detail.message);
    });

    legacyEventsRegistered = true;
}

/**
 * Trigger an event to notify the file upload field has been changed.
 *
 * @method
 * @param {string} elementId The element which was changed
 * @returns {CustomEvent}
 * @fires uploadChanged
 */
export const notifyUploadChanged = elementId => dispatchEvent(
    eventTypes.uploadChanged,
    {},
    document.getElementById(elementId),
    {
        bubbles: true,
        cancellable: false,
    }
);
