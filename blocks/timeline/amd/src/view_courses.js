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
 * Manage the timeline courses view for the timeline block.
 *
 * @package    block_timeline
 * @copyright  2018 Ryan Wyllie <ryan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define(
[
    'jquery',
    'core/notification',
    'core/custom_interaction_events',
    'core/str',
    'core/templates',
    'block_timeline/event_list',
    'core_course/repository',
    'block_timeline/calendar_events_repository'
],
function(
    $,
    Notification,
    CustomEvents,
    Str,
    Templates,
    EventList,
    CourseRepository,
    EventsRepository
) {

    var SELECTORS = {
        MORE_COURSES_BUTTON: '[data-action="more-courses"]',
        MORE_COURSES_BUTTON_CONTAINER: '[data-region="more-courses-button-container"]',
        NO_COURSES_EMPTY_MESSAGE: '[data-region="no-courses-empty-message"]',
        COURSES_LIST: '[data-region="courses-list"]',
        COURSE_ITEMS_LOADING_PLACEHOLDER: '[data-region="course-items-loading-placeholder"]',
        COURSE_EVENTS_CONTAINER: '[data-region="course-events-container"]',
        COURSE_NAME: '[data-region="course-name"]',
        LOADING_ICON: '.loading-icon'
    };

    var TEMPLATES = {
        COURSE_ITEMS: 'block_timeline/course-items',
        LOADING_ICON: 'core/loading'
    };

    var COURSE_CLASSIFICATION = 'inprogress';
    var COURSE_SORT = 'fullname asc';
    var COURSE_EVENT_LIMIT = 5;
    var COURSE_LIMIT = 2;
    var SECONDS_IN_DAY = 60 * 60 * 24;

    /**
     * Hide the loading placeholder elements.
     *
     * @param {object} root The rool element.
     */
    var hideLoadingPlaceholder = function(root) {
        root.find(SELECTORS.COURSE_ITEMS_LOADING_PLACEHOLDER).addClass('hidden');
    };

    /**
     * Hide the "more courses" button.
     *
     * @param {object} root The rool element.
     */
    var hideMoreCoursesButton = function(root) {
        root.find(SELECTORS.MORE_COURSES_BUTTON_CONTAINER).addClass('hidden');
    };

    /**
     * Show the "more courses" button.
     *
     * @param {object} root The rool element.
     */
    var showMoreCoursesButton = function(root) {
        root.find(SELECTORS.MORE_COURSES_BUTTON_CONTAINER).removeClass('hidden');
    };

    /**
     * Disable the "more courses" button and show the loading spinner.
     *
     * @param {object} root The rool element.
     */
    var enableMoreCoursesButtonLoading = function(root) {
        var button = root.find(SELECTORS.MORE_COURSES_BUTTON);
        button.prop('disabled', true);
        Templates.render(TEMPLATES.LOADING_ICON, {})
            .then(function(html) {
                button.append(html);
                return html;
            })
            .catch(function() {
                // It's not important if this false so just do so silently.
                return false;
            });
    };

    /**
     * Enable the "more courses" button and remove the loading spinner.
     *
     * @param {object} root The rool element.
     */
    var disableMoreCoursesButtonLoading = function(root) {
        var button = root.find(SELECTORS.MORE_COURSES_BUTTON);
        button.prop('disabled', false);
        button.find(SELECTORS.LOADING_ICON).remove();
    };

    /**
     * Display the message for when there are no courses available.
     *
     * @param {object} root The rool element.
     */
    var showNoCoursesEmptyMessage = function(root) {
        root.find(SELECTORS.NO_COURSES_EMPTY_MESSAGE).removeClass('hidden');
    };

    /**
     * Render the course items HTML to the page.
     *
     * @param {object} root The rool element.
     * @param {string} html The course items HTML to render.
     */
    var renderCourseItemsHTML = function(root, html) {
        var container = root.find(SELECTORS.COURSES_LIST);
        Templates.appendNodeContents(container, html, '');
    };

    /**
     * Check if any courses have been loaded.
     *
     * @param {object} root The rool element.
     * @return {bool}
     */
    var hasLoadedCourses = function(root) {
        return root.find(SELECTORS.COURSE_EVENTS_CONTAINER).length > 0;
    };

    /**
     * Return the offset value for fetching courses.
     *
     * @param {object} root The rool element.
     * @return {Number}
     */
    var getOffset = function(root) {
        return parseInt(root.attr('data-offset'), 10);
    };

    /**
     * Set the offset value for fetching courses.
     *
     * @param {object} root The rool element.
     * @param {Number} offset Offset value.
     */
    var setOffset = function(root, offset) {
        root.attr('data-offset', offset);
    };

    /**
     * Return the limit value for fetching courses.
     *
     * @param {object} root The rool element.
     * @return {Number}
     */
    var getLimit = function(root) {
        return parseInt(root.attr('data-limit'), 10);
    };

    /**
     * Return the days offset value for fetching events.
     *
     * @param {object} root The rool element.
     * @return {Number}
     */
    var getDaysOffset = function(root) {
        return parseInt(root.attr('data-days-offset'), 10);
    };

    /**
     * Return the days limit value for fetching events. The days
     * limit is optional so undefined will be returned if it isn't
     * set.
     *
     * @param {object} root The rool element.
     * @return {int|undefined}
     */
    var getDaysLimit = function(root) {
        var daysLimit = root.attr('data-days-limit');
        return daysLimit != undefined ? parseInt(daysLimit, 10) : undefined;
    };

    /**
     * Return the timestamp for the user's midnight.
     *
     * @param {object} root The rool element.
     * @return {Number}
     */
    var getMidnight = function(root) {
        return parseInt(root.attr('data-midnight'), 10);
    };

    /**
     * Return the start time for fetching events. This is calculated
     * based on the user's midnight value so that timezones are
     * preserved.
     *
     * @param {object} root The rool element.
     * @return {Number}
     */
    var getStartTime = function(root) {
        var midnight = getMidnight(root);
        var daysOffset = getDaysOffset(root);
        return midnight + (daysOffset * SECONDS_IN_DAY);
    };

    /**
     * Return the end time for fetching events. This is calculated
     * based on the user's midnight value so that timezones are
     * preserved.
     *
     * @param {object} root The rool element.
     * @return {Number}
     */
    var getEndTime = function(root) {
        var midnight = getMidnight(root);
        var daysLimit = getDaysLimit(root);
        return daysLimit != undefined ? midnight + (daysLimit * SECONDS_IN_DAY) : false;
    };

    /**
     * Get a list of events for the given course ids. Returns a promise that will
     * be resolved with the events.
     *
     * @param {array} courseIds The list of course ids to fetch events for.
     * @param {Number} startTime Timestamp to fetch events from.
     * @param {Number} limit Limit to the number of events (this applies per course, not total)
     * @param {Number} endTime Timestamp to fetch events to.
     * @return {object} jQuery promise.
     */
    var getEventsForCourseIds = function(courseIds, startTime, limit, endTime) {
        var args = {
            courseids: courseIds,
            starttime: startTime,
            limit: limit
        };

        if (endTime) {
            args.endtime = endTime;
        }

        return EventsRepository.queryByCourses(args);
    };

    /**
     * Get the last time the events were reloaded.
     *
     * @param {object} root The rool element.
     * @return {Number}
     */
    var getEventReloadTime = function(root) {
        return root.data('last-event-load-time');
    };

    /**
     * Set the last time the events were reloaded.
     *
     * @param {object} root The rool element.
     * @param {Number} time Timestamp in milliseconds.
     */
    var setEventReloadTime = function(root, time) {
        root.data('last-event-load-time', time);
    };

    /**
     * Check if events have begun reloading since the given
     * time.
     *
     * @param {object} root The rool element.
     * @param {Number} time Timestamp in milliseconds.
     * @return {bool}
     */
    var hasReloadedEventsSince = function(root, time) {
        return getEventReloadTime(root) > time;
    };

    /**
     * Send a request to the server to load the events for the courses.
     *
     * @param {array} courses List of course objects.
     * @param {Number} startTime Timestamp to load events after.
     * @param {int|undefined} endTime Timestamp to load events up until.
     * @return {object} jQuery promise resolved with the events.
     */
    var loadEventsForCourses = function(courses, startTime, endTime) {
        var courseIds = courses.map(function(course) {
            return course.id;
        });

        return getEventsForCourseIds(courseIds, startTime, COURSE_EVENT_LIMIT + 1, endTime);
    };

    /**
     * Render the courses in the DOM once the server has returned the courses.
     *
     * @param {array} courses List of course objects.
     * @param {object} root The root element
     * @param {Number} midnight The midnight timestamp in the user's timezone.
     * @param {Number} daysOffset Number of days from today to offset the events.
     * @param {Number} daysLimit Number of days from today to limit the events to.
     * @param {string} noEventsURL URL for the image to display for no events.
     * @return {object} jQuery promise resolved after rendering is complete.
     */
    var updateDisplayFromCourses = function(courses, root, midnight, daysOffset, daysLimit, noEventsURL) {
        // Render the courses template.
        return Templates.render(TEMPLATES.COURSE_ITEMS, {
            courses: courses,
            midnight: midnight,
            hasdaysoffset: true,
            hasdayslimit: daysLimit != undefined,
            daysoffset: daysOffset,
            dayslimit: daysLimit,
            nodayslimit: daysLimit == undefined,
            urls: {
                noevents: noEventsURL
            }
        }).then(function(html) {
            hideLoadingPlaceholder(root);

            if (html) {
                // Template rendering is complete and we have the HTML so we can
                // add it to the DOM.
                renderCourseItemsHTML(root, html);
            } else {
                if (!hasLoadedCourses(root)) {
                    // There were no courses to render so show the empty placeholder
                    // message for the user to tell them.
                    showNoCoursesEmptyMessage(root);
                }
            }

            return html;
        })
        .then(function(html) {
            if (courses.length < COURSE_LIMIT) {
                // We know there aren't any more courses because we got back less
                // than we asked for so hide the button to request more.
                hideMoreCoursesButton(root);
            } else {
                // Make sure the button is visible if there are more courses to load.
                showMoreCoursesButton(root);
            }

            return html;
        })
        .catch(function() {
            hideLoadingPlaceholder(root);
        });
    };

    /**
     * Find all of the visible course blocks and initialise the event
     * list module to being loading the events for the course block.
     *
     * @param {object} root The root element for the timeline courses view.
     * @return {object} jQuery promise resolved with courses and events.
     */
    var loadMoreCourses = function(root) {
        var offset = getOffset(root);
        var limit = getLimit(root);

        // Start loading the next set of courses.
        return CourseRepository.getEnrolledCoursesByTimelineClassification(
            COURSE_CLASSIFICATION,
            limit,
            offset,
            COURSE_SORT
        ).then(function(result) {
            var startEventLoadingTime = Date.now();
            var courses = result.courses;
            var nextOffset = result.nextoffset;
            var daysOffset = getDaysOffset(root);
            var daysLimit = getDaysLimit(root);
            var midnight = getMidnight(root);
            var startTime = getStartTime(root);
            var endTime = getEndTime(root);
            var noEventsURL = root.attr('data-no-events-url');
            // Record the next offset if we want to request more courses.
            setOffset(root, nextOffset);
            // Load the events for these courses.
            var eventsPromise = loadEventsForCourses(courses, startTime, endTime);
            // Render the courses in the DOM.
            var renderPromise = updateDisplayFromCourses(courses, root, midnight, daysOffset, daysLimit, noEventsURL);

            return $.when(eventsPromise, renderPromise)
                .then(function(eventsByCourse) {
                    if (hasReloadedEventsSince(root, startEventLoadingTime)) {
                        // All of the events are being reloaded so ignore our results.
                        return eventsByCourse;
                    }

                    // When we've got all of the courses and events we can render the events in the
                    // correct course event list.
                    courses.forEach(function(course) {
                        var courseId = course.id;
                        var events = [];
                        var containerSelector = '[data-region="course-events-container"][data-course-id="' + courseId + '"]';
                        var courseEventsContainer = root.find(containerSelector);
                        var eventListRoot = courseEventsContainer.find(EventList.rootSelector);
                        var courseGroups = eventsByCourse.groupedbycourse.filter(function(group) {
                            return group.courseid == courseId;
                        });

                        if (courseGroups.length) {
                            // Get the events for this course.
                            events = courseGroups[0].events;
                        }

                        // Create a preloaded page to pass to the event list because we've already
                        // loaded the first page of events.
                        var pageOnePreload = $.Deferred().resolve({events: events}).promise();
                        // Initialise the event list pagination area for this course.
                        Str.get_string('ariaeventlistpaginationnavcourses', 'block_timeline', course.fullnamedisplay)
                            .then(function(string) {
                                EventList.init(eventListRoot, COURSE_EVENT_LIMIT, {'1': pageOnePreload}, string);
                                return string;
                            })
                            .catch(function() {
                                // An error is ok, just render with the default string.
                                EventList.init(eventListRoot, COURSE_EVENT_LIMIT, {'1': pageOnePreload});
                            });
                    });

                    return eventsByCourse;
                });
        }).catch(Notification.exception);
    };

    /**
     * Reload the events for all of the visible courses. These events will be loaded
     * in a single request to the server.
     *
     * @param {object} root The root element.
     * @return {object} jQuery promise resolved with courses and events.
     */
    var reloadCourseEvents = function(root) {
        var startReloadTime = Date.now();
        var startTime = getStartTime(root);
        var endTime = getEndTime(root);
        var courseEventsContainers = root.find(SELECTORS.COURSE_EVENTS_CONTAINER);
        var courseIds = courseEventsContainers.map(function() {
            return $(this).attr('data-course-id');
        }).get();

        // Record when we started our request.
        setEventReloadTime(root, startReloadTime);

        // Load all of the events for the given courses.
        return getEventsForCourseIds(courseIds, startTime, COURSE_EVENT_LIMIT + 1, endTime)
            .then(function(eventsByCourse) {
                if (hasReloadedEventsSince(root, startReloadTime)) {
                    // A new reload has begun so ignore our results.
                    return eventsByCourse;
                }

                courseEventsContainers.each(function(index, container) {
                    container = $(container);
                    var courseId = container.attr('data-course-id');
                    var courseName = container.find(SELECTORS.COURSE_NAME).text();
                    var eventListContainer = container.find(EventList.rootSelector);
                    var pageDeferred = $.Deferred();
                    var events = [];
                    var courseGroups = eventsByCourse.groupedbycourse.filter(function(group) {
                        return group.courseid == courseId;
                    });

                    if (courseGroups.length) {
                        // Get the events just for this course.
                        events = courseGroups[0].events;
                    }

                    pageDeferred.resolve({events: events});

                    // Re-initialise the events list with the preloaded events we just got from
                    // the server.
                    Str.get_string('ariaeventlistpaginationnavcourses', 'block_timeline', courseName)
                        .then(function(string) {
                            EventList.init(eventListContainer, COURSE_EVENT_LIMIT, {'1': pageDeferred.promise()}, string);
                            return string;
                        })
                        .catch(function() {
                            // Ignore a failure to load the string. Just render with the default string.
                            EventList.init(eventListContainer, COURSE_EVENT_LIMIT, {'1': pageDeferred.promise()});
                        });
                });

                return eventsByCourse;
            }).catch(Notification.exception);
    };

    /**
     * Add event listeners to load more courses for the courses view.
     *
     * @param {object} root The root element for the timeline courses view.
     */
    var registerEventListeners = function(root) {
        CustomEvents.define(root, [CustomEvents.events.activate]);
        // Show more courses and load their events when the user clicks the "more courses"
        // button.
        root.on(CustomEvents.events.activate, SELECTORS.MORE_COURSES_BUTTON, function(e, data) {
            enableMoreCoursesButtonLoading(root);
            loadMoreCourses(root)
                .then(function() {
                    disableMoreCoursesButtonLoading(root);
                    return;
                })
                .catch(function() {
                    disableMoreCoursesButtonLoading(root);
                });

            if (data) {
                data.originalEvent.preventDefault();
                data.originalEvent.stopPropagation();
            }
            e.stopPropagation();
        });
    };

    /**
     * Initialise the timeline courses view. Begin loading the events
     * if this view is active. Add the relevant event listeners.
     *
     * This function should only be called once per page load because it
     * is adding event listeners to the page.
     *
     * @param {object} root The root element for the timeline courses view.
     */
    var init = function(root) {
        root = $(root);

        setEventReloadTime(root, Date.now());

        if (root.hasClass('active')) {
            // Only load if this is active otherwise it will be lazy loaded later.
            loadMoreCourses(root);
            root.attr('data-seen', true);
        }

        registerEventListeners(root);
    };

    /**
     * Reset the element back to it's initial state. Begin loading the events again
     * if this view is active.
     *
     * @param {object} root The root element for the timeline courses view.
     */
    var reset = function(root) {
        root.removeAttr('data-seen');
        if (root.hasClass('active')) {
            shown(root);
        }
    };

    /**
     * If this is the first time this view has been displayed then begin loading
     * the events.
     *
     * @param {object} root The root element for the timeline courses view.
     */
    var shown = function(root) {
        if (!root.attr('data-seen')) {
            if (hasLoadedCourses(root)) {
                // This isn't the first time this view is shown so just reload the
                // events for the courses we've already loaded.
                reloadCourseEvents(root);
            } else {
                // We haven't loaded any courses yet so do that now.
                loadMoreCourses(root);
            }

            root.attr('data-seen', true);
        }
    };

    return {
        init: init,
        reset: reset,
        shown: shown
    };
});
