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
 * Javascript Module to help out if registration of pluign via curl fails.
 *
 * @module registration
 * @package course/format
 * @subpackage tiles
 * @copyright 2019 David Watson {@link http://evolutioncode.uk}
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since Moodle 3.3
 */

/* eslint space-before-function-paren: 0 */

define(["jquery", "core/notification", "core/config", "core/str"], function ($, Notification, config, str) {
    "use strict";

    return {
        attemptRegistration: function(sesskey, serverUrl, data) {
            data.useragent = navigator.userAgent;
            data.browserlanguages = navigator.languages;
            $(document).ready(function () {
                $.ajax({
                    url: serverUrl,
                    type: 'POST',
                    crossDomain: true,
                    contentType: 'application/json',
                    data: JSON.stringify(data),
                    dataType: 'json',
                    success: function(data) {
                        if (data.status) {
                            var stringKeys = [
                                {key: "registration", component: "admin"},
                                {key: "registeredthanks", component: "format_tiles"},
                                {key: "registerclicktocomplete", component: "format_tiles"},
                                {key: "ok", component: "format_tiles"},
                                {key: "cancel"},
                            ];
                            str.get_strings(stringKeys).done(function (s) {
                                Notification.confirm(
                                    s[0],
                                    s[1] + " " + s[2],
                                    s[3],
                                    s[4],
                                    function() {
                                        window.location.href = config.wwwroot
                                            + '/course/format/tiles/register.php?key=' + data.key + "&sesskey=" + sesskey;
                                    },
                                    function() {
                                        window.location.href = config.wwwroot + '/admin/settings.php?section=formatsettingtiles';
                                    }
                                );
                            });
                        }
                    },
                    error: Notification.exception
                });
            });
        }
    };

});