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
define(['core/yui'], function(Y) {

    // Private variables and functions.

    return /** @alias module:core/notification */ {
        // Public variables and functions.
        /**
         * Wrap M.core.alert.
         *
         * @method alert
         * @param {string} title
         * @param {string} message
         * @param {string} yesLabel
         */
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

        /**
         * Wrap M.core.confirm.
         *
         * @method confirm
         * @param {string} title
         * @param {string} question
         * @param {string} yesLabel
         * @param {string} noLabel
         * @param {function} callback
         */
        confirm: function(title, question, yesLabel, noLabel, callback) {
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
                    callback();
                });
                modal.show();
            });
        },

        /**
         * Wrap M.core.exception.
         *
         * @method exception
         * @param {Error} ex
         */
        exception: function(ex) {
            // Fudge some parameters.
            if (ex.backtrace) {
                ex.lineNumber = ex.backtrace[0].line;
                ex.fileName = ex.backtrace[0].file;
                ex.fileName = '...' + ex.fileName.substr(ex.fileName.length - 20);
                ex.stack = ex.debuginfo;
                ex.name = ex.errorcode;
            }
            Y.use('moodle-core-notification-exception', function () {
                var modal = new M.core.exception(ex);

                modal.show();
            });
        }
    };
});
