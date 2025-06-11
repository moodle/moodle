/**
 * This file is part of Moodle - http://moodle.org/
 *
 * Moodle is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Moodle is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Moodle.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @package
 * @author    Guy Thomas
 * @copyright Copyright (c) 2016 Open LMS
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define(['jquery', 'core/notification', 'core/ajax', 'core/templates', 'core/str', 'theme_snap/util'],

    function($, notification, ajax, templates, str, util) {

        // Module level variables.
        var loginErrorShown = false;
        var loggingOut = false;
        var redirectInProgress = false;

        // Module level code.
        $(document).ready(function() {
            $('#snap-pm-logout').click(function() {
                loggingOut = true;
            });
        });

        /**
         * This feature is simply to work around this issue - MDL-54551.
         * If the core moodle issue is ever fixed we might not require this module.
         */
        return {

            /**
             * If there is an error in this response then show the best error message for the user.
             *
             * @param {object} response
             * @param {string} failAction
             * @param {string} failMsg
             * @returns {function} promise - resolves with a boolean (true if error shown)
             */
            ifErrorShowBestMsg: function(response, failAction, failMsg) {

                var dfd = $.Deferred();

                /**
                 * Error notification function for non logged out issues.
                 * @param {object} response
                 * @returns {function} promise
                 */
                var errorNotification = function(response) {
                    var endfd = $.Deferred();
                    if (failMsg) {
                        notification.alert(M.util.get_string('error', 'moodle'),
                            failMsg, M.util.get_string('ok', 'moodle'));
                    } else {
                        if (response.backtrace) {
                            notification.exception(response);
                        } else {
                            var errorstr;
                            if (response.error) {
                                errorstr = response.error;
                                if (response.stacktrace) {
                                    errorstr = '<div>' + errorstr + '<pre>' + response.stacktrace + '</pre></div>';
                                }
                            } else {
                                if (response.errorcode && response.message) {
                                    errorstr = response.message;
                                } else {
                                    // Don't display any error messages as we don't know what the error is.
                                    endfd.resolve(false);
                                    return endfd.promise();
                                }
                            }
                            notification.alert(M.util.get_string('error', 'moodle'),
                                errorstr, M.util.get_string('ok', 'moodle'));
                        }
                    }
                    util.whenTrue(function() {
                        var isvisible = $('.modal-dialog').is(':visible');
                        return isvisible;
                    }, function() {
                        endfd.resolve(true);
                    }, true);
                    return endfd.promise();
                };

                if (loginErrorShown) {
                    // We already have a login error message.
                    dfd.resolve(true);
                    return dfd.promise();
                }

                if (loggingOut) {
                    // No point in showing error messages if we are logging out.
                    dfd.resolve(true);
                    return dfd.promise();
                }

                if (typeof response !== 'object') {
                    try {
                        var jsonObj = JSON.parse(response);
                        response = jsonObj;
                    } catch (e) {
                        // Not caring about exceptions.
                    }
                }

                if (typeof response === 'undefined') {
                    return dfd.resolve(false).promise();
                }

                if (response.errorcode && response.errorcode === "sitepolicynotagreed") {
                    var redirect = M.cfg.wwwroot + '/user/policy.php';
                    if (window.location != redirect && !redirectInProgress && $('#primary-nav').is(':visible')) {
                        window.location = redirect;
                        // Prevent further error messages from showing as a redirect is in progress.
                        redirectInProgress = true;
                        loginErrorShown = true;
                        dfd.resolve(true);
                    } else {
                        dfd.resolve(false);
                    }
                    return dfd.promise();
                }

                if (response.error || response.errorcode) {

                    if (M.snapTheme.forcePassChange) {
                        var pwdChangeUrl = M.cfg.wwwroot + '/login/change_password.php';
                        // When a force password change is in effect, warn user in personal menu and redirect to
                        // password change page if appropriate.
                        if ($('#snap-pm-content').length) {
                            str.get_string('forcepwdwarningpersonalmenu', 'theme_snap', pwdChangeUrl).then(
                                function(forcePwdWarning) {
                                    var alertMsg = {"message": forcePwdWarning, "extraclasses": "force-pwd-warning"};
                                    return templates.render('core/notification_warning', alertMsg);
                                }
                            ).then(
                                function(result) {
                                    $('#snap-pm-content').html('<br />' + result);
                                    dfd.resolve(true);
                                }
                            );
                            if ($('#snap-pm-content').is(':visible')) {
                                // If the personal menu is open then it should have a message in it informing the user
                                // that they need to change their password to proceed.
                                loginErrorShown = true;
                                return dfd.promise();
                            }
                        }

                        if (window.location != pwdChangeUrl) {
                            window.location = pwdChangeUrl;
                        }
                        // Regardless of if error was shown, we only want this redirect to happen once so set
                        // loginErrorShown to true.
                        loginErrorShown = true;
                        dfd.resolve(true);
                        return dfd.promise();
                    }

                    // Ajax call login status function to see if we are logged in or not.
                    // Note, we can't use a moodle web service for this ajax call because it will not provide
                    // an error response that we can rely on - see MDL-54551.
                    failAction = failAction ? failAction : '';
                    return $.ajax({
                        type: "POST",
                        async: true,
                        data: {
                            "sesskey": M.cfg.sesskey,
                            "failedactionmsg": failAction
                        },
                        url: M.cfg.wwwroot + '/theme/snap/rest.php?action=get_loginstatus'
                    }).then(function(thisResp) {
                        if (loginErrorShown) {
                            dfd.resolve(true);
                            return dfd.promise();
                        }
                        // Show login error message or original error message.
                        if (!thisResp.loggedin) {
                            // Hide ALL confirmation dialog 2nd buttons and close buttons.
                            // Note - this is not ideal but at this point we need to log in anyway, so not
                            // an issue.
                            $('<style>' +
                                '.confirmation-dialogue .confirmation-buttons input:nth-of-type(2), ' +
                                '.moodle-dialogue-base.moodle-dialogue-confirm button.yui3-button.closebutton' +
                                '{ display : none }' +
                                '</style>'
                            ).appendTo('head');
                            notification.confirm(
                                thisResp.loggedouttitle,
                                thisResp.loggedoutmsg,
                                thisResp.loggedoutcontinue,
                                ' ',
                                function() {
                                    window.location = M.cfg.wwwroot + '/login/index.php';
                                }
                            );
                            loginErrorShown = true;
                            dfd.resolve(true);
                            return dfd.promise();
                        } else {
                            // This is not a login issue, show original error message.
                            return errorNotification(response); // Returns promise which is resolved when dialog shown.
                        }
                    });
                }

                dfd.resolve(false);
                return dfd.promise();
            }
        };
    }
);
