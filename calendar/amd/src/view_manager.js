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
define(['jquery', 'core/templates', 'core/notification', 'core_calendar/repository', 'core_calendar/events'],
    function($, Templates, Notification, CalendarRepository, CalendarEvents) {

        var SELECTORS = {
            ROOT: "[data-region='calendar']",
            CALENDAR_NAV_LINK: ".calendarwrapper .arrow_link",
            CALENDAR_MONTH_WRAPPER: ".calendarwrapper",
            LOADING_ICON_CONTAINER: '[data-region="overlay-icon-container"]'
        };

        /**
         * Register event listeners for the module.
         *
         * @param {object} root The root element.
         */
        var registerEventListeners = function(root) {
            root = $(root);

            root.on('click', SELECTORS.CALENDAR_NAV_LINK, function(e) {
                var wrapper = root.find(SELECTORS.CALENDAR_MONTH_WRAPPER);
                var courseId = wrapper.data('courseid');
                var categoryId = wrapper.data('categoryid');
                var link = $(e.currentTarget);
                changeMonth(root, link.attr('href'), link.data('year'), link.data('month'), courseId, categoryId);

                e.preventDefault();
            });
        };

        /**
         * Refresh the month content.
         *
         * @param {object} root The root element.
         * @param {Number} year Year
         * @param {Number} month Month
         * @param {Number} courseid The id of the course whose events are shown
         * @param {Number} categoryid The id of the category whose events are shown
         * @param {object} target The element being replaced. If not specified, the calendarwrapper is used.
         * @return {promise}
         */
        var refreshMonthContent = function(root, year, month, courseid, categoryid, target) {
            startLoading(root);

            target = target || root.find(SELECTORS.CALENDAR_MONTH_WRAPPER);

            M.util.js_pending([root.get('id'), year, month, courseid].join('-'));
            var includenavigation = root.data('includenavigation');
            return CalendarRepository.getCalendarMonthData(year, month, courseid, categoryid, includenavigation)
                .then(function(context) {
                    return Templates.render(root.attr('data-template'), context);
                })
                .then(function(html, js) {
                    return Templates.replaceNode(target, html, js);
                })
                .then(function() {
                    $('body').trigger(CalendarEvents.viewUpdated);
                    return;
                })
                .always(function() {
                    M.util.js_complete([root.get('id'), year, month, courseid].join('-'));
                    return stopLoading(root);
                })
                .fail(Notification.exception);
        };

        /**
         * Handle changes to the current calendar view.
         *
         * @param {object} root The container element
         * @param {String} url The calendar url to be shown
         * @param {Number} year Year
         * @param {Number} month Month
         * @param {Number} courseid The id of the course whose events are shown
         * @param {Number} categoryid The id of the category whose events are shown
         * @return {promise}
         */
        var changeMonth = function(root, url, year, month, courseid, categoryid) {
            return refreshMonthContent(root, year, month, courseid, categoryid)
                .then(function() {
                    if (url.length && url !== '#') {
                        window.history.pushState({}, '', url);
                    }
                    return arguments;
                })
                .then(function() {
                    $('body').trigger(CalendarEvents.monthChanged, [year, month, courseid, categoryid]);
                    return arguments;
                });
        };

        /**
         * Reload the current month view data.
         *
         * @param {object} root The container element.
         * @param {Number} courseId The course id.
         * @param {Number} categoryId The id of the category whose events are shown
         * @return {promise}
         */
        var reloadCurrentMonth = function(root, courseId, categoryId) {
            var year = root.find(SELECTORS.CALENDAR_MONTH_WRAPPER).data('year');
            var month = root.find(SELECTORS.CALENDAR_MONTH_WRAPPER).data('month');

            if (typeof courseId === 'undefined') {
                courseId = root.find(SELECTORS.CALENDAR_MONTH_WRAPPER).data('courseid');
            }

            if (typeof categoryId === 'undefined') {
                categoryId = root.find(SELECTORS.CALENDAR_MONTH_WRAPPER).data('categoryid');
            }

            return refreshMonthContent(root, year, month, courseId, categoryId);

        };

        /**
         * Set the element state to loading.
         *
         * @param {object} root The container element
         * @method startLoading
         */
        var startLoading = function(root) {
            var loadingIconContainer = root.find(SELECTORS.LOADING_ICON_CONTAINER);

            loadingIconContainer.removeClass('hidden');
        };

        /**
         * Remove the loading state from the element.
         *
         * @param {object} root The container element
         * @method stopLoading
         */
        var stopLoading = function(root) {
            var loadingIconContainer = root.find(SELECTORS.LOADING_ICON_CONTAINER);

            loadingIconContainer.addClass('hidden');
        };

        /**
         * Reload the current month view data.
         *
         * @param {object} root The container element.
         * @param {Number} courseId The course id.
         * @return {promise}
         */
        var reloadCurrentUpcoming = function(root, courseId) {
            startLoading(root);

            var target = root.find(SELECTORS.CALENDAR_MONTH_WRAPPER);

            return CalendarRepository.getCalendarUpcomingData(courseId)
                .then(function(context) {
                    return Templates.render(root.attr('data-template'), context);
                })
                .then(function(html, js) {
                    window.history.replaceState(null, null, '?view=upcoming&course=' + courseId);
                    return Templates.replaceNode(target, html, js);
                })
                .then(function() {
                    $('body').trigger(CalendarEvents.viewUpdated);
                    return;
                })
                .always(function() {
                    return stopLoading(root);
                })
                .fail(Notification.exception);
        };

        return {
            init: function(root) {
                registerEventListeners(root);
            },
            reloadCurrentMonth: reloadCurrentMonth,
            changeMonth: changeMonth,
            refreshMonthContent: refreshMonthContent,
            reloadCurrentUpcoming: reloadCurrentUpcoming
        };
    });
