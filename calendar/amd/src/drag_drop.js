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
 * A javascript module to handle calendar drag and drop. This module
 * unfortunately requires some state to be maintained because of the
 * limitations of the HTML5 drag and drop API which means it can't
 * be used multiple times with the current implementation.
 *
 * @module     core_calendar/drag_drop
 * @class      drag_drop
 * @package    core_calendar
 * @copyright  2017 Ryan Wyllie <ryan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
define([
            'jquery',
            'core_calendar/events'
        ],
        function(
            $,
            CalendarEvents
        ) {

    var SELECTORS = {
        ROOT: "[data-region='calendar']",
        DRAGGABLE: '[draggable="true"]',
        DROP_ZONE: '[data-drop-zone="true"]',
        WEEK: '[data-region="month-view-week"]',
    };
    var HOVER_CLASS = 'bg-primary';

    // Unfortunately we are required to maintain some module
    // level state due to the limitations of the HTML5 drag
    // and drop API. Specifically the inability to pass data
    // between the dragstate and dragover events handlers
    // using the DataTransfer object in the event.

    /** @var int eventId The event id being moved. */
    var eventId = null;
    /** @var int duration The number of days the event spans */
    var duration = null;

    /**
     * Update the hover state for the event in the calendar to reflect
     * which days the event will be moved to.
     *
     * This funciton supports events spanning multiple days and will
     * recurse to highlight (or remove highlight) each of the days
     * that the event will be moved to.
     *
     * For example: An event with a duration of 3 days will have
     * 3 days highlighted when it's dragged elsewhere in the calendar.
     * The current drag target and the 2 days following it (including
     * wrapping to the next week if necessary).
     *
     * @param {string|object} target The drag target element
     * @param {bool} hovered If the target is hovered or not
     * @param {int} count How many days to highlight (default to duration)
     */
    var updateHoverState = function(target, hovered, count) {
        var dropZone = $(target).closest(SELECTORS.DROP_ZONE);
        if (typeof count === 'undefined') {
            // This is how many days we need to highlight.
            count = duration;
        }

        if (hovered) {
            dropZone.addClass(HOVER_CLASS);
        } else {
            dropZone.removeClass(HOVER_CLASS);
        }

        count--;

        // If we've still got days to highlight then we should
        // find the next day.
        if (count > 0) {
            var nextDropZone = dropZone.next();

            // If there are no more days in this week then we
            // need to move down to the next week in the calendar.
            if (!nextDropZone.length) {
                var nextWeek = dropZone.closest(SELECTORS.WEEK).next();

                if (nextWeek.length) {
                    nextDropZone = nextWeek.children(SELECTORS.DROP_ZONE).first();
                }
            }

            // If we found another day then let's recursively
            // update it's hover state.
            if (nextDropZone.length) {
                updateHoverState(nextDropZone, hovered, count);
            }
        }
    };

    /**
     * Set up the module level variables to track which event is being
     * dragged and how many days it spans.
     *
     * @param {event} e The dragstart event
     */
    var dragstartHandler = function(e) {
        var eventElement = $(e.target);

        if (!eventElement.is('[data-event-id]')) {
            eventElement = eventElement.find('[data-event-id]');
        }

        eventId = eventElement.attr('data-event-id');

        var eventsSelector = SELECTORS.ROOT + ' [data-event-id="' + eventId + '"]';
        duration = $(eventsSelector).length;

        e.dataTransfer.effectAllowed = "move";
        e.dataTransfer.dropEffect = "move";
        // Firefox requires a value to be set here or the drag won't
        // work and the dragover handler won't fire.
        e.dataTransfer.setData('text/plain', eventId);
        e.dropEffect = "move";
    };

    /**
     * Update the hover state of the target day element when
     * the user is dragging an event over it.
     *
     * This will add a visual indicator to the calendar UI to
     * indicate which day(s) the event will be moved to.
     *
     * @param {event} e The dragstart event
     */
    var dragoverHandler = function(e) {
        e.preventDefault();
        updateHoverState(e.target, true);
    };

    /**
     * Update the hover state of the target day element that was
     * previously dragged over but has is no longer a drag target.
     *
     * This will remove the visual indicator from the calendar UI
     * that was added by the dragoverHandler.
     *
     * @param {event} e The dragstart event
     */
    var dragleaveHandler = function(e) {
        e.preventDefault();
        updateHoverState(e.target, false);
    };

    /**
     * Determines the event element, origin day, and destination day
     * once the user drops the calendar event. These three bits of data
     * are provided as the payload to the "moveEvent" calendar javascript
     * event that is fired.
     *
     * This will remove the visual indicator from the calendar UI
     * that was added by the dragoverHandler.
     *
     * @param {event} e The dragstart event
     */
    var dropHandler = function(e) {
        e.preventDefault();

        var eventElementSelector = SELECTORS.ROOT + ' [data-event-id="' + eventId + '"]';
        var eventElement = $(eventElementSelector);
        var origin = eventElement.closest(SELECTORS.DROP_ZONE);
        var destination = $(e.target).closest(SELECTORS.DROP_ZONE);

        updateHoverState(e.target, false);
        $('body').trigger(CalendarEvents.moveEvent, [eventElement, origin, destination]);
    };

    return {
        /**
         * Initialise the event handlers for the drag events.
         *
         * @param {object} root The root calendar element that containers the drag drop elements
         */
        init: function(root) {
            root = $(root);

            root.find(SELECTORS.DRAGGABLE).each(function(index, element) {
                element.addEventListener('dragstart', dragstartHandler, true);
            });

            root.find(SELECTORS.DROP_ZONE).each(function(index, element) {
                element.addEventListener('dragover', dragoverHandler, true);
                element.addEventListener('dragleave', dragleaveHandler, true);
                element.addEventListener('drop', dropHandler, true);
            });
        },
    };
});
