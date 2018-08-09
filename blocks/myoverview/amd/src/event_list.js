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
 * Javascript to load and render the list of calendar events for a
 * given day range.
 *
 * @module     block_myoverview/event_list
 * @package    block_myoverview
 * @copyright  2016 Ryan Wyllie <ryan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
define(['jquery', 'core/notification', 'core/templates',
        'core/custom_interaction_events',
        'block_myoverview/calendar_events_repository'],
        function($, Notification, Templates, CustomEvents, CalendarEventsRepository) {

    var SECONDS_IN_DAY = 60 * 60 * 24;

    var SELECTORS = {
        EMPTY_MESSAGE: '[data-region="empty-message"]',
        ROOT: '[data-region="event-list-container"]',
        EVENT_LIST: '[data-region="event-list"]',
        EVENT_LIST_CONTENT: '[data-region="event-list-content"]',
        EVENT_LIST_GROUP_CONTAINER: '[data-region="event-list-group-container"]',
        LOADING_ICON_CONTAINER: '[data-region="loading-icon-container"]',
        VIEW_MORE_BUTTON: '[data-action="view-more"]'
    };

    var TEMPLATES = {
        EVENT_LIST_ITEMS: 'block_myoverview/event-list-items',
        COURSE_EVENT_LIST_ITEMS: 'block_myoverview/course-event-list-items'
    };

    /**
     * Set a flag on the element to indicate that it has completed
     * loading all event data.
     *
     * @method setLoadedAll
     * @private
     * @param {object} root The container element
     */
    var setLoadedAll = function(root) {
        root.attr('data-loaded-all', true);
    };

    /**
     * Check if all event data has finished loading.
     *
     * @method hasLoadedAll
     * @private
     * @param {object} root The container element
     * @return {bool} if the element has completed all loading
     */
    var hasLoadedAll = function(root) {
        return !!root.attr('data-loaded-all');
    };

    /**
     * Set the element state to loading.
     *
     * @method startLoading
     * @private
     * @param {object} root The container element
     */
    var startLoading = function(root) {
        var loadingIcon = root.find(SELECTORS.LOADING_ICON_CONTAINER),
            viewMoreButton = root.find(SELECTORS.VIEW_MORE_BUTTON);

        root.addClass('loading');
        loadingIcon.removeClass('hidden');
        viewMoreButton.prop('disabled', true);
    };

    /**
     * Remove the loading state from the element.
     *
     * @method stopLoading
     * @private
     * @param {object} root The container element
     */
    var stopLoading = function(root) {
        var loadingIcon = root.find(SELECTORS.LOADING_ICON_CONTAINER),
            viewMoreButton = root.find(SELECTORS.VIEW_MORE_BUTTON);

        root.removeClass('loading');
        loadingIcon.addClass('hidden');

        if (!hasLoadedAll(root)) {
            // Only enable the button if we've got more events to load.
            viewMoreButton.prop('disabled', false);
        } else {
            viewMoreButton.addClass('hidden');
        }
    };

    /**
     * Check if the element is currently loading some event data.
     *
     * @method isLoading
     * @private
     * @param {object} root The container element
     * @returns {Boolean}
     */
    var isLoading = function(root) {
        return root.hasClass('loading');
    };

    /**
     * Flag the root element to remember that it contains events.
     *
     * @method setHasContent
     * @private
     * @param {object} root The container element
     */
    var setHasContent = function(root) {
        root.attr('data-has-events', true);
    };

    /**
     * Check if the root element has had events loaded.
     *
     * @method hasContent
     * @private
     * @param {object} root The container element
     * @return {bool}
     */
    var hasContent = function(root) {
        return root.attr('data-has-events') ? true : false;
    };

    /**
     * Update the visibility of the content area. The content area
     * is hidden if we have no events.
     *
     * @method updateContentVisibility
     * @private
     * @param {object} root The container element
     * @param {int} eventCount A count of the events we just received.
     */
    var updateContentVisibility = function(root, eventCount) {
        if (eventCount) {
            // We've rendered some events, let's remember that.
            setHasContent(root);
        } else {
            // If this is the first time trying to load events and
            // we don't have any then there isn't any so let's show
            // the empty message.
            if (!hasContent(root)) {
                hideContent(root);
            }
        }
    };

    /**
     * Hide the content area and display the empty content message.
     *
     * @method hideContent
     * @private
     * @param {object} root The container element
     */
    var hideContent = function(root) {
        root.find(SELECTORS.EVENT_LIST_CONTENT).addClass('hidden');
        root.find(SELECTORS.EMPTY_MESSAGE).removeClass('hidden');
    };

    /**
     * Render a group of calendar events and add them to the event
     * list.
     *
     * @method renderGroup
     * @private
     * @param {object}  group           The group container element
     * @param {array}   calendarEvents  The list of calendar events
     * @param {string}  templateName    The template name
     * @return {promise} Resolved when the elements are attached to the DOM
     */
    var renderGroup = function(group, calendarEvents, templateName) {

        group.removeClass('hidden');

        return Templates.render(
            templateName,
            {events: calendarEvents}
        ).done(function(html, js) {
            Templates.appendNodeContents(group.find(SELECTORS.EVENT_LIST), html, js);
        });
    };

    /**
     * Determine the time (in seconds) from the given timestamp until the calendar
     * event will need actioning.
     *
     * @method timeUntilEvent
     * @private
     * @param {int}     timestamp   The time to compare with
     * @param {object}  event       The calendar event
     * @return {int}
     */
    var timeUntilEvent = function(timestamp, event) {
        var orderTime = event.timesort || 0;
        return orderTime - timestamp;
    };

    /**
     * Check if the given calendar event should be added to the given event
     * list group container. The event list group container will specify a
     * day range for the time boundary it is interested in.
     *
     * If only a start day is specified for the container then it will be treated
     * as an open catchment for all events that begin after that time.
     *
     * @method eventBelongsInContainer
     * @private
     * @param {object} root         The root element
     * @param {object} event        The calendar event
     * @param {object} container    The group event list container
     * @return {bool}
     */
    var eventBelongsInContainer = function(root, event, container) {
        var todayTime = root.attr('data-midnight'),
            timeUntilContainerStart = +container.attr('data-start-day') * SECONDS_IN_DAY,
            timeUntilContainerEnd = +container.attr('data-end-day') * SECONDS_IN_DAY,
            timeUntilEventNeedsAction = timeUntilEvent(todayTime, event);

        if (container.attr('data-end-day') === '') {
            return timeUntilContainerStart <= timeUntilEventNeedsAction;
        } else {
            return timeUntilContainerStart <= timeUntilEventNeedsAction &&
                   timeUntilEventNeedsAction < timeUntilContainerEnd;
        }
    };

    /**
     * Return a function that can be used to filter a list of events based on the day
     * range specified on the given event list group container.
     *
     * @method getFilterCallbackForContainer
     * @private
     * @param {object} root      The root element
     * @param {object} container Event list group container
     * @return {function}
     */
    var getFilterCallbackForContainer = function(root, container) {
        return function(event) {
            return eventBelongsInContainer(root, event, $(container));
        };
    };

    /**
     * Render the given calendar events in the container element. The container
     * elements must have a day range defined using data attributes that will be
     * used to group the calendar events according to their order time.
     *
     * @method render
     * @private
     * @param {object}  root            The container element
     * @param {array}   calendarEvents  A list of calendar events
     * @return {promise} Resolved with a count of the number of rendered events
     */
    var render = function(root, calendarEvents) {
        var renderCount = 0;
        var templateName = TEMPLATES.EVENT_LIST_ITEMS;

        if (root.attr('data-course-id')) {
            templateName = TEMPLATES.COURSE_EVENT_LIST_ITEMS;
        }

        // Loop over each of the element list groups and find the set of calendar events
        // that belong to that group (as defined by the group's day range). The matching
        // list of calendar events are rendered and added to the DOM within that group.
        return $.when.apply($, $.map(root.find(SELECTORS.EVENT_LIST_GROUP_CONTAINER), function(container) {
            var events = calendarEvents.filter(getFilterCallbackForContainer(root, container));

            if (events.length) {
                renderCount += events.length;
                return renderGroup($(container), events, templateName);
            } else {
                return null;
            }
        })).then(function() {
            return renderCount;
        });
    };

    /**
     * Retrieve a list of calendar events, render and append them to the end of the
     * existing list. The events will be loaded based on the set of data attributes
     * on the root element.
     *
     * This function can be provided with a jQuery promise. If it is then it won't
     * attempt to load data by itself, instead it will use the given promise.
     *
     * The provided promise must resolve with an an object that has an events key
     * and value is an array of calendar events.
     * E.g.
     * { events: ['event 1', 'event 2'] }
     *
     * @method load
     * @param {object} root The root element of the event list
     * @param {object} promise A jQuery promise resolved with events
     * @return {promise} A jquery promise
     */
    var load = function(root, promise) {
        root = $(root);
        var limit = +root.attr('data-limit'),
            courseId = +root.attr('data-course-id'),
            lastId = root.attr('data-last-id'),
            midnight = root.attr('data-midnight'),
            startTime = midnight - (14 * SECONDS_IN_DAY);

        // Don't load twice.
        if (isLoading(root)) {
            return $.Deferred().resolve();
        }

        startLoading(root);

        // If we haven't been provided a promise to resolve the
        // data then we will load our own.
        if (typeof promise == 'undefined') {
            var args = {
                starttime: startTime,
                limit: limit,
            };

            if (lastId) {
                args.aftereventid = lastId;
            }

            // If we have a course id then we only want events from that course.
            if (courseId) {
                args.courseid = courseId;
                promise = CalendarEventsRepository.queryByCourse(args);
            } else {
                // Otherwise we want events from any course.
                promise = CalendarEventsRepository.queryByTime(args);
            }
        }

        // Request data from the server.
        return promise.then(function(result) {
            if (!result.events.length) {
                // No events, nothing to do.
                setLoadedAll(root);
                return 0;
            }

            var calendarEvents = result.events;

            // Remember the last id we've seen.
            root.attr('data-last-id', calendarEvents[calendarEvents.length - 1].id);

            if (calendarEvents.length < limit) {
                // No more events to load, disable loading button.
                setLoadedAll(root);
            }

            // Render the events.
            return render(root, calendarEvents).then(function(renderCount) {
                if (renderCount < calendarEvents.length) {
                    // If the number of events that was rendered is less than
                    // the number we sent for rendering we can assume that there
                    // are no groups to add them in. Since the ordering of the
                    // events is guaranteed it means that any future requests will
                    // also yield events that can't be rendered, so let's not bother
                    // sending any more requests.
                    setLoadedAll(root);
                }
                return calendarEvents.length;
            });
        }).then(function(eventCount) {
            return updateContentVisibility(root, eventCount);
        }).fail(
            Notification.exception
        ).always(function() {
            stopLoading(root);
        });
    };

    /**
     * Register the event listeners for the container element.
     *
     * @method registerEventListeners
     * @param {object} root The root element of the event list
     */
    var registerEventListeners = function(root) {
        CustomEvents.define(root, [CustomEvents.events.activate]);
        root.on(CustomEvents.events.activate, SELECTORS.VIEW_MORE_BUTTON, function() {
            load(root);
        });
    };

    return {
        init: function(root) {
            root = $(root);
            load(root);
            registerEventListeners(root);
        },
        registerEventListeners: registerEventListeners,
        load: load,
        rootSelector: SELECTORS.ROOT,
    };
});
