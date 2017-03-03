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
        EVENT_LIST: '[data-region="event-list"]',
        EVENT_LIST_CONTENT: '[data-region="event-list-content"]',
        EVENT_LIST_GROUP_CONTAINER: '[data-region="event-list-group-container"]',
        LOADING_ICON_CONTAINER: '[data-region="loading-icon-container"]',
        VIEW_MORE_BUTTON: '[data-action="view-more"]'
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
        }
    };

    /**
     * Check if the element is currently loading some event data.
     *
     * @method isLoading
     * @private
     * @param {object} root The container element
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
     * @return {promise} Resolved when the elements are attached to the DOM
     */
    var renderGroup = function(group, calendarEvents) {
        group.removeClass('hidden');

        return Templates.render(
            'block_myoverview/event-list-items',
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
     * @param {object} event        The calendar event
     * @param {object} container    The group event list container
     * @return {bool}
     */
    var eventBelongsInContainer = function(event, container) {
        var todayTime = Math.floor(new Date().setHours(0, 0, 0, 0) / 1000),
            timeUntilContainerStart = +container.attr('data-start-day') * SECONDS_IN_DAY,
            timeUntilContainerEnd = +container.attr('data-end-day') * SECONDS_IN_DAY,
            timeUntilEventNeedsAction = timeUntilEvent(todayTime, event);

        if (!timeUntilContainerEnd) {
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
     * @param {object} container Event list group container
     * @return {function}
     */
    var getFilterCallbackForContainer = function(container) {
        return function(event) {
            return eventBelongsInContainer(event, $(container));
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

        // Loop over each of the element list groups and find the set of calendar events
        // that belong to that group (as defined by the group's day range). The matching
        // list of calendar events are rendered and added to the DOM within that group.
        return $.when.apply($, $.map(root.find(SELECTORS.EVENT_LIST_GROUP_CONTAINER), function(container) {
            var events = calendarEvents.filter(getFilterCallbackForContainer(container));

            if (events.length) {
                renderCount += events.length;
                return renderGroup($(container), events);
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
     * @method load
     * @param {object} The root element of the event list
     * @param {promise} A jquery promise
     */
    var load = function(root) {
        root = $(root);
        var limit = +root.attr('data-limit'),
            courseId = +root.attr('data-course-id'),
            lastId = root.attr('data-last-id') ? root.attr('data-last-id') : undefined,
            date = new Date(),
            todayTime = Math.floor(date.setHours(0, 0, 0, 0) / 1000);

        // Don't load twice.
        if (isLoading(root)) {
            return $.Deferred().resolve();
        }

        startLoading(root);

        var promise = null;
        if (courseId) {
            promise = CalendarEventsRepository.queryFromTimeByCourse(courseId, todayTime, limit, lastId);
        } else {
            promise = CalendarEventsRepository.queryFromTime(todayTime, limit, lastId);
        }

        // Request data from the server.
        return promise.then(function(result) {
            return result.events;
        }).then(function(calendarEvents) {
            if (!calendarEvents.length || (calendarEvents.length < limit)) {
                // We have no more events so mark the list as done.
                setLoadedAll(root);
            }

            if (calendarEvents.length) {
                // Remember the last id we've seen.
                root.attr('data-last-id', calendarEvents[calendarEvents.length - 1].id);

                // Render the events.
                return render(root, calendarEvents).then(function(renderCount) {
                    updateContentVisibility(root, calendarEvents.length);

                    if (renderCount < calendarEvents.length) {
                        // if the number of events that was rendered is less than
                        // the number we sent for rendering we can assume that there
                        // are no groups to add them in. Since the ordering of the
                        // events is guaranteed it means that any future requests will
                        // also yield events that can't be rendered, so let's not bother
                        // sending any more requests.
                        setLoadedAll(root);
                    }
                });
            } else {
                updateContentVisibility(root, calendarEvents.length);
            }
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
     * @param {object} The root element of the event list
     * @param {promise} A jquery promise
     */
    var registerEventListeners = function(root) {
        CustomEvents.define(root, [CustomEvents.events.activate]);
        root.one(CustomEvents.events.activate, SELECTORS.VIEW_MORE_BUTTON, function() {
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
        load: load
    };
});
