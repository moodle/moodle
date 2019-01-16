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
 * Controls the search page of the message drawer.
 *
 * @module     core_message/message_drawer_view_search
 * @copyright  2018 Ryan Wyllie <ryan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
define(
[
    'jquery',
    'core/custom_interaction_events',
    'core/notification',
    'core/pubsub',
    'core/str',
    'core/templates',
    'core_message/message_repository',
    'core_message/message_drawer_events',
],
function(
    $,
    CustomEvents,
    Notification,
    PubSub,
    Str,
    Templates,
    Repository,
    Events
) {

    var MESSAGE_SEARCH_LIMIT = 50;
    var USERS_SEARCH_LIMIT = 50;
    var USERS_INITIAL_SEARCH_LIMIT = 3;

    var SELECTORS = {
        BLOCK_ICON_CONTAINER: '[data-region="block-icon-container"]',
        CANCEL_SEARCH_BUTTON: '[data-action="cancel-search"]',
        CONTACTS_CONTAINER: '[data-region="contacts-container"]',
        CONTACTS_LIST: '[data-region="contacts-container"] [data-region="list"]',
        EMPTY_MESSAGE_CONTAINER: '[data-region="empty-message-container"]',
        LIST: '[data-region="list"]',
        LOADING_ICON_CONTAINER: '[data-region="loading-icon-container"]',
        LOADING_PLACEHOLDER: '[data-region="loading-placeholder"]',
        MESSAGES_LIST: '[data-region="messages-container"] [data-region="list"]',
        MESSAGES_CONTAINER: '[data-region="messages-container"]',
        NON_CONTACTS_CONTAINER: '[data-region="non-contacts-container"]',
        NON_CONTACTS_LIST: '[data-region="non-contacts-container"] [data-region="list"]',
        SEARCH_ICON_CONTAINER: '[data-region="search-icon-container"]',
        SEARCH_ACTION: '[data-action="search"]',
        SEARCH_INPUT: '[data-region="search-input"]',
        SEARCH_RESULTS_CONTAINER: '[data-region="search-results-container"]',
        LOAD_MORE_USERS: '[data-action="load-more-users"]',
        LOAD_MORE_MESSAGES: '[data-action="load-more-messages"]',
        BUTTON_TEXT: '[data-region="button-text"]',
        NO_RESULTS_CONTAINTER: '[data-region="no-results-container"]',
        ALL_CONTACTS_CONTAINER: '[data-region="all-contacts-container"]'
    };

    var TEMPLATES = {
        CONTACTS_LIST: 'core_message/message_drawer_contacts_list',
        NON_CONTACTS_LIST: 'core_message/message_drawer_non_contacts_list',
        MESSAGES_LIST: 'core_message/message_drawer_messages_list'
    };

    /**
     * Get the logged in user id.
     *
     * @param  {Object} body Search body container element.
     * @return {Number} User id.
     */
    var getLoggedInUserId = function(body) {
        return body.attr('data-user-id');
    };

    /**
     * Show the no messages container element.
     *
     * @param  {Object} body Search body container element.
     * @return {Object} No messages container element.
     */
    var getEmptyMessageContainer = function(body) {
        return body.find(SELECTORS.EMPTY_MESSAGE_CONTAINER);
    };

    /**
     * Get the search loading icon.
     *
     * @param  {Object} header Search header container element.
     * @return {Object} Loading icon element.
     */
    var getLoadingIconContainer = function(header) {
        return header.find(SELECTORS.LOADING_ICON_CONTAINER);
    };

    /**
     * Get the loading container element.
     *
     * @param  {Object} body Search body container element.
     * @return {Object} Loading container element.
     */
    var getLoadingPlaceholder = function(body) {
        return body.find(SELECTORS.LOADING_PLACEHOLDER);
    };

    /**
     * Get the search icon container.
     *
     * @param  {Object} header Search header container element.
     * @return {Object} Search icon container.
     */
    var getSearchIconContainer = function(header) {
        return header.find(SELECTORS.SEARCH_ICON_CONTAINER);
    };

    /**
     * Get the search input container.
     *
     * @param  {Object} header Search header container element.
     * @return {Object} Search input container.
     */
    var getSearchInput = function(header) {
        return header.find(SELECTORS.SEARCH_INPUT);
    };

    /**
     * Get the search results container.
     *
     * @param  {Object} body Search body container element.
     * @return {Object} Search results container.
     */
    var getSearchResultsContainer = function(body) {
        return body.find(SELECTORS.SEARCH_RESULTS_CONTAINER);
    };

    /**
     * Get the search contacts container.
     *
     * @param  {Object} body Search body container element.
     * @return {Object} Search contacts container.
     */
    var getContactsContainer = function(body) {
        return body.find(SELECTORS.CONTACTS_CONTAINER);
    };

    /**
     * Get the search non contacts container.
     *
     * @param  {Object} body Search body container element.
     * @return {Object} Search non contacts container.
     */
    var getNonContactsContainer = function(body) {
        return body.find(SELECTORS.NON_CONTACTS_CONTAINER);
    };

    /**
     * Get the search messages container.
     *
     * @param  {Object} body Search body container element.
     * @return {Object} Search messages container.
     */
    var getMessagesContainer = function(body) {
        return body.find(SELECTORS.MESSAGES_CONTAINER);
    };


    /**
     * Show the messages empty container.
     *
     * @param {Object} body Search body container element.
     */
    var showEmptyMessage = function(body) {
        getEmptyMessageContainer(body).removeClass('hidden');
    };

    /**
     * Hide the messages empty container.
     *
     * @param {Object} body Search body container element.
     */
    var hideEmptyMessage = function(body) {
        getEmptyMessageContainer(body).addClass('hidden');
    };


    /**
     * Show the loading icon.
     *
     * @param {Object} header Search header container element.
     */
    var showLoadingIcon = function(header) {
        getLoadingIconContainer(header).removeClass('hidden');
    };

    /**
     * Hide the loading icon.
     *
     * @param {Object} header Search header container element.
     */
    var hideLoadingIcon = function(header) {
        getLoadingIconContainer(header).addClass('hidden');
    };

    /**
     * Show loading placeholder.
     *
     * @param {Object} body Search body container element.
     */
    var showLoadingPlaceholder = function(body) {
        getLoadingPlaceholder(body).removeClass('hidden');
    };

    /**
     * Hide loading placeholder.
     *
     * @param {Object} body Search body container element.
     */
    var hideLoadingPlaceholder = function(body) {
        getLoadingPlaceholder(body).addClass('hidden');
    };

    /**
     * Show search icon.
     *
     * @param {Object} header Search header container element.
     */
    var showSearchIcon = function(header) {
        getSearchIconContainer(header).removeClass('hidden');
    };

    /**
     * Hide search icon.
     *
     * @param {Object} header Search header container element.
     */
    var hideSearchIcon = function(header) {
        getSearchIconContainer(header).addClass('hidden');
    };

    /**
     * Show search results.
     *
     * @param {Object} body Search body container element.
     */
    var showSearchResults = function(body) {
        getSearchResultsContainer(body).removeClass('hidden');
    };

    /**
     * Hide search results.
     *
     * @param {Object} body Search body container element.
     */
    var hideSearchResults = function(body) {
        getSearchResultsContainer(body).addClass('hidden');
    };

    /**
     * Show the no search results message.
     *
     * @param {Object} body Search body container element.
     */
    var showNoSearchResults = function(body) {
        var container = getSearchResultsContainer(body);
        container.find(SELECTORS.ALL_CONTACTS_CONTAINER).addClass('hidden');
        container.find(SELECTORS.MESSAGES_CONTAINER).addClass('hidden');
        container.find(SELECTORS.NO_RESULTS_CONTAINTER).removeClass('hidden');
    };

    /**
     * Hide the no search results message.
     *
     * @param {Object} body Search body container element.
     */
    var hideNoSearchResults = function(body) {
        var container = getSearchResultsContainer(body);
        container.find(SELECTORS.ALL_CONTACTS_CONTAINER).removeClass('hidden');
        container.find(SELECTORS.MESSAGES_CONTAINER).removeClass('hidden');
        container.find(SELECTORS.NO_RESULTS_CONTAINTER).addClass('hidden');
    };

    /**
     * Show the whole contacts results area.
     *
     * @param {Object} body Search body container element.
     */
    var showAllContactsSearchResults = function(body) {
        var container = getSearchResultsContainer(body);
        container.find(SELECTORS.ALL_CONTACTS_CONTAINER).removeClass('hidden');
    };

    /**
     * Hide the whole contacts results area.
     *
     * @param {Object} body Search body container element.
     */
    var hideAllContactsSearchResults = function(body) {
        var container = getSearchResultsContainer(body);
        container.find(SELECTORS.ALL_CONTACTS_CONTAINER).addClass('hidden');
    };

    /**
     * Show the contacts results.
     *
     * @param {Object} body Search body container element.
     */
    var showContactsSearchResults = function(body) {
        var container = getSearchResultsContainer(body);
        container.find(SELECTORS.CONTACTS_CONTAINER).removeClass('hidden');
    };

    /**
     * Hide the contacts results.
     *
     * @param {Object} body Search body container element.
     */
    var hideContactsSearchResults = function(body) {
        var container = getSearchResultsContainer(body);
        container.find(SELECTORS.CONTACTS_CONTAINER).addClass('hidden');
    };

    /**
     * Show the non contacts results.
     *
     * @param {Object} body Search body container element.
     */
    var showNonContactsSearchResults = function(body) {
        var container = getSearchResultsContainer(body);
        container.find(SELECTORS.NON_CONTACTS_CONTAINER).removeClass('hidden');
    };

    /**
     * Hide the non contacts results.
     *
     * @param {Object} body Search body container element.
     */
    var hideNonContactsSearchResults = function(body) {
        var container = getSearchResultsContainer(body);
        container.find(SELECTORS.NON_CONTACTS_CONTAINER).addClass('hidden');
    };

    /**
     * Show the messages results.
     *
     * @param {Object} body Search body container element.
     */
    var showMessagesSearchResults = function(body) {
        var container = getSearchResultsContainer(body);
        container.find(SELECTORS.MESSAGES_CONTAINER).removeClass('hidden');
    };

    /**
     * Hide the messages results.
     *
     * @param {Object} body Search body container element.
     */
    var hideMessagesSearchResults = function(body) {
        var container = getSearchResultsContainer(body);
        container.find(SELECTORS.MESSAGES_CONTAINER).addClass('hidden');
    };

    /**
     * Disable the search input.
     *
     * @param {Object} header Search header container element.
     */
    var disableSearchInput = function(header) {
        getSearchInput(header).prop('disabled', true);
    };

    /**
     * Enable the search input.
     *
     * @param {Object} header Search header container element.
     */
    var enableSearchInput = function(header) {
        getSearchInput(header).prop('disabled', false);
    };

    /**
     * Clear the search input.
     *
     * @param {Object} header Search header container element.
     */
    var clearSearchInput = function(header) {
        getSearchInput(header).val('');
    };

    /**
     * Clear all search results
     *
     * @param {Object} body Search body container element.
     */
    var clearAllSearchResults = function(body) {
        body.find(SELECTORS.CONTACTS_LIST).empty();
        body.find(SELECTORS.NON_CONTACTS_LIST).empty();
        body.find(SELECTORS.MESSAGES_LIST).empty();
        hideNoSearchResults(body);
        showAllContactsSearchResults(body);
        showContactsSearchResults(body);
        showNonContactsSearchResults(body);
        showMessagesSearchResults(body);
        showLoadMoreUsersButton(body);
        showLoadMoreMessagesButton(body);
    };

    /**
     * Update the body and header to indicate the search is loading.
     *
     * @param {Object} header Search header container element.
     * @param {Object} body Search body container element.
     */
    var startLoading = function(header, body) {
        hideSearchIcon(header);
        hideEmptyMessage(body);
        hideSearchResults(body);
        showLoadingIcon(header);
        showLoadingPlaceholder(body);
        disableSearchInput(header);
    };

    /**
     * Update the body and header to indicate the search has stopped loading.
     *
     * @param {Object} header Search header container element.
     * @param {Object} body Search body container element.
     */
    var stopLoading = function(header, body) {
        showSearchIcon(header);
        hideEmptyMessage(body);
        showSearchResults(body);
        hideLoadingIcon(header);
        hideLoadingPlaceholder(body);
        enableSearchInput(header);
    };

    /**
     * Show the more users loading icon.
     *
     * @param {Object} root The more users container element.
     */
    var showUsersLoadingIcon = function(root) {
        var button = root.find(SELECTORS.LOAD_MORE_USERS);
        button.prop('disabled', true);
        button.find(SELECTORS.BUTTON_TEXT).addClass('hidden');
        button.find(SELECTORS.LOADING_ICON_CONTAINER).removeClass('hidden');
    };

    /**
     * Hide the more users loading icon.
     *
     * @param {Object} root The more users container element.
     */
    var hideUsersLoadingIcon = function(root) {
        var button = root.find(SELECTORS.LOAD_MORE_USERS);
        button.prop('disabled', false);
        button.find(SELECTORS.BUTTON_TEXT).removeClass('hidden');
        button.find(SELECTORS.LOADING_ICON_CONTAINER).addClass('hidden');
    };

    /**
     * Show the load more users button.
     *
     * @param {Object} root The users container element.
     */
    var showLoadMoreUsersButton = function(root) {
        root.find(SELECTORS.LOAD_MORE_USERS).removeClass('hidden');
    };

    /**
     * Hide the load more users button.
     *
     * @param {Object} root The users container element.
     */
    var hideLoadMoreUsersButton = function(root) {
        root.find(SELECTORS.LOAD_MORE_USERS).addClass('hidden');
    };

    /**
     * Show the messages are loading icon.
     *
     * @param {Object} root Messages root element.
     */
    var showMessagesLoadingIcon = function(root) {
        var button = root.find(SELECTORS.LOAD_MORE_MESSAGES);
        button.prop('disabled', true);
        button.find(SELECTORS.BUTTON_TEXT).addClass('hidden');
        button.find(SELECTORS.LOADING_ICON_CONTAINER).removeClass('hidden');
    };

    /**
     * Hide the messages are loading icon.
     *
     * @param {Object} root Messages root element.
     */
    var hideMessagesLoadingIcon = function(root) {
        var button = root.find(SELECTORS.LOAD_MORE_MESSAGES);
        button.prop('disabled', false);
        button.find(SELECTORS.BUTTON_TEXT).removeClass('hidden');
        button.find(SELECTORS.LOADING_ICON_CONTAINER).addClass('hidden');
    };

    /**
     * Show the load more messages button.
     *
     * @param  {Object} root The messages container element.
     */
    var showLoadMoreMessagesButton = function(root) {
        root.find(SELECTORS.LOAD_MORE_MESSAGES).removeClass('hidden');
    };

    /**
     * Hide the load more messages button.
     *
     * @param  {Object} root The messages container element.
     */
    var hideLoadMoreMessagesButton = function(root) {
        root.find(SELECTORS.LOAD_MORE_MESSAGES).addClass('hidden');
    };

    /**
     * Find a contact in the search results.
     *
     * @param  {Object} root Search results container element.
     * @param  {Number} userId User id.
     * @return {Object} User container element.
     */
    var findContact = function(root, userId) {
        return root.find('[data-contact-user-id="' + userId + '"]');
    };

    /**
     * Add a contact to the search results.
     *
     * @param {Object} root Search results container.
     * @param {Object} contact User in contacts list.
     */
    var addContact = function(root, contact) {
        var nonContactsContainer = getNonContactsContainer(root);
        var nonContact = findContact(nonContactsContainer, contact.userid);

        if (nonContact.length) {
            nonContact.remove();
            var contactsContainer = getContactsContainer(root);
            contactsContainer.removeClass('hidden');
            contactsContainer.find(SELECTORS.LIST).append(nonContact);
        }

        if (!nonContactsContainer.find(SELECTORS.LIST).children().length) {
            nonContactsContainer.addClass('hidden');
        }
    };

    /**
     * Remove a contact from the contacts results.
     *
     * @param {Object} root Search results container.
     * @param {Object} userId Contact user id.
     */
    var removeContact = function(root, userId) {
        var contactsContainer = getContactsContainer(root);
        var contact = findContact(contactsContainer, userId);

        if (contact.length) {
            contact.remove();
            var nonContactsContainer = getNonContactsContainer(root);
            nonContactsContainer.removeClass('hidden');
            nonContactsContainer.find(SELECTORS.LIST).append(contact);
        }

        if (!contactsContainer.find(SELECTORS.LIST).children().length) {
            contactsContainer.addClass('hidden');
        }
    };

    /**
     * Show the contact is blocked icon.
     *
     * @param {Object} root Search results container.
     * @param {Object} userId Contact user id.
     */
    var blockContact = function(root, userId) {
        var contact = findContact(root, userId);
        if (contact.length) {
            contact.find(SELECTORS.BLOCK_ICON_CONTAINER).removeClass('hidden');
        }
    };

    /**
     * Hide the contact is blocked icon.
     *
     * @param {Object} root Search results container.
     * @param {Object} userId Contact user id.
     */
    var unblockContact = function(root, userId) {
        var contact = findContact(root, userId);
        if (contact.length) {
            contact.find(SELECTORS.BLOCK_ICON_CONTAINER).addClass('hidden');
        }
    };

    /**
     * Render contacts in the contacts search results.
     *
     * @param {Object} root Search results container.
     * @param {Array} contacts List of contacts.
     * @return {Promise} Renderer promise.
     */
    var renderContacts = function(root, contacts) {
        var container = getContactsContainer(root);
        var list = container.find(SELECTORS.LIST);

        return Templates.render(TEMPLATES.CONTACTS_LIST, {contacts: contacts})
            .then(function(html) {
                list.append(html);
                return html;
            });
    };

    /**
     * Render non contacts in the contacts search results.
     *
     * @param {Object} root Search results container.
     * @param {Array} nonContacts List of non contacts.
     * @return {Promise} Renderer promise.
     */
    var renderNonContacts = function(root, nonContacts) {
        var container = getNonContactsContainer(root);
        var list = container.find(SELECTORS.LIST);

        return Templates.render(TEMPLATES.NON_CONTACTS_LIST, {noncontacts: nonContacts})
            .then(function(html) {
                list.append(html);
                return html;
            });
    };

    /**
     * Render messages in the messages search results.
     *
     * @param {Object} root Search results container.
     * @param {Array} messages List of messages.
     * @return {Promise} Renderer promise.
     */
    var renderMessages = function(root, messages) {
        var container = getMessagesContainer(root);
        var list = container.find(SELECTORS.LIST);

        return Templates.render(TEMPLATES.MESSAGES_LIST, {messages: messages})
            .then(function(html) {
                list.append(html);
                return html;
            });
    };

    /**
     * Load more users from the repository and render the results into the users search results.
     *
     * @param  {Object} root Search results container.
     * @param  {Number} loggedInUserId Current logged in user.
     * @param  {String} text Search text.
     * @param  {Number} limit Number of users to get.
     * @param  {Number} offset Load users from
     * @return {Object} jQuery promise
     */
    var loadMoreUsers = function(root, loggedInUserId, text, limit, offset) {
        var loadedAll = false;
        showUsersLoadingIcon(root);

        return Repository.searchUsers(loggedInUserId, text, limit + 1, offset)
            .then(function(results) {
                var contacts = results.contacts;
                var noncontacts = results.noncontacts;

                if (contacts.length <= limit && noncontacts.length <= limit) {
                    loadedAll = true;
                    return {
                        contacts: contacts,
                        noncontacts: noncontacts
                    };
                } else {
                    return {
                        contacts: contacts.slice(0, limit),
                        noncontacts: noncontacts.slice(0, limit)
                    };
                }
            })
            .then(function(results) {
                var contactsCount = results.contacts.length;
                var nonContactsCount = results.noncontacts.length;

                return $.when(
                    contactsCount ? renderContacts(root, results.contacts) : true,
                    nonContactsCount ? renderNonContacts(root, results.noncontacts) : true
                )
                .then(function() {
                    return {
                        contactsCount: contactsCount,
                        nonContactsCount: nonContactsCount
                    };
                });
            })
            .then(function(counts) {
                hideUsersLoadingIcon(root);

                if (loadedAll) {
                    hideLoadMoreUsersButton(root);
                }

                return counts;
            })
            .catch(function(error) {
                hideUsersLoadingIcon(root);
                // Rethrow error for other handlers.
                throw error;
            });
    };

    /**
     * Load more messages from the repository and render the results into the messages search results.
     *
     * @param  {Object} root Search results container.
     * @param  {Number} loggedInUserId Current logged in user.
     * @param  {String} text Search text.
     * @param  {Number} limit Number of messages to get.
     * @param  {Number} offset Load messages from
     * @return {Object} jQuery promise
     */
    var loadMoreMessages = function(root, loggedInUserId, text, limit, offset) {
        var loadedAll = false;
        showMessagesLoadingIcon(root);

        return Repository.searchMessages(loggedInUserId, text, limit + 1, offset)
            .then(function(results) {
                var messages = results.contacts;

                if (messages.length <= limit) {
                    loadedAll = true;
                    return messages;
                } else {
                    return messages.slice(0, limit);
                }
            })
            .then(function(messages) {
                if (messages.length) {
                    return renderMessages(root, messages)
                        .then(function() {
                            return messages.length;
                        });
                } else {
                    return messages.length;
                }
            })
            .then(function(count) {
                hideMessagesLoadingIcon(root);

                if (loadedAll) {
                    hideLoadMoreMessagesButton(root);
                }

                return count;
            })
            .catch(function(error) {
                hideMessagesLoadingIcon(root);
                // Rethrow error for other handlers.
                throw error;
            });
    };

    /**
     * Search for users and messages.
     *
     * @param {Object} header Search header container element.
     * @param {Object} body Search body container element.
     * @param {String} searchText Search text.
     * @param {Number} usersLimit The users limit.
     * @param {Number} usersOffset The users offset.
     * @param {Number} messagesLimit The message limit.
     * @param {Number} messagesOffset The message offset.
     * @return {Object} jQuery promise
     */
    var search = function(header, body, searchText, usersLimit, usersOffset, messagesLimit, messagesOffset) {
        var loggedInUserId = getLoggedInUserId(body);
        startLoading(header, body);
        clearAllSearchResults(body);

        return $.when(
            loadMoreUsers(body, loggedInUserId, searchText, usersLimit, usersOffset),
            loadMoreMessages(body, loggedInUserId, searchText, messagesLimit, messagesOffset)
        )
        .then(function(userCounts, messagesCount) {
            var contactsCount = userCounts.contactsCount;
            var nonContactsCount = userCounts.nonContactsCount;

            stopLoading(header, body);

            if (!contactsCount && !nonContactsCount && !messagesCount) {
                showNoSearchResults(body);
            } else {
                if (!contactsCount && !nonContactsCount) {
                    hideAllContactsSearchResults(body);
                } else {
                    if (!contactsCount) {
                        hideContactsSearchResults(body);
                    }

                    if (!nonContactsCount) {
                        hideNonContactsSearchResults(body);
                    }
                }

                if (!messagesCount) {
                    hideMessagesSearchResults(body);
                }
            }

            return;
        });
    };


    /**
     * Listen to and handle events for searching.
     *
     * @param {Object} header Search header container element.
     * @param {Object} body Search body container element.
     */
    var registerEventListeners = function(header, body) {
        var loggedInUserId = getLoggedInUserId(body);
        var searchInput = getSearchInput(header);
        var searchText = '';
        var messagesOffset = 0;
        var usersOffset = 0;

        var searchEventHandler = function(e, data) {
            searchText = searchInput.val().trim();

            if (searchText !== '') {
                messagesOffset = 0;
                usersOffset = 0;
                search(
                    header,
                    body,
                    searchText,
                    USERS_INITIAL_SEARCH_LIMIT,
                    usersOffset,
                    MESSAGE_SEARCH_LIMIT,
                    messagesOffset
                )
                .then(function() {
                    searchInput.focus();
                    usersOffset = usersOffset + USERS_INITIAL_SEARCH_LIMIT;
                    messagesOffset = messagesOffset + MESSAGE_SEARCH_LIMIT;
                    return;
                })
                .catch(Notification.exception);
            }

            data.originalEvent.preventDefault();
        };

        CustomEvents.define(searchInput, [CustomEvents.events.enter]);
        CustomEvents.define(header, [CustomEvents.events.activate]);
        CustomEvents.define(body, [CustomEvents.events.activate]);

        searchInput.on(CustomEvents.events.enter, searchEventHandler);

        header.on(CustomEvents.events.activate, SELECTORS.SEARCH_ACTION, searchEventHandler);

        body.on(CustomEvents.events.activate, SELECTORS.LOAD_MORE_MESSAGES, function(e, data) {
            if (searchText !== '') {
                loadMoreMessages(body, loggedInUserId, searchText, MESSAGE_SEARCH_LIMIT, messagesOffset)
                    .then(function() {
                        messagesOffset = messagesOffset + MESSAGE_SEARCH_LIMIT;
                        return;
                    })
                    .catch(Notification.exception);
            }
            data.originalEvent.preventDefault();
        });

        body.on(CustomEvents.events.activate, SELECTORS.LOAD_MORE_USERS, function(e, data) {
            if (searchText !== '') {
                loadMoreUsers(body, loggedInUserId, searchText, USERS_SEARCH_LIMIT, usersOffset)
                    .then(function() {
                        usersOffset = usersOffset + USERS_SEARCH_LIMIT;
                        return;
                    })
                    .catch(Notification.exception);
            }
            data.originalEvent.preventDefault();
        });

        header.on(CustomEvents.events.activate, SELECTORS.CANCEL_SEARCH_BUTTON, function() {
            clearSearchInput(header);
            showEmptyMessage(body);
            showSearchIcon(header);
            hideSearchResults(body);
            hideLoadingIcon(header);
            hideLoadingPlaceholder(body);
            usersOffset = 0;
            messagesOffset = 0;
        });

        PubSub.subscribe(Events.CONTACT_ADDED, function(userId) {
            addContact(body, userId);
        });

        PubSub.subscribe(Events.CONTACT_REMOVED, function(userId) {
            removeContact(body, userId);
        });

        PubSub.subscribe(Events.CONTACT_BLOCKED, function(userId) {
            blockContact(body, userId);
        });

        PubSub.subscribe(Events.CONTACT_UNBLOCKED, function(userId) {
            unblockContact(body, userId);
        });
    };

    /**
     * Setup the search page.
     *
     * @param {Object} header Contacts header container element.
     * @param {Object} body Contacts body container element.
     * @return {Object} jQuery promise
     */
    var show = function(header, body) {
        if (!body.attr('data-init')) {
            registerEventListeners(header, body);
            body.attr('data-init', true);
        }

        var searchInput = getSearchInput(header);
        searchInput.focus();

        return $.Deferred().resolve().promise();
    };

    /**
     * String describing this page used for aria-labels.
     *
     * @param {Object} header Contacts header container element.
     * @return {Object} jQuery promise
     */
    var description = function(header) {
        var searchInput = getSearchInput(header);
        var searchText = searchInput.val().trim();
        return Str.get_string('messagedrawerviewsearch', 'core_message', searchText);
    };

    return {
        show: show,
        description: description
    };
});
