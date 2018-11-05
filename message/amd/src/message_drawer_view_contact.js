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
 * Controls the contact page in the message drawer.
 *
 * @module     core_message/message_drawer_view_contact
 * @copyright  2018 Ryan Wyllie <ryan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
define(
[
    'jquery',
    'core/str',
    'core/templates'
],
function(
    $,
    Str,
    Templates
) {

    var SELECTORS = {
        CONTENT_CONTAINER: '[data-region="content-container"]'
    };

    var TEMPLATES = {
        CONTENT: 'core_message/message_drawer_view_contact_body_content'
    };

    /**
     * Get the content container of the contact view container.
     *
     * @param {Object} root Contact container element.
     * @returns {Object} jQuery object
     */
    var getContentContainer = function(root) {
        return root.find(SELECTORS.CONTENT_CONTAINER);
    };

    /**
     * Render the contact profile in the content container.
     *
     * @param {Object} root Contact container element.
     * @param {Object} profile Contact profile details.
     * @returns {Object} jQuery promise
     */
    var render = function(root, profile) {
        return Templates.render(TEMPLATES.CONTENT, profile)
            .then(function(html) {
                getContentContainer(root).append(html);
                return html;
            });
    };

    /**
     * Setup the contact page.
     *
     * @param {Object} root Contact container element.
     * @param {Object} contact The contact object.
     * @returns {Object} jQuery promise
     */
    var show = function(root, contact) {
        root = $(root);

        getContentContainer(root).empty();
        return render(root, contact);
    };

    /**
     * String describing this page used for aria-labels.
     *
     * @param {Object} root Contact container element.
     * @param {Object} contact The contact object.
     * @return {Object} jQuery promise
     */
    var description = function(root, contact) {
        return Str.get_string('messagedrawerviewcontact', 'core_message', contact.fullname);
    };

    return {
        show: show,
        description: description
    };
});
