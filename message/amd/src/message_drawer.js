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
    'core_message/message_drawer_helper',
    'core/pending',
    'core/drawer',
    'core/toast',
    'core/str',
    'core/config',
    'core/ajax',
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
    Helper,
    Pending,
    Drawer,
    Toast,
    Str,
    Config,
    Ajax,
) {

    var SELECTORS = {
        DRAWER: '[data-region="right-hand-drawer"]',
        JUMPTO: '.popover-region [data-region="jumpto"]',
        PANEL_BODY_CONTAINER: '[data-region="panel-body-container"]',
        PANEL_HEADER_CONTAINER: '[data-region="panel-header-container"]',
        VIEW_CONTACT: '[data-region="view-contact"]',
        VIEW_CONTACTS: '[data-region="view-contacts"]',
        VIEW_CONVERSATION: '[data-region="view-conversation"]',
        VIEW_CONVERSATION_WITH_ID: '[data-region="view-conversation"][data-conversation-id]',
        VIEW_CONVERSATION_WITH_USER: '[data-region="view-conversation"][data-other-user-id]',
        VIEW_GROUP_INFO: '[data-region="view-group-info"]',
        VIEW_OVERVIEW: '[data-region="view-overview"]',
        VIEW_SEARCH: '[data-region="view-search"]',
        VIEW_SETTINGS: '[data-region="view-settings"]',
        ROUTES: '[data-route]',
        ROUTES_BACK: '[data-route-back]',
        HEADER_CONTAINER: '[data-region="header-container"]',
        BODY_CONTAINER: '[data-region="body-container"]',
        FOOTER_CONTAINER: '[data-region="footer-container"]',
        CLOSE_BUTTON: '[data-action="closedrawer"]',
        MESSAGE_INDEX: '[data-region="message-index"]',
        MESSAGE_TEXT_AREA: '[data-region="send-message-txt"]',
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
     * Store an unsent message.
     *
     * Don't store this if the user has already seen the unsent message.
     * This avoids spamming and ensures the user is only reminded once per unsent message.
     * If the unsent message is sent, this attribute is removed and notification is possible again (see sendMessage).
     */
    const storeUnsentMessage = async() => {
        const messageTextArea = document.querySelector(SELECTORS.MESSAGE_TEXT_AREA);

        if (messageTextArea.value.trim().length > 0 && !messageTextArea.hasAttribute('data-unsent-message-viewed')) {

            let message = messageTextArea.value;
            let conversationid = 0;
            let otheruserid = 0;

            // We don't always have a conversation to link the unsent message to, so let's check for that.
            const conversationId = document.querySelector(SELECTORS.VIEW_CONVERSATION_WITH_ID);
            if (conversationId) {
                const conversationWithId = messageTextArea.closest(SELECTORS.VIEW_CONVERSATION_WITH_ID);
                conversationid = conversationWithId.getAttribute('data-conversation-id');
            }
            // Store the 'other' user id if it is there. This can be used to create conversations.
            const conversationUser = document.querySelector(SELECTORS.VIEW_CONVERSATION_WITH_USER);
            if (conversationUser) {
                const conversationWithUser = messageTextArea.closest(SELECTORS.VIEW_CONVERSATION_WITH_USER);
                otheruserid = conversationWithUser.getAttribute('data-other-user-id');
            }

            setStoredUnsentMessage(message, conversationid, otheruserid);
        }
    };

    /**
     * Get the stored unsent message from the session via web service.
     *
     * @returns {Promise}
     */
    const getStoredUnsentMessage = () => Ajax.call([{
        methodname: 'core_message_get_unsent_message',
        args: {}
    }])[0];

    /**
     * Set the unsent message value in the session via web service.
     *
     * SendBeacon is used here because this is called on 'beforeunload'.
     *
     * @param {string} message The message string.
     * @param {number} conversationid The conversation id.
     * @param {number} otheruserid The other user id.
     * @returns {Promise}
     */
    const setStoredUnsentMessage = (message, conversationid, otheruserid) => {
        const method = 'core_message_set_unsent_message';
        const requestUrl = new URL(`${Config.wwwroot}/lib/ajax/service.php`);
        requestUrl.searchParams.set('sesskey', Config.sesskey);
        requestUrl.searchParams.set('info', method);

        navigator.sendBeacon(requestUrl, JSON.stringify([{
            index: 0,
            methodname: method,
            args: {
                message: message,
                conversationid: conversationid,
                otheruserid: otheruserid,
            }
        }]));
    };

    /**
     * Check for an unsent message.
     *
     * @param {String} uniqueId Unique identifier for the Routes.
     * @param {Object} root The message drawer container.
     */
    const getUnsentMessage = async(uniqueId, root) => {
        let type;
        let messageRoot;

        // We need to check if we are on the message/index page.
        // This logic is needed to handle the two message widgets here and ensure we are targetting the right one.
        const messageIndex = document.querySelector(SELECTORS.MESSAGE_INDEX);
        if (messageIndex !== null) {
            type = 'index';
            messageRoot = document.getElementById(`message-index-${uniqueId}`);
            if (!messageRoot) {
                // This is not the correct widget.
                return;
            }

        } else {
            type = 'drawer';
            messageRoot = document.getElementById(`message-drawer-${uniqueId}`);
        }

        const storedMessage = await getStoredUnsentMessage();
        const messageTextArea = messageRoot.querySelector(SELECTORS.MESSAGE_TEXT_AREA);
        if (storedMessage.message && messageTextArea !== null) {
            showUnsentMessage(messageTextArea, storedMessage, type, uniqueId, root);
        }
    };

    /**
     * Show an unsent message.
     *
     * There are two message widgets on the message/index page.
     * Because of that, we need to try and target the correct widget.
     *
     * @param {String} textArea The textarea element.
     * @param {Object} stored The stored message content.
     * @param {String} type Is this from the drawer or index page?
     * @param {String} uniqueId Unique identifier for the Routes.
     * @param {Object} root The message drawer container.
     */
    const showUnsentMessage = (textArea, stored, type, uniqueId, root) => {
        // The user has already been notified.
        if (textArea.hasAttribute('data-unsent-message-viewed')) {
            return;
        }

        // Depending on the type, show the conversation with the data we have available.
        // A conversation can be continued if there is a conversationid.
        // If the user was messaging a new non-contact, we won't have a conversationid yet.
        // In that case, we use the otheruserid value to start a conversation with them.
        switch (type) {
            case 'index':
                // Show the conversation in the main panel on the message/index page.
                if (stored.conversationid) {
                    Router.go(uniqueId, Routes.VIEW_CONVERSATION, stored.conversationid, 'frompanel');
                // There was no conversation id, let's get a conversation going using the user id.
                } else if (stored.otheruserid) {
                    Router.go(uniqueId, Routes.VIEW_CONVERSATION, null, 'create', stored.otheruserid);
                }
                break;

            case 'drawer':
                // Open the drawer and show the conversation.
                if (stored.conversationid) {
                    let args = {
                        conversationid: stored.conversationid
                    };
                    Helper.showConversation(args);
                // There was no conversation id, let's get a conversation going using the user id.
                } else if (stored.otheruserid) {
                    show(uniqueId, root);
                    Router.go(uniqueId, Routes.VIEW_CONVERSATION, null, 'create', stored.otheruserid);
                }
                break;
        }

        // Populate the text area.
        textArea.value = stored.message;
        textArea.setAttribute('data-unsent-message-viewed', 1);

        // Notify the user.
        Toast.add(Str.get_string('unsentmessagenotification', 'core_message'));
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
        root[0].querySelectorAll('.collapse').forEach((collapse) => {
            collapse.addEventListener('hide.bs.collapse', (e) => {
                var pendingPromise = new Pending();
                e.target.addEventListener('hidden.bs.collapse', function() {
                    pendingPromise.resolve();
                }, {once: true});
            });
        });

        root[0].querySelectorAll('.collapse').forEach((collapse) => {
            collapse.addEventListener('show.bs.collapse', (e) => {
                var pendingPromise = new Pending();
                e.target.addEventListener('shown.bs.collapse', function() {
                    pendingPromise.resolve();
                }, {once: true});
            });
        });

        $(SELECTORS.JUMPTO).focus(function() {
            var firstInput = root.find(SELECTORS.CLOSE_BUTTON);
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
                const buttonElement = document.getElementById(buttonid);
                if (isVisible(root)) {
                    hide(root);
                    buttonElement?.setAttribute('aria-expanded', false);
                    $(SELECTORS.JUMPTO).attr('tabindex', -1);
                } else {
                    show(namespace, root);
                    buttonElement?.setAttribute('aria-expanded', true);
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

        var closebutton = root.find(SELECTORS.CLOSE_BUTTON);
        closebutton.on(CustomEvents.events.activate, function(e, data) {
            data.originalEvent.preventDefault();

            var button = $(SELECTORS.DRAWER).attr('data-origin');
            if (button) {
                $('#' + button).focus();
            }
            PubSub.publish(Events.TOGGLE_VISIBILITY, button);
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

        // If our textarea is modified, remove the attribute which indicates the user has seen the unsent message notification.
        // This will allow the user to be notified again.
        const textArea = document.querySelector(SELECTORS.MESSAGE_TEXT_AREA);
        if (textArea) {
            textArea.addEventListener('keyup', function() {
                textArea.removeAttribute('data-unsent-message-viewed');
            });
        }

        // Catch any unsent messages and store them.
        window.addEventListener('beforeunload', storeUnsentMessage);
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

        // Mark the drawer as ready.
        Helper.markDrawerReady();

        // Get and show any unsent message.
        getUnsentMessage(uniqueId, root);
    };

    return {
        init: init,
    };
});
