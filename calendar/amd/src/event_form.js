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
 * @package    core_calendar
 * @copyright  2017 Ryan Wyllie <ryan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
define(['jquery'], function($) {

    var SELECTORS = {
        EVENT_GROUP_COURSE_ID: '[name="groupcourseid"]',
        EVENT_GROUP_ID: '[name="groupid"]',
        SELECT_OPTION: 'option',
    };

    /**
     * Parse the group id select element in the event form and pull out
     * the course id from the value to allow us to toggle other select
     * elements based on the course id for the group a user selects.
     *
     * This is a little hacky but I couldn't find a better way to pass
     * the course id for each group id with the limitations of mforms.
     *
     * The group id options are rendered with a value like:
     * "<courseid>-<groupid>"
     * E.g.
     * For a group with id 10 in a course with id 3 the value of the
     * option will be 3-10.
     *
     * @method parseGroupSelect
     * @param {object} formElement The root form element
     */
    var parseGroupSelect = function(formElement) {
        formElement.find(SELECTORS.EVENT_GROUP_ID)
            .find(SELECTORS.SELECT_OPTION)
            .each(function(index, element) {
                element = $(element);
                var value = element.attr('value');
                var splits = value.split('-');
                var courseId = splits[0];

                element.attr('data-course-id', courseId);
            });
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
        var groupSelect = formElement.find(SELECTORS.EVENT_GROUP_ID);
        var groupSelectOptions = groupSelect.find(SELECTORS.SELECT_OPTION);
        var filterGroupSelectOptions = function() {
            var selectedCourseId = courseGroupSelect.val();
            var selectedIndex = null;
            var hasGroups = false;
            groupSelectOptions.each(function(index, element) {
                element = $(element);

                if (element.attr('data-course-id') == selectedCourseId) {
                    element.removeClass('hidden');
                    element.prop('disabled', false);
                    hasGroups = true;
                    if (selectedIndex === null || element.attr('selected')) {
                        selectedIndex = index;
                    }
                } else {
                    element.addClass('hidden');
                    element.prop('disabled', true);
                }
            });

            if (hasGroups) {
                groupSelect.prop('disabled', false);
            } else {
                groupSelect.prop('disabled', true);
            }

            groupSelect.prop('selectedIndex', selectedIndex);
        };

        courseGroupSelect.on('change', filterGroupSelectOptions);
        filterGroupSelectOptions();
    };

    /**
     * Initialise all of the form enhancements.
     *
     * @method init
     * @param {string} formId The value of the form's id attribute
     */
    var init = function(formId) {
        var formElement = $('#' + formId);

        parseGroupSelect(formElement);
        addCourseGroupSelectListeners(formElement);
    };

    return {
        init: init,
    };
});
