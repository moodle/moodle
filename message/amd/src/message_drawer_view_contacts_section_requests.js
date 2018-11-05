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
 * Controls the requests section of the contacts page.
 *
 * @module     core_message/message_drawer_view_contacts_section_requests
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
    MessageDrawerEvents,
    LazyLoadList
) {

    var SELECTORS = {
        CONTACT_REQUEST: '[data-region="contact-request"]'
    };

    var TEMPLATES = {
        REQUESTS_LIST: 'core_message/message_drawer_view_contacts_body_section_requests_list'
    };

    /**
     * Render the requests in the content container.
     *
     * @param {Object} contentContainer List container element.
     * @param {Array} requests List of requests.
     * @return {Object} jQuery promise
     */
    var render = function(contentContainer, requests) {
        var formattedRequests = requests.map(function(request) {
            return {
                // This is actually the user id.
                id: request.id,
                profileimageurl: request.profileimageurl,
                fullname: request.fullname
            };
        });
        return Templates.render(TEMPLATES.REQUESTS_LIST, {requests: formattedRequests})
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
        return MessageRepository.getContactRequests(userId)
            .then(function(requests) {
                LazyLoadList.setLoadedAll(listRoot, true);
                return requests;
            })
            .catch(Notification.exception);
    };

    /**
     * Handle when a contact request is accepted or declined by removing the contact
     * list from the page.
     *
     * @param {Object} root The section root element
     * @return {Function} The event handler function
     */
    var handleContactRequestProcessed = function(root) {
        return function(request) {
            root.find('[data-request-id="' + request.userid + '"]').remove();
            var contactRequests = root.find(SELECTORS.CONTACT_REQUEST);

            if (!contactRequests.length) {
                LazyLoadList.showEmptyMessage(root);
                LazyLoadList.hideContent(root);
            }
        };
    };

    /**
     * Listen for any events that might affect the requests section.
     *
     * @param {Object} root The section root element
     */
    var registerEventListeners = function(root) {
        PubSub.subscribe(MessageDrawerEvents.CONTACT_REQUEST_ACCEPTED, handleContactRequestProcessed(root));
        PubSub.subscribe(MessageDrawerEvents.CONTACT_REQUEST_DECLINED, handleContactRequestProcessed(root));
    };

    /**
     * Setup the requests section.
     *
     * @param {Object} root Requests section container.
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
