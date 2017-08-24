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
 * A javascript module to handle calendar drag and drop in the calendar
 * month view.
 *
 * @module     core_calendar/month_view_drag_drop
 * @class      month_view_drag_drop
 * @package    core_calendar
 * @copyright  2017 Ryan Wyllie <ryan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
define([
            'jquery',
            'core_calendar/events',
            'core_calendar/drag_drop_data_store'
        ],
        function(
            $,
            CalendarEvents,
            DataStore
        ) {

    var SELECTORS = {
        ROOT: "[data-region='calendar']",
        DRAGGABLE: '[draggable="true"][data-region="event-item"]',
        DROP_ZONE: '[data-drop-zone="month-view-day"]',
        WEEK: '[data-region="month-view-week"]',
    };
    var HOVER_CLASS = 'bg-primary text-white';
    /* @var {bool} registered If the event listeners have been added */
    var registered = false;

    /**
     * Get the correct drop zone element from the given javascript
     * event.
     *
     * @param {event} e The javascript event
     * @return {object|null}
     */
    var getDropZoneFromEvent = function(e) {
        var dropZone = $(e.target).closest(SELECTORS.DROP_ZONE);
        return (dropZone.length) ? dropZone : null;
    };

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
    var updateHoverState = function(dropZone, hovered, count) {
        if (typeof count === 'undefined') {
            // This is how many days we need to highlight.
            count = DataStore.getDurationDays();
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
        var eventElement = $(e.target).closest(SELECTORS.DRAGGABLE);

        if (!eventElement.length) {
            return;
        }

        eventElement = eventElement.find('[data-event-id]');

        var eventId = eventElement.attr('data-event-id');
        var eventsSelector = SELECTORS.ROOT + ' [data-event-id="' + eventId + '"]';
        var duration = $(eventsSelector).length;

        DataStore.setEventId(eventId);
        DataStore.setDurationDays(duration);

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

        var dropZone = getDropZoneFromEvent(e);

        if (!dropZone) {
            return;
        }

        updateHoverState(dropZone, true);
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
        var dropZone = getDropZoneFromEvent(e);

        if (!dropZone) {
            return;
        }

        updateHoverState(dropZone, false);
        e.preventDefault();
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
        var dropZone = getDropZoneFromEvent(e);

        if (!dropZone) {
            DataStore.clearAll();
            return;
        }

        var eventId = DataStore.getEventId();
        var eventElementSelector = SELECTORS.ROOT + ' [data-event-id="' + eventId + '"]';
        var eventElement = $(eventElementSelector);
        var origin = null;
        var destination = $(e.target).closest(SELECTORS.DROP_ZONE);

        if (eventElement.length) {
            origin = eventElement.closest(SELECTORS.DROP_ZONE);
        }

        updateHoverState(dropZone, false);
        $('body').trigger(CalendarEvents.moveEvent, [eventId, origin, destination]);
        DataStore.clearAll();

        e.preventDefault();
    };

    return {
        /**
         * Initialise the event handlers for the drag events.
         */
        init: function() {
            if (!registered) {
                // These handlers are only added the first time the module
                // is loaded because we don't want to have a new listener
                // added each time the "init" function is called otherwise we'll
                // end up with lots of stale handlers.
                document.addEventListener('dragstart', dragstartHandler, false);
                document.addEventListener('dragover', dragoverHandler, false);
                document.addEventListener('dragleave', dragleaveHandler, false);
                document.addEventListener('drop', dropHandler, false);
                registered = true;
            }
        },
    };
});
