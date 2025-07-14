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
 * Controls the contacts page of the message drawer.
 *
 * @module     core_message/message_drawer_view_contacts
 * @copyright  2018 Ryan Wyllie <ryan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
define(
[
    'jquery',
    'core/pubsub',
    'core/str',
    'core_message/message_drawer_events',
    'core_message/message_drawer_view_contacts_section_contacts',
    'core_message/message_drawer_view_contacts_section_requests'
],
function(
    $,
    PubSub,
    Str,
    MessageDrawerEvents,
    ContactsSection,
    RequestsSection
) {

    var SELECTORS = {
        ACTION_SHOW_CONTACTS_SECTION: '[data-action="show-contacts-section"]',
        ACTION_SHOW_REQUESTS_SECTION: '[data-action="show-requests-section"]',
        CONTACT_REQUEST_COUNT: '[data-region="contact-request-count"]',
        CONTACTS_SECTION_CONTAINER: '[data-section="contacts"]',
        REQUESTS_SECTION_CONTAINER: '[data-section="requests"]',
    };

    /**
     * Get the container element for the contacts section.
     *
     * @param {Object} body Contacts page body element.
     * @return {Object}
     */
    var getContactsSectionContainer = function(body) {
        return body.find(SELECTORS.CONTACTS_SECTION_CONTAINER);
    };

    /**
     * Get the container element for the requests section.
     *
     * @param {Object} body Contacts page body element.
     * @return {Object}
     */
    var getRequestsSectionContainer = function(body) {
        return body.find(SELECTORS.REQUESTS_SECTION_CONTAINER);
    };

    /**
     * Get the element that triggers showing the contacts section.
     *
     * @param {Object} body Contacts page body element.
     * @return {Object}
     */
    var getShowContactsAction = function(body) {
        return body.find(SELECTORS.ACTION_SHOW_CONTACTS_SECTION);
    };

    /**
     * Get the element that triggers showing the requests section.
     *
     * @param {Object} body Contacts page body element.
     * @return {Object}
     */
    var getShowRequestsAction = function(body) {
        return body.find(SELECTORS.ACTION_SHOW_REQUESTS_SECTION);
    };

    /**
     * Check if the given section is visible.
     *
     * @param {Object} sectionRoot The root element for the section
     * @return {Bool}
     */
    var isSectionVisible = function(sectionRoot) {
        return sectionRoot.hasClass('active');
    };

    /**
     * Decrement the contact request count. If the count is zero or below then
     * hide the count.
     *
     * @param {Object} body Conversation body container element.
     * @return {Function} A function to handle decrementing the count.
     */
    var decrementContactRequestCount = function(body) {
        return function() {
            var countContainer = body.find(SELECTORS.CONTACT_REQUEST_COUNT);
            var count = parseInt(countContainer.text(), 10);
            count = isNaN(count) ? 0 : count - 1;

            if (count <= 0) {
                countContainer.addClass('hidden');
            } else {
                countContainer.text(count);
            }
        };
    };

    /**
     * Listen to and handle events for contacts.
     *
     * @param {Object} body Contacts body container element.
     */
    var registerEventListeners = function(body) {
        var contactsSection = getContactsSectionContainer(body);
        var requestsSection = getRequestsSectionContainer(body);
        var showContactsAction = getShowContactsAction(body);
        var showRequestsAction = getShowRequestsAction(body);

        showContactsAction[0].addEventListener('show.bs.tab', function() {
            ContactsSection.show(contactsSection);
        });

        showRequestsAction[0].addEventListener('show.bs.tab', function() {
            RequestsSection.show(requestsSection);
        });

        PubSub.subscribe(MessageDrawerEvents.CONTACT_REQUEST_ACCEPTED, decrementContactRequestCount(body));
        PubSub.subscribe(MessageDrawerEvents.CONTACT_REQUEST_DECLINED, decrementContactRequestCount(body));
    };

    /**
     * Setup the contact page.
     *
     * @param {string} namespace The route namespace.
     * @param {Object} header Contacts header container element.
     * @param {Object} body Contacts body container element.
     * @param {Object} footer Contacts footer container element.
     * @param {String|null} tab Tab to show, either 'requests' or 'contacts', if any.
     * @return {Object} jQuery promise
     */
    var show = function(namespace, header, body, footer, tab) {
        body = $(body);

        if (!body.attr('data-contacts-init')) {
            registerEventListeners(body);
            body.attr('data-contacts-init', true);
        }

        var contactsSection = getContactsSectionContainer(body);
        var requestsSection = getRequestsSectionContainer(body);

        if (tab) {
            var showContactsAction = getShowContactsAction(body);
            var showRequestsAction = getShowRequestsAction(body);

            // Unfortunately we need to hardcode the class changes here rather than trigger
            // the bootstrap tab functionality because the bootstrap JS doesn't appear to be
            // loaded by this point which means the tab plugin isn't added and the event listeners
            // haven't been set up so we can't just trigger a click either.
            if (tab == 'requests') {
                showContactsAction.removeClass('active');
                contactsSection.removeClass('show active');
                showRequestsAction.addClass('active');
                requestsSection.addClass('show active');
            } else {
                showRequestsAction.removeClass('active');
                requestsSection.removeClass('show active');
                showContactsAction.addClass('active');
                contactsSection.addClass('show active');
            }
        }

        if (isSectionVisible(contactsSection)) {
            ContactsSection.show(contactsSection);
        } else {
            RequestsSection.show(requestsSection);
        }

        return $.Deferred().resolve().promise();
    };

    /**
     * String describing this page used for aria-labels.
     *
     * @return {Object} jQuery promise
     */
    var description = function() {
        return Str.get_string('messagedrawerviewcontacts', 'core_message');
    };

    return {
        show: show,
        description: description
    };
});
