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
 * Cross Enrollment Tool
 *
 * @package   block_lsuxe
 * @copyright 2008 onwards Louisiana State University
 * @copyright 2008 onwards David Lowe, Robert Russo
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


define(['jquery', 'core/notification', 'core/modal_factory', 'core/modal_events'],
    function($, notification, MF, ME) {
    'use strict';

    return {
        /**
         * Confirmation window to SAVE or CANCEL
         * Type can be either: success, warning, info, error
         * @param {obj} A simple object with the 'message' and 'type' of notification.
         * @return void
         */
        callYesNoModi: function(data) {
            var this_data = data;
            var promiseObj = new Promise(function (resolve) {

                MF.create({
                    type: MF.types.SAVE_CANCEL,
                    title: this_data.title,
                    body: this_data.body
                })
                .then(function(modal) {
                    modal.setSaveButtonText(this_data.save_button);
                    var root = modal.getRoot();
                    root.on(ME.save, function() {
                        resolve({"status": true, "data": this_data});
                    });
                    modal.show();
                });
            });
            return promiseObj;
        },

        /**
         * Check if a notification is already showing
         * @param  {[type]} data [description]
         * @return {[type]}      [description]
         */
        checkNoti: function() {
            var noti_count = $('span.notifications').html().length;
            return noti_count == 0 ? true : false;
        },

        /**
         * A simple way to call the Moodle core notification system.
         * Type can be either: success, warning, info, error
         *  Example:
         *  noti.callNoti({
         *      message: "This is a success test",
         *      type: "success"
         *  });
         * @param {obj} A simple object with the 'message' and 'type' of notification.
         * @return void
         */
        callNoti: function(data) {
            if (!data.hasOwnProperty('message')) {
                console.log("ERROR -> Notification was called but with no message, aborting.");
            }
            if (!data.hasOwnProperty('type')) {
                // default to info
                data.type = "info";
            }
            // Check to see if there is already a notification posted.
            if (this.checkNoti()) {
                notification.addNotification(data);
            }
        },

        /**
         * An alert using the Moodle core notification system.
         * Type can be either: success, warning, info, error
         *  Example:
         *  noti.callNoti({
         *      title: "Hello",
         *      message: "This is an alert",
         *  });
         * @param {obj} A simple object with the 'message' and 'type' of notification.
         * @return void
         */
        callAlert: function(data) {
            if (!data.hasOwnProperty('title')) {
                console.log("ERROR -> An alert was called but with no message, aborting.");
            }

            notification.addNotification(data);
        },

        /**
         * Store the reponse object to showcase a message after page reload.
         * @param {obj} Server Response {'success', 'data', 'msg'}
         *
         * @return void
         */
        storeMsg: function(data) {
            // Save data to sessionStorage
            sessionStorage.setItem('sent_delete_success', data.success);
            sessionStorage.setItem('sent_delete_msg', data.msg);
        },

        /**
         *  If a message is stored then show the notification and remove it.
         * @param void
         * @return void
         */
        showMsg: function() {
            // Save data to sessionStorage
            if (sessionStorage.getItem('sent_delete_msg')) {
                this.callNoti({
                    message: sessionStorage.getItem('sent_delete_msg'),
                    type: sessionStorage.getItem('sent_delete_success')
                });
                // Remove saved data from sessionStorage
                sessionStorage.removeItem('sent_delete_msg');
                sessionStorage.removeItem('sent_delete_success');
            }
        }
    };
});
