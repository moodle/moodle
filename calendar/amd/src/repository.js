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
 * @class      repository
 * @package    core_calendar
 * @copyright  2017 Simey Lameze <lameze@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
define(['jquery', 'core/ajax'], function($, Ajax) {

    /**
     * Delete a calendar event.
     *
     * @method deleteEvent
     * @param {int} eventId The event id.
     * @param {bool} deleteSeries Whether to delete all events in the series
     * @return {promise} Resolved with requested calendar event
     */
    var deleteEvent = function(eventId, deleteSeries) {
        if (typeof deleteSeries === 'undefined') {
            deleteSeries = false;
        }
        var request = {
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
     * @param {int} eventId The event id.
     * @return {promise} Resolved with requested calendar event
     */
    var getEventById = function(eventId) {

        var request = {
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
     * @param {string} formdata The URL encoded values from the form
     * @return {promise} Resolved with the new or edited event
     */
    var submitCreateUpdateForm = function(formdata) {
        var request = {
            methodname: 'core_calendar_submit_create_update_form',
            args: {
                formdata: formdata
            }
        };

        return Ajax.call([request])[0];
    };

    /**
     * Get calendar data for the month view.
     *
     * @method getCalendarMonthData
     * @param {Number} year Year
     * @param {Number} month Month
     * @param {Number} courseid The course id.
     * @param {Number} categoryid The category id.
     * @param {Bool} includenavigation Whether to include navigation.
     * @return {promise} Resolved with the month view data.
     */
    var getCalendarMonthData = function(year, month, courseid, categoryid, includenavigation) {
        var request = {
            methodname: 'core_calendar_get_calendar_monthly_view',
            args: {
                year: year,
                month: month,
                courseid: courseid,
                categoryid: categoryid,
                includenavigation: includenavigation,
            }
        };

        return Ajax.call([request])[0];
    };

    /**
     * Get calendar data for the day view.
     *
     * @method getCalendarDayData
     * @param {Number} year Year
     * @param {Number} month Month
     * @param {Number} day Day
     * @param {Number} courseid The course id.
     * @param {Number} categoryId The id of the category whose events are shown
     * @return {promise} Resolved with the day view data.
     */
    var getCalendarDayData = function(year, month, day, courseid, categoryId) {
        var request = {
            methodname: 'core_calendar_get_calendar_day_view',
            args: {
                year: year,
                month: month,
                day: day,
                courseid: courseid,
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
    var updateEventStartDay = function(eventId, dayTimestamp) {
        var request = {
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
     * @param {Number} courseid The course id.
     * @param {Number} categoryid The category id.
     * @return {promise} Resolved with the month view data.
     */
    var getCalendarUpcomingData = function(courseid, categoryid) {
        var request = {
            methodname: 'core_calendar_get_calendar_upcoming_view',
            args: {
                courseid: courseid,
                categoryid: categoryid,
            }
        };

        return Ajax.call([request])[0];
    };

    /**
     * Get the groups by course id.
     *
     * @param {Number} courseid The course id to fetch the groups from.
     * @return {promise} Resolved with the course groups.
     */
    var getCourseGroupsData = function(courseid) {
        var request = {
            methodname: 'core_group_get_course_groups',
            args: {
                courseid: courseid
            }
        };

        return Ajax.call([request])[0];
    };

    return {
        getEventById: getEventById,
        deleteEvent: deleteEvent,
        updateEventStartDay: updateEventStartDay,
        submitCreateUpdateForm: submitCreateUpdateForm,
        getCalendarMonthData: getCalendarMonthData,
        getCalendarDayData: getCalendarDayData,
        getCalendarUpcomingData: getCalendarUpcomingData,
        getCourseGroupsData: getCourseGroupsData
    };
});
