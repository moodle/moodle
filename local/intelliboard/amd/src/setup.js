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
    "jquery",
    "local_intelliboard/setup_moodle_settings",
    "local_intelliboard/setup_terms",
    "local_intelliboard/setup_authentication",
], function($, moodleSettings, terms, authentication) {
    var setup = {
        activeRequest: false,
        serviceToken: "",
        useRestProtocol: true,
        init: function() {
            setup.moodleSettingsHandler();
            setup.termsHandler();
        },

        moodleSettingsHandler: function() {
            $(".moodle-settings .tab-header-button button.next, .moodle-settings .tab-body-button button.next").unbind("click");
            $(".moodle-settings .tab-header-button button.next, .moodle-settings .tab-body-button button.next").on("click", function() {
                if (setup.activeRequest) {
                    return;
                }

                setup.activeRequest = true;
                moodleSettings.hideErrors();
                moodleSettings.loaderContainer.removeClass("hidden");
                moodleSettings.save(function(response) {
                    setup.activeRequest = false;
                    moodleSettings.loaderContainer.addClass("hidden");

                    if (response.status === "success") {
                        setup.serviceToken = JSON.parse(response.data).token;
                        setup.useRestProtocol = $("#rest_protocol").is(":checked");
                        moodleSettings.closeTab();
                        terms.openTab();
                    } else {
                        moodleSettings.showErrors(response.data);
                    }
                });
            });

            $(".moodle-settings .choose-group .item").unbind("click");
            $(".moodle-settings .choose-group .item").on("click", function() {
                $(".moodle-settings .choose-group .item").removeClass("active");
                $(".moodle-settings .choose-group .item input").prop("checked", false).attr("checked", "");
                $(this).addClass("active");
                $(this).find("input").attr("checked", "checked").prop("checked", true);
            });
        },

        termsHandler: function() {
            $(".tab-item.terms .tab-header-button button.next, .tab-item.terms .tab-body-button button.next").unbind("click");
            $(".tab-item.terms .tab-header-button button.next, .tab-item.terms .tab-body-button button.next").on("click", function() {
                if (setup.activeRequest) {
                    return;
                }

                terms.hideErrors();
                terms.loaderContainer.removeClass("hidden");

                if (terms.termsIsAccepted()) {
                    terms.loaderContainer.addClass("hidden");
                    terms.closeTab();
                    setup.authenticationHandler();
                } else {
                    terms.showErrorMessage(function() {
                        terms.loaderContainer.addClass("hidden")
                    });
                }
            });

            $(".tab-item.terms .tab-header-button button.back, .tab-item.terms .tab-body-button button.back").on("click", function() {
                terms.closeTab();
                moodleSettings.openTab();
            });
        },

        authenticationHandler: function() {
            var email = $("#subscription_email").val();

            authentication.openTab();
            authentication.setFormEmail(email);
            authentication.loaderContainer.removeClass("hidden");
            authentication.checkEmail(email, function (response) {
                if (response.email_exists) {
                    authentication.showLoginForm();
                    authentication.loginHandler(setup.serviceToken, setup.useRestProtocol, function() {
                        authentication.closeTab();
                        $(".setup-wrapper .tab-item.congrats").removeClass("closed");
                    });
                } else {
                    authentication.showRegisterForm();
                    authentication.registerHandler(setup.serviceToken, setup.useRestProtocol, function() {
                        authentication.closeTab();
                        $(".setup-wrapper .tab-item.congrats").removeClass("closed");
                    });
                }
            });

            $(".tab-item.authentication .tab-header-button button.back, .tab-item.authentication .tab-body-button button.back").on("click", function() {
                authentication.closeTab();
                authentication.hideLoginForm();
                authentication.hideRegisterForm();
                terms.openTab();
            });
        },
    };

    return setup;
});