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
 * Javascript events for the `tool_usertours` subsystem.
 *
 * @module tool_usertours/events
 * @copyright 2021 Andrew Lyons <andrew@nicols.co.uk>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 * @example <caption>Example of listening to a step rendering event and cancelling it.</caption>
 * import {eventTypes as userTourEvents} from 'tool_usertours/events';
 *
 * document.addEventListener(userTourEvents.stepRender, e => {
 *     console.log(e.detail.tour); // The Tour instance
 *     e.preventDefault();
 * });
 */

/**
 * Events for the component.
 *
 * @constant
 * @property {object} eventTypes
 * @property {String} eventTypes.stepRender See {@link event:tool_usertours/stepRender}
 * @property {String} eventTypes.stepRendered See {@link event:tool_usertours/stepRendered}
 * @property {String} eventTypes.tourStart See {@link event:tool_usertours/tourStart}
 * @property {String} eventTypes.tourStarted See {@link event:tool_usertours/tourStarted}
 * @property {String} eventTypes.tourEnd See {@link event:tool_usertours/tourEnd}
 * @property {String} eventTypes.tourEnded See {@link event:tool_usertours/tourEnded}
 * @property {String} eventTypes.stepHide See {@link event:tool_usertours/stepHide}
 * @property {String} eventTypes.stepHidden See {@link event:tool_usertours/stepHidden}
 */
export const eventTypes = {
    /**
     * An event triggered before a user tour step is rendered.
     *
     * This event is cancellable.
     *
     * @event tool_usertours/stepRender
     * @type {CustomEvent}
     * @property {object} detail
     * @property {tool_usertours/tour} detail.tour
     * @property {object} detail.stepConfig
     */
    stepRender: 'tool_usertours/stepRender',

    /**
     * An event triggered after a user tour step has been rendered.
     *
     * @event tool_usertours/stepRendered
     * @type {CustomEvent}
     * @property {object} detail
     * @property {tool_usertours/tour} detail.tour
     * @property {object} detail.stepConfig
     */
    stepRendered: 'tool_usertours/stepRendered',

    /**
     * An event triggered before a user tour starts.
     *
     * This event is cancellable.
     *
     * @event tool_usertours/tourStart
     * @type {CustomEvent}
     * @property {object} detail
     * @property {tool_usertours/tour} detail.tour
     * @property {Number} detail.startAt
     */
    tourStart: 'tool_usertours/tourStart',

    /**
     * An event triggered after a user tour has started.
     *
     * @event tool_usertours/tourStarted
     * @property {object} detail
     * @property {tool_usertours/tour} detail.tour
     * @type {CustomEvent}
     */
    tourStarted: 'tool_usertours/tourStarted',

    /**
     * An event triggered before a tour ends.
     *
     * This event is cancellable.
     *
     * @event tool_usertours/tourEnd
     * @property {object} detail
     * @property {tool_usertours/tour} detail.tour
     * @type {CustomEvent}
     */
    tourEnd: 'tool_usertours/tourEnd',

    /**
     * An event triggered after a tour has ended.
     *
     * @event tool_usertours/tourEnded
     * @property {object} detail
     * @property {tool_usertours/tour} detail.tour
     * @type {CustomEvent}
     */
    tourEnded: 'tool_usertours/tourEnded',

    /**
     * An event triggered before a step is hidden.
     *
     * This event is cancellable.
     *
     * @event tool_usertours/stepHide
     * @property {object} detail
     * @property {tool_usertours/tour} detail.tour
     * @type {CustomEvent}
     */
    stepHide: 'tool_usertours/stepHide',

    /**
     * An event triggered after a step has been hidden.
     *
     * @event tool_usertours/stepHidden
     * @property {object} detail
     * @property {tool_usertours/tour} detail.tour
     * @type {CustomEvent}
     */
    stepHidden: 'tool_usertours/stepHidden',
};
