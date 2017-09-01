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
define(['jquery', 'core/templates'], function($, Templates) {

    var SELECTORS = {
        EVENT_TYPE: '[name="eventtype"]',
        EVENT_COURSE_ID: '[name="courseid"]',
        EVENT_GROUP_COURSE_ID: '[name="groupcourseid"]',
        EVENT_GROUP_ID: '[name="groupid"]',
        FORM_GROUP: '.form-group',
        SELECT_OPTION: 'option',
        ADVANCED_ELEMENT: '.fitem.advanced',
        FIELDSET_ADVANCED_ELEMENTS: 'fieldset.containsadvancedelements',
        MORELESS_TOGGLE: '.moreless-actions'
    };

    var EVENTS = {
        SHOW_ADVANCED: 'event_form-show-advanced',
        HIDE_ADVANCED: 'event_form-hide-advanced',
        ADVANCED_SHOWN: 'event_form-advanced-shown',
        ADVANCED_HIDDEN: 'event_form-advanced-hidden',
    };

    /**
     * Find the old show more / show less toggle added by the mform and destroy it.
     * We are handling the visibility of the advanced fields with the more/less button
     * in the footer of the modal that this form is rendered within.
     *
     * @method destroyOldMoreLessToggle
     * @param {object} formElement The root form element
     */
    var destroyOldMoreLessToggle = function(formElement) {
        formElement.find(SELECTORS.FIELDSET_ADVANCED_ELEMENTS).removeClass('containsadvancedelements');
        var element = formElement.find(SELECTORS.MORELESS_TOGGLE);
        Templates.replaceNode(element, '', '');
    };

    /**
     * Find each of the advanced form elements and make them visible.
     *
     * This function triggers the ADVANCED_SHOWN event for any other
     * component to handle (e.g. the event form modal).
     *
     * @method destroyOldMoreLessToggle
     * @param {object} formElement The root form element
     */
    var showAdvancedElements = function(formElement) {
        formElement.find(SELECTORS.ADVANCED_ELEMENT).removeClass('hidden');
        formElement.trigger(EVENTS.ADVANCED_SHOWN);
    };

    /**
     * Find each of the advanced form elements and hide them.
     *
     * This function triggers the ADVANCED_HIDDEN event for any other
     * component to handle (e.g. the event form modal).
     *
     * @method hideAdvancedElements
     * @param {object} formElement The root form element
     */
    var hideAdvancedElements = function(formElement) {
        formElement.find(SELECTORS.ADVANCED_ELEMENT).addClass('hidden');
        formElement.trigger(EVENTS.ADVANCED_HIDDEN);
    };

    /**
     * Listen for any events telling this module to show or hide it's
     * advanced elements.
     *
     * This function listens for SHOW_ADVANCED and HIDE_ADVANCED.
     *
     * @method listenForShowHideEvents
     * @param {object} formElement The root form element
     */
    var listenForShowHideEvents = function(formElement) {
        formElement.on(EVENTS.SHOW_ADVANCED, function() {
            showAdvancedElements(formElement);
        });

        formElement.on(EVENTS.HIDE_ADVANCED, function() {
            hideAdvancedElements(formElement);
        });
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

            groupSelectOptions.each(function(index, element) {
                element = $(element);

                if (element.attr('data-course-id') == selectedCourseId) {
                    element.removeClass('hidden');
                    element.prop('disabled', false);

                    if (selectedIndex === null || element.attr('selected')) {
                        selectedIndex = index;
                    }
                } else {
                    element.addClass('hidden');
                    element.prop('disabled', true);
                }
            });

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
     * @param {bool} hasError If the form has errors rendered form the server.
     */
    var init = function(formId, hasError) {
        var formElement = $('#' + formId);

        listenForShowHideEvents(formElement);
        destroyOldMoreLessToggle(formElement);
        parseGroupSelect(formElement);
        addCourseGroupSelectListeners(formElement);

        // If we know that the form has been rendered with server side
        // errors then we need to display all of the elements in the form
        // in case one of those elements has the error.
        if (hasError) {
            showAdvancedElements(formElement);
        } else {
            hideAdvancedElements(formElement);
        }
    };

    return {
        init: init,
        events: EVENTS,
    };
});
