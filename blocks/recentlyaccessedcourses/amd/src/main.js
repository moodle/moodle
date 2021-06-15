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
        'core/custom_interaction_events',
        'core/notification',
        'core/pubsub',
        'core/paged_content_paging_bar',
        'core/templates',
        'core_course/events',
        'core_course/repository',
    ],
    function(
        $,
        CustomEvents,
        Notification,
        PubSub,
        PagedContentPagingBar,
        Templates,
        CourseEvents,
        CoursesRepository
    ) {

        // Constants.
        var NUM_COURSES_TOTAL = 10;
        var SELECTORS = {
            BLOCK_CONTAINER: '[data-region="recentlyaccessedcourses"]',
            CARD_CONTAINER: '[data-region="card-deck"]',
            COURSE_IS_FAVOURITE: '[data-region="is-favourite"]',
            CONTENT: '[data-region="view-content"]',
            EMPTY_MESSAGE: '[data-region="empty-message"]',
            LOADING_PLACEHOLDER: '[data-region="loading-placeholder"]',
            PAGING_BAR: '[data-region="paging-bar"]',
            PAGING_BAR_NEXT: '[data-control="next"]',
            PAGING_BAR_PREVIOUS: '[data-control="previous"]'
        };
        // Module variables.
        var contentLoaded = false;
        var allCourses = [];
        var visibleCoursesId = null;
        var cardWidth = null;
        var viewIndex = 0;
        var availableVisibleCards = 1;

        /**
         * Show the empty message when no course are found.
         *
         * @param {object} root The root element for the courses view.
         */
        var showEmptyMessage = function(root) {
            root.find(SELECTORS.EMPTY_MESSAGE).removeClass('hidden');
            root.find(SELECTORS.LOADING_PLACEHOLDER).addClass('hidden');
            root.find(SELECTORS.CONTENT).addClass('hidden');
        };

        /**
         * Show the empty message when no course are found.
         *
         * @param {object} root The root element for the courses view.
         */
        var showContent = function(root) {
            root.find(SELECTORS.CONTENT).removeClass('hidden');
            root.find(SELECTORS.EMPTY_MESSAGE).addClass('hidden');
            root.find(SELECTORS.LOADING_PLACEHOLDER).addClass('hidden');
        };

        /**
         * Show the paging bar.
         *
         * @param {object} root The root element for the courses view.
         */
        var showPagingBar = function(root) {
            var pagingBar = root.find(SELECTORS.PAGING_BAR);
            pagingBar.css('opacity', 1);
            pagingBar.css('visibility', 'visible');
            pagingBar.attr('aria-hidden', 'false');
        };

        /**
         * Hide the paging bar.
         *
         * @param {object} root The root element for the courses view.
         */
        var hidePagingBar = function(root) {
            var pagingBar = root.find(SELECTORS.PAGING_BAR);
            pagingBar.css('opacity', 0);
            pagingBar.css('visibility', 'hidden');
            pagingBar.attr('aria-hidden', 'true');
        };

        /**
         * Show the favourite indicator for the given course (if it's in the list).
         *
         * @param {object} root The root element for the courses view.
         * @param {number} courseId The id of the course to be favourited.
         */
        var favouriteCourse = function(root, courseId) {
            allCourses.forEach(function(course) {
                if (course.attr('data-course-id') == courseId) {
                    course.find(SELECTORS.COURSE_IS_FAVOURITE).removeClass('hidden');
                }
            });
        };

        /**
         * Hide the favourite indicator for the given course (if it's in the list).
         *
         * @param {object} root The root element for the courses view.
         * @param {number} courseId The id of the course to be unfavourited.
         */
        var unfavouriteCourse = function(root, courseId) {
            allCourses.forEach(function(course) {
                if (course.attr('data-course-id') == courseId) {
                    course.find(SELECTORS.COURSE_IS_FAVOURITE).addClass('hidden');
                }
            });
        };

        /**
         * Render the a list of courses.
         *
         * @param {array} courses containing array of courses.
         * @return {promise} Resolved with list of rendered courses as jQuery objects.
         */
        var renderAllCourses = function(courses) {
            var showcoursecategory = $(SELECTORS.BLOCK_CONTAINER).data('displaycoursecategory');
            var promises = courses.map(function(course) {
                course.showcoursecategory = showcoursecategory;
                return Templates.render('block_recentlyaccessedcourses/course-card', course);
            });

            return $.when.apply(null, promises).then(function() {
                var renderedCourses = [];

                promises.forEach(function(promise) {
                    promise.then(function(html) {
                        renderedCourses.push($(html));
                        return;
                    })
                    .catch(Notification.exception);
                });

                return renderedCourses;
            });
        };

        /**
         * Fetch user's recently accessed courses and reload the content of the block.
         *
         * @param {int} userid User whose courses will be shown
         * @returns {promise} The updated content for the block.
         */
        var loadContent = function(userid) {
            return CoursesRepository.getLastAccessedCourses(userid, NUM_COURSES_TOTAL)
                .then(function(courses) {
                    return renderAllCourses(courses);
                });
        };

        /**
         * Recalculate the number of courses that should be visible.
         *
         * @param {object} root The root element for the courses view.
         */
        var recalculateVisibleCourses = function(root) {
            var container = root.find(SELECTORS.CONTENT).find(SELECTORS.CARD_CONTAINER);
            var availableWidth = parseFloat(root.css('width'));
            var numberOfCourses = allCourses.length;
            var start = 0;

            if (!cardWidth) {
                container.html(allCourses[0]);
                // Render one card initially to calculate the width of the cards
                // including the margins.
                cardWidth = allCourses[0].outerWidth(true);
            }

            availableVisibleCards = Math.floor(availableWidth / cardWidth);

            if (viewIndex + availableVisibleCards < numberOfCourses) {
                start = viewIndex;
            } else {
                var overflow = (viewIndex + availableVisibleCards) - numberOfCourses;
                start = viewIndex - overflow;
                start = start >= 0 ? start : 0;
            }

            // At least show one card.
            if (availableVisibleCards === 0) {
                availableVisibleCards = 1;
            }

            var coursesToShow = allCourses.slice(start, start + availableVisibleCards);
            // Create an id for the list of courses we expect to be displayed.
            var newVisibleCoursesId = coursesToShow.reduce(function(carry, course) {
                return carry + course.attr('data-course-id');
            }, '');

            // Centre the courses if we have an overflow of courses.
            if (allCourses.length > coursesToShow.length) {
                container.addClass('justify-content-center');
                container.removeClass('justify-content-start');
            } else {
                container.removeClass('justify-content-center');
                container.addClass('justify-content-start');
            }

            // Don't bother updating the DOM unless the visible courses have changed.
            if (visibleCoursesId != newVisibleCoursesId) {
                var pagingBar = root.find(PagedContentPagingBar.rootSelector);
                container.html(coursesToShow);
                visibleCoursesId = newVisibleCoursesId;

                if (availableVisibleCards >= allCourses.length) {
                    hidePagingBar(root);
                } else {
                    showPagingBar(root);

                    if (viewIndex === 0) {
                        PagedContentPagingBar.disablePreviousControlButtons(pagingBar);
                    } else {
                        PagedContentPagingBar.enablePreviousControlButtons(pagingBar);
                    }

                    if (viewIndex + availableVisibleCards >= allCourses.length) {
                        PagedContentPagingBar.disableNextControlButtons(pagingBar);
                    } else {
                        PagedContentPagingBar.enableNextControlButtons(pagingBar);
                    }
                }
            }
        };

        /**
         * Register event listeners for the block.
         *
         * @param {object} root The root element for the recentlyaccessedcourses block.
         */
        var registerEventListeners = function(root) {
            var resizeTimeout = null;
            var drawerToggling = false;

            PubSub.subscribe(CourseEvents.favourited, function(courseId) {
                favouriteCourse(root, courseId);
            });

            PubSub.subscribe(CourseEvents.unfavorited, function(courseId) {
                unfavouriteCourse(root, courseId);
            });

            PubSub.subscribe('nav-drawer-toggle-start', function() {
                if (!contentLoaded || !allCourses.length || drawerToggling) {
                    // Nothing to recalculate.
                    return;
                }

                drawerToggling = true;
                var recalculationCount = 0;
                // This function is going to recalculate the number of courses while
                // the nav drawer is opening or closes (up to a maximum of 5 recalcs).
                var doRecalculation = function() {
                    setTimeout(function() {
                        recalculateVisibleCourses(root);
                        recalculationCount++;

                        if (recalculationCount < 5 && drawerToggling) {
                            // If we haven't done too many recalculations and the drawer
                            // is still toggling then recurse.
                            doRecalculation();
                        }
                    }, 100);
                };

                // Start the recalculations.
                doRecalculation(root);
            });

            PubSub.subscribe('nav-drawer-toggle-end', function() {
                drawerToggling = false;
            });

            $(window).on('resize', function() {
                if (!contentLoaded || !allCourses.length) {
                    // Nothing to reclculate.
                    return;
                }

                // Resize events fire rapidly so recalculating the visible courses each
                // time can be expensive. Let's debounce them,
                if (!resizeTimeout) {
                    resizeTimeout = setTimeout(function() {
                        resizeTimeout = null;
                        recalculateVisibleCourses(root);
                    // The recalculateVisibleCourses function will execute at a rate of 15fps.
                    }, 66);
                }
            });

            CustomEvents.define(root, [CustomEvents.events.activate]);
            root.on(CustomEvents.events.activate, SELECTORS.PAGING_BAR_NEXT, function(e, data) {
                var button = $(e.target).closest(SELECTORS.PAGING_BAR_NEXT);
                if (!button.hasClass('disabled')) {
                    viewIndex = viewIndex + availableVisibleCards;
                    recalculateVisibleCourses(root);
                }

                data.originalEvent.preventDefault();
            });

            root.on(CustomEvents.events.activate, SELECTORS.PAGING_BAR_PREVIOUS, function(e, data) {
                var button = $(e.target).closest(SELECTORS.PAGING_BAR_PREVIOUS);
                if (!button.hasClass('disabled')) {
                    viewIndex = viewIndex - availableVisibleCards;
                    viewIndex = viewIndex < 0 ? 0 : viewIndex;
                    recalculateVisibleCourses(root);
                }

                data.originalEvent.preventDefault();
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
            loadContent(userid)
                .then(function(renderedCourses) {
                    allCourses = renderedCourses;
                    contentLoaded = true;

                    if (allCourses.length) {
                        showContent(root);
                        recalculateVisibleCourses(root);
                    } else {
                        showEmptyMessage(root);
                    }

                    return;
                })
                .catch(Notification.exception);
        };

        return {
            init: init
        };
    });
