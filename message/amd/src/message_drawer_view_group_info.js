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
 * Controls the group info page of the message drawer.
 *
 * @module     core_message/message_drawer_view_group_info
 * @copyright  2018 Ryan Wyllie <ryan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
define(
[
    'jquery',
    'core/str',
    'core/templates',
    'core_message/message_repository',
    'core_message/message_drawer_lazy_load_list',
],
function(
    $,
    Str,
    Templates,
    Repository,
    LazyLoadList
) {

    var LOAD_MEMBERS_LIMIT = 50;

    var SELECTORS = {
        CONTENT_CONTAINER: '[data-region="group-info-content-container"]',
        MEMBERS_LIST: '[data-region="members-list"]',
    };

    var TEMPLATES = {
        CONTENT: 'core_message/message_drawer_view_group_info_body_content',
        MEMBERS_LIST: 'core_message/message_drawer_view_group_info_participants_list'
    };

    /**
     * Get the content container of the group info view container.
     *
     * @param {Object} root Contact container element.
     * @return {Object} jQuery object
     */
    var getContentContainer = function(root) {
        return root.find(SELECTORS.CONTENT_CONTAINER);
    };

    /**
     * Render the group info page.
     *
     * @param {Object} root Container element.
     * @param {Object} conversation The group conversation.
     * @param {Number} loggedInUserId The logged in user's id.
     * @return {Object} jQuery promise
     */
    var render = function(root, conversation, loggedInUserId) {
        var placeholderCount = conversation.totalMemberCount > 50 ? 50 : conversation.totalMemberCount;
        var placeholders = Array.apply(null, Array(placeholderCount)).map(function() {
            return true;
        });
        var templateContext = {
            name: conversation.name,
            subname: conversation.subname,
            imageurl: conversation.imageUrl,
            placeholders: placeholders,
            loggedinuser: {
                id: loggedInUserId
            }
        };

        return Templates.render(TEMPLATES.CONTENT, templateContext)
            .then(function(html) {
                getContentContainer(root).append(html);
                return html;
            });
    };

    /**
     * Get the callback to load members of the conversation.
     *
     * @param {Object} conversation The conversation
     * @param {Number} limit How many members to load
     * @param {Number} offset How many memebers to skip
     * @return {Function} the callback.
     */
    var getLoadMembersCallback = function(conversation, limit, offset) {
        return function(root, userId) {
            return Repository.getConversationMembers(conversation.id, userId, limit + 1, offset)
                .then(function(members) {
                    if (members.length > limit) {
                        members = members.slice(0, -1);
                    } else {
                        LazyLoadList.setLoadedAll(root, true);
                    }

                    offset = offset + limit;

                    // Filter out the logged in user so that they don't appear in the list.
                    return members.filter(function(member) {
                        return member.id != userId;
                    });
                });
        };
    };

    /**
     * Function to render the members in the list.
     *
     * @param {Object} contentContainer The list content container.
     * @param {Array} members The list of members to render
     * @return {Object} jQuery promise
     */
    var renderMembersCallback = function(contentContainer, members) {
        return Templates.render(TEMPLATES.MEMBERS_LIST, {contacts: members})
            .then(function(html) {
                contentContainer.append(html);
                return html;
            });
    };

    /**
     * Setup the contact page.
     *
     * @param {string} namespace The route namespace.
     * @param {Object} header Contact header container element.
     * @param {Object} body Contact body container element.
     * @param {Object} footer Contact body container element.
     * @param {Number} conversation The conversation
     * @param {Number} loggedInUserId The logged in user id
     * @return {Object} jQuery promise
     */
    var show = function(namespace, header, body, footer, conversation, loggedInUserId) {
        var root = $(body);

        getContentContainer(root).empty();
        return render(root, conversation, loggedInUserId)
            .then(function() {
                var listRoot = LazyLoadList.getRoot(root);
                LazyLoadList.show(
                    listRoot,
                    getLoadMembersCallback(conversation, LOAD_MEMBERS_LIMIT, 0),
                    renderMembersCallback
                );
                return;
            });
    };

    /**
     * String describing this page used for aria-labels.
     *
     * @param {Object} root Contact container element.
     * @param {Number} conversation The conversation
     * @return {Object} jQuery promise
     */
    var description = function(root, conversation) {
        return Str.get_string('messagedrawerviewgroupinfo', 'core_message', conversation.name);
    };

    return {
        show: show,
        description: description
    };
});
