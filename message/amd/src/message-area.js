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
 * This module handles toggling between the 'Conversations' and 'Contacts'
 * tabs in the message area.
 *
 * @module     core_message/message-area
 * @package    core_message
 * @copyright  2016 Mark Nelson <markn@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
define(['jquery', 'core/ajax', 'core/templates', 'core/notification', 'core/str', 'core/config'],
    function($, ajax, templates, notification, str, config) {

    function Messagearea(selector) {
        this._node = $(selector);
        this._init();
    }

    Messagearea.prototype.maxstringlength = 60;

    Messagearea.prototype.find = function(selector) {
        return this._node.find(selector);
    };

    Messagearea.prototype._init = function() {
        this._node.on('click', '.tabconversations', this._loadConversations.bind(this));
        this._node.on('click', '.tabcontacts', this._loadContacts.bind(this));
        this._node.on('click', '.contact-msg', this._viewMessages.bind(this));
        this._node.on('click', '.contact-profile', this._viewContact.bind(this));
        this._node.on('click', '.sendmessagebtn', this._sendMessage.bind(this));
        this._node.on('click', '.viewprofilebtn', this._viewProfile.bind(this));
        this._node.on('click', '.newmessagebtn', this._newMessage.bind(this));
        this._node.on('click', '.blockcontactbtn', this._blockContact.bind(this));
        this._node.on('click', '.unblockcontactbtn', this._unblockContact.bind(this));
        this._node.on('click', '.addcontactbtn', this._addContact.bind(this));
        this._node.on('click', '.removecontactbtn', this._removeContact.bind(this));
        this._node.on('click', '.choosemessagestodeletebtn', this._chooseMessagesToDelete.bind(this));
        this._node.on('click', '.deletemessagesbtn', this._deleteMessages.bind(this));
        this._node.on('click', '.canceldeletemessagesbtn', this._cancelDeleteMessages.bind(this));
    };

    Messagearea.prototype._loadConversations = function() {
        this._loadContactArea('core_message_data_for_messagearea_conversations');
    };

    Messagearea.prototype._loadContacts = function() {
        this._loadContactArea('core_message_data_for_messagearea_contacts');
    };

    Messagearea.prototype._loadContactArea = function(methodname) {
        // Show loading template.
        templates.render('core_message/loading', {}).done(function(html) {
            this.find('.contacts').empty().append(html);
        }.bind(this));

        // Call the web service to return the data we want to view.
        var promises = ajax.call([{
            methodname: methodname,
            args: {
                userid: this._getCurrentUserId()
            }
        }]);

        // After the request render the contacts area.
        promises[0].then(function(data) {
            // We have the data - lets re-render the template with it.
            return templates.render('core_message/contacts', data).then(function(html, js) {
                this.find('.contacts-area').empty().append(html);
                // And execute any JS that was in the template.
                templates.runTemplateJS(js);
            }.bind(this));
        }.bind(this)).fail(notification.exception);
    };

    Messagearea.prototype._viewMessages = function(event) {
        var userid = $(event.currentTarget).data('userid');
        this._loadMessages(userid);
    };

    Messagearea.prototype._sendMessage = function() {
        // Call the web service to save our message.
        var promises = ajax.call([{
            methodname: 'core_message_send_instant_messages',
            args: {
                messages: [
                    {
                        touserid: this.find('.messages').data('userid'),
                        text: this.find('#sendmessagetxt').val()
                    }
                ]
            }
        }]);

        // Update the DOM when we get some data back.
        promises[0].then(function() {
            // Update the messaging area.
            this._addMessageToDom();
        }.bind(this)).fail(notification.exception);
    };

    Messagearea.prototype._addMessageToDom = function() {
        // Get the variables we are going to use.
        var userid = this.find('.messages').data('userid');
        var text = this.find('#sendmessagetxt').val();

        // Call the web service to return how the message should look.
        var promises = ajax.call([{
            methodname: 'core_message_data_for_messagearea_get_most_recent_message',
            args: {
                currentuserid: this._getCurrentUserId(),
                otheruserid: userid
            }
        }]);

        // Add the message.
        promises[0].then(function(data) {
            templates.render('core_message/message', data).then(function(html, js) {
                this.find('.messages').append(html);
                // And execute any JS that was in the template.
                templates.runTemplateJS(js);

                // Update the conversation on the left.
                var leftmsg = text.substr(0, this.maxstringlength);
                if (text.length > this.maxstringlength) {
                    leftmsg += " ...";
                }
                this.find('#contact-' + userid + ' .lastmessage').empty().append(leftmsg);

                // Empty the response text area.
                this.find('#sendmessagetxt').val('');
            }.bind(this));
        }.bind(this)).fail(notification.exception);
    };

    Messagearea.prototype._viewContact = function(event) {
        var userid = $(event.currentTarget).data('userid');

        // Show loading template.
        templates.render('core_message/loading', {}).done(function(html) {
            this.find('.messages-area').empty().append(html);
        }.bind(this));

        // Remove the 'selected' class from any other contact.
        this.find('.contact').removeClass('selected');
        // Set the tab for the user to selected.
        this.find('#contact-' + userid).addClass('selected');

        // Call the web service to return the profile.
        var promises = ajax.call([{
            methodname: 'core_message_data_for_messagearea_get_profile',
            args: {
                currentuserid: this._getCurrentUserId(),
                otheruserid: userid
            }
        }]);

        // Show the profile.
        promises[0].then(function(data) {
            templates.render('core_message/profile', data).then(function(html, js) {
                this.find('.messages-area').empty().append(html);
                // And execute any JS that was in the template.
                templates.runTemplateJS(js);
            }.bind(this));
        }.bind(this)).fail(notification.exception);
    };

    Messagearea.prototype._viewProfile = function() {
        var userid = this.find('.profile').data('userid');
        window.location.href = config.wwwroot + '/user/profile.php?id=' + userid;
    };

    Messagearea.prototype._newMessage = function() {
        var userid = this.find('.profile').data('userid');
        this._loadMessages(userid);
    };

    Messagearea.prototype._blockContact = function() {
        var userid = this.find('.profile').data('userid');

        // Block the contact.
        var promises = ajax.call([{
            methodname: 'core_message_block_contacts',
            args: {
                userid: this._getCurrentUserId(),
                userids: [
                    userid
                ]
            }
        }]);

        // Change the button.
        promises[0].then(function() {
            str.get_string('unblockcontact', 'message').then(function(s) {
                this._changeButton('blockcontactbtn', s, 'unblockcontactbtn', 'unblockcontactbtn-' + userid);
            }.bind(this));
        }.bind(this)).fail(notification.exception);
    };

    Messagearea.prototype._unblockContact = function() {
        var userid = this.find('.profile').data('userid');

        // Unblock the contact.
        var promises = ajax.call([{
            methodname: 'core_message_unblock_contacts',
            args: {
                userid: this._getCurrentUserId(),
                userids: [
                    userid
                ]
            }
        }]);

        // Change the button.
        promises[0].then(function() {
            str.get_string('blockcontact', 'message').then(function(s) {
                this._changeButton('unblockcontactbtn', s, 'blockcontactbtn', 'blockcontactbtn-' + userid);
            }.bind(this));
        }.bind(this)).fail(notification.exception);
    };

    Messagearea.prototype._addContact = function() {
        var userid = this.find('.profile').data('userid');

        // Add the contact.
        var promises = ajax.call([{
            methodname: 'core_message_create_contacts',
            args: {
                userid: this._getCurrentUserId(),
                userids: [
                    userid
                ]
            }
        }]);

        // Change the button.
        promises[0].then(function() {
            str.get_string('removecontact', 'message').then(function(s) {
                this._changeButton('addcontactbtn', s, 'removecontactbtn', 'removecontactbtn-' + userid);
            }.bind(this));
            // Add the contact to the contact list.
            this._loadContactArea('core_message_data_for_messagearea_contacts');
        }.bind(this)).fail(notification.exception);
    };

    Messagearea.prototype._removeContact = function() {
        var userid = this.find('.profile').data('userid');

        // Remove the contact.
        var promises = ajax.call([{
            methodname: 'core_message_delete_contacts',
            args: {
                userid: this._getCurrentUserId(),
                userids: [
                    userid
                ]
            }
        }]);

        // Change the button.
        promises[0].then(function() {
            str.get_string('addcontact', 'message').then(function(s) {
                this._changeButton('removecontactbtn', s, 'addcontactbtn', 'addcontactbtn-' + userid);
            }.bind(this));
            // Remove the contact from the contact list.
            this.find('#contact-' + userid).remove();
        }.bind(this)).fail(notification.exception);
    };

    Messagearea.prototype._chooseMessagesToDelete = function() {
        // Toggle the checkboxes.
        var checkboxes = this.find('.deletemessagecheckbox').toggle();

        // Check if we didn't toggle to delete the messages.
        if (checkboxes.is(':hidden')) {
            // Only show a response field if we are viewing the logged in user's messages.
            this.find('.response').empty();
            if (this._getLoggedInUserId() == this._getCurrentUserId()) {
                templates.render('core_message/message_response', {}).then(function (html, js) {
                    this.find('.response').append(html);
                    templates.runTemplateJS(js);
                }.bind(this));
            }
        } else {
            templates.render('core_message/message_delete_message', {}).then(function(html, js) {
                this.find('.response').empty().append(html);
                templates.runTemplateJS(js);
            }.bind(this));
        }
    };

    Messagearea.prototype._deleteMessages = function() {
        var userid = this._getCurrentUserId();
        var checkboxes = this.find('.deletemessagecheckbox input:checked');
        var requests = [];
        var messagestoremove = [];

        // Go through all the checked checkboxes and prepare them for deletion.
        checkboxes.each(function(id, element) {
            var node = $(element);
            var messageid = node.data('messageid');
            var isread = node.data('messageread') ? 1 : 0;
            var message = this.find('#message-' + messageid + '' + isread);
            messagestoremove.push(message);
            requests.push({
                methodname: 'core_message_delete_message',
                args: {
                    messageid: messageid,
                    userid: userid,
                    read: isread
                }
            });
        }.bind(this));

        ajax.call(requests)[requests.length - 1].then(function() {
            // Remove the messages from the DOM.
            $.each(messagestoremove, function(key, message) {
                // Remove the message.
                message.remove();
            }.bind(this));
            // Now we have removed all the messages from the DOM lets remove any block times we may need to as well.
            $.each(messagestoremove, function(key, message) {
                // First - let's make sure there are no more messages in that time block.
                var blocktime = message.data('blocktime');
                if (this.find('div.message[data-blocktime=\'' + blocktime + '\']').length == 0) {
                    this.find('div.blocktime[data-blocktime=\'' + blocktime + '\']').remove();
                }
            }.bind(this));
            // Simple toggle the delete button action.
            this._chooseMessagesToDelete();
        }.bind(this), notification.exception);
    };

    Messagearea.prototype._cancelDeleteMessages = function() {
        // Simple toggle the delete button action.
        this._chooseMessagesToDelete();
    };

    Messagearea.prototype._loadMessages = function(userid) {
        // Show loading template.
        templates.render('core_message/loading', {}).done(function(html) {
            this.find('.messages-area').empty().append(html);
        }.bind(this));

        // Remove the 'selected' class from any other contact.
        this.find('.contact').removeClass('selected');
        // Set the tab for the user to selected.
        this.find('#contact-' + userid).addClass('selected');

        // Call the web service to get our data.
        var promises = ajax.call([{
            methodname: 'core_message_data_for_messagearea_messages',
            args: {
                currentuserid: this._getCurrentUserId(),
                otheruserid: userid
            }
        }]);

        // Do stuff when we get data back.
        promises[0].then(function(data) {
            // We have the data - lets re-render the template with it.
            return templates.render('core_message/messages', data).then(function(html, js) {
                // Append the message.
                this.find('.messages-area').empty().append(html);
                // And execute any JS that was in the template.
                templates.runTemplateJS(js);
            }.bind(this));
        }.bind(this)).fail(notification.exception);
    };

    Messagearea.prototype._changeButton = function(oldclass, text, newclass, newid) {
        var button = this.find('.' + oldclass);
        button.val(text);
        button.removeClass(oldclass);
        button.addClass(newclass);
        button.prop('id', newid);
    };

    Messagearea.prototype._getLoggedInUserId = function() {
        return this._node.data('loggedinuserid');
    };

    Messagearea.prototype._getCurrentUserId = function() {
        return this._node.data('userid');
    };

    return Messagearea;
});
