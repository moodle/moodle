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
 * @module     block_timeline/event_list
 * @package    block_timeline
 * @copyright  2016 Ryan Wyllie <ryan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
define(
[
    'jquery',
    'core/notification',
    'core/templates',
    'core/paged_content_factory',
    'core/str',
    'core/user_date',
    'block_timeline/calendar_events_repository'
],
function(
    $,
    Notification,
    Templates,
    PagedContentFactory,
    Str,
    UserDate,
    CalendarEventsRepository
) {

    var SECONDS_IN_DAY = 60 * 60 * 24;

    var SELECTORS = {
        EMPTY_MESSAGE: '[data-region="empty-message"]',
        ROOT: '[data-region="event-list-container"]',
        EVENT_LIST_CONTENT: '[data-region="event-list-content"]',
        EVENT_LIST_LOADING_PLACEHOLDER: '[data-region="event-list-loading-placeholder"]',
    };

    var TEMPLATES = {
        EVENT_LIST_CONTENT: 'block_timeline/event-list-content'
    };

    // We want the paged content controls below the paged content area
    // and the controls should be ignored while data is loading.
    var DEFAULT_PAGED_CONTENT_CONFIG = {
        ignoreControlWhileLoading: true,
        controlPlacementBottom: true,
        ariaLabels: {
            itemsperpagecomponents: 'ariaeventlistpagelimit, block_timeline',
        }
    };

    /**
     * Hide the content area and display the empty content message.
     *
     * @param {object} root The container element
     */
    var hideContent = function(root) {
        root.find(SELECTORS.EVENT_LIST_CONTENT).addClass('hidden');
        root.find(SELECTORS.EMPTY_MESSAGE).removeClass('hidden');
    };

    /**
     * Show the content area and hide the empty content message.
     *
     * @param {object} root The container element
     */
    var showContent = function(root) {
        root.find(SELECTORS.EVENT_LIST_CONTENT).removeClass('hidden');
        root.find(SELECTORS.EMPTY_MESSAGE).addClass('hidden');
    };

    /**
     * Empty the content area.
     *
     * @param {object} root The container element
     */
    var emptyContent = function(root) {
        root.find(SELECTORS.EVENT_LIST_CONTENT).empty();
    };

    /**
     * Construct the template context from a list of calendar events. The events
     * are grouped by which day they are on. The day is calculated from the user's
     * midnight timestamp to ensure that the calculation is timezone agnostic.
     *
     * The return data structure will look like:
     * {
     *      eventsbyday: [
     *          {
     *              dayTimestamp: 1533744000,
     *              events: [
     *                  { ...event 1 data... },
     *                  { ...event 2 data... }
     *              ]
     *          },
     *          {
     *              dayTimestamp: 1533830400,
     *              events: [
     *                  { ...event 3 data... },
     *                  { ...event 4 data... }
     *              ]
     *          }
     *      ]
     * }
     *
     * Each day timestamp is the day's midnight in the user's timezone.
     *
     * @param {array} calendarEvents List of calendar events
     * @param {Number} midnight A timestamp representing midnight in the user's timezone
     * @return {object}
     */
    var buildTemplateContext = function(calendarEvents, midnight) {
        var eventsByDay = {};
        var templateContext = {
            eventsbyday: []
        };

        calendarEvents.forEach(function(calendarEvent) {
            var dayTimestamp = UserDate.getUserMidnightForTimestamp(calendarEvent.timesort, midnight);
            if (eventsByDay[dayTimestamp]) {
                eventsByDay[dayTimestamp].push(calendarEvent);
            } else {
                eventsByDay[dayTimestamp] = [calendarEvent];
            }
        });

        Object.keys(eventsByDay).forEach(function(dayTimestamp) {
            var events = eventsByDay[dayTimestamp];
            templateContext.eventsbyday.push({
                past: dayTimestamp < midnight,
                dayTimestamp: dayTimestamp,
                events: events
            });
        });

        return templateContext;
    };

    /**
     * Render the HTML for the given calendar events.
     *
     * @param {array} calendarEvents  A list of calendar events
     * @param {Number} midnight A timestamp representing midnight for the user
     * @return {promise} Resolved with HTML and JS strings.
     */
    var render = function(calendarEvents, midnight) {
        var templateContext = buildTemplateContext(calendarEvents, midnight);
        var templateName = TEMPLATES.EVENT_LIST_CONTENT;

        return Templates.render(templateName, templateContext);
    };

    /**
     * Retrieve a list of calendar events from the server for the given
     * constraints.
     *
     * @param {Number} midnight The user's midnight time in unix timestamp.
     * @param {Number} limit Limit the result set to this number of items
     * @param {Number} daysOffset How many days (from midnight) to offset the results from
     * @param {int|undefined} daysLimit How many dates (from midnight) to limit the result to
     * @param {int|falsey} lastId The ID of the last seen event (if any)
     * @param {int|undefined} courseId Course ID to restrict events to
     * @return {promise} A jquery promise
     */
    var load = function(midnight, limit, daysOffset, daysLimit, lastId, courseId) {
        var startTime = midnight + (daysOffset * SECONDS_IN_DAY);
        var endTime = daysLimit != undefined ? midnight + (daysLimit * SECONDS_IN_DAY) : false;

        var args = {
            starttime: startTime,
            limit: limit,
        };

        if (lastId) {
            args.aftereventid = lastId;
        }

        if (endTime) {
            args.endtime = endTime;
        }

        if (courseId) {
            // If we have a course id then we only want events from that course.
            args.courseid = courseId;
            return CalendarEventsRepository.queryByCourse(args);
        } else {
            // Otherwise we want events from any course.
            return CalendarEventsRepository.queryByTime(args);
        }
    };

    /**
     * Handle a single page request from the paged content. Uses the given page data to request
     * the events from the server.
     *
     * Checks the given preloadedPages before sending a request to the server to make sure we
     * don't load data unnecessarily.
     *
     * @param {object} pageData A single page data (see core/paged_content_pages for more info).
     * @param {object} actions Paged content actions (see core/paged_content_pages for more info).
     * @param {Number} midnight The user's midnight time in unix timestamp.
     * @param {object} lastIds The last event ID for each loaded page. Page number is key, id is value.
     * @param {object} preloadedPages An object of preloaded page data. Page number as key, data promise as value.
     * @param {int|undefined} courseId Course ID to restrict events to
     * @param {Number} daysOffset How many days (from midnight) to offset the results from
     * @param {int|undefined} daysLimit How many dates (from midnight) to limit the result to
     * @return {object} jQuery promise resolved with calendar events.
     */
    var loadEventsFromPageData = function(
        pageData,
        actions,
        midnight,
        lastIds,
        preloadedPages,
        courseId,
        daysOffset,
        daysLimit
    ) {
        var pageNumber = pageData.pageNumber;
        var limit = pageData.limit;
        var lastPageNumber = pageNumber;

        // This is here to protect us if, for some reason, the pages
        // are loaded out of order somehow and we don't have a reference
        // to the previous page. In that case, scan back to find the most
        // recent page we've seen.
        while (!lastIds.hasOwnProperty(lastPageNumber)) {
            lastPageNumber--;
        }
        // Use the last id of the most recent page.
        var lastId = lastIds[lastPageNumber];
        var eventsPromise = null;

        if (preloadedPages && preloadedPages.hasOwnProperty(pageNumber)) {
            // This page has been preloaded so use that rather than load the values
            // again.
            eventsPromise = preloadedPages[pageNumber];
        } else {
            // Load one more than the given limit so that we can tell if there
            // is more content to load after this.
            eventsPromise = load(midnight, limit + 1, daysOffset, daysLimit, lastId, courseId);
        }

        return eventsPromise.then(function(result) {
            if (!result.events.length) {
                // If we didn't get any events back then tell the paged content
                // that we're done loading.
                actions.allItemsLoaded(pageNumber);
                return [];
            }

            var calendarEvents = result.events;
            // We expect to receive limit + 1 events back from the server.
            // Any less means there are no more events to load.
            var loadedAll = calendarEvents.length <= limit;

            if (loadedAll) {
                // Tell the pagination that everything is loaded.
                actions.allItemsLoaded(pageNumber);
            } else {
                // Remove the last element from the array because it isn't
                // needed in this result set.
                calendarEvents.pop();
            }

            return calendarEvents;
        });
    };

    /**
     * Use the paged content factory to create a paged content element for showing
     * the event list. We only provide a page limit to the factory because we don't
     * know exactly how many pages we'll need. This creates a paging bar with just
     * next/previous buttons.
     *
     * This function specifies the callback for loading the event data that the user
     * is requesting.
     *
     * @param {int|array} pageLimit A single limit or list of limits as options for the paged content
     * @param {object} preloadedPages An object of preloaded page data. Page number as key, data promise as value.
     * @param {Number} midnight The user's midnight time in unix timestamp.
     * @param {object} firstLoad A jQuery promise to be resolved after the first set of data is loaded.
     * @param {int|undefined} courseId Course ID to restrict events to
     * @param {Number} daysOffset How many days (from midnight) to offset the results from
     * @param {int|undefined} daysLimit How many dates (from midnight) to limit the result to
     * @param {string} paginationAriaLabel String to set as the aria label for the pagination bar.
     * @return {object} jQuery promise.
     */
    var createPagedContent = function(
        pageLimit,
        preloadedPages,
        midnight,
        firstLoad,
        courseId,
        daysOffset,
        daysLimit,
        paginationAriaLabel
    ) {
        // Remember the last event id we loaded on each page because we can't
        // use the offset value since the backend can skip events if the user doesn't
        // have the capability to see them. Instead we load the next page of events
        // based on the last seen event id.
        var lastIds = {'1': 0};
        var hasContent = false;
        var config = $.extend({}, DEFAULT_PAGED_CONTENT_CONFIG);

        return Str.get_string(
                'ariaeventlistpagelimit',
                'block_timeline',
                $.isArray(pageLimit) ? pageLimit[0] : pageLimit
            )
            .then(function(string) {
                config.ariaLabels.itemsperpage = string;
                config.ariaLabels.paginationnav = paginationAriaLabel;
                return string;
            })
            .then(function() {
                return PagedContentFactory.createWithLimit(
                    pageLimit,
                    function(pagesData, actions) {
                        var promises = [];

                        pagesData.forEach(function(pageData) {
                            var pageNumber = pageData.pageNumber;
                            // Load the page data.
                            var pagePromise = loadEventsFromPageData(
                                pageData,
                                actions,
                                midnight,
                                lastIds,
                                preloadedPages,
                                courseId,
                                daysOffset,
                                daysLimit
                            ).then(function(calendarEvents) {
                                if (calendarEvents.length) {
                                    // Remember that we've loaded content.
                                    hasContent = true;
                                    // Remember the last id we've seen.
                                    var lastEventId = calendarEvents[calendarEvents.length - 1].id;
                                    // Record the id that the next page will need to start from.
                                    lastIds[pageNumber + 1] = lastEventId;
                                    // Get the HTML and JS for these calendar events.
                                    return render(calendarEvents, midnight);
                                } else {
                                    return calendarEvents;
                                }
                            })
                            .catch(Notification.exception);

                            promises.push(pagePromise);
                        });

                        $.when.apply($, promises).then(function() {
                            // Tell the calling code that the first page has been loaded
                            // and whether it contains any content.
                            firstLoad.resolve(hasContent);
                            return;
                        })
                        .catch(function() {
                            firstLoad.resolve(hasContent);
                        });

                        return promises;
                    },
                    config
                );
            });
    };

    /**
     * Create a paged content region for the calendar events in the given root element.
     * The content of the root element are replaced with a new paged content section
     * each time this function is called.
     *
     * This function will be called each time the offset or limit values are changed to
     * reload the event list region.
     *
     * @param {object} root The event list container element
     * @param {int|array} pageLimit A single limit or list of limits as options for the paged content
     * @param {object} preloadedPages An object of preloaded page data. Page number as key, data promise as value.
     * @param {string} paginationAriaLabel String to set as the aria label for the pagination bar.
     */
    var init = function(root, pageLimit, preloadedPages, paginationAriaLabel) {
        root = $(root);

        // Create a promise that will be resolved once the first set of page
        // data has been loaded. This ensures that the loading placeholder isn't
        // hidden until we have all of the data back to prevent the page elements
        // jumping around.
        var firstLoad = $.Deferred();
        var eventListContent = root.find(SELECTORS.EVENT_LIST_CONTENT);
        var loadingPlaceholder = root.find(SELECTORS.EVENT_LIST_LOADING_PLACEHOLDER);
        var courseId = root.attr('data-course-id');
        var daysOffset = parseInt(root.attr('data-days-offset'), 10);
        var daysLimit = root.attr('data-days-limit');
        var midnight = parseInt(root.attr('data-midnight'), 10);

        // Make sure the content area and loading placeholder is visible.
        // This is because the init function can be called to re-initialise
        // an existing event list area.
        emptyContent(root);
        showContent(root);
        loadingPlaceholder.removeClass('hidden');

        // Days limit isn't mandatory.
        if (daysLimit != undefined) {
            daysLimit = parseInt(daysLimit, 10);
        }

        // Created the paged content element.
        createPagedContent(pageLimit, preloadedPages, midnight, firstLoad, courseId, daysOffset, daysLimit, paginationAriaLabel)
            .then(function(html, js) {
                html = $(html);
                // Hide the content for now.
                html.addClass('hidden');
                // Replace existing elements with the newly created paged content.
                // If we're reinitialising an existing event list this will replace
                // the old event list (including removing any event handlers).
                Templates.replaceNodeContents(eventListContent, html, js);

                firstLoad.then(function(hasContent) {
                    // Prevent changing page elements too much by only showing the content
                    // once we've loaded some data for the first time. This allows our
                    // fancy loading placeholder to shine.
                    html.removeClass('hidden');
                    loadingPlaceholder.addClass('hidden');

                    if (!hasContent) {
                        // If we didn't get any data then show the empty data message.
                        hideContent(root);
                    }

                    return hasContent;
                })
                .catch(function() {
                    return false;
                });

                return html;
            })
            .catch(Notification.exception);
    };

    return {
        init: init,
        rootSelector: SELECTORS.ROOT,
    };
});
