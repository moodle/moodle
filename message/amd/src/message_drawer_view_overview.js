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
    'core_message/message_drawer_view_overview_section_favourites',
    'core_message/message_drawer_view_overview_section_group_messages',
    'core_message/message_drawer_view_overview_section_messages',
    'core_message/message_drawer_router',
    'core_message/message_drawer_routes',
    'core_message/message_drawer_events'
],
function(
    $,
    KeyCodes,
    PubSub,
    Str,
    Favourites,
    GroupMessages,
    Messages,
    Router,
    Routes,
    MessageDrawerEvents
) {

    var SELECTORS = {
        CONTACT_REQUEST_COUNT: '[data-region="contact-request-count"]',
        FAVOURITES: '[data-region="view-overview-favourites"]',
        GROUP_MESSAGES: '[data-region="view-overview-group-messages"]',
        MESSAGES: '[data-region="view-overview-messages"]',
        SEARCH_INPUT: '[data-region="view-overview-search-input"]'
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
     * @param {Object} header Conversation header container element.
     */
    var registerEventListeners = function(header) {
        var searchInput = getSearchInput(header);
        var ignoredKeys = [KeyCodes.tab, KeyCodes.shift, KeyCodes.ctrl, KeyCodes.alt];

        searchInput.on('click', function() {
            Router.go(Routes.VIEW_SEARCH);
        });
        searchInput.on('keydown', function(e) {
            if (ignoredKeys.indexOf(e.keyCode) < 0 && e.key != 'Meta') {
                Router.go(Routes.VIEW_SEARCH);
            }
        });

        PubSub.subscribe(MessageDrawerEvents.CONTACT_REQUEST_ACCEPTED, decrementContactRequestCount(header));
        PubSub.subscribe(MessageDrawerEvents.CONTACT_REQUEST_DECLINED, decrementContactRequestCount(header));
    };

    /**
     * Setup the overview page.
     *
     * @param {Object} header Overview header container element.
     * @param {Object} body Overview body container element.
     * @return {Object} jQuery promise
     */
    var show = function(header, body) {
        if (!header.attr('data-init')) {
            registerEventListeners(header);
            header.attr('data-init', true);
        }

        getSearchInput(header).val('');

        return $.when(
            Favourites.show(body.find(SELECTORS.FAVOURITES)),
            GroupMessages.show(body.find(SELECTORS.GROUP_MESSAGES)),
            Messages.show(body.find(SELECTORS.MESSAGES))
        );
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
