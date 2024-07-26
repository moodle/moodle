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
    'core_message/message_drawer_events',
    'core/aria',
    'core/pending',
],
function(
    $,
    PubSub,
    Str,
    MessageDrawerEvents,
    Aria,
    PendingPromise,
) {

    /* @var {object} routes Message drawer route elements and callbacks. */
    var routes = {};

    /* @var {object} history Store for route objects history. */
    var history = {};

    var SELECTORS = {
        CAN_RECEIVE_FOCUS: 'input:not([type="hidden"]), a[href], button, textarea, select, [tabindex]',
        ROUTES_BACK: '[data-route-back]'
    };

    /**
     * Add a route.
     *
     * @param {String} namespace Unique identifier for the Routes
     * @param {string} route Route config name.
     * @param {array} parameters Route parameters.
     * @param {callback} onGo Route initialization function.
     * @param {callback} getDescription Route initialization function.
     */
    var add = function(namespace, route, parameters, onGo, getDescription) {
        if (!routes[namespace]) {
            routes[namespace] = [];
        }

        routes[namespace][route] =
            {
                parameters: parameters,
                onGo: onGo,
                getDescription: getDescription
            };
    };

    /**
     * Go to a defined route and run the route callbacks.
     *
     * @param {String} namespace Unique identifier for the Routes
     * @param {string} newRoute Route config name.
     * @return {object} record Current route record with route config name and parameters.
     */
    var changeRoute = function(namespace, newRoute) {
        var newConfig;
        var pendingPromise = new PendingPromise(`message-drawer-router-${namespace}-${newRoute}`);

        // Check if the Route change call is made from an element in the app panel.
        var fromPanel = [].slice.call(arguments).some(function(arg) {
            return arg == 'frompanel';
        });
        // Get the rest of the arguments, if any.
        var args = [].slice.call(arguments, 2);
        var renderPromise = $.Deferred().resolve().promise();

        Object.keys(routes[namespace]).forEach(function(route) {
            var config = routes[namespace][route];
            var isMatch = route === newRoute;

            if (isMatch) {
                newConfig = config;
            }

            config.parameters.forEach(function(element) {
                // Some parameters may be null, or not an element.
                if (typeof element !== 'object' || element === null) {
                    return;
                }

                element.removeClass('previous');
                element.attr('data-from-panel', false);

                if (isMatch) {
                    if (fromPanel) {
                        // Set this attribute to let the conversation renderer know not to show a back button.
                        element.attr('data-from-panel', true);
                    }
                    element.removeClass('hidden');
                    Aria.unhide(element.get());
                } else {
                    // For the message index page elements in the left panel should not be hidden.
                    if (!element.attr('data-in-panel')) {
                        element.addClass('hidden');
                        Aria.hide(element.get());
                    } else if (newRoute == 'view-search' || newRoute == 'view-overview') {
                        element.addClass('hidden');
                        Aria.hide(element.get());
                    }
                }
            });
        });

        if (newConfig) {
            if (newConfig.onGo) {
                renderPromise = newConfig.onGo.apply(undefined, newConfig.parameters.concat(args));
                var currentFocusElement = $(document.activeElement);
                var hasFocus = false;
                var firstFocusable = null;

                // No need to start at 0 as we know that is the namespace.
                for (var i = 1; i < newConfig.parameters.length; i++) {
                    var element = newConfig.parameters[i];

                    // Some parameters may be null, or not an element.
                    if (typeof element !== 'object' || element === null) {
                        continue;
                    }

                    if (!firstFocusable) {
                        firstFocusable = element;
                    }

                    if (element.has(currentFocusElement).length) {
                        hasFocus = true;
                        break;
                    }
                }

                if (!hasFocus) {
                    // This page doesn't have focus yet so focus the first focusable
                    // element in the new view.
                    firstFocusable.find(SELECTORS.CAN_RECEIVE_FOCUS).filter(':visible').first().focus();
                }
            }
        }

        var record = {
            route: newRoute,
            params: args,
            renderPromise: renderPromise
        };

        PubSub.publish(MessageDrawerEvents.ROUTE_CHANGED, record);

        renderPromise.then(() => pendingPromise.resolve());
        return record;
    };

    /**
     * Go to a defined route and store the route history.
     *
     * @param {String} namespace Unique identifier for the Routes
     * @return {object} record Current route record with route config name and parameters.
     */
    var go = function(namespace) {
        var currentFocusElement = $(document.activeElement);

        var record = changeRoute.apply(namespace, arguments);
        var inHistory = false;

        if (!history[namespace]) {
            history[namespace] = [];
        }

        // History stores a unique list of routes. Check to see if the new route
        // is already in the history, if it is then forget all history after it.
        // This ensures there are no duplicate routes in history and that it represents
        // a linear path of routes (it never stores something like [foo, bar, foo])).
        history[namespace] = history[namespace].reduce(function(carry, previous) {
            if (previous.route === record.route) {
                inHistory = true;
            }

            if (!inHistory) {
                carry.push(previous);
            }

            return carry;
        }, []);

        var historylength = history[namespace].length;
        var previousRecord = historylength ? history[namespace][historylength - 1] : null;

        if (previousRecord) {
            var prevConfig = routes[namespace][previousRecord.route];
            var elements = prevConfig.parameters;

            // The first one will be the namespace, skip it.
            for (var i = 1; i < elements.length; i++) {
                // Some parameters may be null, or not an element.
                if (typeof elements[i] !== 'object' || elements[i] === null) {
                    continue;
                }

                elements[i].addClass('previous');
            }

            previousRecord.focusElement = currentFocusElement;

            if (prevConfig.getDescription) {
                // If the route has a description then set it on the back button for
                // the new page we're displaying.
                prevConfig.getDescription.apply(null, prevConfig.parameters.concat(previousRecord.params))
                    .then(function(description) {
                        return Str.get_string('backto', 'core_message', description);
                    })
                    .then(function(label) {
                        // Wait for the new page to finish rendering so that we know
                        // that the back button is visible.
                        return record.renderPromise.then(function() {
                            // Find the elements for the new route we displayed.
                            routes[namespace][record.route].parameters.forEach(function(element) {
                                // Some parameters may be null, or not an element.
                                if (typeof element !== 'object' || !element) {
                                    return;
                                }
                                // Update the aria label for the back button.
                                element.find(SELECTORS.ROUTES_BACK).attr('aria-label', label);
                            });
                        });
                    })
                    .catch(function() {
                        // Silently ignore.
                    });
            }
        }
        history[namespace].push(record);
        return record;
    };

    /**
     * Go back to the previous route record stored in history.
     *
     * @param {String} namespace Unique identifier for the Routes
     */
    var back = function(namespace) {
        if (history[namespace].length) {
            // Remove the current route.
            history[namespace].pop();
            var previous = history[namespace].pop();

            if (previous) {
                // If we have a previous route then show it.
                go.apply(undefined, [namespace, previous.route].concat(previous.params));
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
