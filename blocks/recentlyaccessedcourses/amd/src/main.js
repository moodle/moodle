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
            COURSE_IS_FAVOURITE: '[data-region="is-favourite"]',
            COURSES_VIEW: '[data-region="recentlyaccessedcourses-view"]',
            COURSES_VIEW_CONTENT: '[data-region="recentlyaccessedcourses-view-content"]',
            EMPTY_MESSAGE: '[data-region="empty-message"]'
        };

        var NUM_COURSES_TOTAL = 10;

        /**
         * Show the empty message when no course are found.
         *
         * @param {object} root The root element for the courses view.
         */
        var showEmptyMessage = function(root) {
            root.find(SELECTORS.EMPTY_MESSAGE).removeClass('hidden');
            root.find(SELECTORS.COURSES_VIEW_CONTENT).addClass('hidden');
        };

        /**
         * Show the favourite indicator for the given course (if it's in the list).
         *
         * @param {object} root The root element for the courses view.
         * @param {number} courseId The id of the course to be favourited.
         */
        var favouriteCourse = function(root, courseId) {
            var course = root.find('[data-course-id="' + courseId + '"]');
            if (course.length) {
                course.find(SELECTORS.COURSE_IS_FAVOURITE).removeClass('hidden');
            }
        };

        /**
         * Hide the favourite indicator for the given course (if it's in the list).
         *
         * @param {object} root The root element for the courses view.
         * @param {number} courseId The id of the course to be unfavourited.
         */
        var unfavouriteCourse = function(root, courseId) {
            var course = root.find('[data-course-id="' + courseId + '"]');
            if (course.length) {
                course.find(SELECTORS.COURSE_IS_FAVOURITE).addClass('hidden');
            }
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
            return Templates.render('core_course/view-cards', {
                courses: courses
            })
            .then(function(html, js) {
                var contentContainer = root.find(SELECTORS.COURSES_VIEW_CONTENT);
                return Templates.replaceNodeContents(contentContainer, html, js);
            });
        };

        /**
         * Fetch user's recently accessed courses and reload the content of the block.
         *
         * @param {int} userid User whose courses will be shown
         * @param {object} root The root element for the recentlyaccessedcourses view.
         * @returns {promise} The updated content for the block.
         */
        var loadContent = function(userid, root) {
            CoursesRepository.getLastAccessedCourses(userid, NUM_COURSES_TOTAL)
                .then(function(courses) {
                    if (courses.length) {
                        return renderCourses(root, courses);
                    } else {
                        return showEmptyMessage(root);
                    }
                })
                .catch(Notification.exception);
        };

        /**
         * Register event listeners for the block.
         *
         * @param {object} root The root element for the recentlyaccessedcourses block.
         */
        var registerEventListeners = function(root) {
            PubSub.subscribe(CourseEvents.favourited, function(courseId) {
                favouriteCourse(root, courseId);
            });

            PubSub.subscribe(CourseEvents.unfavorited, function(courseId) {
                unfavouriteCourse(root, courseId);
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

            registerEventListeners(root);
            loadContent(userid, root);
        };

        return {
            init: init
        };
    });
