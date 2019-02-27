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
 * Controls the overview page of the message drawer.
 *
 * @module     core_message/message_drawer_view_overview
 * @copyright  2018 Ryan Wyllie <ryan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
define(
[
    'jquery',
    'core/key_codes',
    'core/pubsub',
    'core/str',
    'core_message/message_drawer_router',
    'core_message/message_drawer_routes',
    'core_message/message_drawer_events',
    'core_message/message_drawer_view_overview_section',
    'core_message/message_repository'
],
function(
    $,
    KeyCodes,
    PubSub,
    Str,
    Router,
    Routes,
    MessageDrawerEvents,
    Section,
    MessageRepository
) {

    var SELECTORS = {
        CONTACT_REQUEST_COUNT: '[data-region="contact-request-count"]',
        FAVOURITES: '[data-region="view-overview-favourites"]',
        GROUP_MESSAGES: '[data-region="view-overview-group-messages"]',
        MESSAGES: '[data-region="view-overview-messages"]',
        SEARCH_INPUT: '[data-region="view-overview-search-input"]',
        SECTION_TOGGLE_BUTTON: '[data-toggle]'
    };

    var CONVERSATION_TYPES = {
        PRIVATE: 1,
        PUBLIC: 2,
        FAVOURITE: null
    };

    var loadAllCountsPromise = null;

    /**
     * Load the total and unread conversation counts from the server for this user. This function
     * returns a jQuery promise that will be resolved with the counts.
     *
     * The request is only sent once per page load and will be cached for subsequent
     * calls to this function.
     *
     * @param {Number} loggedInUserId The logged in user's id
     * @return {Object} jQuery promise
     */
    var loadAllCounts = function(loggedInUserId) {
        if (loadAllCountsPromise === null) {
            loadAllCountsPromise = MessageRepository.getAllConversationCounts(loggedInUserId);
        }

        return loadAllCountsPromise;
    };

    /**
     * Filter a set of counts to return only the count for the given type.
     *
     * This is used on the result returned by the loadAllCounts function.
     *
     * @param {Object} counts Conversation counts indexed by conversation type.
     * @param {String|null} type The conversation type (null for favourites only).
     * @return {Number}
     */
    var filterCountsByType = function(counts, type) {
        return type === CONVERSATION_TYPES.FAVOURITE ? counts.favourites : counts.types[type];
    };

    /**
     * Opens one of the sections based on whether the section has unread conversations
     * or any conversations
     *
     * Default section priority is favourites, groups, then messages. A section can increase
     * in priority if it has conversations in it. It can increase even further if it has
     * unread conversations.
     *
     * @param {Array} sections List of section roots, total counts, and unread counts.
     */
    var openSection = function(sections) {
        var isAlreadyOpen = sections.some(function(section) {
            var sectionRoot = section[0];
            return Section.isVisible(sectionRoot);
        });

        if (isAlreadyOpen) {
            // The user has already opened a section so there is nothing to do.
            return;
        }

        // Order the sections so that sections with unread conversations are prioritised
        // over sections without and sections with total conversations are prioritised
        // over sections without.
        sections.sort(function(a, b) {
            var aTotal = a[1];
            var aUnread = a[2];
            var bTotal = b[1];
            var bUnread = b[2];

            if (aUnread > 0 && bUnread == 0) {
                return -1;
            } else if (aUnread == 0 && bUnread > 0) {
                return 1;
            } else if (aTotal > 0 && bTotal == 0) {
                return -1;
            } else if (aTotal == 0 && bTotal > 0) {
                return 1;
            } else {
                return 0;
            }
        });

        // Get the root of the first section after sorting.
        var sectionRoot = sections[0][0];
        var button = sectionRoot.find(SELECTORS.SECTION_TOGGLE_BUTTON);
        // Click it to expand it.
        button.click();
    };

    /**
     * Get the search input text element.
     *
     * @param  {Object} header Overview header container element.
     * @return {Object} The search input element.
     */
    var getSearchInput = function(header) {
        return header.find(SELECTORS.SEARCH_INPUT);
    };

    /**
     * Get the logged in user id.
     *
     * @param {Object} body Overview body container element.
     * @return {String} Logged in user id.
     */
    var getLoggedInUserId = function(body) {
        return body.attr('data-user-id');
    };

    /**
     * Decrement the contact request count. If the count is zero or below then
     * hide the count.
     *
     * @param {Object} header Conversation header container element.
     * @return {Function} A function to handle decrementing the count.
     */
    var decrementContactRequestCount = function(header) {
        return function() {
            var countContainer = header.find(SELECTORS.CONTACT_REQUEST_COUNT);
            var count = parseInt(countContainer.text(), 10);
            count = isNaN(count) ? 0 : count - 1;

            if (count <= 0) {
                countContainer.addClass('hidden');
            } else {
                countContainer.text(count);
            }
        };
    };

    /**
     * Listen to, and handle event in the overview header.
     *
     * @param {String} namespace Unique identifier for the Routes
     * @param {Object} header Conversation header container element.
     */
    var registerEventListeners = function(namespace, header) {
        var searchInput = getSearchInput(header);
        var ignoredKeys = [KeyCodes.tab, KeyCodes.shift, KeyCodes.ctrl, KeyCodes.alt];

        searchInput.on('click', function() {
            Router.go(namespace, Routes.VIEW_SEARCH);
        });
        searchInput.on('keydown', function(e) {
            if (ignoredKeys.indexOf(e.keyCode) < 0 && e.key != 'Meta') {
                Router.go(namespace, Routes.VIEW_SEARCH);
            }
        });

        PubSub.subscribe(MessageDrawerEvents.CONTACT_REQUEST_ACCEPTED, decrementContactRequestCount(header));
        PubSub.subscribe(MessageDrawerEvents.CONTACT_REQUEST_DECLINED, decrementContactRequestCount(header));
    };

    /**
     * Setup the overview page.
     *
     * @param {String} namespace Unique identifier for the Routes
     * @param {Object} header Overview header container element.
     * @param {Object} body Overview body container element.
     * @return {Object} jQuery promise
     */
    var show = function(namespace, header, body) {
        if (!header.attr('data-init')) {
            registerEventListeners(namespace, header);
            header.attr('data-init', true);
        }

        getSearchInput(header).val('');
        var loggedInUserId = getLoggedInUserId(body);
        var allCounts = loadAllCounts(loggedInUserId);

        var sections = [
            // Favourite conversations section.
            [body.find(SELECTORS.FAVOURITES), CONVERSATION_TYPES.FAVOURITE, true],
            // Group conversations section.
            [body.find(SELECTORS.GROUP_MESSAGES), CONVERSATION_TYPES.PUBLIC, false],
            // Private conversations section.
            [body.find(SELECTORS.MESSAGES), CONVERSATION_TYPES.PRIVATE, false]
        ];

        sections.forEach(function(args) {
            var sectionRoot = args[0];
            var sectionType = args[1];
            var includeFavourites = args[2];
            var totalCountPromise = allCounts.then(function(result) {
                return filterCountsByType(result.total, sectionType);
            });
            var unreadCountPromise = allCounts.then(function(result) {
                return filterCountsByType(result.unread, sectionType);
            });

            Section.show(namespace, null, sectionRoot, null, sectionType, includeFavourites,
                totalCountPromise, unreadCountPromise);
        });

        return allCounts.then(function(result) {
                var sectionParams = sections.map(function(section) {
                    var sectionRoot = section[0];
                    var sectionType = section[1];
                    var totalCount = filterCountsByType(result.total, sectionType);
                    var unreadCount = filterCountsByType(result.unread, sectionType);

                    return [sectionRoot, totalCount, unreadCount];
                });

                // Open up one of the sections for the user.
                return openSection(sectionParams);
            });
    };

    /**
     * String describing this page used for aria-labels.
     *
     * @return {Object} jQuery promise
     */
    var description = function() {
        return Str.get_string('messagedrawerviewoverview', 'core_message');
    };

    return {
        show: show,
        description: description
    };
});
