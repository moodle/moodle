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
 * Contain the events the form component can trigger.
 *
 * @module core_form/events
 * @copyright 2021 Huong Nguyen <huongnv13@gmail.com>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since 3.10
 */

import {get_string as getString} from 'core/str';

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
    uploadChanged: 'core_form/uploadChanged'
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

    return customEvent;
};

/**
 * Trigger an event to notify the file upload field has been changed.
 *
 * @method
 * @param {String} elementId The element which was changed
 * @returns {CustomEvent}
 */
 export const notifyUploadChanged = elementId => {

    const customEvent = new CustomEvent(types.uploadChanged, {
        bubbles: true,
        cancellable: false
    });

    const element = document.getElementById(elementId);
    element.dispatchEvent(customEvent);

    return customEvent;
};
