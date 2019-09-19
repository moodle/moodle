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
 * Module for the list of discussions on when viewing a forum.
 *
 * @module     mod_forum/discussion_list
 * @package    mod_forum
 * @copyright  2019 Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
define([
    'jquery',
    'core/templates',
    'core/str',
    'core/notification',
    'mod_forum/subscription_toggle',
    'mod_forum/selectors',
    'mod_forum/repository',
    'core/pubsub',
    'mod_forum/forum_events',
], function(
    $,
    Templates,
    String,
    Notification,
    SubscriptionToggle,
    Selectors,
    Repository,
    PubSub,
    ForumEvents
) {
    var registerEventListeners = function(root) {
        PubSub.subscribe(ForumEvents.SUBSCRIPTION_TOGGLED, function(data) {
            var discussionId = data.discussionId;
            var subscribed = data.subscriptionState;
            var subscribedLabel = root.find(Selectors.discussion.item + '[data-discussionid= ' + discussionId + '] '
                + Selectors.discussion.subscribedLabel);
            if (subscribed) {
                subscribedLabel.removeAttr('hidden');
            } else {
                subscribedLabel.attr('hidden', true);
            }
        });

        root.on('click', Selectors.favourite.toggle, function() {
            var toggleElement = $(this);
            var forumId = toggleElement.data('forumid');
            var discussionId = toggleElement.data('discussionid');
            var subscriptionState = toggleElement.data('targetstate');
            Repository.setFavouriteDiscussionState(forumId, discussionId, subscriptionState)
                .then(function() {
                    return location.reload();
                })
                .catch(Notification.exception);
        });

        root.on('click', Selectors.pin.toggle, function(e) {
            e.preventDefault();
            var toggleElement = $(this);
            var forumId = toggleElement.data('forumid');
            var discussionId = toggleElement.data('discussionid');
            var state = toggleElement.data('targetstate');
            Repository.setPinDiscussionState(forumId, discussionId, state)
                .then(function() {
                    return location.reload();
                })
                .catch(Notification.exception);
        });

        root.on('click', Selectors.lock.toggle, function(e) {
            var toggleElement = $(this);
            var forumId = toggleElement.data('forumid');
            var discussionId = toggleElement.data('discussionid');
            var state = toggleElement.data('state');

            Repository.setDiscussionLockState(forumId, discussionId, state)
                .then(function(context) {
                    var icon = toggleElement.parents(Selectors.summary.actions).find(Selectors.lock.icon);
                    var lockedLabel = toggleElement.parents(Selectors.discussion.item).find(Selectors.discussion.lockedLabel);
                    if (context.locked) {
                        icon.removeClass('hidden');
                        lockedLabel.removeAttr('hidden');
                    } else {
                        icon.addClass('hidden');
                        lockedLabel.attr('hidden', true);
                    }
                    return context;
                })
                .then(function(context) {
                    context.forumid = forumId;
                    return Templates.render('mod_forum/discussion_lock_toggle', context);
                })
                .then(function(html, js) {
                    return Templates.replaceNode(toggleElement, html, js);
                })
                .then(function() {
                    return String.get_string('lockupdated', 'forum')
                        .done(function(s) {
                            return Notification.addNotification({
                                message: s,
                                type: "info"
                            });
                        });
                })
                .catch(Notification.exception);

            e.preventDefault();
        });
    };

    return {
        init: function(root) {
            SubscriptionToggle.init(root, true, function(toggleElement, context) {
                return Templates.render('mod_forum/discussion_subscription_toggle', context)
                    .then(function(html, js) {
                        return Templates.replaceNode(toggleElement, html, js);
                    });
            });
            registerEventListeners(root);
        }
    };
});
