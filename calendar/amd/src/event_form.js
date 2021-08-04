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
 * A javascript module to enhance the event form.
 *
 * @module     core_calendar/event_form
 * @copyright  2017 Ryan Wyllie <ryan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
define(['jquery', 'core_calendar/repository'], function($, CalendarRepository) {

    var SELECTORS = {
        EVENT_GROUP_COURSE_ID: '[name="groupcourseid"]',
        EVENT_GROUP_ID: '[name="groupid"]',
        SELECT_OPTION: 'option'
    };

    /**
     * Listen for when the user changes the group course when configuring
     * a group event and filter the options in the group select to only
     * show the groups available within the course the user has selected.
     *
     * @method addCourseGroupSelectListeners
     * @param {object} formElement The root form element
     */
    var addCourseGroupSelectListeners = function(formElement) {
        var courseGroupSelect = formElement.find(SELECTORS.EVENT_GROUP_COURSE_ID);

        var loadGroupSelectOptions = function(groups) {
            var groupSelect = formElement.find(SELECTORS.EVENT_GROUP_ID),
                groupSelectOptions = groupSelect.find(SELECTORS.SELECT_OPTION),
                courseGroups = $(groups);

            // Let's clear all options first.
            groupSelectOptions.remove();
            groupSelect.prop("disabled", false);
            courseGroups.each(function(id, group) {
                $(groupSelect).append($("<option></option>").attr("value", group.id).text(group.name));
            });
        };

        // If the user choose a course in the selector do a WS request to get groups.
        courseGroupSelect.on('change', function() {
            var courseId = formElement.find(SELECTORS.EVENT_GROUP_COURSE_ID).val();
            CalendarRepository.getCourseGroupsData(courseId)
                .then(function(groups) {
                    return loadGroupSelectOptions(groups);
                })
                .catch(Notification.exception);
        });
    };

    /**
     * Initialise all of the form enhancements.
     *
     * @method init
     * @param {string} formId The value of the form's id attribute
     */
    var init = function(formId) {
        var formElement = $('#' + formId);
        addCourseGroupSelectListeners(formElement);
    };

    return {
        init: init,
    };
});
