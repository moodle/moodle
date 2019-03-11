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
 * Forum repository class to encapsulate all of the AJAX requests that
 * can be sent for forum.
 *
 * @module     mod_forum/repository
 * @package    mod_forum
 * @copyright  2019 Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
define(['core/ajax'], function(Ajax) {
    /**
     * Set the subscription state for a discussion in a forum.
     *
     * @param {number} forumId ID of the forum the discussion belongs to
     * @param {number} discussionId ID of the discussion with the subscription state
     * @param {boolean} targetState Set the subscribed state. True == subscribed; false == unsubscribed.
     * @return {object} jQuery promise
     */
    var setDiscussionSubscriptionState = function(forumId, discussionId, targetState) {
        var request = {
            methodname: 'mod_forum_set_subscription_state',
            args: {
                forumid: forumId,
                discussionid: discussionId,
                targetstate: targetState
            }
        };
        return Ajax.call([request])[0];
    };

    return {
        setDiscussionSubscriptionState: setDiscussionSubscriptionState,
    };
});
