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
 * @package    block_quickmail
 * @copyright  2008 onwards Louisiana State University
 * @copyright  2008 onwards Chad Mazilly, Robert Russo, Jason Peak, Dave Elliott, Adam Zapletal, Philip Cali, David Lowe
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define(['jquery', 'core/notification'], function($, notification) {
    'use strict';

    return {

        /**
         * A simple way to call the Moodle core notification system.
         * Type can be either: success, warning, info, error
         *  Example:
         *  noti.callNoti({
         *      message: "This is a success test",
         *      type: "success"
         *  });
         * @param {obj} data A simple object with the 'message' and 'type' of notification.
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
            notification.addNotification(data);
        },
        /**
         * Store the reponse object to showcase a message after reload.
         * @param {obj} data Server Response {'success', 'data', 'msg'}
         *
         * @return void
         */
        storeMsg: function(data) {
            // Save data to sessionStorage
            if (data.hasOwnProperty('success')) {
                sessionStorage.setItem('sent_delete_success', data.success);
                sessionStorage.setItem('sent_delete_msg', data.msg);
            } else {
                console.log("NOTI -> Error: There was an error with the data from the server, please contact Moodle Dev Team.");
            }
        },

        /**
         *  If a message is stored then show the notification and remove it.
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