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
 * Javascript to initialise the starred courses block.
 *
 * @copyright   2018 Simey Lameze <simey@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define(
[
    'jquery',
    'core/notification',
    'block_starredcourses/repository',
    'core/pubsub',
    'core/templates',
    'core_course/events'
],
function(
    $,
    Notification,
    Repository,
    PubSub,
    Templates,
    CourseEvents
) {

    var SELECTORS = {
        BLOCK_CONTAINER: '[data-region="starred-courses"]',
        STARRED_COURSES_REGION_VIEW: '[data-region="starred-courses-view"]',
        STARRED_COURSES_REGION: '[data-region="starred-courses-view-content"]'
    };

    /**
     * Render the starred courses.
     *
     * @method renderCourses
     * @param {object} root The root element for the starred view.
     * @param {array} courses containing array of returned courses.
     * @returns {promise} Resolved with HTML and JS strings
     */
    var renderCourses = function(root, courses) {
        if (courses.length > 0) {
            return Templates.render('core_course/view-cards', {
                courses: courses
            });
        } else {
            var nocoursesimg = root.find(SELECTORS.STARRED_COURSES_REGION_VIEW).attr('data-nocoursesimg');
            return Templates.render('block_starredcourses/no-courses', {
                nocoursesimg: nocoursesimg
            });
        }
    };

    /**
     * Fetch user's starred courses and reload the content of the block.
     *
     * @param {object} root The root element for the starred view.
     * @returns {promise} The updated content for the block.
     */
    var reloadContent = function(root) {
        var content = root.find(SELECTORS.STARRED_COURSES_REGION);

        var args = {
            limit: 0,
            offset: 0,
        };

        return Repository.getStarredCourses(args)
            .then(function(courses) {
                // Whether the course category should be displayed in the course item.
                var showcoursecategory = $(SELECTORS.BLOCK_CONTAINER).data('displaycoursecategory');
                courses = courses.map(function(course) {
                    course.showcoursecategory = showcoursecategory;
                    return course;
                });
                return renderCourses(root, courses);
            }).then(function(html, js) {
                return Templates.replaceNodeContents(content, html, js);
            }).catch(Notification.exception);
    };

    /**
     * Register event listeners for the block.
     *
     * @param {object} root The calendar root element
     */
    var registerEventListeners = function(root) {
        PubSub.subscribe(CourseEvents.favourited, function() {
            reloadContent(root);
        });

        PubSub.subscribe(CourseEvents.unfavorited, function() {
            reloadContent(root);
        });
    };

    /**
     * Initialise all of the modules for the starred courses block.
     *
     * @param {object} root The root element for the block.
     */
    var init = function(root) {
        root = $(root);

        registerEventListeners(root);
        reloadContent(root);
    };

    return {
        init: init
    };
});
