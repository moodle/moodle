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
 * @package core_form
 * @copyright 2021 Huong Nguyen <huongnv13@gmail.com>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since 3.10
 */

import {get_string as getString} from 'core/str';
import {dispatchEvent} from 'core/event_dispatcher';

let changesMadeString;
getString('changesmadereallygoaway', 'moodle').then(string => {
    changesMadeString = string;
    return string;
}).catch();

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
 * List of the events.
 **/
export const types = {
    uploadStarted: 'core_form/uploadStarted',
    uploadCompleted: 'core_form/uploadCompleted',
};

/**
 * Events for `core_form`.
 *
 * @constant
 * @property {String} formFieldValidationFailed See {@link event:formFieldValidationFailed}
 * @property {String} formSubmittedByJavascript See {@link event:formSubmittedByJavascript}
 */
export const eventTypes = {
    /**
     * An event triggered when an mform is about to be submitted via javascript.
     *
     * @event formSubmittedByJavascript
     * @type {CustomEvent}
     * @property {HTMLElement} target The form that was submitted
     * @property {Boolean} skipValidation Whether the form was submitted without validation (i.e. via a Cancel button)
     * @property {Boolean} fallbackHandled Whether the legacy YUI event has been handled
     */
    formSubmittedByJavascript: 'core_form/submittedByJavascript',

    /**
     * An event triggered upon form field validation failure.
     *
     * @event formFieldValidationFailed
     * @type {CustomEvent}
     * @property {HTMLElement} target The field that failed validation
     * @property {String} message The message displayed upon failure
     */
    formFieldValidationFailed: 'core_form/fieldValidationFailed',
};

/**
 * Trigger upload start event.
 *
 * @param {String} elementId
 * @returns {CustomEvent<unknown>}
 */
export const triggerUploadStarted = elementId => {
    // Add an additional check for changes made.
    window.addEventListener('beforeunload', changesMadeCheck);
    const customEvent = new CustomEvent(types.uploadStarted, {
        bubbles: true,
        cancellable: false
    });
    const element = document.getElementById(elementId);
    element.dispatchEvent(customEvent);

    return customEvent;
};

/**
 * Trigger upload complete event.
 *
 * @param {String} elementId
 * @returns {CustomEvent<unknown>}
 */
export const triggerUploadCompleted = elementId => {
    // Remove the additional check for changes made.
    window.removeEventListener('beforeunload', changesMadeCheck);
    const customEvent = new CustomEvent(types.uploadCompleted, {
        bubbles: true,
        cancellable: false
    });
    const element = document.getElementById(elementId);
    element.dispatchEvent(customEvent);

    return CustomEvent;
};

// These are only imported for legacy.
import jQuery from 'jquery';
import Y from 'core/yui';

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

let legacyEventsRegistered = false;
if (!legacyEventsRegistered) {
    // The following event triggers are legacy and will be removed in the future.
    // The following approach provides a backwards-compatability layer for the new events.
    // Code should be updated to make use of native events.
    Y.use('event', 'moodle-core-event', () => {

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
