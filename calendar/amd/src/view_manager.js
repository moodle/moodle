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
 * A javascript module to handler calendar view changes.
 *
 * @module     core_calendar/view_manager
 * @package    core_calendar
 * @copyright  2017 Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
define(['jquery', 'core/templates', 'core/notification', 'core_calendar/repository'],
    function($, Templates, Notification, CalendarRepository) {

        var SELECTORS = {
            ROOT: "[data-region='calendar']",
            CALENDAR_NAV_LINK: "span.calendarwrapper .arrow_link",
            CALENDAR_MONTH_WRAPPER: ".calendarwrapper",
        };

        /**
         * Register event listeners for the module.
         *
         * @param {object} root The root element.
         */
        var registerEventListeners = function(root) {
            root = $(root);

            $(root).on('click', SELECTORS.CALENDAR_NAV_LINK, function(e) {
                var courseId = $(root).find(SELECTORS.CALENDAR_MONTH_WRAPPER).data('courseid');
                var link = $(e.currentTarget);
                changeMonth(link.attr('href'), link.data('time'), courseId);

                e.preventDefault();
            });
        };


        /**
         * Handle changes to the current calendar view.
         *
         * @param {Number} time The calendar time to be shown
         * @param {Number} courseid The id of the course whose events are shown
         */
        var changeMonth = function(url, time, courseid) {
            CalendarRepository.getCalendarMonthData(time, courseid)
            .then(function(context) {
                // TODO Fetch the page title from somewhere..?
                window.history.pushState({}, 'Some new title', url);

                return Templates.render('core_calendar/month_detailed', context);
            })
            .then(function(html, js) {
                $(SELECTORS.CALENDAR_MONTH_WRAPPER).replaceWith(html);

                return Templates.runTemplateJS(js);
            })
            .then(function() {
                // TODO Fire an event to say the month changed.
            })
            .fail(Notification.exception);
        };

        return {
            init: function() {
                registerEventListeners(SELECTORS.ROOT);
            }
        };
    });
