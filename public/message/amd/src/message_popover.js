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
 * Controls the message popover in the nav bar.
 *
 * @module     core_message/message_popover
 * @copyright  2018 Ryan Wyllie <ryan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
define(
[
    'jquery',
    'core/custom_interaction_events',
    'core/pubsub',
    'core_message/message_drawer_events'
],
function(
    $,
    CustomEvents,
    PubSub,
    MessageDrawerEvents
) {
    var SELECTORS = {
        COUNT_CONTAINER: '[data-region="count-container"]'
    };

    /**
     * Toggle the message drawer visibility.
     *
     * @param {String} buttonid The button id for the popover.
     */
    var toggleMessageDrawerVisibility = function(buttonid) {
        PubSub.publish(MessageDrawerEvents.TOGGLE_VISIBILITY, buttonid);
    };

    /**
     * Decrement the unread conversation count in the nav bar if a conversation
     * is read. When there are no unread conversations then hide the counter.
     *
     * @param {Object} button The button element for the popover.
     * @return {Function}
     */
    var handleDecrementConversationCount = function(button) {
        return function() {
            var countContainer = button.find(SELECTORS.COUNT_CONTAINER);
            var count = parseInt(countContainer.text(), 10);

            if (isNaN(count)) {
                countContainer.addClass('hidden');
            } else if (!count || count < 2) {
                countContainer.addClass('hidden');
            } else {
                count = count - 1;
                countContainer.text(count);
            }
        };
    };

    /**
     * Add events listeners for when the popover icon is clicked and when conversations
     * are read.
     *
     * @param {Object} button The button element for the popover.
     */
    var registerEventListeners = function(button) {
        CustomEvents.define(button, [CustomEvents.events.activate]);

        button.on(CustomEvents.events.activate, function(e, data) {
            toggleMessageDrawerVisibility(button.attr('id'));
            data.originalEvent.preventDefault();
        });

        PubSub.subscribe(MessageDrawerEvents.CONVERSATION_READ, handleDecrementConversationCount(button));
        PubSub.subscribe(MessageDrawerEvents.CONTACT_REQUEST_ACCEPTED, handleDecrementConversationCount(button));
        PubSub.subscribe(MessageDrawerEvents.CONTACT_REQUEST_DECLINED, handleDecrementConversationCount(button));
    };

    /**
     * Initialise the message popover.
     *
     * @param {Object} button The button element for the popover.
     */
    var init = function(button) {
        button = $(button);
        registerEventListeners(button);
    };

    return {
        init: init,
    };
});
