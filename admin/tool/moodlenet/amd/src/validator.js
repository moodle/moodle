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
 * Our validator that splits the user's input then fires off to a webservice
 *
 * @module     tool_moodlenet/validator
 * @copyright  2020 Mathew May <mathew.solutions>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
define(['jquery', 'core/ajax', 'core/str', 'core/notification'], function($, Ajax, Str, Notification) {
    /**
     * Handle form validation
     *
     * @method validation
     * @param {HTMLElement} inputElement The element the user entered text into.
     * @return {Promise} Was the users' entry a valid profile URL?
     */
    var validation = function validation(inputElement) {
        var inputValue = inputElement.value;

        // They didn't submit anything or they gave us a simple string that we can't do anything with.
        if (inputValue === "" || !inputValue.includes("@")) {
            // Create a promise and immediately reject it.
            $.when(Str.get_string('profilevalidationerror', 'tool_moodlenet')).then(function(strings) {
                return Promise.reject().catch(function() {
                    return {result: false, message: strings[0]};
                });
            }).fail(Notification.exception);
        }

        return Ajax.call([{
            methodname: 'tool_moodlenet_verify_webfinger',
            args: {
                profileurl: inputValue,
                course: inputElement.dataset.courseid,
                section: inputElement.dataset.sectionid
            }
        }])[0].then(function(result) {
            return result;
        }).catch();
    };
    return {
        validation: validation,
    };
});
