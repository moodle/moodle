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
 * This plugin provides access to Moodle data in form of analytics and reports in real time.
 *
 *
 * @package    local_intelliboard
 * @copyright  2020 IntelliBoard, Inc
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @website    https://intelliboard.net/
 */

define([
    "jquery", "core/ajax", "local_intelliboard/validator", "core/str"
], function($, ajax, validator, str) {
    var authentication = {
        errorsContainer: $(".tab-item.authentication .warnings-block"),
        loaderContainer: $(".tab-item.authentication .loader"),

        showErrors: function(errorsText) {
            authentication.errorsContainer.html(errorsText);
            authentication.errorsContainer.removeClass("hidden");
        },

        hideErrors: function() {
            authentication.errorsContainer.html("");
            authentication.errorsContainer.addClass("hidden");
        },

        closeTab: function() {
            $(".tab-item.authentication").addClass("closed");
        },

        openTab: function() {
            $(".tab-item.authentication").removeClass("closed");
        },

        showLoginForm: function() {
            $(".tab-item .tab-body .login-form").removeClass("hidden");
        },

        hideLoginForm: function() {
            $(".tab-item .tab-body .login-form").addClass("hidden");
        },

        showRegisterForm: function() {
            $(".tab-item .tab-body .register-form").removeClass("hidden");
        },

        hideRegisterForm: function() {
            $(".tab-item .tab-body .register-form").addClass("hidden");
        },

        setFormEmail: function(email) {
            $("#loginEmail").val(email);
            $("#registerEmail").val(email);
        },

        checkEmail: function(email, callback) {
            if (validator.validateEmail(email)) {
                var checkEmailPromises = ajax.call([{
                    methodname: "local_intelliboard_setup_check_email", args: {
                        email: $("#subscription_email").val()
                    }
                }]);

                checkEmailPromises[0].done(function(response) {
                    callback(response);
                });

                checkEmailPromises[0].fail(function() {
                    callback({email_exists: false});
                });
            } else {
                callback({email_exists: false});
            }
        },

        loginHandler: function(token, useRestProtocol, callback) {
            authentication.loaderContainer.addClass("hidden");

            $(".tab-body .login-form .sign-in").unbind("click");
            $(".tab-body .login-form .sign-in").on("click", function() {
                var email = $("#loginEmail").val();
                var password = $("#loginPassword").val();

                authentication.loaderContainer.removeClass("hidden");
                authentication.hideErrors();

                if (!email || !password) {
                    var allFieldsRequiredPromises = str.get_string('all_fields_required', "local_intelliboard");

                    $.when(allFieldsRequiredPromises).done(function(localizedEditString) {
                        authentication.loaderContainer.addClass("hidden");
                        authentication.showErrors(localizedEditString);
                    });
                } else {
                    var loginPromises = ajax.call([{
                        methodname: "local_intelliboard_setup_login",
                        args: {
                            email: email,
                            password: password,
                            moodle_service_token: token,
                            restProtocol: useRestProtocol,
                        }
                    }]);

                    loginPromises[0].done(function(response) {
                        authentication.loaderContainer.addClass("hidden");

                        if (response.status === "error") {
                            authentication.showErrors(response.message);
                        } else {
                            callback();
                        }
                    });

                    loginPromises[0].fail(function() {
                        var stringPromises = str.get_string('server_error', "local_intelliboard");

                        $.when(stringPromises).done(function(localizedEditString) {
                            authentication.loaderContainer.addClass("hidden");
                            authentication.showErrors('<p>' + localizedEditString + '</p>');
                        });
                    });
                }
            });
        },

        registerHandler: function(token, useRestProtocol, callback) {
            authentication.loaderContainer.addClass("hidden");

            $(".tab-body .register-form .sign-up").unbind("click");
            $(".tab-body .register-form .sign-up").on("click", function() {
                var email = $("#registerEmail").val();
                var password = $("#registerPassword").val();
                var name = $("#registerName").val();

                authentication.loaderContainer.removeClass("hidden");
                authentication.hideErrors();

                if (!email || !password || !name) {
                    var allFieldsRequiredPromises = str.get_string('all_fields_required', "local_intelliboard");

                    $.when(allFieldsRequiredPromises).done(function(localizedEditString) {
                        authentication.loaderContainer.addClass("hidden");
                        authentication.showErrors(localizedEditString);
                    });
                } else {
                    var registerPromises = ajax.call([{
                        methodname: "local_intelliboard_setup_register",
                        args: {
                            name: name,
                            email: email,
                            password: password,
                            country: $("#registerCountry").val(),
                            moodle_service_token: token,
                            restProtocol: useRestProtocol,
                        }
                    }]);

                    registerPromises[0].done(function(response) {
                        authentication.loaderContainer.addClass("hidden");

                        if (response.status === "error") {
                            authentication.showErrors(response.message);
                        } else {
                            callback();
                        }
                    });

                    registerPromises[0].fail(function() {
                        var stringPromises = str.get_string('server_error', "local_intelliboard");

                        $.when(stringPromises).done(function(localizedEditString) {
                            authentication.loaderContainer.addClass("hidden");
                            authentication.showErrors('<p>' + localizedEditString + '</p>');
                        });
                    });
                }
            });
        }
    };

    return authentication;
});