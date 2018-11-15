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
 * A simple router for the message drawer that allows navigating between
 * the "pages" in the drawer.
 *
 * This module will maintain a linear history of the unique pages access
 * to allow navigating back.
 *
 * @module     core_message/message_drawer_router
 * @copyright  2018 Ryan Wyllie <ryan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
define(
[
    'jquery',
    'core/pubsub',
    'core/str',
    'core_message/message_drawer_events'
],
function(
    $,
    PubSub,
    Str,
    MessageDrawerEvents
) {

    /* @var {object} routes Message drawer route elements and callbacks. */
    var routes = {};

    /* @var {array} history Store for route objects history. */
    var history = [];

    var SELECTORS = {
        CAN_RECEIVE_FOCUS: 'input:not([type="hidden"]), a[href], button, textarea, select, [tabindex]',
        ROUTES_BACK: '[data-route-back]'
    };

    /**
     * Add a route.
     *
     * @param {string} route Route config name.
     * @param {array} elements Route container objects.
     * @param {callback} onGo Route initialization function.
     * @param {callback} getDescription Route initialization function.
     */
    var add = function(route, elements, onGo, getDescription) {
        routes[route] = {
            elements: elements,
            onGo: onGo,
            getDescription: getDescription
        };
    };

    /**
     * Go to a defined route and run the route callbacks.
     *
     * @param {string} newRoute Route config name.
     * @return {object} record Current route record with route config name and parameters.
     */
    var changeRoute = function(newRoute) {
        var newConfig;
        // Get the rest of the arguments, if any.
        var args = [].slice.call(arguments, 1);
        var renderPromise = $.Deferred().resolve().promise();

        Object.keys(routes).forEach(function(route) {
            var config = routes[route];
            var isMatch = route === newRoute;

            if (isMatch) {
                newConfig = config;
            }

            config.elements.forEach(function(element) {
                element.removeClass('previous');

                if (isMatch) {
                    element.removeClass('hidden');
                    element.attr('aria-hidden', false);
                } else {
                    element.addClass('hidden');
                    element.attr('aria-hidden', true);
                }
            });
        });

        if (newConfig) {
            if (newConfig.onGo) {
                renderPromise = newConfig.onGo.apply(undefined, newConfig.elements.concat(args));
                var currentFocusElement = $(document.activeElement);
                var hasFocus = false;

                for (var i = 0; i < newConfig.elements.length; i++) {
                    var element = newConfig.elements[i];

                    if (element.has(currentFocusElement).length) {
                        hasFocus = true;
                        break;
                    }
                }

                if (!hasFocus) {
                    // This page doesn't have focus yet so focus the first focusable
                    // element in the new view.
                    newConfig.elements[0].find(SELECTORS.CAN_RECEIVE_FOCUS).filter(':visible').first().focus();
                }
            }
        }

        var record = {
            route: newRoute,
            params: args,
            renderPromise: renderPromise
        };

        PubSub.publish(MessageDrawerEvents.ROUTE_CHANGED, record);

        return record;
    };

    /**
     * Go to a defined route and store the route history.
     *
     * @param {string} newRoute Route config name.
     * @return {object} record Current route record with route config name and parameters.
     */
    var go = function() {
        var currentFocusElement = $(document.activeElement);
        var record = changeRoute.apply(null, arguments);
        var inHistory = false;
        // History stores a unique list of routes. Check to see if the new route
        // is already in the history, if it is then forget all history after it.
        // This ensures there are no duplicate routes in history and that it represents
        // a linear path of routes (it never stores something like [foo, bar, foo])/
        history = history.reduce(function(carry, previous) {
            if (previous.route === record.route) {
                inHistory = true;
            }

            if (!inHistory) {
                carry.push(previous);
            }

            return carry;
        }, []);

        var previousRecord = history.length ? history[history.length - 1] : null;

        if (previousRecord) {
            var prevConfig = routes[previousRecord.route];
            prevConfig.elements.forEach(function(element) {
                element.addClass('previous');
            });

            previousRecord.focusElement = currentFocusElement;

            if (prevConfig.getDescription) {
                // If the route has a description then set it on the back button for
                // the new page we're displaying.
                prevConfig.getDescription.apply(null, prevConfig.elements.concat(previousRecord.params))
                    .then(function(description) {
                        return Str.get_string('backto', 'core_message', description);
                    })
                    .then(function(label) {
                        // Wait for the new page to finish rendering so that we know
                        // that the back button is visible.
                        return record.renderPromise.then(function() {
                            // Find the elements for the new route we displayed.
                            routes[record.route].elements.forEach(function(element) {
                                // Update the aria label for the back button.
                                element.find(SELECTORS.ROUTES_BACK).attr('aria-label', label);
                            });

                            return;
                        });
                    })
                    .catch(function() {
                        // Silently ignore.
                    });
            }
        }

        history.push(record);
        return record;
    };

    /**
     * Go back to the previous route record stored in history.
     */
    var back = function() {
        if (history.length) {
            // Remove the current route.
            history.pop();
            var previous = history.pop();

            if (previous) {
                // If we have a previous route then show it.
                go.apply(undefined, [previous.route].concat(previous.params));
                // Delay the focus 50 milliseconds otherwise it doesn't correctly
                // focus the element for some reason...
                window.setTimeout(function() {
                    previous.focusElement.focus();
                }, 50);
            }
        }
    };

    return {
        add: add,
        go: go,
        back: back
    };
});
