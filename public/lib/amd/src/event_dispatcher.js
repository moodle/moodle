// This file is part of Moodle - http://moodle.org/ //
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
 * An Event dispatcher used to dispatch Native JS CustomEvent objects with custom default properties.
 *
 * @module     core/event_dispatcher
 * @copyright  2021 Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since      4.0
 */

/**
 * Dispatch an event as a CustomEvent on the specified container.
 * By default events are bubbled, and cancelable.
 *
 * The eventName should typically by sourced using a constant. See the supplied examples.
 *
 * Note: This function uses native events. Any additional details are passed to the function in event.detail.
 *
 * This function mimics the behaviour of EventTarget.dispatchEvent but bubbles by default.
 *
 * @method dispatchEvent
 * @param {String} eventName The name of the event
 * @param {Object} detail Any additional details to pass into the eveent
 * @param {HTMLElement} container The point at which to dispatch the event
 * @param {Object} options
 * @param {Boolean} options.bubbles Whether to bubble up the DOM
 * @param {Boolean} options.cancelable Whether preventDefault() can be called
 * @param {Boolean} options.composed Whether the event can bubble across the ShadowDOM bounadry
 * @returns {CustomEvent}
 *
 * @example <caption>Using a native CustomEvent to indicate that some example data was displayed.</caption>
 * // mod/example/amd/src/events.js
 *
 * import {dispatchEvent} from 'core/event_dispatcher';
 *
 * export const eventTypes = {
 *     exampleDataDisplayed: 'mod_example/exampleDataDisplayed',
 * };
 *
 * export const notifyExampleDisplayed = someArgument => dispatchEvent(eventTypes.exampleDataDisplayed, {
 *     someArgument,
 * }, document, {
 *     cancelable: false,
 * });
 */
export const dispatchEvent = (
    eventName,
    detail = {},
    container = document,
    {
        bubbles = true,
        cancelable = false,
        composed = false,
    } = {}
) => {
    const customEvent = new CustomEvent(
        eventName,
        {
            bubbles,
            cancelable,
            composed,
            detail,
        }
    );

    container.dispatchEvent(customEvent);

    return customEvent;
};
