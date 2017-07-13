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
 * This module handles the profile area of the messaging area.
 *
 * @module     core_message/message_area_profile
 * @package    core_message
 * @copyright  2016 Mark Nelson <markn@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
define(['jquery', 'core/ajax', 'core/templates', 'core/notification', 'core/str', 'core/config',
        'core/custom_interaction_events', 'core_message/message_area_events'],
    function($, Ajax, Templates, Notification, Str, Config, CustomEvents, Events) {

        /** @type {Object} The list of selectors for the message area. */
        var SELECTORS = {
            PROFILE: "[data-region='profile']",
            PROFILEADDCONTACT: "[data-action='profile-add-contact']",
            PROFILEBLOCKCONTACT: "[data-action='profile-block-contact']",
            PROFILEREMOVECONTACT: "[data-action='profile-remove-contact']",
            PROFILESENDMESSAGE: "[data-action='profile-send-message']",
            PROFILEUNBLOCKCONTACT: "[data-action='profile-unblock-contact']",
            PROFILEVIEW: "[data-action='profile-view']",
            SHOWCONTACTS: "[data-action='show-contacts']",
            MESSAGESAREA: "[data-region='messages-area']",
            MESSAGINGAREA: "[data-region='messaging-area']"
        };

        /**
         * Profile class.
         *
         * @param {Messagearea} messageArea The messaging area object.
         */
        function Profile(messageArea) {
            this.messageArea = messageArea;
            this._init();
        }

        /** @type {Messagearea} The messaging area object. */
        Profile.prototype.messageArea = null;

        /**
         * Initialise the event listeners.
         *
         * @private
         */
        Profile.prototype._init = function() {
            CustomEvents.define(this.messageArea.node, [
                CustomEvents.events.activate
            ]);

            this.messageArea.onCustomEvent(Events.CONTACTSELECTED, this._viewProfile.bind(this));
            this.messageArea.onDelegateEvent(CustomEvents.events.activate, SELECTORS.PROFILEVIEW,
                function(e, data) {
                    this._viewFullProfile();
                    data.originalEvent.preventDefault();
                }.bind(this));
            this.messageArea.onDelegateEvent(CustomEvents.events.activate, SELECTORS.PROFILESENDMESSAGE,
                function(e, data) {
                    this._sendMessage();
                    data.originalEvent.preventDefault();
                }.bind(this));
            this.messageArea.onDelegateEvent(CustomEvents.events.activate, SELECTORS.PROFILEUNBLOCKCONTACT,
                function(e, data) {
                    this._unblockContact();
                    data.originalEvent.preventDefault();
                }.bind(this));
            this.messageArea.onDelegateEvent(CustomEvents.events.activate, SELECTORS.PROFILEBLOCKCONTACT,
                function(e, data) {
                    this._blockContact();
                    data.originalEvent.preventDefault();
                }.bind(this));
            this.messageArea.onDelegateEvent(CustomEvents.events.activate, SELECTORS.PROFILEADDCONTACT,
                function(e, data) {
                    this._addContact();
                    data.originalEvent.preventDefault();
                }.bind(this));
            this.messageArea.onDelegateEvent(CustomEvents.events.activate, SELECTORS.PROFILEREMOVECONTACT,
                function(e, data) {
                    this._removeContact();
                    data.originalEvent.preventDefault();
                }.bind(this));
            this.messageArea.onDelegateEvent(CustomEvents.events.activate, SELECTORS.SHOWCONTACTS,
                this._hideMessagingArea.bind(this));
        };

        /**
         * Handles viewing the profile.
         *
         * @param {Event} event
         * @param {int} userid
         * @return {Promise} The promise resolved when the profile has been rendered
         * @private
         */
        Profile.prototype._viewProfile = function(event, userid) {
            // Show loading template.
            Templates.render('core/loading', {}).done(function(html, js) {
                Templates.replaceNodeContents(this.messageArea.find(SELECTORS.MESSAGESAREA), html, js);
            }.bind(this));

            // Call the web service to return the profile.
            var promises = Ajax.call([{
                methodname: 'core_message_data_for_messagearea_get_profile',
                args: {
                    currentuserid: this.messageArea.getCurrentUserId(),
                    otheruserid: userid
                }
            }]);

            // Show the profile.
            return promises[0].then(function(data) {
                return Templates.render('core_message/message_area_profile', data);
            }).then(function(html, js) {
                Templates.replaceNodeContents(this.messageArea.find(SELECTORS.MESSAGESAREA), html, js);
            }.bind(this)).fail(Notification.exception);
        };

        /**
         * Handles viewing the user's full profile.
         *
         * @private
         */
        Profile.prototype._viewFullProfile = function() {
            window.location.href = Config.wwwroot + '/user/profile.php?id=' + this._getUserId();
        };

        /**
         * Handles viewing the messages with the user.
         *
         * @private
         */
        Profile.prototype._sendMessage = function() {
            this.messageArea.trigger(Events.SENDMESSAGE, this._getUserId());
        };

        /**
         * Handles blocking the contact.
         *
         * @return {Promise} The promise resolved when the contact has been blocked
         * @private
         */
        Profile.prototype._blockContact = function() {
            var action = this._performAction('core_message_block_contacts', 'unblockcontact', 'profile-block-contact',
                'profile-unblock-contact', '');
            return action.then(function() {
                this.messageArea.trigger(Events.CONTACTBLOCKED, this._getUserId());
            }.bind(this));
        };

        /**
         * Handles unblocking the contact.
         *
         * @return {Promise} The promise resolved when the contact has been unblocked
         * @private
         */
        Profile.prototype._unblockContact = function() {
            var action = this._performAction('core_message_unblock_contacts', 'blockcontact', 'profile-unblock-contact',
                'profile-block-contact', 'danger');
            return action.then(function() {
                this.messageArea.trigger(Events.CONTACTUNBLOCKED, this._getUserId());
            }.bind(this));
        };

        /**
         * Handles adding the contact.
         *
         * @return {Promise} The promise resolved when the contact has been added
         * @private
         */
        Profile.prototype._addContact = function() {
            var action = this._performAction('core_message_create_contacts', 'removecontact', 'profile-add-contact',
                'profile-remove-contact', 'danger');
            return action.then(function() {
                this.messageArea.trigger(Events.CONTACTADDED, this._getUserId());
            }.bind(this));
        };

        /**
         * Handles removing the contact.
         *
         * @return {Promise} The promise resolved when the contact has been removed
         * @private
         */
        Profile.prototype._removeContact = function() {
            var action = this._performAction('core_message_delete_contacts', 'addcontact', 'profile-remove-contact',
                'profile-add-contact', '');
            return action.then(function() {
                this.messageArea.trigger(Events.CONTACTREMOVED, this._getUserId());
            }.bind(this));
        };

        /**
         * Helper function to perform actions on the profile page.
         *
         * @param {String} service The web service to call.
         * @param {String} string The string to change the button value to
         * @param {String} oldaction The data-action of the button
         * @param {string} newaction The data-action to change the button to
         * @param {String} newclass The CSS class we want to add
         * @return {Promise} The promise resolved when the action has been performed
         * @private
         */
        Profile.prototype._performAction = function(service, string, oldaction, newaction, newclass) {
            var promises = Ajax.call([{
                methodname: service,
                args: {
                    userid: this.messageArea.getCurrentUserId(),
                    userids: [
                        this._getUserId()
                    ]
                }
            }]);

            return promises[0].then(function() {
                return Str.get_string(string, 'message');
            }).then(function(s) {
                this._changeText(s, oldaction, newaction, newclass);
            }.bind(this)).fail(Notification.exception);
        };

        /**
         * Changes the text in the profile area.
         *
         * @param {String} text The string to change the button value to
         * @param {string} oldaction The data-action of the button
         * @param {string} newaction The data-action to change the button to
         * @param {String} newclass The CSS class we want to add
         * @private
         */
        Profile.prototype._changeText = function(text, oldaction, newaction, newclass) {
            var anchor = this.messageArea.find("[data-action='" + oldaction + "']");
            // Change the text.
            anchor.text(text);
            // Remove any class.
            anchor.removeClass();
            // Add the class if there is one.
            if (newclass) {
                anchor.addClass(newclass);
            }

            anchor.attr('data-action', newaction);
        };

        /**
         * Returns the ID of the user whos profile we are viewing.
         *
         * @return {int} The user ID
         * @private
         */
        Profile.prototype._getUserId = function() {
            return this.messageArea.find(SELECTORS.PROFILE).data('userid');
        };

        /**
         * Hide the messaging area. This only applies on smaller screen resolutions.
         */
        Profile.prototype._hideMessagingArea = function() {
            this.messageArea.find(SELECTORS.MESSAGINGAREA)
                .removeClass('show-messages')
                .addClass('hide-messages');
        };

        return Profile;
    }
);
