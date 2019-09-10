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
 * Handle the manual locking of individual discussions
 *
 * @module     mod_forum/lock_toggle
 * @package    mod_forum
 * @copyright  2019 Peter Dias <peter@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
define([
        'jquery',
        'core/templates',
        'core/notification',
        'mod_forum/repository',
        'mod_forum/selectors',
        'core/custom_interaction_events',
    ], function(
        $,
        Templates,
        Notification,
        Repository,
        Selectors,
        CustomEvents
    ) {

    /**
     * Toggles the locked state of a discussion and refreshes the page.
     *
     * @param {Object} toggleElement
     */
    var toggleLockState = function(toggleElement) {
        var toggleElement = $(toggleElement);
        var forumId = toggleElement.data('forumid');
        var discussionId = toggleElement.data('discussionid');
        var state = toggleElement.data('state');

        Repository.setDiscussionLockState(forumId, discussionId, state)
            .then(function() {
                return location.reload();
            })
            .catch(Notification.exception);
    };

    /**
     * Register event listeners for the subscription toggle.
     *
     * @param {object} root The discussion list root element
     */
    var registerEventListeners = function(root) {
        root.on('click', Selectors.lock.toggle, function(e) {
            toggleLockState(this);

            e.preventDefault();
        });

        root.on(CustomEvents.events.activate, Selectors.lock.toggleSwitch, function(e) {
            toggleLockState(this);

            e.preventDefault();
        });
    };

    return {
        init: function(root) {
            registerEventListeners(root);
        }
    };
});
