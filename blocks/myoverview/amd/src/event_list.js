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
        EVENT_LIST: '[data-region="event-list"]',
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
     * A filter function to check if the given calendar event falls within the
     * specified time range.
     *
     * @method filterByDay
     * @private
     * @param {int}     seedTime        The start timestamp from which to calculate
     * @param {int}     startDayOffset  The number of days to offset from the seedTime when
     *                                  calculating the start of the time range
     * @param {int}     endDayOffset    The number of days to offset from the seedTime when
     *                                  calculating the end of the time range
     * @param {object}  calendarEvent   The list of calendar events
     * @return {bool}
     */
    var filterByDay = function(seedTime, startDayOffset, endDayOffset, calendarEvent) {
        var orderTime = calendarEvent.orderTime || 0,
            startTime = seedTime + (startDayOffset * SECONDS_IN_DAY);

        if (!endDayOffset) {
            return (orderTime >= startTime);
        } else {
            var endTime = seedTime + (endDayOffset * SECONDS_IN_DAY);

            return (orderTime >= startTime && orderTime < endTime);
        }
    };

    /**
     * Render the given calendar events in the container element.
     *
     * @method render
     * @private
     * @param {object}  root            The container element
     * @param {array}   calendarEvents  A list of calendar events
     * @return {promise} Resolved with a count of the number of rendered events
     */
    var render = function(root, calendarEvents) {
        var promises = [],
            date = new Date(),
            todayTime = Math.floor(date.setHours(0, 0, 0, 0) / 1000),
            eventListGroups = root.find(SELECTORS.EVENT_LIST_GROUP_CONTAINER),
            renderCount = 0;

        // For each event list group find the events from the calender event
        // list that fit in the day range they are listening for and render
        // the events within that group.
        eventListGroups.each(function() {
            var group = $(this),
                startDay = +group.attr('data-start-day'),
                endDay = +group.attr('data-end-day') || 0,
                groupCalendarEvents = calendarEvents.filter(function(value) {
                    return filterByDay(todayTime, startDay, endDay, value);
                });

            if (groupCalendarEvents.length) {
                renderCount += groupCalendarEvents.length;
                promises.push(renderGroup(group, groupCalendarEvents));
            }
        });

        return $.when.apply(null, promises).then(function() {
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
            offset = +root.attr('data-offset'),
            date = new Date(),
            todayTime = Math.floor(date.setHours(0, 0, 0, 0) / 1000);

        // Don't load twice.
        if (isLoading(root)) {
            return $.Deferred().resolve();
        }

        startLoading(root);

        // Request data from the server.
        return CalendarEventsRepository.queryFromTime(todayTime, limit, offset).then(function(calendarEvents) {
            if (!calendarEvents.length || (calendarEvents.length < limit)) {
                // We have no more events so mark the list as done.
                setLoadedAll(root);
            }

            if (calendarEvents.length) {
                // Increment the offset by the number of events returned.
                root.attr('data-offset', offset + calendarEvents.length);

                // Render the events.
                return render(root, calendarEvents).then(function(renderCount) {
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
        load: load,
        registerEventListeners: registerEventListeners,
    };
});
