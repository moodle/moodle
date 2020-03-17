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
 * Controls the message drawer.
 *
 * @module     core_message/message_drawer
 * @copyright  2018 Ryan Wyllie <ryan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
define(
[
    'jquery',
    'core/custom_interaction_events',
    'core/pubsub',
    'core_message/message_drawer_view_contact',
    'core_message/message_drawer_view_contacts',
    'core_message/message_drawer_view_conversation',
    'core_message/message_drawer_view_group_info',
    'core_message/message_drawer_view_overview',
    'core_message/message_drawer_view_search',
    'core_message/message_drawer_view_settings',
    'core_message/message_drawer_router',
    'core_message/message_drawer_routes',
    'core_message/message_drawer_events',
    'core/pending',
    'core/drawer',
],
function(
    $,
    CustomEvents,
    PubSub,
    ViewContact,
    ViewContacts,
    ViewConversation,
    ViewGroupInfo,
    ViewOverview,
    ViewSearch,
    ViewSettings,
    Router,
    Routes,
    Events,
    Pending,
    Drawer
) {

    var SELECTORS = {
        DRAWER: '[data-region="right-hand-drawer"]',
        JUMPTO: '.popover-region [data-region="jumpto"]',
        PANEL_BODY_CONTAINER: '[data-region="panel-body-container"]',
        PANEL_HEADER_CONTAINER: '[data-region="panel-header-container"]',
        VIEW_CONTACT: '[data-region="view-contact"]',
        VIEW_CONTACTS: '[data-region="view-contacts"]',
        VIEW_CONVERSATION: '[data-region="view-conversation"]',
        VIEW_GROUP_INFO: '[data-region="view-group-info"]',
        VIEW_OVERVIEW: '[data-region="view-overview"]',
        VIEW_SEARCH: '[data-region="view-search"]',
        VIEW_SETTINGS: '[data-region="view-settings"]',
        ROUTES: '[data-route]',
        ROUTES_BACK: '[data-route-back]',
        HEADER_CONTAINER: '[data-region="header-container"]',
        BODY_CONTAINER: '[data-region="body-container"]',
        FOOTER_CONTAINER: '[data-region="footer-container"]',
    };

    /**
     * Get elements for route.
     *
     * @param {String} namespace Unique identifier for the Routes
     * @param {Object} root The message drawer container.
     * @param {string} selector The route container.
     *
     * @return {array} elements Found route container objects.
    */
    var getParametersForRoute = function(namespace, root, selector) {

        var header = root.find(SELECTORS.HEADER_CONTAINER).find(selector);
        if (!header.length) {
            header = root.find(SELECTORS.PANEL_HEADER_CONTAINER).find(selector);
        }
        var body = root.find(SELECTORS.BODY_CONTAINER).find(selector);
        if (!body.length) {
            body = root.find(SELECTORS.PANEL_BODY_CONTAINER).find(selector);
        }
        var footer = root.find(SELECTORS.FOOTER_CONTAINER).find(selector);

        return [
            namespace,
            header.length ? header : null,
            body.length ? body : null,
            footer.length ? footer : null
        ];
    };

    var routes = [
        [Routes.VIEW_CONTACT, SELECTORS.VIEW_CONTACT, ViewContact.show, ViewContact.description],
        [Routes.VIEW_CONTACTS, SELECTORS.VIEW_CONTACTS, ViewContacts.show, ViewContacts.description],
        [Routes.VIEW_CONVERSATION, SELECTORS.VIEW_CONVERSATION, ViewConversation.show, ViewConversation.description],
        [Routes.VIEW_GROUP_INFO, SELECTORS.VIEW_GROUP_INFO, ViewGroupInfo.show, ViewGroupInfo.description],
        [Routes.VIEW_OVERVIEW, SELECTORS.VIEW_OVERVIEW, ViewOverview.show, ViewOverview.description],
        [Routes.VIEW_SEARCH, SELECTORS.VIEW_SEARCH, ViewSearch.show, ViewSearch.description],
        [Routes.VIEW_SETTINGS, SELECTORS.VIEW_SETTINGS, ViewSettings.show, ViewSettings.description]
    ];

    /**
     * Create routes.
     *
     * @param {String} namespace Unique identifier for the Routes
     * @param {Object} root The message drawer container.
     */
    var createRoutes = function(namespace, root) {
        routes.forEach(function(route) {
            Router.add(namespace, route[0], getParametersForRoute(namespace, root, route[1]), route[2], route[3]);
        });
    };

    /**
     * Show the message drawer.
     *
     * @param {string} namespace The route namespace.
     * @param {Object} root The message drawer container.
     */
    var show = function(namespace, root) {
        if (!root.attr('data-shown')) {
            Router.go(namespace, Routes.VIEW_OVERVIEW);
            root.attr('data-shown', true);
        }

        var drawerRoot = Drawer.getDrawerRoot(root);
        if (drawerRoot.length) {
            Drawer.show(drawerRoot);
        }
    };

    /**
     * Hide the message drawer.
     *
     * @param {Object} root The message drawer container.
     */
    var hide = function(root) {
        var drawerRoot = Drawer.getDrawerRoot(root);
        if (drawerRoot.length) {
            Drawer.hide(drawerRoot);
        }
    };

    /**
     * Check if the drawer is visible.
     *
     * @param {Object} root The message drawer container.
     * @return {boolean}
     */
    var isVisible = function(root) {
        var drawerRoot = Drawer.getDrawerRoot(root);
        if (drawerRoot.length) {
            return Drawer.isVisible(drawerRoot);
        }
        return true;
    };

    /**
     * Set Jump from button
     *
     * @param {String} buttonid The originating button id
     */
    var setJumpFrom = function(buttonid) {
        $(SELECTORS.DRAWER).attr('data-origin', buttonid);
    };

    /**
     * Listen to and handle events for routing, showing and hiding the message drawer.
     *
     * @param {string} namespace The route namespace.
     * @param {Object} root The message drawer container.
     * @param {bool} alwaysVisible Is this messaging app always shown?
     */
    var registerEventListeners = function(namespace, root, alwaysVisible) {
        CustomEvents.define(root, [CustomEvents.events.activate]);
        var paramRegex = /^data-route-param-?(\d*)$/;

        root.on(CustomEvents.events.activate, SELECTORS.ROUTES, function(e, data) {
            var element = $(e.target).closest(SELECTORS.ROUTES);
            var route = element.attr('data-route');
            var attributes = [];

            for (var i = 0; i < element[0].attributes.length; i++) {
                attributes.push(element[0].attributes[i]);
            }

            var paramAttributes = attributes.filter(function(attribute) {
                var name = attribute.nodeName;
                var match = paramRegex.test(name);
                return match;
            });
            paramAttributes.sort(function(a, b) {
                var aParts = paramRegex.exec(a.nodeName);
                var bParts = paramRegex.exec(b.nodeName);
                var aIndex = aParts.length > 1 ? aParts[1] : 0;
                var bIndex = bParts.length > 1 ? bParts[1] : 0;

                if (aIndex < bIndex) {
                    return -1;
                } else if (bIndex < aIndex) {
                    return 1;
                } else {
                    return 0;
                }
            });

            var params = paramAttributes.map(function(attribute) {
                return attribute.nodeValue;
            });

            var routeParams = [namespace, route].concat(params);

            Router.go.apply(null, routeParams);

            data.originalEvent.preventDefault();
        });

        root.on(CustomEvents.events.activate, SELECTORS.ROUTES_BACK, function(e, data) {
            Router.back(namespace);

            data.originalEvent.preventDefault();
        });

        // These are theme-specific to help us fix random behat fails.
        // These events target those events defined in BS3 and BS4 onwards.
        root.on('hide.bs.collapse', '.collapse', function(e) {
            var pendingPromise = new Pending();
            $(e.target).one('hidden.bs.collapse', function() {
                pendingPromise.resolve();
            });
        });

        root.on('show.bs.collapse', '.collapse', function(e) {
            var pendingPromise = new Pending();
            $(e.target).one('shown.bs.collapse', function() {
                pendingPromise.resolve();
            });
        });

        $(SELECTORS.JUMPTO).focus(function() {
            var firstInput = $(SELECTORS.HEADER_CONTAINER).find('input:visible');
            if (firstInput.length) {
                firstInput.focus();
            } else {
                $(SELECTORS.HEADER_CONTAINER).find(SELECTORS.ROUTES_BACK).focus();
            }
        });

        $(SELECTORS.DRAWER).focus(function() {
            var button = $(this).attr('data-origin');
            if (button) {
                $('#' + button).focus();
            }
        });

        if (!alwaysVisible) {
            PubSub.subscribe(Events.SHOW, function() {
                show(namespace, root);
            });

            PubSub.subscribe(Events.HIDE, function() {
                hide(root);
            });

            PubSub.subscribe(Events.TOGGLE_VISIBILITY, function(buttonid) {
                if (isVisible(root)) {
                    hide(root);
                    $(SELECTORS.JUMPTO).attr('tabindex', -1);
                } else {
                    show(namespace, root);
                    setJumpFrom(buttonid);
                    $(SELECTORS.JUMPTO).attr('tabindex', 0);
                }
            });
        }

        PubSub.subscribe(Events.SHOW_CONVERSATION, function(args) {
            setJumpFrom(args.buttonid);
            show(namespace, root);
            Router.go(namespace, Routes.VIEW_CONVERSATION, args.conversationid);
        });

        PubSub.subscribe(Events.CREATE_CONVERSATION_WITH_USER, function(args) {
            setJumpFrom(args.buttonid);
            show(namespace, root);
            Router.go(namespace, Routes.VIEW_CONVERSATION, null, 'create', args.userid);
        });

        PubSub.subscribe(Events.SHOW_SETTINGS, function() {
            show(namespace, root);
            Router.go(namespace, Routes.VIEW_SETTINGS);
        });

        PubSub.subscribe(Events.PREFERENCES_UPDATED, function(preferences) {
            var filteredPreferences = preferences.filter(function(preference) {
                return preference.type == 'message_entertosend';
            });
            var enterToSendPreference = filteredPreferences.length ? filteredPreferences[0] : null;

            if (enterToSendPreference) {
                var viewConversationFooter = root.find(SELECTORS.FOOTER_CONTAINER).find(SELECTORS.VIEW_CONVERSATION);
                viewConversationFooter.attr('data-enter-to-send', enterToSendPreference.value);
            }
        });
    };

    /**
     * Initialise the message drawer.
     *
     * @param {Object} root The message drawer container.
     * @param {String} uniqueId Unique identifier for the Routes
     * @param {bool} alwaysVisible Should we show the app now, or wait for the user?
     * @param {Object} route
     */
    var init = function(root, uniqueId, alwaysVisible, route) {
        root = $(root);
        createRoutes(uniqueId, root);
        registerEventListeners(uniqueId, root, alwaysVisible);

        if (alwaysVisible) {
            show(uniqueId, root);

            if (route) {
                var routeParams = route.params || [];
                routeParams = [uniqueId, route.path].concat(routeParams);
                Router.go.apply(null, routeParams);
            }
        }
    };

    return {
        init: init,
    };
});
