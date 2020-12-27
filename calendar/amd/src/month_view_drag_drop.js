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
            'core/notification',
            'core/str',
            'core_calendar/events',
            'core_calendar/drag_drop_data_store'
        ],
        function(
            $,
            Notification,
            Str,
            CalendarEvents,
            DataStore
        ) {

    var SELECTORS = {
        ROOT: "[data-region='calendar']",
        DRAGGABLE: '[draggable="true"][data-region="event-item"]',
        DROP_ZONE: '[data-drop-zone="month-view-day"]',
        WEEK: '[data-region="month-view-week"]',
    };
    var INVALID_DROP_ZONE_CLASS = 'bg-faded';
    var INVALID_HOVER_CLASS = 'bg-danger text-white';
    var VALID_HOVER_CLASS = 'bg-primary text-white';
    var ALL_CLASSES = INVALID_DROP_ZONE_CLASS + ' ' + INVALID_HOVER_CLASS + ' ' + VALID_HOVER_CLASS;
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
     * Determine if the given dropzone element is within the acceptable
     * time range.
     *
     * The drop zone timestamp is midnight on that day so we should check
     * that the event's acceptable timestart value
     *
     * @param {object} dropZone The drop zone day from the calendar
     * @return {bool}
     */
    var isValidDropZone = function(dropZone) {
        var dropTimestamp = dropZone.attr('data-day-timestamp');
        var minTimestart = DataStore.getMinTimestart();
        var maxTimestart = DataStore.getMaxTimestart();

        if (minTimestart && minTimestart > dropTimestamp) {
            return false;
        }

        if (maxTimestart && maxTimestart < dropTimestamp) {
            return false;
        }

        return true;
    };

    /**
     * Get the error string to display for a given drop zone element
     * if it is invalid.
     *
     * @param {object} dropZone The drop zone day from the calendar
     * @return {string}
     */
    var getDropZoneError = function(dropZone) {
        var dropTimestamp = dropZone.attr('data-day-timestamp');
        var minTimestart = DataStore.getMinTimestart();
        var maxTimestart = DataStore.getMaxTimestart();

        if (minTimestart && minTimestart > dropTimestamp) {
            return DataStore.getMinError();
        }

        if (maxTimestart && maxTimestart < dropTimestamp) {
            return DataStore.getMaxError();
        }

        return null;
    };

    /**
     * Remove all of the styling from each of the drop zones in the calendar.
     */
    var clearAllDropZonesState = function() {
        $(SELECTORS.ROOT).find(SELECTORS.DROP_ZONE).each(function(index, dropZone) {
            dropZone = $(dropZone);
            dropZone.removeClass(ALL_CLASSES);
        });
    };

    /**
     * Update the hover state for the event in the calendar to reflect
     * which days the event will be moved to.
     *
     * If the drop zone is not being hovered then it will apply some
     * styling to reflect whether the drop zone is a valid or invalid
     * drop place for the current dragging event.
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

        var valid = isValidDropZone(dropZone);
        dropZone.removeClass(ALL_CLASSES);

        if (hovered) {

            if (valid) {
                dropZone.addClass(VALID_HOVER_CLASS);
            } else {
                dropZone.addClass(INVALID_HOVER_CLASS);
            }
        } else {
            dropZone.removeClass(VALID_HOVER_CLASS + ' ' + INVALID_HOVER_CLASS);

            if (!valid) {
                dropZone.addClass(INVALID_DROP_ZONE_CLASS);
            }
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
     * Find all of the calendar event drop zones in the calendar and update the display
     * for the user to indicate which zones are valid and invalid.
     */
    var updateAllDropZonesState = function() {
        $(SELECTORS.ROOT).find(SELECTORS.DROP_ZONE).each(function(index, dropZone) {
            dropZone = $(dropZone);

            if (!isValidDropZone(dropZone)) {
                updateHoverState(dropZone, false);
            }
        });
    };


    /**
     * Set up the module level variables to track which event is being
     * dragged and how many days it spans.
     *
     * @param {event} e The dragstart event
     */
    var dragstartHandler = function(e) {
        var target = $(e.target);
        var draggableElement = target.closest(SELECTORS.DRAGGABLE);

        if (!draggableElement.length) {
            return;
        }

        var eventElement = draggableElement.find('[data-event-id]');
        var eventId = eventElement.attr('data-event-id');
        var minTimestart = draggableElement.attr('data-min-day-timestamp');
        var maxTimestart = draggableElement.attr('data-max-day-timestamp');
        var minError = draggableElement.attr('data-min-day-error');
        var maxError = draggableElement.attr('data-max-day-error');
        var eventsSelector = SELECTORS.ROOT + ' [data-event-id="' + eventId + '"]';
        var duration = $(eventsSelector).length;

        DataStore.setEventId(eventId);
        DataStore.setDurationDays(duration);

        if (minTimestart) {
            DataStore.setMinTimestart(minTimestart);
        }

        if (maxTimestart) {
            DataStore.setMaxTimestart(maxTimestart);
        }

        if (minError) {
            DataStore.setMinError(minError);
        }

        if (maxError) {
            DataStore.setMaxError(maxError);
        }

        e.dataTransfer.effectAllowed = "move";
        e.dataTransfer.dropEffect = "move";
        // Firefox requires a value to be set here or the drag won't
        // work and the dragover handler won't fire.
        e.dataTransfer.setData('text/plain', eventId);
        e.dropEffect = "move";

        updateAllDropZonesState();
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
        // Ignore dragging of non calendar events.
        if (!DataStore.hasEventId()) {
            return;
        }

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
        // Ignore dragging of non calendar events.
        if (!DataStore.hasEventId()) {
            return;
        }

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
        // Ignore dragging of non calendar events.
        if (!DataStore.hasEventId()) {
            return;
        }

        var dropZone = getDropZoneFromEvent(e);

        if (!dropZone) {
            DataStore.clearAll();
            clearAllDropZonesState();
            return;
        }

        if (isValidDropZone(dropZone)) {
            var eventId = DataStore.getEventId();
            var eventElementSelector = SELECTORS.ROOT + ' [data-event-id="' + eventId + '"]';
            var eventElement = $(eventElementSelector);
            var origin = null;

            if (eventElement.length) {
                origin = eventElement.closest(SELECTORS.DROP_ZONE);
            }

            $('body').trigger(CalendarEvents.moveEvent, [eventId, origin, dropZone]);
        } else {
            // If the drop zone is not valid then there is not need for us to
            // try to process it. Instead we can just show an error to the user.
            var message = getDropZoneError(dropZone);
            Str.get_string('errorinvaliddate', 'calendar').then(function(string) {
                Notification.exception({
                    name: string,
                    message: message || string
                });
            });
        }

        DataStore.clearAll();
        clearAllDropZonesState();

        e.preventDefault();
    };

    /**
     * Clear the data store and remove the drag indicators from the UI
     * when the drag event has finished.
     */
    var dragendHandler = function() {
        DataStore.clearAll();
        clearAllDropZonesState();
    };

    /**
     * Re-render the drop zones in the new month to highlight
     * which areas are or aren't acceptable to drop the calendar
     * event.
     */
    var calendarMonthChangedHandler = function() {
        updateAllDropZonesState();
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
                document.addEventListener('dragend', dragendHandler, false);
                $('body').on(CalendarEvents.monthChanged, calendarMonthChangedHandler);
                registered = true;
            }
        },
    };
});
