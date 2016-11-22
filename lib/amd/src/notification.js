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
 * A system for displaying notifications to users from the session.
 *
 * Wrapper for the YUI M.core.notification class. Allows us to
 * use the YUI version in AMD code until it is replaced.
 *
 * @module     core/notification
 * @class      notification
 * @package    core
 * @copyright  2015 Damyon Wiese <damyon@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since      2.9
 */
define(['core/yui', 'jquery', 'core/log'],
function(Y, $, log) {
    var notificationModule = {
        types: {
            'success':  'core/notification_success',
            'info':     'core/notification_info',
            'warning':  'core/notification_warning',
            'error':    'core/notification_error',
        },

        fieldName: 'user-notifications',

        fetchNotifications: function() {
            require(['core/ajax'], function(ajax) {
                var promises = ajax.call([{
                    methodname: 'core_fetch_notifications',
                    args: {
                        contextid: notificationModule.contextid
                    }
                }]);

                promises[0]
                    .done(notificationModule.addNotifications)
                    ;
            });
        },

        addNotifications: function(notifications) {
            if (!notifications) {
                notifications = [];
            }

            $.each(notifications, function(i, notification) {
                notificationModule.renderNotification(notification.template, notification.variables);
            });
        },

        setupTargetRegion: function() {
            var targetRegion = $('#' + notificationModule.fieldName);
            if (targetRegion.length) {
                return;
            }

            var newRegion = $('<span>').attr('id', notificationModule.fieldName);

            targetRegion = $('#region-main');
            if (targetRegion.length) {
                return targetRegion.prepend(newRegion);
            }

            targetRegion = $('[role="main"]');
            if (targetRegion.length) {
                return targetRegion.prepend(newRegion);
            }

            targetRegion = $('body');
            return targetRegion.prepend(newRegion);
        },

        addNotification: function(notification) {
            var template = notificationModule.types.error;

            notification = $.extend({
                    closebutton:    true,
                    announce:       true,
                    type:           'error'
                }, notification);

            if (notification.template) {
                template = notification.template;
                delete notification.template;
            } else if (notification.type){
                if (typeof notificationModule.types[notification.type] !== 'undefined') {
                    template = notificationModule.types[notification.type];
                }
                delete notification.type;
            }

            return notificationModule.renderNotification(template, notification);
        },

        renderNotification: function(template, variables) {
            if (typeof variables.message === 'undefined' || !variables.message) {
                log.debug('Notification received without content. Skipping.');
                return;
            }
            require(['core/templates'], function(templates) {
                templates.render(template, variables)
                    .done(function(html, js) {
                        $('#' + notificationModule.fieldName).prepend(html);
                        templates.runTemplateJS(js);
                    })
                    .fail(notificationModule.exception)
                    ;
            });
        },

        alert: function(title, message, yesLabel) {
            // Here we are wrapping YUI. This allows us to start transitioning, but
            // wait for a good alternative without having inconsistent dialogues.
            Y.use('moodle-core-notification-alert', function () {
                var alert = new M.core.alert({
                    title : title,
                    message : message,
                    yesLabel: yesLabel
                });

                alert.show();
            });
        },

        confirm: function(title, question, yesLabel, noLabel, yesCallback, noCallback) {
            // Here we are wrapping YUI. This allows us to start transitioning, but
            // wait for a good alternative without having inconsistent dialogues.
            Y.use('moodle-core-notification-confirm', function () {
                var modal = new M.core.confirm({
                    title : title,
                    question : question,
                    yesLabel: yesLabel,
                    noLabel: noLabel
                });

                modal.on('complete-yes', function() {
                    yesCallback();
                });
                if (noCallback) {
                    modal.on('complete-no', function() {
                        noCallback();
                    });
                }
                modal.show();
            });
        },

        exception: function(ex) {
            // Fudge some parameters.
            if (typeof ex.stack == 'undefined') {
                ex.stack = '';
            }
            if (ex.debuginfo) {
                ex.stack += ex.debuginfo + '\n';
            }
            if (ex.backtrace) {
                ex.stack += ex.backtrace;
                var ln = ex.backtrace.match(/line ([^ ]*) of/);
                var fn = ex.backtrace.match(/ of ([^:]*): /);
                if (ln && ln[1]) {
                    ex.lineNumber = ln[1];
                }
                if (fn && fn[1]) {
                    ex.fileName = fn[1];
                    if (ex.fileName.length > 30) {
                        ex.fileName = '...' + ex.fileName.substr(ex.fileName.length - 27);
                    }
                }
            }
            if (typeof ex.name == 'undefined' && ex.errorcode) {
                ex.name = ex.errorcode;
            }

            Y.use('moodle-core-notification-exception', function() {
                var modal = new M.core.exception(ex);

                modal.show();
            });
        }
    };

    return /** @alias module:core/notification */{
        init: function(contextid, notifications) {
            notificationModule.contextid = contextid;

            // Setup the message target region if it isn't setup already
            notificationModule.setupTargetRegion();

            // Add provided notifications.
            notificationModule.addNotifications(notifications);

            // Poll for any new notifications.
            notificationModule.fetchNotifications();
        },

        /**
         * Poll the server for any new notifications.
         *
         * @method fetchNotifications
         */
        fetchNotifications: notificationModule.fetchNotifications,

        /**
         * Add a notification to the page.
         *
         * Note: This does not cause the notification to be added to the session.
         *
         * @method addNotification
         * @param {Object}  notification                The notification to add.
         * @param {string}  notification.message        The body of the notification
         * @param {string}  notification.type           The type of notification to add (error, warning, info, success).
         * @param {Boolean} notification.closebutton    Whether to show the close button.
         * @param {Boolean} notification.announce       Whether to announce to screen readers.
         */
        addNotification: notificationModule.addNotification,

        /**
         * Wrap M.core.alert.
         *
         * @method alert
         * @param {string} title
         * @param {string} message
         * @param {string} yesLabel
         */
        alert: notificationModule.alert,

        /**
         * Wrap M.core.confirm.
         *
         * @method confirm
         * @param {string} title
         * @param {string} question
         * @param {string} yesLabel
         * @param {string} noLabel
         * @param {function} yesCallback
         * @param {function} noCallback Optional parameter to be called if the user presses cancel.
         */
        confirm: notificationModule.confirm,

        /**
         * Wrap M.core.exception.
         *
         * @method exception
         * @param {Error} ex
         */
        exception: notificationModule.exception
    };
});
