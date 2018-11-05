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
 * Controls the contacts section of the contacts page.
 *
 * @module     core_message/message_drawer_view_contacts_section_contacts
 * @package    message
 * @copyright  2018 Ryan Wyllie <ryan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
define(
[
    'jquery',
    'core/notification',
    'core/pubsub',
    'core/templates',
    'core_message/message_repository',
    'core_message/message_drawer_events',
    'core_message/message_drawer_lazy_load_list'
],
function(
    $,
    Notification,
    PubSub,
    Templates,
    MessageRepository,
    Events,
    LazyLoadList
) {

    var limit = 100;
    var offset = 0;

    var SELECTORS = {
        BLOCK_ICON_CONTAINER: '[data-region="block-icon-container"]',
        CONTACT: '[data-region="contact"]',
        CONTENT_CONTAINER: '[data-region="contacts-content-container"]'
    };

    var TEMPLATES = {
        CONTACTS_LIST: 'core_message/message_drawer_contacts_list'
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
     * Render the contacts in the content container.
     *
     * @param {Object} contentContainer Content container element.
     * @param {Array} contacts List of contacts.
     * @return {Object} jQuery promise
     */
    var render = function(contentContainer, contacts) {
        var formattedContacts = contacts.map(function(contact) {
            return $.extend(contact, {id: contact.userid});
        });
        return Templates.render(TEMPLATES.CONTACTS_LIST, {contacts: formattedContacts})
            .then(function(html) {
                contentContainer.append(html);
                return html;
            })
            .catch(Notification.exception);
    };

    /**
     * Load the user contacts and call the renderer.
     *
     * @param {Object} listRoot The lazy loaded list root element
     * @param {Integer} userId The logged in user id.
     * @return {Object} jQuery promise
     */
    var load = function(listRoot, userId) {
        return MessageRepository.getContacts(userId, (limit + 1), offset)
            .then(function(result) {
                return result.contacts;
            })
            .then(function(contacts) {
                if (contacts.length > limit) {
                    contacts.pop();
                } else {
                    LazyLoadList.setLoadedAll(listRoot, true);
                }
                return contacts;
            })
            .then(function(contacts) {
                offset = offset + limit;
                return contacts;
            })
            .catch(Notification.exception);
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
     * @param {Object} root Contacts section container element.
     */
    var registerEventListeners = function(root) {
        PubSub.subscribe(Events.CONTACT_ADDED, function(profile) {
            var listContentContainer = LazyLoadList.getContentContainer(root);
            render(listContentContainer, [profile]);
            LazyLoadList.hideEmptyMessage(root);
            LazyLoadList.showContent(root);
        });

        PubSub.subscribe(Events.CONTACT_REMOVED, function(userId) {
            removeContact(root, userId);
            var contacts = root.find(SELECTORS.CONTACT);

            if (!contacts.length) {
                LazyLoadList.hideContent(root);
                LazyLoadList.showEmptyMessage(root);
            }
        });

        PubSub.subscribe(Events.CONTACT_BLOCKED, function(userId) {
            showContactBlocked(root, userId);
        });

        PubSub.subscribe(Events.CONTACT_UNBLOCKED, function(userId) {
            showContactUnblocked(root, userId);
        });
    };

    /**
     * Setup the contacts section.
     *
     * @param {Object} root Contacts section container.
     */
    var show = function(root) {
        if (!root.attr('data-contacts-init')) {
            registerEventListeners(root);
            root.attr('data-contacts-init', true);
        }

        // The root element is already the lazy loaded list root.
        LazyLoadList.show(root, load, render);
    };

    return {
        show: show,
    };
});
