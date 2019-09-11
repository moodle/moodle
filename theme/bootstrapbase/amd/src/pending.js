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
define(['jquery', 'core/pending'], function($, Pending) {
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

    var getTransitionDurationFromElement = function(element) {
        var MILLISECONDS_MULTIPLIER = 1000;
        if (!element) {
            return 0;
        }
        // Get transition-duration of the element

        var transitionDuration = $(element).css('transition-duration');
        var transitionDelay = $(element).css('transition-delay');
        var floatTransitionDuration = parseFloat(transitionDuration);
        var floatTransitionDelay = parseFloat(transitionDelay);

        // Return 0 if element or transition duration is not found
        if (!floatTransitionDuration && !floatTransitionDelay) {
            return 0;
        }

        // If multiple durations are defined, take the first
        transitionDuration = transitionDuration.split(',')[0];
        transitionDelay = transitionDelay.split(',')[0];
        return (parseFloat(transitionDuration) + parseFloat(transitionDelay)) * MILLISECONDS_MULTIPLIER;
    };

    Object.keys(moduleTransitions).forEach(function(key) {
        moduleTransitions[key].forEach(function(pair) {
            $(document.body).on(pair.start, pair.classMatch, function(e) {
                var element = $(e.target);
                var pendingPromise = new Pending(pair.start);

                var called = false;
                $(document.body).one(pair.end, pair.classMatch, function() {
                    called = true;
                    pendingPromise.resolve();
                });

                setTimeout(function() {
                     if (!called) {
                        element.trigger(pair.end);
                     }
                }, getTransitionDurationFromElement(element));
            });
        });
    });
});
