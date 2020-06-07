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
 * Module to message a user from their profile page.
 *
 * @module     core_message/message_user_button
 * @copyright  2019 Mark Nelson <markn@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
define(['jquery', 'core/custom_interaction_events', 'core_message/message_drawer_helper', 'core/templates'],
    function($, CustomEvents, MessageDrawerHelper, Templates) {


        var SELECTORS = {
            MESSAGE_TEXTAREA: '[data-region="send-message-txt"]',
            MESSAGE_USER_BUTTON: '#message-user-button',
            MESSAGE_JUMP: '[data-region="jumpto"]'
        };

        var TEMPLATES = {
            CONTENT: 'core_message/message_jumpto'
        };

        /**
         * Get the id for the user being messaged.
         *
         * @param {object} element jQuery object for the button
         * @return {int}
         */
        var getUserId = function(element) {
            return parseInt(element.attr('data-userid'));
        };

        /**
         * Returns the conversation id, 0 if none.
         *
         * @param {object} element jQuery object for the button
         * @return {int}
         */
        var getConversationId = function(element) {
            return parseInt(element.attr('data-conversationid'));
        };

        /**
         * Handles opening the messaging drawer to send a
         * message to a given user.
         *
         * @method enhance
         * @param {object} element jQuery object for the button
         */
        var send = function(element) {
            element = $(element);

            var args = {
                conversationid: getConversationId(element),
                buttonid: $(element).attr('id'),
                userid: getUserId(element)
            };

            Templates.render(TEMPLATES.CONTENT, {})
                .then(function(html) {
                    element.after(html);
                })
                .then(function() {
                    $(SELECTORS.MESSAGE_USER_BUTTON).next().focus(function() {
                        $(SELECTORS.MESSAGE_TEXTAREA).focus();
                    });
                });

            CustomEvents.define(element, [CustomEvents.events.activate]);

            element.on(CustomEvents.events.activate, function(e, data) {
                if ($(e.target).hasClass('active')) {
                    MessageDrawerHelper.hide();
                    $(SELECTORS.MESSAGE_USER_BUTTON).next().attr('tabindex', -1);
                } else {
                    $(SELECTORS.MESSAGE_USER_BUTTON).next().attr('tabindex', 0);
                    if (args.conversationid) {
                        MessageDrawerHelper.showConversation(args);
                    } else {
                        MessageDrawerHelper.createConversationWithUser(args);
                    }
                }
                $(e.target).focus();
                $(e.target).toggleClass('active');
                e.preventDefault();
                data.originalEvent.preventDefault();
            });
        };

        return /** @alias module:core_message/message_user_button */ {
            send: send
        };
    });
