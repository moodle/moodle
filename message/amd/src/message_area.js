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
 * This module instantiates the functionality of the messaging area.
 *
 * @module     core_message/message_area
 * @package    core_message
 * @copyright  2016 Mark Nelson <markn@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
define(['jquery', 'core_message/message_area_contacts', 'core_message/message_area_messages',
        'core_message/message_area_profile', 'core_message/message_area_menubar', 'core_message/message_area_tabs',
        'core_message/message_area_actions'],
    function($, Contacts, Messages, Profile, Menubar, Tabs, Actions) {

        /**
         * Messagearea class.
         *
         * @param {String} selector The selector for the page region containing the message area.
         */
        function Messagearea(selector) {
            this.node = $(selector);
            this._init();
        }

        /** @type {Object} The list of selectors for the message area. */
        Messagearea.prototype.SELECTORS = {
            ACTIVECONTACTSTAB: "[data-region='contacts-area'] [role='tab'][aria-selected='true']",
            BLOCKTIME: "[data-region='blocktime']",
            CANCELDELETEMESSAGES: "[data-action='cancel-delete-messages']",
            CONTACT: "[data-region='contact']",
            CONTACTS: "[data-region='contacts'][data-region-content='contacts']",
            CONTACTSPANELS: "[data-region='contacts']",
            CONVERSATIONS: "[data-region='contacts'][data-region-content='conversations']",
            DELETECONVERSATIONCHECKBOX: "[data-region='delete-conversation-checkbox']",
            DELETEMESSAGES: "[data-action='delete-messages']",
            DELETEMESSAGECHECKBOX: "[data-region='delete-message-checkbox']",
            LASTMESSAGE: '.lastmessage',
            LOADINGICON: '.loading-icon',
            MENU: "[data-region='menu']",
            MESSAGE: "[data-region='message']",
            MESSAGES: "[data-region='messages']",
            MESSAGESAREA: "[data-region='messages-area']",
            MESSAGERESPONSE: "[data-region='response']",
            MESSAGETEXT: "[data-region='message-text']",
            PROFILE: "[data-region='profile']",
            PROFILEADDCONTACT: "[data-action='profile-add-contact']",
            PROFILEBLOCKCONTACT: "[data-action='profile-block-contact']",
            PROFILEREMOVECONTACT: "[data-action='profile-remove-contact']",
            PROFILESENDMESSAGE: "[data-action='profile-send-message']",
            PROFILEUNBLOCKCONTACT: "[data-action='profile-unblock-contact']",
            PROFILEVIEW: "[data-action='profile-view']",
            SENDMESSAGE: "[data-action='send-message']",
            SENDMESSAGETEXT: "[data-region='send-message-txt']",
            VIEWCONTACTS: "[data-action='contacts-view']",
            VIEWCONVERSATION: "[data-action='view-contact-msg']",
            VIEWCONVERSATIONS: "[data-action='conversations-view']",
            VIEWPROFILE: "[data-action='view-contact-profile']"
        };

        /** @type {Object} The list of events triggered in the message area. */
        Messagearea.prototype.EVENTS = {
            CANCELDELETEMESSAGES: 'cancel-delete-messages',
            CHOOSEMESSAGESTODELETE: 'choose-messages-to-delete',
            CONTACTADDED: 'contact-added',
            CONTACTBLOCKED: 'contact-blocked',
            CONTACTREMOVED: 'contact-removed',
            CONTACTSELECTED: 'contact-selected',
            CONTACTSSELECTED: 'contacts-selected',
            CONTACTUNBLOCKED: 'contact-unblocked',
            CONVERSATIONDELETED: 'conversation-deleted',
            CONVERSATIONSELECTED: 'conversation-selected',
            CONVERSATIONSSELECTED: 'conversations-selected',
            MESSAGESDELETED: 'messages-deleted',
            MESSAGESENT: 'message-sent',
            SENDMESSAGE: 'message-send'
        };

        /** @type {jQuery} The jQuery node for the page region containing the message area. */
        Messagearea.prototype.node = null;

        /**
         * Initialise the other objects we require.
         */
        Messagearea.prototype._init = function() {
            new Contacts(this);
            new Messages(this);
            new Profile(this);
            new Tabs(this);
            var actions = new Actions(this);

            Menubar.enhance(this.find(this.SELECTORS.MENU), {
                "[data-action='delete-messages']": actions.chooseMessagesToDelete.bind(actions)
            });
        };

        /**
         * Handles adding a delegate event to the messaging area node.
         *
         * @param {String} action The action we are listening for
         * @param {String} selector The selector for the page we are assigning the action to
         * @param {Function} callable The function to call when the event happens
         */
        Messagearea.prototype.onDelegateEvent = function(action, selector, callable) {
            this.node.on(action, selector, callable);
        };

        /**
         * Handles adding a custom event to the messaging area node.
         *
         * @param {String} action The action we are listening for
         * @param {Function} callable The function to call when the event happens
         */
        Messagearea.prototype.onCustomEvent = function(action, callable) {
            this.node.on(action, callable);
        };

        /**
         * Handles triggering an event on the messaging area node.
         *
         * @param {String} event The selector for the page region containing the message area
         * @param {Object=} data The data to pass when we trigger the event
         */
        Messagearea.prototype.trigger = function(event, data) {
            if (typeof data == 'undefined') {
                data = '';
            }
            this.node.trigger(event, data);
        };

        /**
         * Handles finding a node in the messaging area.
         *
         * @param {String} selector The selector for the node we are looking for
         * @returns {jQuery} The node
         */
        Messagearea.prototype.find = function(selector) {
            return this.node.find(selector);
        };

        /**
         * Returns the ID of the logged in user.
         *
         * @returns {int} The user id
         */
        Messagearea.prototype.getLoggedInUserId = function() {
            return this.node.data('loggedinuserid');
        };

        /**
         * Returns the ID of the user whose message area we are viewing.
         *
         * @returns {int} The user id
         */
        Messagearea.prototype.getCurrentUserId = function() {
            return this.node.data('userid');
        };

        return Messagearea;
    }
);
