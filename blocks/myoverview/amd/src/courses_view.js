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
 * Javascript module to load courses view actions.
 *
 * @module     block_myoverview/courses-view
 * @package    block_myoverview
 * @copyright  2016 Simey Lameze <simey@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
define(['jquery', 'core/templates', 'block_myoverview/courses_view_repository', 'core/notification'],
    function($, Templates, CoursesRepository, Notification) {

    var SELECTORS = {
        COURSES_IN_PROGRESS_CONTAINER: '[data-region="courses-in-progress-container"]',
        LOADING_ICON_CONTAINER: '[data-region="loading-icon-container"]'
    };

    /**
     * Load the module.
     *
     * @method load
     * @param {Object} root The root element of courses view by status.
     * @param {Object} pagingRoot The root element of paging bar.
     */
    var load = function(root, pagingRoot) {

        root = $(root);

        var limit  = +root.attr('data-limit'),
            offset = +root.attr('data-offset'),
            status = root.attr('data-status'),
            total = CoursesRepository.getTotalByStatus('past');

        // Don't load twice.
        if (isLoading(root)) {
            return $.Deferred().resolve();
        }


        // Request data from the server.
        return CoursesRepository.queryFromStatus(status, limit, offset).then(function(courses) {

            if (!courses.length || (courses.length < limit)) {
                // We have no more events so mark the list as done.
                setLoadedAll(root);
            }

            if (courses.length) {
                root.attr('data-offset', offset + courses.length);

                // Render the courses.
                render(root, courses);

            }
        }).fail(
            Notification.exception
        ).always(function() {
            stopLoading(root);

            renderPagingBar(pagingRoot, limit, total);
        });
    };


    /**
     * Render the list of courses.
     *
     * @param root
     * @param courses
     */
    var render = function(root, courses) {
        root = $(root);

        // We need to delete the content before append the new content.
        root.empty();

        $.each(courses, function(index, value) {
            Templates.render(
                'block_myoverview/courses-view-course-item', value
            ).done(function(html, js) {
                Templates.appendNodeContents(root, html, js);
            });
        });
    };

    return {
        init: function(root, pagingRoot) {
            root = $(root);
            pagingRoot = $(pagingRoot);

            registerEventListener(root, pagingRoot);
            load(root, pagingRoot);
        }
    };
});
