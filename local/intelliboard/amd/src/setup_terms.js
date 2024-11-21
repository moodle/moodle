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
    "jquery", "core/str"
], function($, str) {
    var terms = {
        errorsContainer: $(".tab-item.terms .warnings-block"),
        loaderContainer: $(".tab-item.terms .loader"),
        errorMessage: '',
        isInit: false,

        showErrorMessage: function(callback) {
            if (!terms.errorMessage) {
                var termsErrorPromises = str.get_string("please_accept_terms_and_conditions", "local_intelliboard");

                $.when(termsErrorPromises).done(function(localizedEditString) {
                    terms.errorMessage = localizedEditString;
                    terms.errorsContainer.html(localizedEditString);
                    terms.errorsContainer.removeClass("hidden");
                    callback();
                });
            } else {
                terms.errorsContainer.html(terms.errorMessage);
                terms.errorsContainer.removeClass("hidden");
                callback();
            }
        },

        hideErrors: function() {
            terms.errorsContainer.html("");
            terms.errorsContainer.addClass("hidden");
        },

        closeTab: function() {
            $(".tab-item.terms").addClass("closed");
        },

        openTab: function() {
            $(".tab-item.terms").removeClass("closed");
        },

        termsIsAccepted: function() {
            var terms = $("#termsField").is(":checked");
            var shield = $("#shieldField").is(":checked");
            var privacy = $("#privacyField").is(":checked");

            return terms && shield && privacy;
        }
    };

    return terms;
});