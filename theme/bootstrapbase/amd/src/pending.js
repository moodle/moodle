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
                classMatch: '.alert',
                start: 'close',
                end: 'closed',
            },
        ],

        carousel: [
            {
                classMatch: '.carousel',
                start: 'slide',
                end: 'slid',
            },
        ],

        collapse: [
            {
                classMatch: '.collapse',
                start: 'hide',
                end: 'hidden',
            },
            {
                classMatch: '.collapse',
                start: 'show',
                end: 'shown',
            },
        ],

        modal: [
            {
                classMatch: '.modal',
                start: 'hide',
                end: 'hidden',
            },
            {
                classMatch: '.modal',
                start: 'show',
                end: 'shown',
            },
        ],

        tab: [
            {
                classMatch: '.tab',
                start: 'hide',
                end: 'hidden',
            },
            {
                classMatch: '.tab',
                start: 'show',
                end: 'shown',
            },
        ],

        tooltip: [
            {
                classMatch: '.tooltip',
                start: 'hide',
                end: 'hidden',
            },
            {
                classMatch: '.tooltip',
                start: 'show',
                end: 'shown',
            },
        ],
    };

    Object.keys(moduleTransitions).forEach(function(key) {
        moduleTransitions[key].forEach(function(pair) {
            $(document.body).on(pair.start, pair.classMatch, function() {
                M.util.js_pending(pair.end);
            });

            $(document.body).on(pair.end, pair.classMatch, function() {
                M.util.js_complete(pair.end);
            });
        });
    });
});
