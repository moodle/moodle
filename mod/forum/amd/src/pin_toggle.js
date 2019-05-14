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
 * This module is the highest level module for the calendar. It is
 * responsible for initialising all of the components required for
 * the calendar to run. It also coordinates the interaction between
 * components by listening for and responding to different events
 * triggered within the calendar UI.
 *
 * @module     mod_forum/pin_toggle
 * @package    mod_forum
 * @copyright  2018 Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
define([
    'jquery',
    'core/ajax',
    'core/str',
    'core/templates',
    'core/notification',
    'mod_forum/repository',
    'mod_forum/selectors',
    'core/str',
], function(
    $,
    Ajax,
    Str,
    Templates,
    Notification,
    Repository,
    Selectors,
    String
) {

    /**
     * Registery event listeners for the pin toggle.
     *
     * @param {object} root The calendar root element
     */
    var registerEventListeners = function(root) {
        root.on('click', Selectors.pin.toggle, function(e) {
            var toggleElement = $(this);
            var forumid = toggleElement.data('forumid');
            var discussionid = toggleElement.data('discussionid');
            var pinstate = toggleElement.data('targetstate');
            Repository.setPinDiscussionState(forumid, discussionid, pinstate)
                .then(function(context) {
                    return Templates.render('mod_forum/discussion_pin_toggle', context);
                })
                .then(function(html, js) {
                    return Templates.replaceNode(toggleElement, html, js);
                })
                .then(function() {
                    return String.get_string("pinupdated", "forum")
                        .done(function(s) {
                            return Notification.addNotification({
                                message: s,
                                type: "info"
                            });
                        });
                })
                .fail(Notification.exception);

            e.preventDefault();
        });
    };

    return {
        init: function(root) {
            registerEventListeners(root);
        }
    };
});