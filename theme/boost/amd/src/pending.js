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
 * Add Pending JS checks to stock Bootstrap transitions.
 *
 * @module     theme_boost/pending
 * @copyright  2019 Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
define(['jquery'], function($) {
    var moduleTransitions = {
        alert: [
            // Alert.
            {
                start: 'close',
                end: 'closed',
            },
        ],

        carousel: [
            {
                start: 'slide',
                end: 'slid',
            },
        ],

        collapse: [
            {
                start: 'hide',
                end: 'hidden',
            },
            {
                start: 'show',
                end: 'shown',
            },
        ],

        dropdown: [
            {
                start: 'hide',
                end: 'hidden',
            },
            {
                start: 'show',
                end: 'shown',
            },
        ],

        modal: [
            {
                start: 'hide',
                end: 'hidden',
            },
            {
                start: 'show',
                end: 'shown',
            },
        ],

        popover: [
            {
                start: 'hide',
                end: 'hidden',
            },
            {
                start: 'show',
                end: 'shown',
            },
        ],

        tab: [
            {
                start: 'hide',
                end: 'hidden',
            },
            {
                start: 'show',
                end: 'shown',
            },
        ],

        toast: [
            {
                start: 'hide',
                end: 'hidden',
            },
            {
                start: 'show',
                end: 'shown',
            },
        ],

        tooltip: [
            {
                start: 'hide',
                end: 'hidden',
            },
            {
                start: 'show',
                end: 'shown',
            },
        ],
    };

    Object.keys(moduleTransitions).forEach(function(key) {
        moduleTransitions[key].forEach(function(pair) {
            var eventStart = pair.start + '.bs.' + key;
            var eventEnd = pair.end + '.bs.' + key;
            $(document.body).on(eventStart, function(e) {
                M.util.js_pending(eventEnd);
                $(e.target).one(eventEnd, function() {
                    M.util.js_complete(eventEnd);
                });
            });
        });
    });
});
