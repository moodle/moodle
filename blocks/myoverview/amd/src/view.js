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
 * Manage the courses view for the overview block.
 *
 * @package    block_myoverview
 * @copyright  2018 Bas Brands <bas@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define(
[
    'jquery',
    'block_myoverview/repository',
    'core/paged_content_factory',
    'core/custom_interaction_events',
    'core/notification',
    'core/templates',
],
function(
    $,
    Repository,
    PagedContentFactory,
    CustomEvents,
    Notification,
    Templates
) {

    var SELECTORS = {
        ACTION_ADD_FAVOURITE: '[data-action="add-favourite"]',
        ACTION_REMOVE_FAVOURITE: '[data-action="remove-favourite"]',
        FAVOURITE_ICON: '[data-region="favourite-icon"]',
        ICON_IS_FAVOURITE: '[data-region="is-favourite"]',
        ICON_NOT_FAVOURITE: '[data-region="not-favourite"]',
        PAGED_CONTENT_CONTAINER: '[data-region="page-container"]'

    };

    var TEMPLATES = {
        COURSES_CARDS: 'block_myoverview/view-cards',
        COURSES_LIST: 'block_myoverview/view-list',
        COURSES_SUMMARY: 'block_myoverview/view-summary',
        NOCOURSES: 'block_myoverview/no-courses'
    };

    var NUMCOURSES_PERPAGE = [12, 24, 48];

    var loadedPages = [];

    /**
     * Get filter values from DOM.
     *
     * @param {object} root The root element for the courses view.
     * @return {filters} Set filters.
     */
    var getFilterValues = function(root) {
        var filters = {};
        filters.display = root.attr('data-display');
        filters.grouping = root.attr('data-grouping');
        filters.sort = root.attr('data-sort');
        return filters;
    };

    // We want the paged content controls below the paged content area.
    // and the controls should be ignored while data is loading.
    var DEFAULT_PAGED_CONTENT_CONFIG = {
        ignoreControlWhileLoading: true,
        controlPlacementBottom: true,
    };

    /**
     * Get enrolled courses from backend.
     *
     * @param {object} filters The filters for this view.
     * @param {int} limit The number of courses to show.
     * @param {int} pageNumber The pagenumber to view.
     * @return {promise} Resolved with an array of courses.
     */
    var getMyCourses = function(filters, limit, pageNumber) {

        return Repository.getEnrolledCoursesByTimeline({
            offset:  pageNumber * limit,
            limit: limit,
            classification: filters.grouping,
            sort: filters.sort
        });
    };

    /**
     * Get the container element for the favourite icon.
     *
     * @param  {Object} root The course overview container
     * @param  {Number} courseId Course id number
     * @return {Object} The favourite icon container
     */
    var getFavouriteIconContainer = function(root, courseId) {
        return root.find(SELECTORS.FAVOURITE_ICON + '[data-course-id="' + courseId + '"]');
    };

    /**
     * Get the paged content container element.
     *
     * @param  {Object} root The course overview container
     * @param  {Number} index Rendered page index.
     * @return {Object} The rendered paged container.
     */
    var getPagedContentContainer = function(root, index) {
        return root.find('[data-region="paged-content-page"][data-page="' + index + '"]');
    };

    /**
     * Get the course id from a favourite element.
     *
     * @param {Object} root The favourite icon container element.
     * @return {Number} Course id.
     */
    var getFavouriteCourseId = function(root) {
        return root.attr('data-course-id');
    };

    /**
     * Hide the favourite icon.
     *
     * @param {Object} root The favourite icon container element.
     * @param  {Number} courseId Course id number.
     */
    var hideFavouriteIcon = function(root, courseId) {
        var iconContainer = getFavouriteIconContainer(root, courseId);
        var isFavouriteIcon = iconContainer.find(SELECTORS.ICON_IS_FAVOURITE);
        isFavouriteIcon.addClass('hidden');
        isFavouriteIcon.attr('aria-hidden', true);
        var notFavourteIcon = iconContainer.find(SELECTORS.ICON_NOT_FAVOURITE);
        notFavourteIcon.removeClass('hidden');
        notFavourteIcon.attr('aria-hidden', false);
    };

    /**
     * Show the favourite icon.
     *
     * @param  {Object} root The course overview container.
     * @param  {Number} courseId Course id number.
     */
    var showFavouriteIcon = function(root, courseId) {
        var iconContainer = getFavouriteIconContainer(root, courseId);
        var isFavouriteIcon = iconContainer.find(SELECTORS.ICON_IS_FAVOURITE);
        isFavouriteIcon.removeClass('hidden');
        isFavouriteIcon.attr('aria-hidden', false);
        var notFavourteIcon = iconContainer.find(SELECTORS.ICON_NOT_FAVOURITE);
        notFavourteIcon.addClass('hidden');
        notFavourteIcon.attr('aria-hidden', true);
    };

    /**
     * Get the action menu item
     *
     * @param {Object} root  root The course overview container
     * @param {Number} courseId Course id.
     * @return {Object} The add to favourite menu item.
     */
    var getAddFavouriteMenuItem = function(root, courseId) {
        return root.find('[data-action="add-favourite"][data-course-id="' + courseId + '"]');
    };

    /**
     * Get the action menu item
     *
     * @param {Object} root  root The course overview container
     * @param {Number} courseId Course id.
     * @return {Object} The remove from favourites menu item.
     */
    var getRemoveFavouriteMenuItem = function(root, courseId) {
        return root.find('[data-action="remove-favourite"][data-course-id="' + courseId + '"]');
    };

    /**
     * Add course to favourites
     *
     * @param  {Object} root The course overview container
     * @param  {Number} courseId Course id number
     */
    var addToFavourites = function(root, courseId) {
        var removeAction = getRemoveFavouriteMenuItem(root, courseId);
        var addAction = getAddFavouriteMenuItem(root, courseId);

        setCourseFavouriteState(courseId, true).then(function(success) {
            if (success) {
                removeAction.removeClass('hidden');
                addAction.addClass('hidden');
                showFavouriteIcon(root, courseId);
            } else {
                Notification.alert('Starring course failed', 'Could not change favourite state');
            }
            return;
        }).catch(Notification.exception);
    };

    /**
     * Remove course from favourites
     *
     * @param  {Object} root The course overview container
     * @param  {Number} courseId Course id number
     */
    var removeFromFavourites = function(root, courseId) {
        var removeAction = getRemoveFavouriteMenuItem(root, courseId);
        var addAction = getAddFavouriteMenuItem(root, courseId);

        setCourseFavouriteState(courseId, false).then(function(success) {
            if (success) {
                removeAction.addClass('hidden');
                addAction.removeClass('hidden');
                hideFavouriteIcon(root, courseId);
            } else {
                Notification.alert('Starring course failed', 'Could not change favourite state');
            }
            return;
        }).catch(Notification.exception);
    };

    /**
     * Set the courses favourite status and push to repository
     *
     * @param  {Number} courseId Course id to favourite.
     * @param  {Bool} status new favourite status.
     * @return {Promise} Repository promise.
     */
    var setCourseFavouriteState = function(courseId, status) {

        return Repository.setFavouriteCourses({
            courses: [
                    {
                        'id': courseId,
                        'favourite': status
                    }
                ]
        }).then(function(result) {
            if (result.warnings.length == 0) {
                loadedPages.forEach(function(courseList) {
                    courseList.courses.forEach(function(course, index) {
                        if (course.id == courseId) {
                            courseList.courses[index].isfavourite = status;
                        }
                    });
                });
                return true;
            } else {
                return false;
            }
        }).catch(Notification.exception);
    };

    /**
     * Render the dashboard courses.
     *
     * @param {object} root The root element for the courses view.
     * @param {array} coursesData containing array of returned courses.
     * @return {promise} jQuery promise resolved after rendering is complete.
     */
    var renderCourses = function(root, coursesData) {

        var filters = getFilterValues(root);

        var currentTemplate = '';
        if (filters.display == 'cards') {
            currentTemplate = TEMPLATES.COURSES_CARDS;
        } else if (filters.display == 'list') {
            currentTemplate = TEMPLATES.COURSES_LIST;
        } else {
            currentTemplate = TEMPLATES.COURSES_SUMMARY;
        }

        if (coursesData.courses.length) {
            return Templates.render(currentTemplate, {
                courses: coursesData.courses
            });
        } else {
            var nocoursesimg = root.attr('data-nocoursesimg');
            return Templates.render(TEMPLATES.NOCOURSES, {
                nocoursesimg: nocoursesimg
            });
        }
    };

    /**
     * Intialise the courses list and cards views on page load.
     *
     * @param {object} root The root element for the courses view.
     * @param {object} content The content element for the courses view.
     */
    var init = function(root, content) {

        root = $(root);

        if (!root.attr('data-init')) {
            registerEventListeners(root);
            root.attr('data-init', true);
        }

        var filters = getFilterValues(root);

        var pagedContentPromise = PagedContentFactory.createWithLimit(
            NUMCOURSES_PERPAGE,
            function(pagesData, actions) {
                var promises = [];

                pagesData.forEach(function(pageData) {
                    var currentPage = pageData.pageNumber;
                    var pageNumber = pageData.pageNumber - 1;

                    var pagePromise = getMyCourses(
                        filters,
                        pageData.limit,
                        pageNumber
                    ).then(function(coursesData) {
                        if (coursesData.courses.length < pageData.limit) {
                            actions.allItemsLoaded(pageData.pageNumber);
                        }
                        loadedPages[currentPage] = coursesData;
                        return renderCourses(root, coursesData);
                    })
                    .catch(Notification.exception);

                    promises.push(pagePromise);
                });

                return promises;
            },
            DEFAULT_PAGED_CONTENT_CONFIG
        );

        pagedContentPromise.then(function(html, js) {
            return Templates.replaceNodeContents(content, html, js);
        }).catch(Notification.exception);
    };

    /**
     * Listen to, and handle events for  the myoverview block.
     *
     * @param {Object} root The myoverview block container element.
     */
    var registerEventListeners = function(root) {
        CustomEvents.define(root, [
            CustomEvents.events.activate
        ]);

        root.on(CustomEvents.events.activate, SELECTORS.ACTION_ADD_FAVOURITE, function(e, data) {
            var favourite = $(e.target).closest(SELECTORS.ACTION_ADD_FAVOURITE);
            var courseId = getFavouriteCourseId(favourite);
            addToFavourites(root, courseId);
            data.originalEvent.preventDefault();
        });

        root.on(CustomEvents.events.activate, SELECTORS.ACTION_REMOVE_FAVOURITE, function(e, data) {
            var favourite = $(e.target).closest(SELECTORS.ACTION_REMOVE_FAVOURITE);
            var courseId = getFavouriteCourseId(favourite);
            removeFromFavourites(root, courseId);
            data.originalEvent.preventDefault();
        });

        root.on(CustomEvents.events.activate, SELECTORS.FAVOURITE_ICON, function(e, data) {
            data.originalEvent.preventDefault();
        });
    };

    /**
     * Reset the courses views to their original
     * state on first page load.
     *
     * This is called when configuration has changed for the event lists
     * to cause them to reload their data.
     *
     * @param {Object} root The root element for the timeline view.
     * @param {Object} content The content element for the timeline view.
     */
    var reset = function(root, content) {

        if (loadedPages.length > 0) {
            loadedPages.forEach(function(courseList, index) {
                var pagedContentPage = getPagedContentContainer(root, index);
                renderCourses(root, courseList).then(function(html, js) {
                    return Templates.replaceNodeContents(pagedContentPage, html, js);
                }).catch(Notification.exception);
            });
        } else {
            init(root, content);
        }
    };

    return {
        init: init,
        reset: reset
    };
});
