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
define(['jquery', 'core/ajax', 'core/templates', 'core/notification', 'core/custom_interaction_events'],
    function($, ajax, templates, notification, customEvents) {

        /**
         * Contacts class.
         *
         * @param {Messagearea} messageArea The messaging area object.
         */
        function Contacts(messageArea) {
            this.messageArea = messageArea;
            this._init();
        }

        /** @type {Boolean} checks if we are currently deleting */
        Contacts.prototype._isDeleting = false;

        /** @type {Boolean} checks if we are currently loading conversations */
        Contacts.prototype._isLoadingConversations = false;

        /** @type {Boolean} checks if we are currently loading contacts */
        Contacts.prototype._isLoadingContacts = false;

        /** @type {int} the number of contacts displayed */
        Contacts.prototype._numContactsDisplayed = 0;

        /** @type {int} the number of contacts to retrieve */
        Contacts.prototype._numContactsToRetrieve = 20;

        /** @type {int} the number of conversations displayed */
        Contacts.prototype._numConversationsDisplayed = 0;

        /** @type {int} the number of conversations to retrieve */
        Contacts.prototype._numConversationsToRetrieve = 20;

        /** @type {int} the number of chars of the message to show */
        Contacts.prototype._messageLength = 60;

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
            this.messageArea.onCustomEvent(this.messageArea.EVENTS.MESSAGESENT, this._handleMessageSent.bind(this));
            this.messageArea.onCustomEvent(this.messageArea.EVENTS.CONTACTREMOVED, function(e, userid) {
                this._removeContact(this.messageArea.SELECTORS.CONTACTS, userid);
            }.bind(this));
            this.messageArea.onCustomEvent(this.messageArea.EVENTS.CONTACTADDED, function(e, userid) {
                this._addContact(userid);
            }.bind(this));
            this.messageArea.onCustomEvent(this.messageArea.EVENTS.CHOOSEMESSAGESTODELETE,
                this._chooseConversationsToDelete.bind(this));
            this.messageArea.onCustomEvent(this.messageArea.EVENTS.CANCELDELETEMESSAGES,
                this._cancelConversationsToDelete.bind(this));
            this.messageArea.onDelegateEvent('click', this.messageArea.SELECTORS.VIEWCONVERSATION,
                this._viewConversation.bind(this));
            this.messageArea.onDelegateEvent('click', this.messageArea.SELECTORS.VIEWPROFILE, this._viewContact.bind(this));

            // Now enable the ability to infinitely scroll through conversations and contacts.
            customEvents.define(this.messageArea.SELECTORS.CONVERSATIONS, [
                customEvents.events.scrollBottom
            ]);
            customEvents.define(this.messageArea.SELECTORS.CONTACTS, [
                customEvents.events.scrollBottom
            ]);
            this.messageArea.onDelegateEvent(customEvents.events.scrollBottom, this.messageArea.SELECTORS.CONVERSATIONS,
                this._loadConversations.bind(this));
            this.messageArea.onDelegateEvent(customEvents.events.scrollBottom, this.messageArea.SELECTORS.CONTACTS,
                this._loadContacts.bind(this));

            // Set the number of conversations that have been loaded on page load.
            this._numConversationsDisplayed = this.messageArea.find(this.messageArea.SELECTORS.CONVERSATIONS + " " +
                this.messageArea.SELECTORS.CONTACT).length;
        };

        /**
         * Handles viewing the list of conversations.
         *
         * @private
         */
        Contacts.prototype._viewConversations = function() {
            this.messageArea.find(this.messageArea.SELECTORS.CONTACTS).hide();
            this.messageArea.find(this.messageArea.SELECTORS.CONVERSATIONS).show();
        };

        /**
         * Handles viewing the list of contacts.
         *
         * @private
         */
        Contacts.prototype._viewContacts = function() {
            // If contacts is empty then load some.
            if (this._numContactsDisplayed === 0) {
                this._loadContacts();
            }

            this.messageArea.find(this.messageArea.SELECTORS.CONVERSATIONS).hide();
            this.messageArea.find(this.messageArea.SELECTORS.CONTACTS).show();
        };

        /**
         * Handles when a message is sent.
         *
         * @param {Event} event The message sent event
         * @param {int} userid The id of the user who the message was sent to
         * @param {String} text The message text
         * @private
         */
        Contacts.prototype._handleMessageSent = function(event, userid, text) {
            // Switch to viewing the conversations.
            this._viewConversations();
            // Get the text we will display on the contact panel.
            text = this._getContactText(text);
            // Get the user node.
            var user = this.messageArea.find(this.messageArea.SELECTORS.CONVERSATIONS + " " +
                this.messageArea.SELECTORS.CONTACT + "[data-userid='" + userid + "']");
            // If the user has not been loaded yet, let's copy the element from contact panel to the conversation panel.
            if (user.length === 0) {
                // Let's clone the data on the contact page.
                var usercontact = this.messageArea.find(this.messageArea.SELECTORS.CONTACTS + " " +
                    this.messageArea.SELECTORS.CONTACT + "[data-userid='" + userid + "']");
                user = usercontact.clone();
                // Change the data action attribute.
                user.attr('data-action', 'view-contact-msg');
                // Increment the number of conversations displayed.
                this._numConversationsDisplayed++;
            }
            // Move the contact to the top of the list.
            user.prependTo(this.messageArea.find(this.messageArea.SELECTORS.CONVERSATIONS));
            // Scroll to the top.
            this.messageArea.find(this.messageArea.SELECTORS.CONVERSATIONS).scrollTop(0);
            // Replace the text.
            user.find(this.messageArea.SELECTORS.LASTMESSAGE).empty().append(text);
            // Ensure user is selected.
            this._setSelectedUser(userid);
        };

        /**
         * Handles loading conversations.
         *
         * @returns {Promise} The promise resolved when the contact area has been rendered,
         * @private
         */
        Contacts.prototype._loadConversations = function() {
            if (this._isDeleting) {
                return;
            }

            if (this._isLoadingConversations) {
                return;
            }

            // Tell the user we are loading items.
            this._isLoadingConversations = true;

            // Keep track of the number of contacts
            var numberreceived = 0;
            // Add loading icon to the end of the list.
            return templates.render('core/loading', {}).then(function(html, js) {
                templates.appendNodeContents(this.messageArea.SELECTORS.CONVERSATIONS,
                    "<div style='text-align:center'>" + html + "</div>", js);
                return this._getItems('core_message_data_for_messagearea_conversations',
                    this._numConversationsDisplayed, this._numConversationsToRetrieve);
            }.bind(this)).then(function(data) {
                numberreceived = data.contacts.length;
                return templates.render('core_message/message_area_contacts', data);
            }).then(function(html, js) {
                // Remove the loading icon.
                this.messageArea.find(this.messageArea.SELECTORS.CONVERSATIONS + " " +
                    this.messageArea.SELECTORS.LOADINGICON).remove();
                // Only append data if we got data back.
                if (numberreceived > 0) {
                    // Show the new content.
                    templates.appendNodeContents(this.messageArea.SELECTORS.CONVERSATIONS, html, js);
                    // Increment the number of conversations displayed.
                    this._numConversationsDisplayed += numberreceived;
                }
                // Mark that we are no longer busy loading data.
                this._isLoadingConversations = false;
            }.bind(this)).fail(notification.exception);
        };

        /**
         * Handles loading contacts.
         *
         * @returns {Promise} The promise resolved when the contact area has been rendered
         * @private
         */
        Contacts.prototype._loadContacts = function() {
            if (this._isDeleting) {
                return;
            }

            if (this._isLoadingContacts) {
                return;
            }

            // Tell the user we are loading items.
            this._isLoadingContacts = true;

            // Keep track of the number of contacts
            var numberreceived = 0;
            // Add loading icon to the end of the list.
            return templates.render('core/loading', {}).then(function(html, js) {
                templates.appendNodeContents(this.messageArea.SELECTORS.CONTACTS,
                    "<div style='text-align:center'>" + html + "</div>", js);
                return this._getItems('core_message_data_for_messagearea_contacts',
                    this._numContactsDisplayed, this._numContactsToRetrieve);
            }.bind(this)).then(function(data) {
                numberreceived = data.contacts.length;
                return templates.render('core_message/message_area_contacts', data);
            }).then(function(html, js) {
                // Remove the loading icon.
                this.messageArea.find(this.messageArea.SELECTORS.CONTACTS + " " +
                    this.messageArea.SELECTORS.LOADINGICON).remove();
                // Only append data if we got data back.
                if (numberreceived > 0) {
                    // Show the new content.
                    templates.appendNodeContents(this.messageArea.SELECTORS.CONTACTS, html, js);
                    // Increment the number of contacts displayed.
                    this._numContactsDisplayed += numberreceived;
                }
                // Mark that we are no longer busy loading data.
                this._isLoadingContacts = false;
            }.bind(this)).fail(notification.exception);
        };

        /**
         * Handles viewing a particular conversation.
         *
         * @param {Event} event
         * @private
         */
        Contacts.prototype._viewConversation = function(event) {
            if (!this._isDeleting) {
                var userid = $(event.currentTarget).data('userid');
                this._setSelectedUser(userid);
                this.messageArea.trigger(this.messageArea.EVENTS.CONVERSATIONSELECTED, userid);
            }
        };

        /**
         * Handles viewing a particular contact.
         *
         * @param {Event} event
         * @private
         */
        Contacts.prototype._viewContact = function(event) {
            if (!this._isDeleting) {
                var userid = $(event.currentTarget).data('userid');
                this._setSelectedUser(userid);
                this.messageArea.trigger(this.messageArea.EVENTS.CONTACTSELECTED, userid);
            }
        };

        /**
         * Handles returning a list of items to display.
         *
         * @param {String} webservice The web service to call
         * @param {int} limitfrom
         * @param {int} limitnum
         * @returns {Promise} The promise resolved when the contact area has been rendered
         * @private
         */
        Contacts.prototype._getItems = function(webservice, limitfrom, limitnum) {
            // Call the web service to return the data we want to view.
            var promises = ajax.call([{
                methodname: webservice,
                args: {
                    userid: this.messageArea.getCurrentUserId(),
                    limitfrom: limitfrom,
                    limitnum: limitnum
                }
            }]);

            // After the request render the contacts area.
            return promises[0];
        };

        /**
         * Handles selecting conversations to delete.
         *
         * @private
         */
        Contacts.prototype._chooseConversationsToDelete = function() {
            this._isDeleting = true;
            this.messageArea.find(this.messageArea.SELECTORS.DELETECONVERSATIONCHECKBOX).show();
        };

        /**
         * Handles canceling conversations to delete.
         *
         * @private
         */
        Contacts.prototype._cancelConversationsToDelete = function() {
            this._isDeleting = false;
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
                        // Remove the conversation.
                        this._removeContact(this.messageArea.SELECTORS.CONVERSATIONS, requests[i].args.otheruserid);
                        // Trigger conversation deleted events.
                        this.messageArea.trigger(this.messageArea.EVENTS.CONVERSATIONDELETED, requests[i].args.otheruserid);
                    }
                }.bind(this), notification.exception);
            }

            // Check if the last message needs updating.
            var user = this.messageArea.find(this.messageArea.SELECTORS.CONVERSATIONS + " " +
                this.messageArea.SELECTORS.CONTACT + "[data-userid='" + userid + "']");
            if (user.length !== 0) {
                var lastmessagelisted = user.find(this.messageArea.SELECTORS.LASTMESSAGE);
                lastmessagelisted = lastmessagelisted.html();
                // Go through and get the actual last message after all the deletions.
                var messages = this.messageArea.find(this.messageArea.SELECTORS.MESSAGESAREA + " " +
                    this.messageArea.SELECTORS.MESSAGE);
                var messageslength = messages.length;

                messages.each(function(index, element) {
                    if (index === messageslength - 1) {
                        var actuallastmessage = $(element).find(this.messageArea.SELECTORS.MESSAGETEXT).html().trim();
                        if (lastmessagelisted != actuallastmessage) {
                            user.find(this.messageArea.SELECTORS.LASTMESSAGE).empty().append(
                                this._getContactText(actuallastmessage));
                        }
                    }
                }.bind(this));
            }

            // Now we have done all the deletion we can set the flag back to false.
            this._isDeleting = false;

            // Hide all the checkboxes.
            this._cancelConversationsToDelete();
        };

        /**
         * Handles adding a contact to the list.
         *
         * @param {int} userid
         * @private
         */
        Contacts.prototype._addContact = function(userid) {
            var user = this.messageArea.find(this.messageArea.SELECTORS.CONTACTS + " " + this.messageArea.SELECTORS.CONTACT +
                "[data-userid='" + userid + "']").hide();
            if (user.length !== 0) {
                user.show();
            }
        };

        /**
         * Handles removing a contact from the list.
         *
         * @param {String} selector
         * @param {int} userid
         * @private
         */
        Contacts.prototype._removeContact = function(selector, userid) {
            this.messageArea.find(selector + " " + this.messageArea.SELECTORS.CONTACT +
                "[data-userid='" + userid + "']").hide();
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
         * Converts a text message into the text that should be stored in the contact list
         *
         * @param {String} text
         */
        Contacts.prototype._getContactText = function(text) {
            if (text.length > this._messageLength) {
                text = text.substr(0, this._messageLength - 3);
                text += '...';
            }

            return text;
        };

        return Contacts;
    }
);