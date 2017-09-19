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
 * This module handles display of multiple mini calendars in a view, and
 * movement through them.
 *
 * @module     core_calendar/calendar_threemonth
 * @package    core_calendar
 * @copyright  2017 Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
define([
    'jquery',
    'core/notification',
    'core_calendar/selectors',
    'core_calendar/events',
    'core/templates',
    'core_calendar/view_manager',
],
function(
    $,
    Notification,
    CalendarSelectors,
    CalendarEvents,
    Templates,
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
        body.on(CalendarEvents.monthChanged, function(e, year, month, courseId, categoryId) {
            // We have to use a queue here because the calling code is decoupled from these listeners.
            // It's possible for the event to be called multiple times before one call is fully resolved.
            root.queue(function(next) {
                return processRequest(e, year, month, courseId, categoryId)
                .then(function() {
                    return next();
                })
                .fail(Notification.exception)
                ;
            });
        });

        var processRequest = function(e, year, month, courseId, categoryId) {
            var newCurrentMonth = root.find('[data-year="' + year + '"][data-month="' + month + '"]');
            var newParent = newCurrentMonth.closest(CalendarSelectors.calendarPeriods.month);
            var allMonths = root.find(CalendarSelectors.calendarPeriods.month);

            var previousMonth = $(allMonths[0]);
            var nextMonth = $(allMonths[2]);

            var placeHolder = $('<span>');
            placeHolder.attr('data-template', 'core_calendar/threemonth_month');
            placeHolder.attr('data-includenavigation', false);
            var placeHolderContainer = $('<div>');
            placeHolderContainer.hide();
            placeHolderContainer.append(placeHolder);

            var requestYear;
            var requestMonth;
            var oldMonth;

            if (newParent.is(previousMonth)) {
                // Fetch the new previous month.
                placeHolderContainer.insertBefore(previousMonth);

                requestYear = previousMonth.data('previousYear');
                requestMonth = previousMonth.data('previousMonth');
                oldMonth = nextMonth;
            } else if (newParent.is(nextMonth)) {
                // Fetch the new next month.
                placeHolderContainer.insertAfter(nextMonth);
                requestYear = nextMonth.data('nextYear');
                requestMonth = nextMonth.data('nextMonth');
                oldMonth = previousMonth;
            }

            return CalendarViewManager.refreshMonthContent(
                placeHolder,
                requestYear,
                requestMonth,
                courseId,
                categoryId,
                placeHolder
            )
            .then(function() {
                var slideUpPromise = $.Deferred();
                var slideDownPromise = $.Deferred();
                oldMonth.slideUp('fast', function() {
                    $(this).remove();
                    slideUpPromise.resolve();
                });
                placeHolderContainer.slideDown('fast', function() {
                    slideDownPromise.resolve();
                });

                return $.when(slideUpPromise, slideDownPromise);
            });
        };
    };

    return {
        init: function(root) {
            root = $(root);

            registerCalendarEventListeners(root);
        }
    };
});
