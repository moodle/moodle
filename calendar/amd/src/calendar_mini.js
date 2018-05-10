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
 * This module is the highest level module for the calendar. It is
 * responsible for initialising all of the components required for
 * the calendar to run. It also coordinates the interaction between
 * components by listening for and responding to different events
 * triggered within the calendar UI.
 *
 * @module     core_calendar/calendar
 * @package    core_calendar
 * @copyright  2017 Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
define([
    'jquery',
    'core_calendar/selectors',
    'core_calendar/events',
    'core_calendar/view_manager',
],
function(
    $,
    CalendarSelectors,
    CalendarEvents,
    CalendarViewManager
) {

    /**
     * Listen to and handle any calendar events fired by the calendar UI.
     *
     * @method registerCalendarEventListeners
     * @param {object} root The calendar root element
     */
    var registerCalendarEventListeners = function(root) {
        var body = $('body');
        var namespace = '.' + root.attr('id');

        body.on(CalendarEvents.created + namespace, root, reloadMonth);
        body.on(CalendarEvents.deleted + namespace, root, reloadMonth);
        body.on(CalendarEvents.updated + namespace, root, reloadMonth);
        body.on(CalendarEvents.eventMoved + namespace, root, reloadMonth);
    };

    /**
     * Reload the month view in this month.
     *
     * @param {EventFacade} e
     */
    var reloadMonth = function(e) {
        var root = e.data;
        var body = $('body');
        var namespace = '.' + root.attr('id');

        if (root.is(':visible')) {
            CalendarViewManager.reloadCurrentMonth(root);
        } else {
            // The root has been removed.
            // Remove all events in the namespace.
            body.off(CalendarEvents.created + namespace);
            body.off(CalendarEvents.deleted + namespace);
            body.off(CalendarEvents.updated + namespace);
            body.off(CalendarEvents.eventMoved + namespace);
        }
    };

    var registerEventListeners = function(root) {
        $('body').on(CalendarEvents.filterChanged, function(e, data) {
            var daysWithEvent = root.find(CalendarSelectors.eventType[data.type]);

            daysWithEvent.toggleClass('calendar_event_' + data.type, !data.hidden);
        });

        var namespace = '.' + root.attr('id');
        $('body').on('change' + namespace, CalendarSelectors.elements.courseSelector, function() {
            if (root.is(':visible')) {
                var selectElement = $(this);
                var courseId = selectElement.val();
                var categoryId = null;

                CalendarViewManager.reloadCurrentMonth(root, courseId, categoryId);
            } else {
                $('body').off('change' + namespace);
            }
        });

    };

    return {
        init: function(root, loadOnInit) {
            root = $(root);

            CalendarViewManager.init(root);
            registerEventListeners(root);
            registerCalendarEventListeners(root);

            if (loadOnInit) {
                // The calendar hasn't yet loaded it's events so we
                // should load them as soon as we've initialised.
                CalendarViewManager.reloadCurrentMonth(root);
            }

        }
    };
});
