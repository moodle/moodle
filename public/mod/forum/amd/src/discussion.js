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
 * Module for viewing a discussion.
 *
 * @module     mod_forum/discussion
 * @copyright  2019 Ryan Wyllie <ryan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
define(
[
    'jquery',
    'core/custom_interaction_events',
    'mod_forum/selectors',
    'core/pubsub',
    'mod_forum/forum_events',
    'core/str',
    'core/notification',
],
function(
    $,
    CustomEvents,
    Selectors,
    PubSub,
    ForumEvents,
    String,
    Notification
) {

    /**
     * Set the focus on the previous post in the list. Previous post is calculated
     * based on position in list as viewed top to bottom.
     *
     * @param {Object} currentPost The post that currently has focus
     */
    var focusPreviousPost = function(currentPost) {
        // See if there is a previous sibling post.
        var prevPost = currentPost.prev(Selectors.post.post);

        if (prevPost.length) {
            // The previous post might have replies that appear visually between
            // it and the current post (see nested view) so if that's the case
            // then the last reply will be the previous post in the list.
            var replyPost = prevPost.find(Selectors.post.post).last();

            if (replyPost.length) {
                // Focus the last reply.
                replyPost.focus();
            } else {
                // No replies so we can focus straight on the sibling.
                prevPost.focus();
            }
        } else {
            // If there are no siblings then jump up the tree to the parent
            // post and focus the first parent post we find.
            currentPost.parents(Selectors.post.post).first().focus();
        }
    };

    /**
     * Set the focus on the next post in the list. Previous post is calculated
     * based on position in list as viewed top to bottom.
     *
     * @param {Object} currentPost The post that currently has focus
     */
    var focusNextPost = function(currentPost) {
        // The next post in the visual list would be the first reply to this one
        // so let's see if we have one.
        var replyPost = currentPost.find(Selectors.post.post).first();

        if (replyPost.length) {
            // Got a reply.
            replyPost.focus();
        } else {
            // If we don't have a reply then the next post in the visual list would
            // be a sibling post (replying to the same parent).
            var siblingPost = currentPost.next(Selectors.post.post);

            if (siblingPost.length) {
                siblingPost.focus();
            } else {
                // No siblings either. That means we're the lowest level reply in a thread
                // so we need to walk back up the tree of posts and find an ancestor post that
                // has a sibling post we can focus.
                var parentPosts = currentPost.parents(Selectors.post.post).toArray();

                for (var i = 0; i < parentPosts.length; i++) {
                    var ancestorSiblingPost = $(parentPosts[i]).next(Selectors.post.post);

                    if (ancestorSiblingPost.length) {
                        ancestorSiblingPost.focus();
                        break;
                    }
                }
            }
        }
    };

    /**
     * Check if the element is inside the in page reply section.
     *
     * @param {Object} element The element to check
     * @return {Boolean}
     */
    var isElementInInPageReplySection = function(element) {
        var inPageReply = $(element).closest(Selectors.post.inpageReplyContent);
        return inPageReply.length ? true : false;
    };

    /**
     * Initialise the keyboard accessibility controls for the discussion.
     *
     * @param {Object} root The discussion root element
     */
    var initAccessibilityKeyboardNav = function(root) {
        var posts = root.find(Selectors.post.post);

        // Take each post action out of the tab index.
        posts.each(function(index, post) {
            var actions = $(post).find(Selectors.post.action);
            var firstAction = actions.first();
            actions.attr('tabindex', '-1');
            firstAction.attr('tabindex', 0);
        });

        CustomEvents.define(root, [
            CustomEvents.events.up,
            CustomEvents.events.down,
            CustomEvents.events.next,
            CustomEvents.events.previous,
            CustomEvents.events.home,
            CustomEvents.events.end,
        ]);

        root.on(CustomEvents.events.up, function(e, data) {
            var activeElement = document.activeElement;

            if (isElementInInPageReplySection(activeElement)) {
                // Focus is currently inside the in page reply section so don't move focus
                // to another post.
                return;
            }

            var focusPost = $(activeElement).closest(Selectors.post.post);

            if (focusPost.length) {
                focusPreviousPost(focusPost);
            } else {
                root.find(Selectors.post.post).first().focus();
            }

            data.originalEvent.preventDefault();
        });

        root.on(CustomEvents.events.down, function(e, data) {
            var activeElement = document.activeElement;

            if (isElementInInPageReplySection(activeElement)) {
                // Focus is currently inside the in page reply section so don't move focus
                // to another post.
                return;
            }

            var focusPost = $(activeElement).closest(Selectors.post.post);

            if (focusPost.length) {
                focusNextPost(focusPost);
            } else {
                root.find(Selectors.post.post).first().focus();
            }

            data.originalEvent.preventDefault();
        });

        root.on(CustomEvents.events.home, function(e, data) {
            if (isElementInInPageReplySection(document.activeElement)) {
                // Focus is currently inside the in page reply section so don't move focus
                // to another post.
                return;
            }
            root.find(Selectors.post.post).first().focus();
            data.originalEvent.preventDefault();
        });

        root.on(CustomEvents.events.end, function(e, data) {
            if (isElementInInPageReplySection(document.activeElement)) {
                // Focus is currently inside the in page reply section so don't move focus
                // to another post.
                return;
            }
            root.find(Selectors.post.post).last().focus();
            data.originalEvent.preventDefault();
        });

        root.on(CustomEvents.events.next, Selectors.post.action, function(e, data) {
            var currentAction = $(e.target);
            var container = currentAction.closest(Selectors.post.actionsContainer);
            var actions = container.find(Selectors.post.action);
            var nextAction = currentAction.next(Selectors.post.action);

            actions.attr('tabindex', '-1');

            if (!nextAction.length) {
                nextAction = actions.first();
            }

            nextAction.attr('tabindex', 0);
            nextAction.focus();

            data.originalEvent.preventDefault();
        });

        root.on(CustomEvents.events.previous, Selectors.post.action, function(e, data) {
            var currentAction = $(e.target);
            var container = currentAction.closest(Selectors.post.actionsContainer);
            var actions = container.find(Selectors.post.action);
            var nextAction = currentAction.prev(Selectors.post.action);

            actions.attr('tabindex', '-1');

            if (!nextAction.length) {
                nextAction = actions.last();
            }

            nextAction.attr('tabindex', 0);
            nextAction.focus();

            data.originalEvent.preventDefault();
        });

        root.on(CustomEvents.events.home, Selectors.post.action, function(e, data) {
            var currentAction = $(e.target);
            var container = currentAction.closest(Selectors.post.actionsContainer);
            var actions = container.find(Selectors.post.action);
            var firstAction = actions.first();

            actions.attr('tabindex', '-1');
            firstAction.attr('tabindex', 0);
            firstAction.focus();

            e.stopPropagation();
            data.originalEvent.preventDefault();
        });

        root.on(CustomEvents.events.end, Selectors.post.action, function(e, data) {
            var currentAction = $(e.target);
            var container = currentAction.closest(Selectors.post.actionsContainer);
            var actions = container.find(Selectors.post.action);
            var lastAction = actions.last();

            actions.attr('tabindex', '-1');
            lastAction.attr('tabindex', 0);
            lastAction.focus();

            e.stopPropagation();
            data.originalEvent.preventDefault();
        });

        PubSub.subscribe(ForumEvents.SUBSCRIPTION_TOGGLED, function(data) {
            var subscribed = data.subscriptionState;
            var updateMessage = subscribed ? 'discussionsubscribed' : 'discussionunsubscribed';
            String.get_string(updateMessage, "forum")
                .then(function(s) {
                    return Notification.addNotification({
                        message: s,
                        type: "info"
                    });
                })
                .catch(Notification.exception);
        });
    };

    return {
        init: function(root) {
            initAccessibilityKeyboardNav(root);
        }
    };
});
