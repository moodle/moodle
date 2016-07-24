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
 * This module handles the contacts area of the messaging area.
 *
 * @module     core_message/message_area_contacts
 * @package    core_message
 * @copyright  2016 Mark Nelson <markn@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
define(['jquery', 'core/ajax', 'core/templates', 'core/notification'],
    function($, ajax, templates, notification) {

        /**
         * Contacts class.
         *
         * @param {Messagearea} messageArea The messaging area object.
         */
        function Contacts(messageArea) {
            this.messageArea = messageArea;
            this._init();
        }

        /** @type {Messagearea} The messaging area object. */
        Contacts.prototype.messageArea = null;

        /**
         * Initialise the event listeners.
         *
         * @private
         */
        Contacts.prototype._init = function() {
            this.messageArea.onCustomEvent(this.messageArea.EVENTS.CONVERSATIONSSELECTED, this._viewConversations.bind(this));
            this.messageArea.onCustomEvent(this.messageArea.EVENTS.CONTACTSSELECTED, this._viewContacts.bind(this));
            this.messageArea.onCustomEvent(this.messageArea.EVENTS.MESSAGESDELETED, this._deleteConversations.bind(this));
            this.messageArea.onCustomEvent(this.messageArea.EVENTS.SENDMESSAGE, this._viewConversationsWithUserSelected.bind(this));
            this.messageArea.onCustomEvent(this.messageArea.EVENTS.MESSAGESENT, this._viewConversationsWithUserSelected.bind(this));
            this.messageArea.onCustomEvent(this.messageArea.EVENTS.CONTACTREMOVED, this._removeContact.bind(this));
            this.messageArea.onCustomEvent(this.messageArea.EVENTS.CONTACTADDED, this._viewContacts.bind(this));
            this.messageArea.onCustomEvent(this.messageArea.EVENTS.CHOOSEMESSAGESTODELETE,
                this._chooseConversationsToDelete.bind(this));
            this.messageArea.onCustomEvent(this.messageArea.EVENTS.CANCELDELETEMESSAGES,
                this._cancelConversationsToDelete.bind(this));
            this.messageArea.onDelegateEvent('click', this.messageArea.SELECTORS.VIEWCONVERSATION,
                this._viewConversation.bind(this));
            this.messageArea.onDelegateEvent('click', this.messageArea.SELECTORS.VIEWPROFILE, this._viewContact.bind(this));
        };

        /**
         * Handles viewing the list of conversations.
         *
         * @returns {Promise} The promise resolved when the contact area has been rendered,
         * @private
         */
        Contacts.prototype._viewConversations = function() {
            if (this._isCurrentlyDeleting()) {
                return;
            }

            return this._loadContactArea('core_message_data_for_messagearea_conversations');
        };

        /**
         * Handles viewing the list of conversations and selecting a user.
         *
         * @param {Event} event The message sent event
         * @param {int} userid The id of the user who the message was sent to
         * @returns {Promise} The promise resolved when the message sent after actions are done
         * @private
         */
        Contacts.prototype._viewConversationsWithUserSelected = function(event, userid) {
            if (this._isCurrentlyDeleting()) {
                return;
            }

            return this._viewConversations().then(function() {
                this._setSelectedUser(userid);
            }.bind(this));
        };

        /**
         * Handles viewing the list of contacts.
         *
         * @returns {Promise} The promise resolved when the contact area has been rendered
         * @private
         */
        Contacts.prototype._viewContacts = function() {
            if (this._isCurrentlyDeleting()) {
                return;
            }

            return this._loadContactArea('core_message_data_for_messagearea_contacts');
        };

        /**
         * Handles viewing a particular conversation.
         *
         * @param {Event} event
         * @private
         */
        Contacts.prototype._viewConversation = function(event) {
            if (this._isCurrentlyDeleting()) {
                return;
            }

            var userid = $(event.currentTarget).data('userid');
            this._setSelectedUser(userid);
            this.messageArea.trigger(this.messageArea.EVENTS.CONVERSATIONSELECTED, userid);
        };

        /**
         * Handles viewing a particular contact.
         *
         * @param {Event} event
         * @private
         */
        Contacts.prototype._viewContact = function(event) {
            if (!this._isCurrentlyDeleting()) {
                var userid = $(event.currentTarget).data('userid');
                this._setSelectedUser(userid);
                this.messageArea.trigger(this.messageArea.EVENTS.CONTACTSELECTED, userid);
            }
        };

        /**
         * Handles loading the contact area.
         *
         * @param {String} webservice The web service to call
         * @returns {Promise} The promise resolved when the contact area has been rendered
         * @private
         */
        Contacts.prototype._loadContactArea = function(webservice) {
            // Show loading template.
            templates.render('core/loading', {}).done(function(html, js) {
                templates.replaceNodeContents("[data-region='contacts']", html, js);
            });

            // Call the web service to return the data we want to view.
            var promises = ajax.call([{
                methodname: webservice,
                args: {
                    userid: this.messageArea.getCurrentUserId()
                }
            }]);

            // After the request render the contacts area.
            return promises[0].then(function(data) {
                // We have the data - lets re-render the template with it.
                return templates.render('core_message/message_area_contacts', data);
            }).then(function(html, js) {
                templates.replaceNodeContents("[data-region='contacts-area']", html, js);
            }).fail(notification.exception);
        };

        /**
         * Handles selecting conversations to delete.
         *
         * @private
         */
        Contacts.prototype._chooseConversationsToDelete = function() {
            // Only show the checkboxes for the contact if we are also deleting messages.
            if (this.messageArea.find(this.messageArea.SELECTORS.DELETEMESSAGECHECKBOX).length !== 0) {
                this.messageArea.find(this.messageArea.SELECTORS.DELETECONVERSATIONCHECKBOX).show();
            }
        };

        /**
         * Handles canceling conversations to delete.
         *
         * @private
         */
        Contacts.prototype._cancelConversationsToDelete = function() {
            // Uncheck all checkboxes.
            this.messageArea.find(this.messageArea.SELECTORS.DELETECONVERSATIONCHECKBOX + " input:checked").removeAttr('checked');
            // Hide the checkboxes.
            this.messageArea.find(this.messageArea.SELECTORS.DELETECONVERSATIONCHECKBOX).hide();
        };

        /**
         * Handles deleting conversations.
         *
         * @params {Event} event
         * @params {int} The user id belonging to the messages we are deleting.
         * @private
         */
        Contacts.prototype._deleteConversations = function(event, userid) {
            var checkboxes = this.messageArea.find(this.messageArea.SELECTORS.DELETECONVERSATIONCHECKBOX + " input:checked");
            var requests = [];

            // Go through all the checked checkboxes and prepare them for deletion.
            checkboxes.each(function(id, element) {
                var node = $(element);
                var otheruserid = node.parents(this.messageArea.SELECTORS.CONTACT).data('userid');
                requests.push({
                    methodname: 'core_message_delete_conversation',
                    args: {
                        userid: this.messageArea.getCurrentUserId(),
                        otheruserid: otheruserid
                    }
                });
            }.bind(this));

            if (requests.length > 0) {
                ajax.call(requests)[requests.length - 1].then(function() {
                    for (var i = 0; i <= requests.length - 1; i++) {
                        // Trigger conversation deleted events.
                        this.messageArea.trigger(this.messageArea.EVENTS.CONVERSATIONDELETED, requests[i].args.otheruserid);
                    }
                }.bind(this), notification.exception);
            }

            // Hide all the checkboxes.
            this._cancelConversationsToDelete();

            // Reload conversation panel. We do this regardless if a conversation was deleted or not
            // as a message may have been removed which means a conversation in the list may have to
            // be moved.
            this._viewConversationsWithUserSelected(event, userid);
        };

        /**
         * Handles removing a contact from the list.
         *
         * @param {Event} event
         * @param {int} userid
         * @private
         */
        Contacts.prototype._removeContact = function(event, userid) {
            this.messageArea.find(this.messageArea.SELECTORS.CONTACT + "[data-userid='" + userid + "']").remove();
        };

        /**
         * Handles selecting a contact in the list.
         *
         * @param {int} userid
         * @private
         */
        Contacts.prototype._setSelectedUser = function(userid) {
            // Remove the 'selected' class from any other contact.
            this.messageArea.find(this.messageArea.SELECTORS.CONTACT).removeClass('selected');
            // Set the tab for the user to selected.
            this.messageArea.find(this.messageArea.SELECTORS.CONTACT + "[data-userid='" + userid + "']").addClass('selected');
        };

        /**
         * Checks if we are currently choosing conversations to delete.
         *
         * @return {Boolean}
         */
        Contacts.prototype._isCurrentlyDeleting = function() {
            if (this.messageArea.find(this.messageArea.SELECTORS.DELETECONVERSATIONCHECKBOX + ":visible").length !== 0) {
                return true;
            }

            return false;
        };

        return Contacts;
    }
);