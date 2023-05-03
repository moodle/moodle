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
 * CSS selectors for the calendar.
 *
 * @module     core_calendar/selectors
 * @copyright  2017 Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
define([], function() {
    return {
        eventFilterItem: "[data-action='filter-event-type']",
        eventType: {
            site: "[data-eventtype-site]",
            category: "[data-eventtype-category]",
            course: "[data-eventtype-course]",
            group: "[data-eventtype-group]",
            user: "[data-eventtype-user]",
            other: "[data-eventtype-other]",
        },
        popoverType: {
            site: "[data-popover-eventtype-site]",
            category: "[data-popover-eventtype-category]",
            course: "[data-popover-eventtype-course]",
            group: "[data-popover-eventtype-group]",
            user: "[data-popover-eventtype-user]",
            other: "[data-popover-eventtype-other]",
        },
        calendarPeriods: {
            month: "[data-period='month']",
        },
        courseSelector: 'select[name="course"]',
        viewSelector: 'div[data-region="view-selector"]',
        actions: {
            create: '[data-action="new-event-button"]',
            edit: '[data-action="edit"]',
            remove: '[data-action="delete"]',
            viewEvent: '[data-action="view-event"]',
            deleteSubscription: '[data-action="delete-subscription"]',
        },
        elements: {
            courseSelector: 'select[name="course"]',
            dateContainer: '.clickable.hasevent',
            dateContent: '[data-region="day-content"]',
            monthDetailed: '.calendarmonth.calendartable',
        },
        today: '.today',
        day: '[data-region="day"]',
        calendarMain: '[data-region="calendar"]',
        wrapper: '.calendarwrapper',
        eventItem: '[data-type="event"]',
        links: {
            navLink: '.calendarwrapper .arrow_link',
            eventLink: "[data-region='event-item']",
            miniDayLink: "[data-region='mini-day-link']",
        },
        containers: {
            loadingIcon: '[data-region="overlay-icon-container"]',
        },
        mainCalendar: '.maincalendar .heightcontainer',
        fullCalendarView: 'page-calendar-view',
    };
});
