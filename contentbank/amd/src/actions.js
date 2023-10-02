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
 * Module to manage content bank actions, such as delete or rename.
 *
 * @module     core_contentbank/actions
 * @copyright  2020 Sara Arjona <sara@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
define([
    'jquery',
    'core/ajax',
    'core/notification',
    'core/str',
    'core/templates',
    'core/url',
    'core/modal_save_cancel',
    'core/modal_events'],
function($, Ajax, Notification, Str, Templates, Url, ModalSaveCancel, ModalEvents) {

    /**
     * List of action selectors.
     *
     * @type {{DELETE_CONTENT: string}}
     */
    var ACTIONS = {
        DELETE_CONTENT: '[data-action="deletecontent"]',
        RENAME_CONTENT: '[data-action="renamecontent"]',
        SET_CONTENT_VISIBILITY: '[data-action="setcontentvisibility"]',
        COPY_CONTENT: '[data-action="copycontent"]',
    };

    /**
     * Actions class.
     */
    var Actions = function() {
        this.registerEvents();
    };

    /**
     * Register event listeners.
     */
    Actions.prototype.registerEvents = function() {
        $(ACTIONS.DELETE_CONTENT).click(function(e) {
            e.preventDefault();

            var contentname = $(this).data('contentname');
            var contentuses = $(this).data('uses');
            var contentid = $(this).data('contentid');
            var contextid = $(this).data('contextid');

            var strings = [
                {
                    key: 'deletecontent',
                    component: 'core_contentbank'
                },
                {
                    key: 'deletecontentconfirm',
                    component: 'core_contentbank',
                    param: {
                        name: contentname,
                    }
                },
                {
                    key: 'deletecontentconfirmlinked',
                    component: 'core_contentbank',
                },
                {
                    key: 'delete',
                    component: 'core'
                },
            ];

            var deleteButtonText = '';
            Str.get_strings(strings).then(function(langStrings) {
                var modalTitle = langStrings[0];
                var modalContent = langStrings[1];
                if (contentuses > 0) {
                    modalContent += ' ' + langStrings[2];
                }
                deleteButtonText = langStrings[3];

                return ModalSaveCancel.create({
                    title: modalTitle,
                    body: modalContent,
                    large: true,
                    removeOnClose: true,
                    show: true,
                    buttons: {
                        save: deleteButtonText,
                    },
                });
            }).then(function(modal) {
                modal.getRoot().on(ModalEvents.save, function() {
                    // The action is now confirmed, sending an action for it.
                    return deleteContent(contentid, contextid);
                });

                return;
            }).catch(Notification.exception);
        });

        $(ACTIONS.RENAME_CONTENT).click(function(e) {
            e.preventDefault();

            var contentname = $(this).data('contentname');
            var contentid = $(this).data('contentid');

            var strings = [
                {
                    key: 'renamecontent',
                    component: 'core_contentbank'
                },
                {
                    key: 'rename',
                    component: 'core_contentbank'
                },
            ];

            var saveButtonText = '';
            Str.get_strings(strings).then(function(langStrings) {
                var modalTitle = langStrings[0];
                saveButtonText = langStrings[1];

                return ModalSaveCancel.create({
                    title: modalTitle,
                    body: Templates.render('core_contentbank/renamecontent', {'contentid': contentid, 'name': contentname}),
                    removeOnClose: true,
                    show: true,
                    buttons: {
                        save: saveButtonText,
                    },
                });
            }).then(function(modal) {
                modal.getRoot().on(ModalEvents.save, function(e) {
                    // The action is now confirmed, sending an action for it.
                    var newname = $("#newname").val().trim();
                    if (newname) {
                        renameContent(contentid, newname);
                    } else {
                        var errorStrings = [
                            {
                                key: 'error',
                            },
                            {
                                key: 'emptynamenotallowed',
                                component: 'core_contentbank',
                            },
                        ];
                        Str.get_strings(errorStrings).then(function(langStrings) {
                            Notification.alert(langStrings[0], langStrings[1]);
                        }).catch(Notification.exception);
                        e.preventDefault();
                    }
                });

                return;
            }).catch(Notification.exception);
        });

        $(ACTIONS.COPY_CONTENT).click(function(e) {
            e.preventDefault();

            var contentname = $(this).data('contentname');
            var contentid = $(this).data('contentid');

            var strings = [
                {
                    key: 'copycontent',
                    component: 'core_contentbank'
                },
                {
                    key: 'error',
                },
                {
                    key: 'emptynamenotallowed',
                    component: 'core_contentbank',
                },
            ];

            let errorTitle, errorMessage;
            Str.get_strings(strings).then(function(langStrings) {
                var modalTitle = langStrings[0];
                errorTitle = langStrings[1];
                errorMessage = langStrings[2];

                return ModalSaveCancel.create({
                    title: modalTitle,
                    body: Templates.render('core_contentbank/copycontent', {'contentid': contentid, 'name': contentname}),
                    removeOnClose: true,
                    show: true,
                });
            }).then(function(modal) {
                modal.getRoot().on(ModalEvents.save, function() {
                    // The action is now confirmed, sending an action for it.
                    var newname = $("#newname").val().trim();
                    if (newname) {
                        copyContent(contentid, newname);
                    } else {
                        Notification.alert(errorTitle, errorMessage);
                        return false;
                    }
                });
                return;
            }).catch(Notification.exception);
        });

        $(ACTIONS.SET_CONTENT_VISIBILITY).click(function(e) {
            e.preventDefault();

            var contentid = $(this).data('contentid');
            var visibility = $(this).data('visibility');

            setContentVisibility(contentid, visibility);
        });
    };

    /**
     * Delete content from the content bank.
     *
     * @param {int} contentid The content to delete.
     * @param {int} contextid The contextid where the content belongs.
     */
    function deleteContent(contentid, contextid) {
        var request = {
            methodname: 'core_contentbank_delete_content',
            args: {
                contentids: {contentid}
            }
        };

        var requestType = 'success';
        Ajax.call([request])[0].then(function(data) {
            if (data.result) {
                return 'contentdeleted';
            }
            requestType = 'error';
            return 'contentnotdeleted';

        }).done(function(message) {
            var params = {
                contextid: contextid
            };
            if (requestType == 'success') {
                params.statusmsg = message;
            } else {
                params.errormsg = message;
            }
            // Redirect to the main content bank page and display the message as a notification.
            window.location.href = Url.relativeUrl('contentbank/index.php', params, false);
        }).fail(Notification.exception);
    }

    /**
     * Rename content in the content bank.
     *
     * @param {int} contentid The content to rename.
     * @param {string} name The new name for the content.
     */
    function renameContent(contentid, name) {
        var request = {
            methodname: 'core_contentbank_rename_content',
            args: {
                contentid: contentid,
                name: name
            }
        };
        var requestType = 'success';
        Ajax.call([request])[0].then(function(data) {
            if (data.result) {
                return 'contentrenamed';
            }
            requestType = 'error';
            return data.warnings[0].message;

        }).then(function(message) {
            var params = null;
            if (requestType == 'success') {
                params = {
                    id: contentid,
                    statusmsg: message
                };
                // Redirect to the content view page and display the message as a notification.
                window.location.href = Url.relativeUrl('contentbank/view.php', params, false);
            } else {
                // Fetch error notifications.
                Notification.addNotification({
                    message: message,
                    type: 'error'
                });
                Notification.fetchNotifications();
            }
            return;
        }).catch(Notification.exception);
    }

    /**
     * Copy content in the content bank.
     *
     * @param {int} contentid The content to copy.
     * @param {string} name The name for the new content.
     */
    function copyContent(contentid, name) {
        var request = {
            methodname: 'core_contentbank_copy_content',
            args: {
                contentid: contentid,
                name: name
            }
        };
        Ajax.call([request])[0].then(function(data) {
            if (data.id == 0) {
                // Fetch error notifications.
                Notification.addNotification({
                    message: data.warnings[0].message,
                    type: 'error'
                });
                Notification.fetchNotifications();
                return data.warnings[0].message;
            } else {
                let params = {
                    id: data.id,
                    statusmsg: 'contentcopied'
                };
                // Redirect to the content view page and display the message as a notification.
                window.location.href = Url.relativeUrl('contentbank/view.php', params, false);
            }
            return '';
        }).catch(Notification.exception);
    }

    /**
     * Set content visibility in the content bank.
     *
     * @param {int} contentid The content to modify
     * @param {int} visibility The new visibility value
     */
    function setContentVisibility(contentid, visibility) {
        var request = {
            methodname: 'core_contentbank_set_content_visibility',
            args: {
                contentid: contentid,
                visibility: visibility
            }
        };
        var requestType = 'success';
        Ajax.call([request])[0].then(function(data) {
            if (data.result) {
                return 'contentvisibilitychanged';
            }
            requestType = 'error';
            return data.warnings[0].message;

        }).then(function(message) {
            var params = null;
            if (requestType == 'success') {
                params = {
                    id: contentid,
                    statusmsg: message
                };
                // Redirect to the content view page and display the message as a notification.
                window.location.href = Url.relativeUrl('contentbank/view.php', params, false);
            } else {
                // Fetch error notifications.
                Notification.addNotification({
                    message: message,
                    type: 'error'
                });
                Notification.fetchNotifications();
            }
            return;
        }).catch(Notification.exception);
    }

    return /** @alias module:core_contentbank/actions */ {
        // Public variables and functions.

        /**
         * Initialise the contentbank actions.
         *
         * @method init
         * @return {Actions}
         */
        'init': function() {
            return new Actions();
        }
    };
});
