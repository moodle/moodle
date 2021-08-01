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
 * A javascript module to handle calendar ajax actions.
 *
 * @module     core_calendar/repository
 * @copyright  2017 Simey Lameze <lameze@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
import Ajax from 'core/ajax';

/**
 * Delete a calendar event.
 *
 * @method deleteEvent
 * @param {number} eventId The event id.
 * @param {boolean} deleteSeries Whether to delete all events in the series
 * @return {promise} Resolved with requested calendar event
 */
export const deleteEvent = (eventId, deleteSeries = false) => {
    const request = {
        methodname: 'core_calendar_delete_calendar_events',
        args: {
            events: [{
                eventid: eventId,
                repeat: deleteSeries,
            }]
        }
    };

    return Ajax.call([request])[0];
};

/**
 * Get a calendar event by id.
 *
 * @method getEventById
 * @param {number} eventId The event id.
 * @return {promise} Resolved with requested calendar event
 */
export const getEventById = (eventId) => {

    const request = {
        methodname: 'core_calendar_get_calendar_event_by_id',
        args: {
            eventid: eventId
        }
    };

    return Ajax.call([request])[0];
};

/**
 * Submit the form data for the event form.
 *
 * @method submitCreateUpdateForm
 * @param {string} formData The URL encoded values from the form
 * @return {promise} Resolved with the new or edited event
 */
export const submitCreateUpdateForm = (formData) => {
    const request = {
        methodname: 'core_calendar_submit_create_update_form',
        args: {
            formdata: formData
        }
    };

    return Ajax.call([request])[0];
};

/**
 * Get calendar data for the month view.
 *
 * @method getCalendarMonthData
 * @param {number} year Year
 * @param {number} month Month
 * @param {number} courseId The course id.
 * @param {number} categoryId The category id.
 * @param {boolean} includeNavigation Whether to include navigation.
 * @param {boolean} mini Whether the month is in mini view.
 * @param {number} day Day (optional)
 * @param {string} view The calendar view mode.
 * @return {promise} Resolved with the month view data.
 */
export const getCalendarMonthData = (year, month, courseId, categoryId, includeNavigation, mini, day = 1, view = 'month') => {
    const request = {
        methodname: 'core_calendar_get_calendar_monthly_view',
        args: {
            year,
            month,
            courseid: courseId,
            categoryid: categoryId,
            includenavigation: includeNavigation,
            mini,
            day,
            view,
        }
    };

    return Ajax.call([request])[0];
};

/**
 * Get calendar data for the day view.
 *
 * @method getCalendarDayData
 * @param {number} year Year
 * @param {number} month Month
 * @param {number} day Day
 * @param {number} courseId The course id.
 * @param {number} categoryId The id of the category whose events are shown
 * @return {promise} Resolved with the day view data.
 */
export const getCalendarDayData = (year, month, day, courseId, categoryId) => {
    const request = {
        methodname: 'core_calendar_get_calendar_day_view',
        args: {
            year,
            month,
            day,
            courseid: courseId,
            categoryid: categoryId,
        }
    };

    return Ajax.call([request])[0];
};

/**
 * Change the start day for the given event id. The day timestamp
 * only has to be any time during the target day because only the
 * date information is extracted, the time of the day is ignored.
 *
 * @param {int} eventId The id of the event to update
 * @param {int} dayTimestamp A timestamp for some time during the target day
 * @return {promise}
 */
export const updateEventStartDay = (eventId, dayTimestamp) => {
    const request = {
        methodname: 'core_calendar_update_event_start_day',
        args: {
            eventid: eventId,
            daytimestamp: dayTimestamp
        }
    };

    return Ajax.call([request])[0];
};

/**
 * Get calendar upcoming data.
 *
 * @method getCalendarUpcomingData
 * @param {number} courseId The course id.
 * @param {number} categoryId The category id.
 * @return {promise} Resolved with the month view data.
 */
export const getCalendarUpcomingData = (courseId, categoryId) => {
    const request = {
        methodname: 'core_calendar_get_calendar_upcoming_view',
        args: {
            courseid: courseId,
            categoryid: categoryId,
        }
    };

    return Ajax.call([request])[0];
};

/**
 * Get the groups by course id.
 *
 * @param {Number} courseId The course id to fetch the groups from.
 * @return {promise} Resolved with the course groups.
 */
export const getCourseGroupsData = (courseId) => {
    const request = {
        methodname: 'core_group_get_course_groups',
        args: {
            courseid: courseId
        }
    };

    return Ajax.call([request])[0];
};

/**
 * Delete calendar subscription by id.
 *
 * @param {Number} subscriptionId The subscription id
 * @return {promise}
 */
export const deleteSubscription = (subscriptionId) => {
    const request = {
        methodname: 'core_calendar_delete_subscription',
        args: {
            subscriptionid: subscriptionId
        }
    };

    return Ajax.call([request])[0];
};
