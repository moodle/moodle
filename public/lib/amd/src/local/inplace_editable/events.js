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
 * Inplace editable module events
 *
 * @module      core/local/inplace_editable/events
 * @copyright   2021 Paul Holden <paulh@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import {dispatchEvent} from 'core/event_dispatcher';

/**
 * Module events
 *
 * @constant
 * @property {String} elementUpdated See {@link event:core/inplace_editable:updated}
 * @property {String} elementUpdateFailed See {@link event:core/inplace_editable:updateFailed}
 */
export const eventTypes = {

    /**
     * Event triggered when an element has been updated
     *
     * @event core/inplace_editable:updated
     * @type {CustomEvent}
     * @property {HTMLElement} target The element that was updated
     * @property {Object} detail
     * @property {Object} detail.ajaxreturn The data returned from the update AJAX request
     * @property {String} detail.oldvalue The previous value of the element
     */
    elementUpdated: 'core/inplace_editable:updated',

    /**
     * Event triggered when an element update has failed
     *
     * @event core/inplace_editable:updateFailed
     * @type {CustomEvent}
     * @property {HTMLElement} target The element that failed to update
     * @property {Object} detail
     * @property {Object} detail.exception The raised exception
     * @property {String} detail.newvalue The intended value of the element
     */
    elementUpdateFailed: 'core/inplace_editable:updateFailed',
};

/**
 * Notify element of successful update
 *
 * @method
 * @param {HTMLElement} element The element that was updated
 * @param {Object} ajaxreturn The data returned from the update AJAX request
 * @param {String} oldvalue The previous value of the element
 * @returns {CustomEvent}
 * @fires event:core/inplace_editable:updated
 */
export const notifyElementUpdated = (element, ajaxreturn, oldvalue) => dispatchEvent(
    eventTypes.elementUpdated,
    {
        ajaxreturn,
        oldvalue,
    },
    element
);

/**
 * Notify element of failed update
 *
 * @method
 * @param {HTMLElement} element The element that failed to update
 * @param {Object} exception The raised exception
 * @param {String} newvalue The intended value of the element
 * @returns {CustomEvent}
 * @fires event:core/inplace_editable:updateFailed
 */
export const notifyElementUpdateFailed = (element, exception, newvalue) => dispatchEvent(
    eventTypes.elementUpdateFailed,
    {
        exception,
        newvalue,
    },
    element,
    {
        cancelable: true
    }
);
