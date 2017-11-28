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
define(['jquery', 'core/ajax', 'core/templates', 'core/notification', 'core/custom_interaction_events', 'core/str',
        'core_message/message_area_events'],
    function($, Ajax, Templates, Notification, CustomEvents, Str, Events) {

        /** @type {Object} The list of selectors for the message area. */
        var SELECTORS = {
            CONTACT: "[data-region='contact']",
            CONTACTICONBLOCKED: "[data-region='contact-icon-blocked']",
            CONTACTS: "[data-region='contacts'][data-region-content='contacts']",
            CONTACTSAREA: "[data-region='contacts-area']",
            CONVERSATIONS: "[data-region='contacts'][data-region-content='conversations']",
            COURSE: "[data-region='course']",
            LASTMESSAGETEXT: "[data-region='last-message-text']",
            LASTMESSAGEUSER: "[data-region='last-message-user']",
            LOADINGICON: '.loading-icon',
            MESSAGETEXT: "[data-region='message-text']",
            MESSAGINGAREA: "[data-region='messaging-area']",
            NOCONTACTS: "[data-region=no-contacts]",
            SEARCHBOX: "[data-region='search-box']",
            SEARCHRESULTSAREA: "[data-region='search-results-area']",
            SEARCHTEXTAREA: "[data-region='search-text-area']",
            SELECTEDVIEWCONVERSATION: "[data-action='view-contact-msg'].selected",
            SELECTEDVIEWPROFILE: "[data-action='view-contact-profile'].selected",
            SHOWMESSAGES: "[data-action='show-messages']",
            VIEWCONVERSATION: "[data-action='view-contact-msg']",
            VIEWPROFILE: "[data-action='view-contact-profile']"
        };

        /**
         * Contacts class.
         *
         * @param {Messagearea} messageArea The messaging area object.
         */
        function Contacts(messageArea) {
            this.messageArea = messageArea;
            this._init();
        }

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
            CustomEvents.define(this.messageArea.node, [
                CustomEvents.events.activate,
                CustomEvents.events.down,
                CustomEvents.events.up,
            ]);

            this.messageArea.onCustomEvent(Events.MESSAGESEARCHCANCELED, this._viewConversations.bind(this));
            this.messageArea.onCustomEvent(Events.USERSSEARCHCANCELED, this._viewContacts.bind(this));
            this.messageArea.onCustomEvent(Events.CONTACTSSELECTED, this._viewContacts.bind(this));
            this.messageArea.onCustomEvent(Events.CONVERSATIONDELETED, this._deleteConversation.bind(this));
            this.messageArea.onCustomEvent(Events.CONVERSATIONSSELECTED, this._viewConversations.bind(this));
            this.messageArea.onCustomEvent(Events.CONTACTSSELECTED, this._viewContacts.bind(this));
            this.messageArea.onCustomEvent(Events.MESSAGESDELETED, this._updateLastMessage.bind(this));
            this.messageArea.onCustomEvent(Events.MESSAGESENT, this._handleMessageSent.bind(this));
            this.messageArea.onCustomEvent(Events.CONTACTREMOVED, function(e, userid) {
                this._removeContact(SELECTORS.CONTACTS, userid);
            }.bind(this));
            this.messageArea.onCustomEvent(Events.CONTACTADDED, function(e, userid) {
                this._addContact(userid);
            }.bind(this));
            this.messageArea.onCustomEvent(Events.CONTACTBLOCKED, function(e, userid) {
                this._blockContact(userid);
            }.bind(this));
            this.messageArea.onCustomEvent(Events.CONTACTUNBLOCKED, function(e, userid) {
                this._unblockContact(userid);
            }.bind(this));
            this.messageArea.onCustomEvent(Events.CHOOSEMESSAGESTODELETE,
                this._startDeleting.bind(this));
            this.messageArea.onCustomEvent(Events.CANCELDELETEMESSAGES,
                this._stopDeleting.bind(this));
            this.messageArea.onDelegateEvent(CustomEvents.events.activate, SELECTORS.VIEWCONVERSATION,
                this._viewConversation.bind(this));
            this.messageArea.onDelegateEvent(CustomEvents.events.activate, SELECTORS.VIEWPROFILE,
                this._viewContact.bind(this));
            this.messageArea.onDelegateEvent(CustomEvents.events.activate, SELECTORS.SHOWMESSAGES,
                this._showMessagingArea.bind(this));

            this.messageArea.onDelegateEvent(CustomEvents.events.up, SELECTORS.CONTACT,
                this._selectPreviousContact.bind(this));
            this.messageArea.onDelegateEvent(CustomEvents.events.down, SELECTORS.CONTACT,
                this._selectNextContact.bind(this));
            this.messageArea.onDelegateEvent(CustomEvents.events.up, SELECTORS.VIEWCONVERSATION,
                this._selectPreviousConversation.bind(this));
            this.messageArea.onDelegateEvent(CustomEvents.events.down, SELECTORS.VIEWCONVERSATION,
                this._selectNextConversation.bind(this));

            this.messageArea.onDelegateEvent(CustomEvents.events.up, SELECTORS.COURSE, this._selectPreviousCourse.bind());
            this.messageArea.onDelegateEvent(CustomEvents.events.down, SELECTORS.COURSE, this._selectNextCourse.bind());

            this.messageArea.onDelegateEvent('focus', SELECTORS.SEARCHBOX, this._setSearching.bind(this));
            this.messageArea.onDelegateEvent('blur', SELECTORS.SEARCHBOX, this._clearSearching.bind(this));

            // Now enable the ability to infinitely scroll through conversations and contacts.
            CustomEvents.define(this.messageArea.find(SELECTORS.CONVERSATIONS), [
                CustomEvents.events.scrollBottom
            ]);
            CustomEvents.define(this.messageArea.find(SELECTORS.CONTACTS), [
                CustomEvents.events.scrollBottom
            ]);
            this.messageArea.onDelegateEvent(CustomEvents.events.scrollBottom, SELECTORS.CONVERSATIONS,
                this._loadConversations.bind(this));
            this.messageArea.onDelegateEvent(CustomEvents.events.scrollBottom, SELECTORS.CONTACTS,
                this._loadContacts.bind(this));

            if (!this.messageArea.showContactsFirst()) {
                // Set the initial number of conversations to retrieve. Otherwise it will display no conversations.
                this._numConversationsDisplayed = 20;
            }
        };

        /**
         * Turn on deleting.
         *
         * @private
         */
        Contacts.prototype._startDeleting = function() {
            this.messageArea.find(SELECTORS.CONTACTSAREA).addClass('editing');
        };

        /**
         * Turn off deleting.
         *
         * @private
         */
        Contacts.prototype._stopDeleting = function() {
            this.messageArea.find(SELECTORS.CONTACTSAREA).removeClass('editing');
        };

        /**
         * Handles viewing the list of conversations.
         *
         * @private
         */
        Contacts.prototype._viewConversations = function() {
            // If conversations is empty then try load some.
            if (this._numConversationsDisplayed === 0) {
                this._loadConversations();
            }

            this.messageArea.find(SELECTORS.CONTACTS).hide();
            this.messageArea.find(SELECTORS.CONVERSATIONS).show();
        };

        /**
         * Handles viewing the list of contacts.
         *
         * @private
         */
        Contacts.prototype._viewContacts = function() {
            // If contacts is empty then try load some.
            if (this._numContactsDisplayed === 0) {
                this._loadContacts();
            }

            this.messageArea.find(SELECTORS.CONVERSATIONS).hide();
            this.messageArea.find(SELECTORS.CONTACTS).show();
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
            // Get the user node.
            var user = this._getUserNode(SELECTORS.CONVERSATIONS, userid);
            // If the user has not been loaded yet, let's copy the element from contact or search panel to the conversation panel.
            if (user.length === 0) {
                // Let's clone the data on the contact page.
                var usercontact = this._getUserNode(SELECTORS.CONTACTS, userid);
                if (usercontact.length === 0) {
                    // No luck, maybe we sent the message to a user we searched for - check search page.
                    usercontact = this._getUserNode(SELECTORS.SEARCHRESULTSAREA, userid);
                }
                if (usercontact.length == 0) {
                    // Can't do much.
                    return;
                }
                user = usercontact.clone();
                // Change the data action attribute.
                user.attr('data-action', 'view-contact-msg');
                // Remove the 'no conversations' message.
                this.messageArea.find(SELECTORS.CONVERSATIONS + " " +
                    SELECTORS.NOCONTACTS).remove();
                // Increment the number of conversations displayed.
                this._numConversationsDisplayed++;
            }
            // Move the contact to the top of the list.
            user.prependTo(this.messageArea.find(SELECTORS.CONVERSATIONS));
            // Scroll to the top.
            this.messageArea.find(SELECTORS.CONVERSATIONS).scrollTop(0);
            // Get the new text to show.
            this._updateContactText(user, text, true);
            // Ensure user is selected.
            this._setSelectedUser("[data-userid='" + userid + "']");
        };

        /**
         * Handles loading conversations.
         *
         * @return {Promise|boolean} The promise resolved when the contact area has been rendered,
         * @private
         */
        Contacts.prototype._loadConversations = function() {
            if (this._isLoadingConversations) {
                return false;
            }

            // Tell the user we are loading items.
            this._isLoadingConversations = true;

            // Keep track of the number of contacts
            var numberreceived = 0;
            // Add loading icon to the end of the list.
            return Templates.render('core/loading', {}).then(function(html, js) {
                if (this._numConversationsDisplayed) {
                    Templates.appendNodeContents(this.messageArea.find(SELECTORS.CONVERSATIONS),
                        "<div style='text-align:center'>" + html + "</div>", js);
                } else { // No conversations, just replace contents.
                    Templates.replaceNodeContents(this.messageArea.find(SELECTORS.CONVERSATIONS),
                        "<div style='text-align:center'>" + html + "</div>", js);
                }
                return this._getItems('core_message_data_for_messagearea_conversations',
                    this._numConversationsDisplayed, this._numConversationsToRetrieve);
            }.bind(this)).then(function(data) {
                numberreceived = data.contacts.length;
                data.isconversation = true;
                return Templates.render('core_message/message_area_contacts', data);
            }).then(function(html, js) {
                // Remove the loading icon.
                this.messageArea.find(SELECTORS.CONVERSATIONS + " " +
                    SELECTORS.LOADINGICON).remove();
                // Only append data if we got data back.
                if (numberreceived > 0) {
                    // Show the new content.
                    Templates.appendNodeContents(this.messageArea.find(SELECTORS.CONVERSATIONS), html, js);
                    // Increment the number of conversations displayed. We increment by the number of conversations we
                    // asked to retrieve not by the number that was actually retrieved, see MDL-55870.
                    this._numConversationsDisplayed += this._numConversationsToRetrieve;
                } else if (!this._numConversationsDisplayed) {
                    // If we didn't receive any contacts and there are currently none, then we want to show a message.
                    Templates.replaceNodeContents(this.messageArea.find(SELECTORS.CONVERSATIONS), html, js);
                }
                // Mark that we are no longer busy loading data.
                this._isLoadingConversations = false;
            }.bind(this)).fail(Notification.exception);
        };

        /**
         * Handles loading contacts.
         *
         * @return {Promise|boolean} The promise resolved when the contact area has been rendered
         * @private
         */
        Contacts.prototype._loadContacts = function() {
            if (this._isLoadingContacts) {
                return false;
            }

            // Tell the user we are loading items.
            this._isLoadingContacts = true;

            // Keep track of the number of contacts
            var numberreceived = 0;
            // Add loading icon to the end of the list.
            return Templates.render('core/loading', {}).then(function(html, js) {
                if (this._numContactsDisplayed) {
                    Templates.appendNodeContents(this.messageArea.find(SELECTORS.CONTACTS),
                        "<div style='text-align:center'>" + html + "</div>", js);
                } else { // No contacts, just replace contents.
                    Templates.replaceNodeContents(this.messageArea.find(SELECTORS.CONTACTS),
                        "<div style='text-align:center'>" + html + "</div>", js);
                }
                return this._getItems('core_message_data_for_messagearea_contacts',
                    this._numContactsDisplayed, this._numContactsToRetrieve);
            }.bind(this)).then(function(data) {
                numberreceived = data.contacts.length;
                data.isconversation = false;
                return Templates.render('core_message/message_area_contacts', data);
            }).then(function(html, js) {
                // Remove the loading icon.
                this.messageArea.find(SELECTORS.CONTACTS + " " +
                    SELECTORS.LOADINGICON).remove();
                // Only append data if we got data back.
                if (numberreceived > 0) {
                    // Show the new content.
                    Templates.appendNodeContents(this.messageArea.find(SELECTORS.CONTACTS), html, js);
                    // Increment the number of contacts displayed.
                    this._numContactsDisplayed += numberreceived;
                } else if (!this._numContactsDisplayed) {
                    // If we didn't receive any contacts and there are currently none, then we want to show a message.
                    Templates.replaceNodeContents(this.messageArea.find(SELECTORS.CONTACTS), html, js);
                }
                // Mark that we are no longer busy loading data.
                this._isLoadingContacts = false;
            }.bind(this)).fail(Notification.exception);
        };

        /**
         * Handles viewing a particular conversation.
         *
         * @param {Event} event
         * @private
         */
        Contacts.prototype._viewConversation = function(event) {
            // Cancel any deletion of messages we may have.
            this.messageArea.trigger(Events.CANCELDELETEMESSAGES);

            var userid = $(event.currentTarget).data('userid');
            var messageid = $(event.currentTarget).data('messageid');
            var selector = "[data-userid='" + userid + "']";
            // If we have a specific message id then we did a search and the contact may appear in multiple
            // places - we don't want to highlight them all.
            if (messageid) {
                selector = "[data-messageid='" + messageid + "']";
            }

            this._setSelectedUser(selector);
            this.messageArea.trigger(Events.CONVERSATIONSELECTED, userid);
            // Don't highlight the contact because the message region has changed.
            this.messageArea.find(SELECTORS.SELECTEDVIEWPROFILE).removeClass('selected');
            this._showMessagingArea();
        };

        /**
         * Handles viewing a particular contact.
         *
         * @param {Event} event
         * @private
         */
        Contacts.prototype._viewContact = function(event) {
            // Cancel any deletion of messages we may have.
            this.messageArea.trigger(Events.CANCELDELETEMESSAGES);

            var userid = $(event.currentTarget).data('userid');
            this._setSelectedUser("[data-userid='" + userid + "']");
            this.messageArea.trigger(Events.CONTACTSELECTED, userid);
            // Don't highlight the conversation because the message region has changed.
            this.messageArea.find(SELECTORS.SELECTEDVIEWCONVERSATION).removeClass('selected');
            this._showMessagingArea();
        };

        /**
         * Handles returning a list of items to display.
         *
         * @param {String} webservice The web service to call
         * @param {int} limitfrom
         * @param {int} limitnum
         * @return {Promise} The promise resolved when the contact area has been rendered
         * @private
         */
        Contacts.prototype._getItems = function(webservice, limitfrom, limitnum) {
            // Call the web service to return the data we want to view.
            var promises = Ajax.call([{
                methodname: webservice,
                args: {
                    userid: this.messageArea.getCurrentUserId(),
                    limitfrom: limitfrom,
                    limitnum: limitnum
                }
            }]);

            return promises[0];
        };

        /**
         * Handles deleting a conversation.
         *
         * @param {Event} event
         * @param {int} userid The user id belonging to the messages we are deleting.
         * @private
         */
        Contacts.prototype._deleteConversation = function(event, userid) {
            // Remove the conversation.
            this._removeContact(SELECTORS.CONVERSATIONS, userid);
            this._numConversationsDisplayed--;
            this._hideMessagingArea();
            // Now we have done all the deletion we can set the flag back to false.
            this._stopDeleting();
        };

        /**
         * Handles updating the last message in the contact.
         *
         * @param {Event} event
         * @param {int} userid The user id belonging to the messages we are deleting
         * @param {jQuery|null} updatemessage The message we need to update the contact panel with
         * @private
         */
        Contacts.prototype._updateLastMessage = function(event, userid, updatemessage) {
            // Check if the last message needs updating.
            if (updatemessage) {
                var user = this._getUserNode(SELECTORS.CONVERSATIONS, userid);
                var updatemessagetext = updatemessage.find(SELECTORS.MESSAGETEXT).text().trim();
                var sentbyuser = false;
                if (updatemessage.data('useridto') == userid) {
                    // Must have been sent by the currently logged in user.
                    sentbyuser = true;
                }

                this._updateContactText(user, updatemessagetext, sentbyuser);
            }

            // Now we have done all the deletion we can set the flag back to false.
            this._stopDeleting();
        };

        /**
         * Handles adding a contact to the list.
         *
         * @private
         */
        Contacts.prototype._addContact = function() {
            this.messageArea.find(SELECTORS.CONTACTS).empty();
            this._numContactsDisplayed = 0;
            this._loadContacts();
        };

        /**
         * Handles removing a contact from the list.
         *
         * @param {String} selector
         * @param {int} userid
         * @private
         */
        Contacts.prototype._removeContact = function(selector, userid) {
            this._getUserNode(selector, userid).remove();
            this._numContactsDisplayed--;
        };

        /**
         * Handles marking a contact as blocked on the list.
         *
         * @param {int} userid
         * @private
         */
        Contacts.prototype._blockContact = function(userid) {
            var user = this._getUserNode(SELECTORS.CONTACTS, userid);
            user.find(SELECTORS.CONTACTICONBLOCKED).removeClass('hidden');

            user = this._getUserNode(SELECTORS.CONVERSATIONS, userid);
            user.find(SELECTORS.CONTACTICONBLOCKED).removeClass('hidden');

            user = this._getUserNode(SELECTORS.SEARCHRESULTSAREA, userid);
            user.find(SELECTORS.CONTACTICONBLOCKED).removeClass('hidden');
        };

        /**
         * Handles marking a contact as unblocked on the list.
         *
         * @param {int} userid
         * @private
         */
        Contacts.prototype._unblockContact = function(userid) {
            var user = this._getUserNode(SELECTORS.CONTACTS, userid);
            user.find(SELECTORS.CONTACTICONBLOCKED).addClass('hidden');

            user = this._getUserNode(SELECTORS.CONVERSATIONS, userid);
            user.find(SELECTORS.CONTACTICONBLOCKED).addClass('hidden');

            user = this._getUserNode(SELECTORS.SEARCHRESULTSAREA, userid);
            user.find(SELECTORS.CONTACTICONBLOCKED).addClass('hidden');
        };

        /**
         * Handles retrieving a user node from a list.
         *
         * @param {String} selector
         * @param {int} userid
         * @return {jQuery} The user node
         * @private
         */
        Contacts.prototype._getUserNode = function(selector, userid) {
            return this.messageArea.find(selector + " " + SELECTORS.CONTACT +
                "[data-userid='" + userid + "']");
        };

        /**
         * Handles selecting a contact in the list.
         *
         * @param {String} selector
         * @private
         */
        Contacts.prototype._setSelectedUser = function(selector) {
            // Remove the 'selected' class from any other contact.
            this.messageArea.find(SELECTORS.CONTACT).removeClass('selected');
            this.messageArea.find(SELECTORS.CONTACT).attr('aria-pressed', false);
            // Set the tab for the user to selected.
            this.messageArea.find(SELECTORS.CONTACT + selector).addClass('selected');
            this.messageArea.find(SELECTORS.CONTACT + selector).attr('aria-pressed', true);
        };

        /**
         * Converts a text message into the text that should be stored in the contact list
         *
         * @param {String} text
         * @return {String} The altered text
         */
        Contacts.prototype._getContactText = function(text) {
            // Remove the HTML tags to render the contact text.
            text = $(document.createElement('div')).html(text).text();

            if (text.length > this._messageLength) {
                text = text.substr(0, this._messageLength - 3);
                text += '...';
            }

            return text;
        };

        /**
         * Handles updating the contact text.
         *
         * @param {jQuery} user The user to update
         * @param {String} text The text to update the contact with
         * @param {Boolean} sentbyuser Was it sent by the currently logged in user?
         * @private
         */
        Contacts.prototype._updateContactText = function(user, text, sentbyuser) {
            // Get the text we will display on the contact panel.
            text = this._getContactText(text);
            if (sentbyuser) {
                Str.get_string('you', 'message').done(function(string) {
                    // Ensure we display that the message is from this user.
                    user.find(SELECTORS.LASTMESSAGEUSER).empty().append(string);
                }).always(function() {
                    user.find(SELECTORS.LASTMESSAGETEXT).empty().append(text);
                });
            } else {
                user.find(SELECTORS.LASTMESSAGEUSER).empty();
                user.find(SELECTORS.LASTMESSAGETEXT).empty().append(text);
            }
        };

        /**
         * Shifts focus to the next contact in the list.
         *
         * @param {event} e The jquery event
         * @param {object} data Additional event data
         */
        Contacts.prototype._selectNextContact = function(e, data) {
            var contact = $(e.target).closest(SELECTORS.CONTACT);
            var next = contact.next();
            next.focus();

            data.originalEvent.preventDefault();
            data.originalEvent.stopPropagation();
        };

        /**
         * Shifts focus to the previous contact in the list.
         *
         * @param {event} e The jquery event
         * @param {object} data Additional event data
         */
        Contacts.prototype._selectPreviousContact = function(e, data) {
            var contact = $(e.target).closest(SELECTORS.CONTACT);
            var previous = contact.prev();
            previous.focus();

            data.originalEvent.preventDefault();
            data.originalEvent.stopPropagation();
        };

        /**
         * Shifts focus to the next course in the list.
         *
         * @param {event} e The jquery event
         * @param {object} data Additional event data
         */
        Contacts.prototype._selectNextCourse = function(e, data) {
            var course = $(e.target).closest(SELECTORS.COURSE);
            course.next().focus();

            data.originalEvent.preventDefault();
            data.originalEvent.stopPropagation();
        };

        /**
         * Shifts focus to the previous course in the list.
         *
         * @param {event} e The jquery event
         * @param {object} data Additional event data
         */
        Contacts.prototype._selectPreviousCourse = function(e, data) {
            var course = $(e.target).closest(SELECTORS.COURSE);
            course.prev().focus();

            data.originalEvent.preventDefault();
            data.originalEvent.stopPropagation();
        };

        /**
         * Shifts focus to the next conversation in the list.
         *
         * @param {event} e The jquery event
         * @param {object} data Additional event data
         */
        Contacts.prototype._selectNextConversation = function(e, data) {
            var conversation = $(e.target).closest(SELECTORS.VIEWCONVERSATION);
            var next = conversation.next();
            next.focus();

            data.originalEvent.preventDefault();
            data.originalEvent.stopPropagation();
        };

        /**
         * Shifts focus to the previous conversation in the list.
         *
         * @param {event} e The jquery event
         * @param {object} data Additional event data
         */
        Contacts.prototype._selectPreviousConversation = function(e, data) {
            var conversation = $(e.target).closest(SELECTORS.VIEWCONVERSATION);
            var previous = conversation.prev();
            previous.focus();

            data.originalEvent.preventDefault();
            data.originalEvent.stopPropagation();
        };

        /**
         * Flags the search area as seaching.
         */
        Contacts.prototype._setSearching = function() {
            $(SELECTORS.SEARCHTEXTAREA).addClass('searching');
        };

        /**
         * Flags the search area as seaching.
         */
        Contacts.prototype._clearSearching = function() {
            $(SELECTORS.SEARCHTEXTAREA).removeClass('searching');
        };

        /**
         * Make the messaging area visible.
         */
        Contacts.prototype._showMessagingArea = function() {
            this.messageArea.find(SELECTORS.MESSAGINGAREA)
                .removeClass('hide-messages')
                .addClass('show-messages');
        };

        /**
         * Hide the messaging area.
         */
        Contacts.prototype._hideMessagingArea = function() {
            this.messageArea.find(SELECTORS.MESSAGINGAREA)
                .removeClass('show-messages')
                .addClass('hide-messages');
        };

        return Contacts;
    }
);
