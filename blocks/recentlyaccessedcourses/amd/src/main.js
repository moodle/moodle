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
 * Javascript to initialise the Recently accessed courses block.
 *
 * @module     block_recentlyaccessedcourses/main.js
 * @package    block_recentlyaccessedcourses
 * @copyright  2018 Victor Deniz <victor@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define(
    [
        'jquery',
        'core_course/repository',
        'core/templates',
        'core/notification',
        'core/pubsub',
        'core_course/events'
    ],
    function(
        $,
        CoursesRepository,
        Templates,
        Notification,
        PubSub,
        CourseEvents
    ) {

        var SELECTORS = {
            COURSES_VIEW: '[data-region="recentlyaccessedcourses-view"]',
            COURSES_VIEW_CONTENT: '[data-region="recentlyaccessedcourses-view-content"]'
        };

        var NUM_COURSES_TOTAL = 10;

        /**
         * Get enrolled courses from backend.
         *
         * @method getRecentCourses
         * @param {int} userid User from which the courses will be obtained
         * @param {int} limit Only return this many results
         * @return {array} Courses user has accessed
         */
        var getRecentCourses = function(userid, limit) {
            return CoursesRepository.getLastAccessedCourses(userid, limit);
        };

        /**
         * Render the dashboard courses.
         *
         * @method renderCourses
         * @param {object} root The root element for the courses view.
         * @param {array} courses containing array of returned courses.
         * @return {promise} Resolved with HTML and JS strings
         */
        var renderCourses = function(root, courses) {
            if (courses.length > 0) {
                return Templates.render('core_course/view-cards', {
                    courses: courses
                });
            } else {
                var nocoursesimgurl = root.attr('data-nocoursesimg');
                return Templates.render('block_recentlyaccessedcourses/no-courses', {
                    nocoursesimg: nocoursesimgurl
                });
            }
        };

        /**
         * Fetch user's recently accessed courses and reload the content of the block.
         *
         * @param {int} userid User whose courses will be shown
         * @param {object} root The root element for the recentlyaccessedcourses view.
         * @returns {promise} The updated content for the block.
         */
        var reloadContent = function(userid, root) {

            var recentcoursesViewRoot = root.find(SELECTORS.COURSES_VIEW);
            var recentcoursesViewContent = root.find(SELECTORS.COURSES_VIEW_CONTENT);

            var coursesPromise = getRecentCourses(userid, NUM_COURSES_TOTAL);

            return coursesPromise.then(function(courses) {
                var pagedContentPromise = renderCourses(recentcoursesViewRoot, courses);

                pagedContentPromise.then(function(html, js) {
                    return Templates.replaceNodeContents(recentcoursesViewContent, html, js);
                }).catch(Notification.exception);
                return coursesPromise;
            }).catch(Notification.exception);
        };

        /**
         * Register event listeners for the block.
         *
         * @param {int} userid User whose courses will be shown
         * @param {object} root The root element for the recentlyaccessedcourses block.
         */
        var registerEventListeners = function(userid, root) {
            PubSub.subscribe(CourseEvents.favourited, function() {
                reloadContent(userid, root);
            });

            PubSub.subscribe(CourseEvents.unfavorited, function() {
                reloadContent(userid, root);
            });
        };

        /**
         * Get and show the recent courses into the block.
         *
         * @param {int} userid User from which the courses will be obtained
         * @param {object} root The root element for the recentlyaccessedcourses block.
         */
        var init = function(userid, root) {
            root = $(root);

            registerEventListeners(userid, root);
            reloadContent(userid, root);
        };

        return {
            init: init
        };
    });
