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
 * Module for viewing a discussion in nested v2 view.
 *
 * @copyright  2019 Ryan Wyllie <ryan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
import $ from 'jquery';
import AutoRows from 'core/auto_rows';
import CustomEvents from 'core/custom_interaction_events';
import Notification from 'core/notification';
import Templates from 'core/templates';
import Discussion from 'mod_forum/discussion';
import InPageReply from 'mod_forum/inpage_reply';
import LockToggle from 'mod_forum/lock_toggle';
import FavouriteToggle from 'mod_forum/favourite_toggle';
import Pin from 'mod_forum/pin_toggle';
import Selectors from 'mod_forum/selectors';
import Subscribe from 'mod_forum/subscription_toggle';

const ANIMATION_DURATION = 150;

/**
 * Get the closest post container element from the given element.
 *
 * @param {Object} element jQuery element to search from
 * @return {Object} jQuery element
 */
const getPostContainer = (element) => {
    return element.closest(Selectors.post.post);
};

/**
 * Get the closest post container element from the given element.
 *
 * @param {Object} element jQuery element to search from
 * @param {Number} id Id of the post to find.
 * @return {Object} jQuery element
 */
const getPostContainerById = (element, id) => {
    return element.find(`${Selectors.post.post}[data-post-id=${id}]`);
};

/**
 * Get the parent post container elements from the given element.
 *
 * @param {Object} element jQuery element to search from
 * @return {Object} jQuery element
 */
const getParentPostContainers = (element) => {
    return element.parents(Selectors.post.post);
};

/**
 * Get the post content container element from the post container element.
 *
 * @param {Object} postContainer jQuery element for the post container
 * @return {Object} jQuery element
 */
const getPostContentContainer = (postContainer) => {
    return postContainer.children().not(Selectors.post.repliesContainer).find(Selectors.post.forumCoreContent);
};

/**
 * Get the in page reply container element from the post container element.
 *
 * @param {Object} postContainer jQuery element for the post container
 * @return {Object} jQuery element
 */
const getInPageReplyContainer = (postContainer) => {
    return postContainer.children().filter(Selectors.post.inpageReplyContainer);
};

/**
 * Get the in page reply form element from the post container element.
 *
 * @param {Object} postContainer jQuery element for the post container
 * @return {Object} jQuery element
 */
const getInPageReplyForm = (postContainer) => {
    return getInPageReplyContainer(postContainer).find(Selectors.post.inpageReplyContent);
};

/**
 * Get the in page reply create (reply) button element from the post container element.
 *
 * @param {Object} postContainer jQuery element for the post container
 * @return {Object} jQuery element
 */
const getInPageReplyCreateButton = (postContainer) => {
    return getPostContentContainer(postContainer).find(Selectors.post.inpageReplyCreateButton);
};

/**
 * Get the replies visibility toggle container (show/hide replies button container) element
 * from the post container element.
 *
 * @param {Object} postContainer jQuery element for the post container
 * @return {Object} jQuery element
 */
const getRepliesVisibilityToggleContainer = (postContainer) => {
    return postContainer.children(Selectors.post.repliesVisibilityToggleContainer);
};

/**
 * Get the replies container element from the post container element.
 *
 * @param {Object} postContainer jQuery element for the post container
 * @return {Object} jQuery element
 */
const getRepliesContainer = (postContainer) => {
    return postContainer.children(Selectors.post.repliesContainer);
};

/**
 * Check if the post has any replies.
 *
 * @param {Object} postContainer jQuery element for the post container
 * @return {Bool}
 */
const hasReplies = (postContainer) => {
    return getRepliesContainer(postContainer).children().length > 0;
};

/**
 * Get the show replies button element from the replies visibility toggle container element.
 *
 * @param {Object} replyVisibilityToggleContainer jQuery element for the toggle container
 * @return {Object} jQuery element
 */
const getShowRepliesButton = (replyVisibilityToggleContainer) => {
    return replyVisibilityToggleContainer.find(Selectors.post.showReplies);
};

/**
 * Get the hide replies button element from the replies visibility toggle container element.
 *
 * @param {Object} replyVisibilityToggleContainer jQuery element for the toggle container
 * @return {Object} jQuery element
 */
const getHideRepliesButton = (replyVisibilityToggleContainer) => {
    return replyVisibilityToggleContainer.find(Selectors.post.hideReplies);
};

/**
 * Check if the replies are visible.
 *
 * @param {Object} postContainer jQuery element for the post container
 * @return {Bool}
 */
const repliesVisible = (postContainer) => {
    const repliesContainer = getRepliesContainer(postContainer);
    return repliesContainer.is(':visible');
};

/**
 * Show the post replies.
 *
 * @param {Object} postContainer jQuery element for the post container
 * @param {Number|null} postIdToSee Id of the post to scroll into view (if any)
 */
const showReplies = (postContainer, postIdToSee = null) => {
    const repliesContainer = getRepliesContainer(postContainer);
    const replyVisibilityToggleContainer = getRepliesVisibilityToggleContainer(postContainer);
    const showButton = getShowRepliesButton(replyVisibilityToggleContainer);
    const hideButton = getHideRepliesButton(replyVisibilityToggleContainer);

    showButton.addClass('hidden');
    hideButton.removeClass('hidden');

    repliesContainer.slideDown({
        duration: ANIMATION_DURATION,
        queue: false,
        complete: () => {
            if (postIdToSee) {
                const postContainerToSee = getPostContainerById(repliesContainer, postIdToSee);
                if (postContainerToSee.length) {
                    postContainerToSee[0].scrollIntoView();
                }
            }
        }
    }).css('display', 'none').fadeIn(ANIMATION_DURATION);
};

/**
 * Hide the post replies.
 *
 * @param {Object} postContainer jQuery element for the post container
 */
const hideReplies = (postContainer) => {
    const repliesContainer = getRepliesContainer(postContainer);
    const replyVisibilityToggleContainer = getRepliesVisibilityToggleContainer(postContainer);
    const showButton = getShowRepliesButton(replyVisibilityToggleContainer);
    const hideButton = getHideRepliesButton(replyVisibilityToggleContainer);

    showButton.removeClass('hidden');
    hideButton.addClass('hidden');

    repliesContainer.slideUp({
        duration: ANIMATION_DURATION,
        queue: false
    }).fadeOut(ANIMATION_DURATION);
};

/** Variable to hold the showInPageReplyForm function after it's built. */
let showInPageReplyForm = null;

/**
 * Build the showInPageReplyForm function with the given additional template context.
 *
 * @param {Object} additionalTemplateContext Additional render context for the in page reply template.
 * @return {Function}
 */
const buildShowInPageReplyFormFunction = (additionalTemplateContext) => {
    /**
     * Show the in page reply form in the given in page reply container. The form
     * display will be animated.
     *
     * @param {Object} postContainer jQuery element for the post container
     */
    return async (postContainer) => {

        const inPageReplyContainer = getInPageReplyContainer(postContainer);
        const repliesVisibilityToggleContainer = getRepliesVisibilityToggleContainer(postContainer);
        const inPageReplyCreateButton = getInPageReplyCreateButton(postContainer);

        if (!hasInPageReplyForm(inPageReplyContainer)) {
            try {
                const html = await renderInPageReplyTemplate(additionalTemplateContext, inPageReplyCreateButton, postContainer);
                Templates.appendNodeContents(inPageReplyContainer, html, '');
            } catch (e) {
                Notification.exception(e);
            }
        }

        inPageReplyCreateButton.fadeOut(ANIMATION_DURATION, () => {
            const inPageReplyForm = getInPageReplyForm(postContainer);
            inPageReplyForm.slideDown({
                duration: ANIMATION_DURATION,
                queue: false,
                complete: () => {
                    inPageReplyForm.find('textarea').focus();
                }
            }).css('display', 'none').fadeIn(ANIMATION_DURATION);

            if (repliesVisibilityToggleContainer.length && hasReplies(postContainer)) {
                repliesVisibilityToggleContainer.fadeIn(ANIMATION_DURATION);
                hideReplies(postContainer);
            }
        });
    };
};

/**
 * Hide the in page reply form in the given in page reply container. The form
 * display will be animated.
 *
 * @param {Object} postContainer jQuery element for the post container
 * @param {Number|null} postIdToSee Id of the post to scroll into view (if any)
 */
const hideInPageReplyForm = (postContainer, postIdToSee = null) => {
    const inPageReplyForm = getInPageReplyForm(postContainer);
    const inPageReplyCreateButton = getInPageReplyCreateButton(postContainer);
    const repliesVisibilityToggleContainer = getRepliesVisibilityToggleContainer(postContainer);

    if (repliesVisibilityToggleContainer.length && hasReplies(postContainer)) {
        repliesVisibilityToggleContainer.fadeOut(ANIMATION_DURATION);
        if (!repliesVisible(postContainer)) {
            showReplies(postContainer, postIdToSee);
        }
    }

    inPageReplyForm.slideUp({
        duration: ANIMATION_DURATION,
        queue: false,
        complete: () => {
            inPageReplyCreateButton.fadeIn(ANIMATION_DURATION);
        }
    }).fadeOut(200);
};

/**
 * Check if the in page reply container contains the in page reply form.
 *
 * @param {Object} inPageReplyContainer jQuery element for the in page reply container
 * @return {Bool}
 */
const hasInPageReplyForm = (inPageReplyContainer) => {
    return inPageReplyContainer.find(Selectors.post.inpageReplyContent).length > 0;
};

/**
 * Render the template to generate the in page reply form HTML.
 *
 * @param {Object} additionalTemplateContext Additional render context for the in page reply template
 * @param {Object} button jQuery element for the reply button that was clicked
 * @param {Object} postContainer jQuery element for the post container
 * @return {Object} jQuery promise
 */
const renderInPageReplyTemplate = (additionalTemplateContext, button, postContainer) => {
    const postContentContainer = getPostContentContainer(postContainer);
    const currentSubject = postContentContainer.find(Selectors.post.forumSubject).text();
    const currentAuthorName = postContentContainer.find(Selectors.post.authorName).text();
    const context = {
        postid: postContainer.data('post-id'),
        "reply_url": button.attr('data-href'),
        sesskey: M.cfg.sesskey,
        parentsubject: currentSubject,
        parentauthorname: currentAuthorName,
        canreplyprivately: button.data('can-reply-privately'),
        postformat: InPageReply.CONTENT_FORMATS.MOODLE,
        ...additionalTemplateContext
    };

    return Templates.render('mod_forum/inpage_reply_v2', context);
};

/**
 * Increment the total reply count in the show/hide replies buttons for the post.
 *
 * @param {Object} postContainer jQuery element for the post container
 */
const incrementTotalReplyCount = (postContainer) => {
    getRepliesVisibilityToggleContainer(postContainer).find(Selectors.post.replyCount).each((index, element) => {
        const currentCount = parseInt(element.innerText, 10);
        element.innerText = currentCount + 1;
    });
};

/**
 * Create all of the event listeners for the discussion.
 *
 * @param {Object} root jQuery element for the discussion container
 */
const registerEventListeners = (root) => {
    CustomEvents.define(root, [CustomEvents.events.activate]);
    // Auto expanding text area for in page reply.
    AutoRows.init(root);

    // Reply button is clicked.
    root.on(CustomEvents.events.activate, Selectors.post.inpageReplyCreateButton, (e, data) => {
        data.originalEvent.preventDefault();
        const postContainer = getPostContainer($(e.currentTarget));
        showInPageReplyForm(postContainer);
    });

    // Cancel in page reply button.
    root.on(CustomEvents.events.activate, Selectors.post.inpageReplyCancelButton, (e, data) => {
        data.originalEvent.preventDefault();
        const postContainer = getPostContainer($(e.currentTarget));
        hideInPageReplyForm(postContainer);
    });

    // Show replies button clicked.
    root.on(CustomEvents.events.activate, Selectors.post.showReplies, (e, data) => {
        data.originalEvent.preventDefault();
        const postContainer = getPostContainer($(e.target));
        showReplies(postContainer);
    });

    // Hide replies button clicked.
    root.on(CustomEvents.events.activate, Selectors.post.hideReplies, (e, data) => {
        data.originalEvent.preventDefault();
        const postContainer = getPostContainer($(e.target));
        hideReplies(postContainer);
    });

    // Post created with in page reply.
    root.on(InPageReply.EVENTS.POST_CREATED, Selectors.post.inpageSubmitBtn, (e, newPostId) => {
        const currentTarget = $(e.currentTarget);
        const postContainer = getPostContainer(currentTarget);
        const postContainers = getParentPostContainers(currentTarget);
        hideInPageReplyForm(postContainer, newPostId);

        postContainers.each((index, container) => {
            incrementTotalReplyCount($(container));
        });
    });
};

/**
 * Initialise the javascript for the discussion in nested v2 display mode.
 *
 * @param {Object} root jQuery element for the discussion container
 * @param {Object} context Additional render context for the in page reply template
 */
export const init = (root, context) => {
    // Build the showInPageReplyForm function with the additional render context.
    showInPageReplyForm = buildShowInPageReplyFormFunction(context);
    // Add discussion event listeners.
    registerEventListeners(root);
    // Initialise default discussion javascript (keyboard nav etc).
    Discussion.init(root);
    // Add in page reply javascript.
    InPageReply.init(root);

    // Initialise the settings menu javascript.
    const discussionToolsContainer = root.find(Selectors.discussion.tools);
    LockToggle.init(discussionToolsContainer, false);
    FavouriteToggle.init(discussionToolsContainer, false, (toggleElement, response) => {
        const newTargetState = response.userstate.favourited ? 0 : 1;
        return toggleElement.data('targetstate', newTargetState);
    });
    Pin.init(discussionToolsContainer, false, (toggleElement, response) => {
        const newTargetState = response.pinned ? 0 : 1;
        return toggleElement.data('targetstate', newTargetState);
    });
    Subscribe.init(discussionToolsContainer, false, (toggleElement, response) => {
        const newTargetState = response.userstate.subscribed ? 0 : 1;
        toggleElement.data('targetstate', newTargetState);
    });
};
