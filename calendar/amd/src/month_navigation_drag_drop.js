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
 * month view navigation.
 *
 * This code is run each time the calendar month view is re-rendered. We
 * only register the event handlers once per page load so that the in place
 * DOM updates that happen on month change don't continue to register handlers.
 *
 * @module     core_calendar/month_navigation_drag_drop
 * @class      month_navigation_drag_drop
 * @package    core_calendar
 * @copyright  2017 Ryan Wyllie <ryan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
define([
            'jquery',
            'core_calendar/drag_drop_data_store',
        ],
        function(
            $,
            DataStore
        ) {

    var SELECTORS = {
        DRAGGABLE: '[draggable="true"][data-region="event-item"]',
        DROP_ZONE: '[data-drop-zone="nav-link"]',
    };
    var HOVER_CLASS = 'bg-primary text-white';
    var TARGET_CLASS = 'drop-target';
    var HOVER_TIME = 1000; // 1 second hover to change month.

    // We store some static variables at the module level because this
    // module is called each time the calendar month view is reloaded but
    // we want some actions to only occur ones.

    /* @var {bool} registered If the event listeners have been added */
    var registered = false;
    /* @var {int} hoverTimer The timeout id of any timeout waiting for hover */
    var hoverTimer = null;
    /* @var {object} root The root nav element we're operating on */
    var root = null;

    /**
     * Add or remove the appropriate styling to indicate whether
     * the drop target is being hovered over.
     *
     * @param {object} target The target drop zone element
     * @param {bool} hovered If the element is hovered over ot not
     */
    var updateHoverState = function(target, hovered) {
        if (hovered) {
            target.addClass(HOVER_CLASS);
        } else {
            target.removeClass(HOVER_CLASS);
        }
    };

    /**
     * Add some styling to the UI to indicate that the nav links
     * are an acceptable drop target.
     */
    var addDropZoneIndicator = function() {
        root.find(SELECTORS.DROP_ZONE).addClass(TARGET_CLASS);
    };

    /**
     * Remove the styling from the nav links.
     */
    var removeDropZoneIndicator = function() {
        root.find(SELECTORS.DROP_ZONE).removeClass(TARGET_CLASS);
    };

    /**
     * Get the drop zone target from the event, if one is found.
     *
     * @param {event} e Javascript event
     * @return {object|null}
     */
    var getTargetFromEvent = function(e) {
        var target = $(e.target).closest(SELECTORS.DROP_ZONE);
        return (target.length) ? target : null;
    };

    /**
     * This will add a visual indicator to the calendar UI to
     * indicate which nav link is a valid drop zone.
     */
    var dragstartHandler = function(e) {
        // Make sure the drag event is for a calendar event.
        var eventElement = $(e.target).closest(SELECTORS.DRAGGABLE);

        if (eventElement.length) {
            addDropZoneIndicator();
        }
    };

    /**
     * Update the hover state of the target nav element when
     * the user is dragging an event over it.
     *
     * This will add a visual indicator to the calendar UI to
     * indicate which nav link is being hovered.
     *
     * @param {event} e The dragover event
     */
    var dragoverHandler = function(e) {
        // Ignore dragging of non calendar events.
        if (!DataStore.hasEventId()) {
            return;
        }

        e.preventDefault();
        var target = getTargetFromEvent(e);

        if (!target) {
            return;
        }

        // If we're not draggin a calendar event then
        // ignore it.
        if (!DataStore.hasEventId()) {
            return;
        }

        if (!hoverTimer) {
            hoverTimer = setTimeout(function() {
                target.click();
                hoverTimer = null;
            }, HOVER_TIME);
        }

        updateHoverState(target, true);
        removeDropZoneIndicator();
    };

    /**
     * Update the hover state of the target nav element that was
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

        var target = getTargetFromEvent(e);

        if (!target) {
            return;
        }

        if (hoverTimer) {
            clearTimeout(hoverTimer);
            hoverTimer = null;
        }

        updateHoverState(target, false);
        addDropZoneIndicator();
        e.preventDefault();
    };

    /**
     * Remove the visual indicator from the calendar UI that was
     * added by the dragoverHandler.
     *
     * @param {event} e The drop event
     */
    var dropHandler = function(e) {
        // Ignore dragging of non calendar events.
        if (!DataStore.hasEventId()) {
            return;
        }

        removeDropZoneIndicator();
        var target = getTargetFromEvent(e);

        if (!target) {
            return;
        }

        updateHoverState(target, false);
        e.preventDefault();
    };

    return {
        /**
         * Initialise the event handlers for the drag events.
         *
         * @param {object} rootElement The element containing calendar nav links
         */
        init: function(rootElement) {
            // Only register the handlers once on the first load.
            if (!registered) {
                // These handlers are only added the first time the module
                // is loaded because we don't want to have a new listener
                // added each time the "init" function is called otherwise we'll
                // end up with lots of stale handlers.
                document.addEventListener('dragstart', dragstartHandler, false);
                document.addEventListener('dragover', dragoverHandler, false);
                document.addEventListener('dragleave', dragleaveHandler, false);
                document.addEventListener('drop', dropHandler, false);
                document.addEventListener('dragend', removeDropZoneIndicator, false);
                registered = true;
            }

            // Update the module variable to operate on the given
            // root element.
            root = $(rootElement);

            // If we're currently dragging then add the indicators.
            if (DataStore.hasEventId()) {
                addDropZoneIndicator();
            }
        },
    };
});
