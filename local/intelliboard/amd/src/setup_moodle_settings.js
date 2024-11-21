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
    var moodleSettings = {
        errorsContainer: $(".moodle-settings .warnings-block"),
        loaderContainer: $(".moodle-settings .loader"),
        save: function(callback) {
            if (validator.validateEmail($("#subscription_email").val())) {
                var baseSettingsPromises = ajax.call([
                    {
                        methodname: "local_intelliboard_setup_base_settings", args: {
                            params: {
                                webservice: $("#webservice").is(":checked"),
                                rest_protocol: $("#rest_protocol").is(":checked"),
                                soap_protocol: $("#soap_protocol").is(":checked"),
                                user_identifier: $("#usernameorid").val(),
                                enable_tracking: $("#enable_tracking").is(":checked"),
                                enable_sso_link: $("#enable_sso_link").is(":checked"),
                                email: $("#subscription_email").val()
                            }
                        }
                    },
                ]);

                baseSettingsPromises[0].done(function(response) {
                    callback(response);
                });

                baseSettingsPromises[0].fail(function() {
                    var stringPromises = str.get_string('server_error', "local_intelliboard");

                    $.when(stringPromises).done(function(localizedEditString) {
                        callback({status: "error", data: '<p>' + localizedEditString + '</p>'});
                    });
                });
            } else {
                var stringPromises = str.get_string('invalid_email', "local_intelliboard");

                $.when(stringPromises).done(function(localizedEditString) {
                    callback({status: "error", data: '<p>' + localizedEditString + '</p>'});
                });
            }
        },

        showErrors: function(errorsText) {
            moodleSettings.errorsContainer.html(errorsText);
            moodleSettings.errorsContainer.removeClass("hidden");
        },

        hideErrors: function() {
            moodleSettings.errorsContainer.html("");
            moodleSettings.errorsContainer.addClass("hidden");
        },

        closeTab: function() {
            $(".moodle-settings").addClass("closed");
        },

        openTab: function() {
            $(".moodle-settings").removeClass("closed");
        }
    };

    return moodleSettings;
});