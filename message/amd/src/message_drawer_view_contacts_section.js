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
 * Controls a section on the contacts page of the message drawer.
 *
 * @module     core_message/message_drawer_view_contacts_section
 * @copyright  2018 Ryan Wyllie <ryan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
define(
[
    'jquery',
    'core/notification',
    'core/pubsub',
    'core/templates',
    'core/custom_interaction_events',
    'core_message/message_repository',
    'core_message/message_drawer_events'
],
function(
    $,
    Notification,
    PubSub,
    Templates,
    CustomEvents,
    MessageRepository,
    Events
) {

    var LOAD_CONTACTS_LIMIT = 100;

    var numContacts = 0;
    var contactsOffset = 0;
    var loadedAllContacts = false;
    var waitForScrollLoad = false;

    var SELECTORS = {
        BLOCK_ICON_CONTAINER: '[data-region="block-icon-container"]',
        CONTACTS: '[data-region="contacts-container"]',
        LOADING_ICON_CONTAINER: '[data-region="loading-icon-container"]',
        CONTENT_CONTAINER: '[data-region="contacts-content-container"]',
        EMPTY_MESSAGE: '[data-region="empty-message-container"]',
        PLACEHOLDER: '[data-region="placeholder-container"]'
    };

    var TEMPLATES = {
        CONTACTS_LIST: 'core_message/message_drawer_contacts_list'
    };

    /**
     * Show the loading icon.
     *
     * @param {Object} body Contacts body container element.
     */
    var startLoading = function(body) {
        body.find(SELECTORS.LOADING_ICON_CONTAINER).removeClass('hidden');
    };

    /**
     * Hide the loading icon.
     *
     * @param {Object} body Contacts body container element.
     */
    var stopLoading = function(body) {
        body.find(SELECTORS.LOADING_ICON_CONTAINER).addClass('hidden');
    };

    /**
     * Get the content container of the contacts body container element.
     *
     * @param {Object} body Contacts body container element.
     * @return {Object} jQuery element
     */
    var getContentContainer = function(body) {
        return body.find(SELECTORS.CONTENT_CONTAINER);
    };

    /**
     * Get the contacts container of the contacts body container element.
     *
     * @param {Object} body Contacts body container element.
     * @return {Object} jQuery element
     */
    var getContactsContainer = function(body) {
        return body.find(SELECTORS.CONTACTS);
    };

    /**
     * Show a message when no contacts found.
     *
     * @param {Object} body Contacts body container element.
     */
    var showEmptyMessage = function(body) {
        getContentContainer(body).addClass('hidden');
        body.find(SELECTORS.EMPTY_MESSAGE).removeClass('hidden');
    };

    /**
     * Hide the placeholder image.
     *
     * @param {Object} body Contacts body container element.
     */
    var hidePlaceholder = function(body) {
        body.find(SELECTORS.PLACEHOLDER).addClass('hidden');
    };

    /**
     * Show the content container.
     *
     * @param {Object} body Contacts body container element.
     */
    var showContent = function(body) {
        getContentContainer(body).removeClass('hidden');
    };

    /**
     * Find a contact element.
     *
     * @param {Object} body Contacts body container element.
     * @param {Number} userId User id of contact.
     * @return {Object} contact element.
     */
    var findContact = function(body, userId) {
        return body.find('[data-contact-user-id="' + userId + '"]');
    };

    /**
     * Get logged in userid.
     *
     * @param {Object} body Contacts body container element.
     * @return {Number} Logged in userid.
     */
    var getLoggedInUserId = function(body) {
        return body.attr('data-user-id');
    };

    /**
     * Render the contacts in the content container.
     *
     * @param {Object} body Contacts body container element.
     * @param {Array} contacts List of contacts.
     * @return {Object} jQuery promise
     */
    var render = function(body, contacts) {
        var contentContainer = getContentContainer(body);
        return Templates.render(TEMPLATES.CONTACTS_LIST, {contacts: contacts})
            .then(function(html) {
                hidePlaceholder(body);
                contentContainer.append(html);
                showContent(body);
                return html;
            });
    };

    /**
     * Load the user contacts and call the renderer.
     *
     * @param {Object} body Contacts body container element.
     * @return {Object} jQuery promise
     */
    var loadContacts = function(body) {
        var userId = getLoggedInUserId(body);
        return MessageRepository.getContacts(userId, (LOAD_CONTACTS_LIMIT + 1), contactsOffset)
            .then(function(result) {
                return result.contacts;
            })
            .then(function(contacts) {
                if (contacts.length > LOAD_CONTACTS_LIMIT) {
                    contacts.pop();
                } else {
                    loadedAllContacts = true;
                }
                return contacts;
            })
            .then(function(contacts) {
                if (contactsOffset == 0 && contacts.length == 0) {
                    hidePlaceholder(body);
                    showEmptyMessage(body);
                }

                numContacts = numContacts + contacts.length;

                contactsOffset = contactsOffset + LOAD_CONTACTS_LIMIT;
                if (contacts.length > 0) {
                    return render(body, contacts);
                }

                return contacts;
            });
    };

    /**
     * Remove contact from view.
     *
     * @param {Object} body Contacts body container element.
     * @param {Number} userId Contact userid.
     */
    var removeContact = function(body, userId) {
        findContact(body, userId).remove();
    };

    /**
     * Show the contact has been blocked.
     *
     * @param {Object} body Contacts body container element.
     * @param {Number} userId Contact userid.
     */
    var showContactBlocked = function(body, userId) {
        var contact = findContact(body, userId);
        if (contact.length) {
            contact.find(SELECTORS.BLOCK_ICON_CONTAINER).removeClass('hidden');
        }
    };

    /**
     * Show the contact has been unblocked.
     *
     * @param {Object} body Contacts body container element.
     * @param {Number} userId Contact userid.
     */
    var showContactUnblocked = function(body, userId) {
        var contact = findContact(body, userId);
        if (contact.length) {
            contact.find(SELECTORS.BLOCK_ICON_CONTAINER).addClass('hidden');
        }
    };

    /**
     * Listen to and handle events for contacts.
     *
     * @param {Object} body Contacts body container element.
     */
    var registerEventListeners = function(body) {
        PubSub.subscribe(Events.CONTACT_ADDED, function() {
            contactsOffset = 0;
            loadedAllContacts = false;
            getContentContainer(body).empty();
            loadContacts(body);
        });

        PubSub.subscribe(Events.CONTACT_REMOVED, function(userId) {
            removeContact(body, userId);
        });

        PubSub.subscribe(Events.CONTACT_BLOCKED, function(userId) {
            showContactBlocked(body, userId);
        });

        PubSub.subscribe(Events.CONTACT_UNBLOCKED, function(userId) {
            showContactUnblocked(body, userId);
        });

        var contactsContainer = getContactsContainer(body);

        CustomEvents.define(contactsContainer, [
            CustomEvents.events.scrollBottom,
            CustomEvents.events.scrollLock
        ]);

        contactsContainer.on(CustomEvents.events.scrollBottom, function(e, data) {
            var hasContacts = numContacts > 1;
            if (!loadedAllContacts && hasContacts && !waitForScrollLoad) {
                waitForScrollLoad = true;
                startLoading(body);
                loadContacts(body)
                    .then(function() {
                        stopLoading(body);
                        waitForScrollLoad = false;
                        return;
                    })
                    .catch(function(error) {
                        stopLoading(body);
                        waitForScrollLoad = false;
                        Notification.exception(error);
                    });
            }
            data.originalEvent.preventDefault();
        });
    };

    /**
     * Setup the contact page.
     *
     * @param {Object} header Contacts header container element.
     * @param {Object} body Contacts body container element.
     */
    var show = function(header, body) {
        body = $(body);
        contactsOffset = 0;

        if (!body.attr('data-contacts-init')) {
            registerEventListeners(body);
            body.attr('data-contacts-init', true);
        }

        if (!loadedAllContacts) {
            loadContacts(body);
        }
    };

    return {
        show: show,
    };
});
