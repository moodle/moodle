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
     * Set a flag on the element to indicate that it has completed
     * loading all event data.
     *
     * @method setLoadedAll
     * @param {object} root The container element
     */
    var setLoadedAll = function(root) {
        root.attr('data-loaded-all', true);
    };

    /**
     * Set the element state to loading.
     *
     * @method startLoading
     * @param {object} root The container element
     */
    var startLoading = function(root) {
        var loadingIcon = root.find(SELECTORS.LOADING_ICON_CONTAINER);

        root.addClass('loading');
        loadingIcon.removeClass('hidden');
    };

    /**
     * Remove the loading state from the element.
     *
     * @method stopLoading
     * @param {object} root The container element
     */
    var stopLoading = function(root) {
        var loadingIcon = root.find(SELECTORS.LOADING_ICON_CONTAINER);

        root.removeClass('loading');
        loadingIcon.addClass('hidden');
    };

    /**
     * Check if the element is currently loading some event data.
     *
     * @method isLoading
     * @param {object} root The container element
     */
    var isLoading = function(root) {
        return root.hasClass('loading');
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

        startLoading(root);

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
                setLoadedAll(root);
            }
        }).fail(
            Notification.exception
        ).always(function() {
            stopLoading(root);

            renderPagingBar(pagingRoot, limit, total);
        });
    };

    /**
     * Render the paging bar.
     *
     * @param pagingRoot The root element of the paging bar.
     * @param {int} limit Limit of courses per page.
     * @param {int} total Total of courses per status.
     * @returns {string} Rendered paging bar html.
     */
    var renderPagingBar = function(pagingRoot, limit, total) {
        pagingRoot = $(pagingRoot);
        pagingRoot.empty();
        var pageCounter,
            pageTotal = total / limit;

        var pagingBar = {
            haspages: (total > 0),
            previous : {},
            pages: [],
            next: {}
        };

        for(pageCounter = 0; pageCounter < pageTotal; pageCounter++) {
            pagingBar.pages.push({page: pageCounter + 1});
        }

        return Templates.render(
            'core/paging_bar',pagingBar
        ).done(function(html, js) {
            Templates.appendNodeContents(pagingRoot, html, js);
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

    /**
     * Register event listeners.
     */
    var registerEventListener = function(root, pagingRoot) {
        root = $(root);
        pagingRoot = $(pagingRoot);

        var offset = +root.attr('data-offset'),
            limit  = +root.attr('data-limit'),
            status = root.attr('data-status');

        pagingRoot.on('click', 'a', function(e) {
            // Don't go anywhere!
            e.preventDefault();

            var targetElement = $(e.currentTarget);

            var pageNumber = targetElement[0].innerText,
                calc = (limit * pageNumber / pageNumber);

            if (pageNumber == 1) {
                calc = 0;
            }

            root.attr('data-offset', calc);
            load(root, pagingRoot);
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
