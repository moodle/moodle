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
 * Poll the server to keep the session alive.
 *
 * @module     core/network
 * @package    core
 * @copyright  2019 Damyon Wiese
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
define(['jquery', 'core/ajax', 'core/config', 'core/notification', 'core/str'],
        function($, Ajax, Config, Notification, Str) {

    var started = false;
    var warningDisplayed = false;
    var keepAliveFrequency = 0;
    var requestTimeout = 0;
    var keepAliveMessage = false;
    var sessionTimeout = false;
    // 1/10 of session timeout, max of 10 minutes.
    var checkFrequency = Math.min((Config.sessiontimeout / 10), 600) * 1000;
    // 1/5 of sessiontimeout.
    var warningLimit = checkFrequency * 2;

    /**
     * The session time has expired - we can't extend it now.
     */
    var timeoutSessionExpired = function() {
        sessionTimeout = true;
    };

    /**
     * Ping the server to keep the session alive.
     *
     * @return {Promise}
     */
    var touchSession = function() {
        var request = {
            methodname: 'core_session_touch',
            args: { }
        };

        if (sessionTimeout) {
            // We timed out before we extended the session.
            return Str.get_strings([
                {key: 'sessionexpired', component: 'error'},
                {key: 'sessionerroruser', component: 'error'}
            ]).then(function(strings) {
                Notification.alert(
                    strings[0], // Title.
                    strings[1] // Message.
                );
                return true;
            }).fail(Notification.exception);
        } else {
            return Ajax.call([request], true, true, false, requestTimeout)[0].then(function() {
                if (keepAliveFrequency > 0) {
                    setTimeout(touchSession, keepAliveFrequency);
                }
                return true;
            }).fail(function() {
                Notification.alert('', keepAliveMessage);
            });
        }
    };

    /**
     * Ask the server how much time is remaining in this session and
     * show confirm/cancel notifications if the session is about to run out.
     *
     * @return {Promise}
     */
    var checkSession = function() {
        var request = {
            methodname: 'core_session_time_remaining',
            args: { }
        };

        sessionTimeout = false;
        return Ajax.call([request], true, true, true)[0].then(function(args) {
            if (args.userid <= 0) {
                return false;
            }
            if (args.timeremaining < 0) {
                Str.get_strings([
                    {key: 'sessionexpired', component: 'error'},
                    {key: 'sessionerroruser', component: 'error'}
                ]).then(function(strings) {
                    Notification.alert(
                        strings[0], // Title.
                        strings[1] // Message.
                    );
                    return true;
                }).fail(Notification.exception);

            } else if (args.timeremaining * 1000 < warningLimit && !warningDisplayed) {
                // If we don't extend the session before the timeout - warn.
                setTimeout(timeoutSessionExpired, args.timeremaining * 1000);
                warningDisplayed = true;
                Str.get_strings([
                    {key: 'norecentactivity', component: 'moodle'},
                    {key: 'sessiontimeoutsoon', component: 'moodle'},
                    {key: 'extendsession', component: 'moodle'},
                    {key: 'cancel', component: 'moodle'}
                ]).then(function(strings) {
                    Notification.confirm(
                        strings[0], // Title.
                        strings[1], // Message.
                        strings[2], // Extend session.
                        strings[3], // Cancel.
                        function() {
                            touchSession();
                            warningDisplayed = false;
                            // First wait is half the session timeout.
                            setTimeout(checkSession, checkFrequency * 5);
                            return true;
                        },
                        function() {
                            warningDisplayed = false;
                            setTimeout(checkSession, checkFrequency);
                        }
                    );
                    return true;
                }).fail(Notification.exception);
            } else {
                setTimeout(checkSession, checkFrequency);
            }
            return true;
        });
        // We do not catch the fails from the above ajax call because they will fail when
        // we are not logged in - we don't need to take any action then.
    };

    /**
     * Start calling a function to check if the session is still alive.
     */
    var start = function() {
        if (keepAliveFrequency > 0) {
            setTimeout(touchSession, keepAliveFrequency);
        } else {
            // First wait is half the session timeout.
            setTimeout(checkSession, checkFrequency * 5);
        }
    };

    /**
     * Don't allow more than one of these polling loops in a single page.
     */
    var init = function() {
        // We only allow one concurrent instance of this checker.
        if (started) {
            return;
        }
        started = true;

        start();
    };

    /**
     * Start polling with more specific values for the frequency, timeout and message.
     *
     * @param {number} freq How ofter to poll the server.
     * @param {number} timeout The time to wait for each request to the server.
     * @param {string} message The message to display if the session is going to time out.
     */
    var keepalive = function(freq, timeout, message) {
        // We only allow one concurrent instance of this checker.
        if (started) {
            return;
        }
        started = true;

        keepAliveFrequency = freq * 1000;
        keepAliveMessage = message;
        requestTimeout = timeout * 1000;
        start();
    };

    return {
        keepalive: keepalive,
        init: init
    };
});
