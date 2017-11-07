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
 * Some UI stuff for participants page.
 * This is also used by the report/participants/index.php because it has the same functionality.
 *
 * @module     core_user/participants
 * @package    core_user
 * @copyright  2017 Damyon Wiese
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
define(['jquery', 'core/str', 'core/modal_factory', 'core/modal_events', 'core/templates', 'core/notification', 'core/ajax'],
        function($, Str, ModalFactory, ModalEvents, Templates, Notification, Ajax) {

    var SELECTORS = {
        BULKACTIONSELECT: "#formactionid",
        BULKUSERCHECKBOXES: "input.usercheckbox",
        BULKUSERNOSCHECKBOXES: "input.usercheckbox[value='0']",
        BULKUSERSELECTEDCHECKBOXES: "input.usercheckbox:checked",
        BULKACTIONFORM: "#participantsform",
        CHECKALLBUTTON: "#checkall",
        CHECKALLNOSBUTTON: "#checkallnos",
        CHECKALLONPAGEBUTTON: "#checkallonpage",
        CHECKNONEBUTTON: "#checknone"
    };

    /**
     * Constructor
     *
     * @param {Object} options Object containing options. Contextid is required.
     * Each call to templates.render gets it's own instance of this class.
     */
    var Participants = function(options) {

        this.courseId = options.courseid;
        this.noteStateNames = options.noteStateNames;
        this.stateHelpIcon = options.stateHelpIcon;

        this.attachEventListeners();
    };
    // Class variables and functions.

    /**
     * @var {Modal} modal
     * @private
     */
    Participants.prototype.modal = null;

    /**
     * @var {int} courseId
     * @private
     */
    Participants.prototype.courseId = -1;

    /**
     * @var {Object} noteStateNames
     * @private
     */
    Participants.prototype.noteStateNames = {};

    /**
     * @var {String} stateHelpIcon
     * @private
     */
    Participants.prototype.stateHelpIcon = "";

    /**
     * Private method
     *
     * @method attachEventListeners
     * @private
     */
    Participants.prototype.attachEventListeners = function() {
        $(SELECTORS.BULKACTIONSELECT).on('change', function(e) {
            var action = $(e.target).val();
            if (action.indexOf('#') !== -1) {
                e.preventDefault();

                var ids = [];
                $(SELECTORS.BULKUSERSELECTEDCHECKBOXES).each(function(index, ele) {
                    var name = $(ele).attr('name');
                    var id = name.replace('user', '');
                    ids.push(id);
                });

                if (action == '#messageselect') {
                    this.showSendMessage(ids).fail(Notification.exception);
                } else if (action == '#addgroupnote') {
                    this.showAddNote(ids).fail(Notification.exception);
                }
                $(SELECTORS.BULKACTIONSELECT + ' option[value=""]').prop('selected', 'selected');
            } else if (action !== '') {
                if ($(SELECTORS.BULKUSERSELECTEDCHECKBOXES).length > 0) {
                    $(SELECTORS.BULKACTIONFORM).submit();
                } else {
                    $(SELECTORS.BULKACTIONSELECT + ' option[value=""]').prop('selected', 'selected');
                }
            }
        }.bind(this));

        $(SELECTORS.CHECKALLBUTTON).on('click', function() {
            var showallink = $(this).data('showallink');
            if (showallink) {
                window.location = showallink;
            }
        });

        $(SELECTORS.CHECKALLNOSBUTTON).on('click', function() {
            $(SELECTORS.BULKUSERNOSCHECKBOXES).prop('checked', true);
        });
        $(SELECTORS.CHECKALLONPAGEBUTTON).on('click', function() {
            $(SELECTORS.BULKUSERCHECKBOXES).prop('checked', true);
        });

        $(SELECTORS.CHECKNONEBUTTON).on('click', function() {
            $(SELECTORS.BULKUSERCHECKBOXES).prop('checked', false);
        });
    };

    /**
     * Show the add note popup
     *
     * @method showAddNote
     * @private
     * @param {int[]} users
     * @return {Promise}
     */
    Participants.prototype.showAddNote = function(users) {

        if (users.length == 0) {
            // Nothing to do.
            return $.Deferred().resolve().promise();
        }

        var states = [];
        for (var key in this.noteStateNames) {
            switch (key) {
                case 'draft':
                    states.push({value: 'personal', label: this.noteStateNames[key]});
                    break;
                case 'public':
                    states.push({value: 'course', label: this.noteStateNames[key], selected: 1});
                    break;
                case 'site':
                    states.push({value: key, label: this.noteStateNames[key]});
                    break;
            }
        }

        var context = {stateNames: states, stateHelpIcon: this.stateHelpIcon};
        var titlePromise = null;
        if (users.length == 1) {
            titlePromise = Str.get_string('addbulknotesingle', 'core_notes');
        } else {
            titlePromise = Str.get_string('addbulknote', 'core_notes', users.length);
        }

        return $.when(
            ModalFactory.create({
                type: ModalFactory.types.SAVE_CANCEL,
                body: Templates.render('core_user/add_bulk_note', context)
            }),
            titlePromise
        ).then(function(modal, title) {
            // Keep a reference to the modal.
            this.modal = modal;
            this.modal.setTitle(title);
            this.modal.setSaveButtonText(title);

            // We want to focus on the action select when the dialog is closed.
            this.modal.getRoot().on(ModalEvents.hidden, function() {
                var notification = $('#user-notifications [role=alert]');
                if (notification.length) {
                    notification.focus();
                } else {
                    $(SELECTORS.BULKACTIONSELECT).focus();
                }
                this.modal.getRoot().remove();
            }.bind(this));

            this.modal.getRoot().on(ModalEvents.save, this.submitAddNote.bind(this, users));

            this.modal.show();

            return this.modal;
        }.bind(this));
    };

    /**
     * Add a note to this list of users.
     *
     * @method submitAddNote
     * @private
     * @param {int[]} users
     * @return {Promise}
     */
    Participants.prototype.submitAddNote = function(users) {
        var noteText = this.modal.getRoot().find('form textarea').val();
        var publishState = this.modal.getRoot().find('form select').val();
        var notes = [],
            i = 0;

        for (i = 0; i < users.length; i++) {
            notes.push({userid: users[i], text: noteText, courseid: this.courseId, publishstate: publishState});
        }

        return Ajax.call([{
            methodname: 'core_notes_create_notes',
            args: {notes: notes}
        }])[0].then(function(noteIds) {
            if (noteIds.length == 1) {
                return Str.get_string('addbulknotedonesingle', 'core_notes');
            } else {
                return Str.get_string('addbulknotedone', 'core_notes', noteIds.length);
            }
        }).then(function(msg) {
            Notification.addNotification({
                message: msg,
                type: "success"
            });
            return true;
        }).catch(Notification.exception);
    };

    /**
     * Show the send message popup.
     *
     * @method showSendMessage
     * @private
     * @param {int[]} users
     * @return {Promise}
     */
    Participants.prototype.showSendMessage = function(users) {

        if (users.length == 0) {
            // Nothing to do.
            return $.Deferred().resolve().promise();
        }
        var titlePromise = null;
        if (users.length == 1) {
            titlePromise = Str.get_string('sendbulkmessagesingle', 'core_message');
        } else {
            titlePromise = Str.get_string('sendbulkmessage', 'core_message', users.length);
        }

        return $.when(
            ModalFactory.create({
                type: ModalFactory.types.SAVE_CANCEL,
                body: Templates.render('core_user/send_bulk_message', {})
            }),
            titlePromise
        ).then(function(modal, title) {
            // Keep a reference to the modal.
            this.modal = modal;

            this.modal.setTitle(title);
            this.modal.setSaveButtonText(title);

            // We want to focus on the action select when the dialog is closed.
            this.modal.getRoot().on(ModalEvents.hidden, function() {
                $(SELECTORS.BULKACTIONSELECT).focus();
                this.modal.getRoot().remove();
            }.bind(this));

            this.modal.getRoot().on(ModalEvents.save, this.submitSendMessage.bind(this, users));

            this.modal.show();

            return this.modal;
        }.bind(this));
    };

    /**
     * Send a message to these users.
     *
     * @method submitSendMessage
     * @private
     * @param {int[]} users
     * @param {Event} e Form submission event.
     * @return {Promise}
     */
    Participants.prototype.submitSendMessage = function(users) {

        var messageText = this.modal.getRoot().find('form textarea').val();

        var messages = [],
            i = 0;

        for (i = 0; i < users.length; i++) {
            messages.push({touserid: users[i], text: messageText});
        }

        return Ajax.call([{
            methodname: 'core_message_send_instant_messages',
            args: {messages: messages}
        }])[0].then(function(messageIds) {
            if (messageIds.length == 1) {
                return Str.get_string('sendbulkmessagesentsingle', 'core_message');
            } else {
                return Str.get_string('sendbulkmessagesent', 'core_message', messageIds.length);
            }
        }).then(function(msg) {
            Notification.addNotification({
                message: msg,
                type: "success"
            });
            return true;
        }).catch(Notification.exception);
    };

    return /** @alias module:core_user/participants */ {
        // Public variables and functions.

        /**
         * Initialise the unified user filter.
         *
         * @method init
         * @param {Object} options - List of options.
         * @return {Participants}
         */
        'init': function(options) {
            return new Participants(options);
        }
    };
});
