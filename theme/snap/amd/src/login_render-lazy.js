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
 * @copyright Copyright (c) 2020 Open LMS (https://www.openlms.net)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Login option rendering validation function.
 */
define(['jquery'],
    function($) {

        'use strict';

        var self = {};
        self.enabledLoginOptions = [];
        self.enabledLoginOptions['ENABLED_LOGIN_BOTH']        = '0';
        self.enabledLoginOptions['ENABLED_LOGIN_MOODLE']      = '1';
        self.enabledLoginOptions['ENABLED_LOGIN_ALTERNATIVE'] = '2';

        self.enabledLoginOrder = [];
        self.enabledLoginOrder['ORDER_LOGIN_MOODLE_FIRST']      = '0';
        self.enabledLoginOrder['ORDER_LOGIN_ALTERNATIVE_FIRST'] = '1';

        /**
         * AMD return object.
         */
        return {
            /**
             * Snap login option render
             * @param {int} loginrender
             * @param {int} loginorder
             */
            loginRender : function (loginrender, loginorder) {
                var show = function (render, order, cb) {
                    switch (render) {
                        case self.enabledLoginOptions['ENABLED_LOGIN_MOODLE']:
                            $('.snap-login-option').show();
                            $('#snap-alt-login').hide();
                            cb();
                            break;

                        case self.enabledLoginOptions['ENABLED_LOGIN_ALTERNATIVE']:
                            $('.snap-login-option').show();
                            $('#login').hide();
                            $('#snap_alt_login_hr_first').hide();
                            cb();
                            break;

                        default:
                            $('.snap-login-option').show();
                            if (order == self.enabledLoginOrder['ORDER_LOGIN_ALTERNATIVE_FIRST']){
                                $('#login').remove().insertAfter($('#snap-alt-login'));
                                $('#snap_alt_login_hr_first').hide();
                                $('#login_hr_first').show();
                            }
                            cb();
                    }
                };
                show(loginrender, loginorder, function () {
                    $('.snap-log-in-more').hide();
                    $('.snap-log-in-more').css('visibility','hidden');
                });

                // This code is to add the floating label in the password field when the password toggle is enabled.
                const passwordToggleObserver = function(mutationsList, observer) {
                    for (const mutation of mutationsList) {
                        if (mutation.type === 'childList') {
                            const passwordToggle = document.querySelector('.snap-login .input-group-append');
                            const passwordFloatingLabel = document.querySelector('.snap-login label[for="password"]');
                            const passwordInputField = document.querySelector('.snap-login .toggle-sensitive-wrapper');
                            if (passwordToggle && passwordFloatingLabel && passwordInputField) {
                                passwordInputField.appendChild(passwordFloatingLabel);
                                observer.disconnect();
                            }
                        }
                    }
                };
                if (document.querySelector('.snap-login')) {
                    // In Snap we have a floating label in the login input fields. Core uses a JS code to add a
                    // wrapper container and add the password toggle. We need to move the label inside that wrapper
                    // only when the toggle exists to make the floating label work aslo with the wrapper. We need a
                    // mutation observer because we need to wait for that element to exist to move the label.
                    const observer = new MutationObserver(passwordToggleObserver);
                    observer.observe(document.body, { childList: true, subtree: true });
                }
            }
        };
    }
);
