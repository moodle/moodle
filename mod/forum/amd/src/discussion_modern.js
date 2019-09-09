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
 * Module for viewing a discussion in modern view.
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
 * Show the in page reply form in the given in page reply container. The form
 * display will be animated.
 *
 * @param {Object} inPageReplyContainer jQuery element for the in page reply container
 * @param {Function} afterAnimationCallback Callback after animation completes
 */
const showInPageReplyForm = (inPageReplyContainer, afterAnimationCallback) => {
    const form = inPageReplyContainer.find(Selectors.post.inpageReplyContent);

    form.slideDown({
        duration: ANIMATION_DURATION,
        queue: false,
        complete: afterAnimationCallback
    }).css('display', 'none').fadeIn(ANIMATION_DURATION);
};

/**
 * Hide the in page reply form in the given in page reply container. The form
 * display will be animated.
 *
 * @param {Object} inPageReplyContainer jQuery element for the in page reply container
 * @param {Function} afterAnimationCallback Callback after animation completes
 */
const hideInPageReplyForm = (inPageReplyContainer, afterAnimationCallback) => {
    const form = inPageReplyContainer.find(Selectors.post.inpageReplyContent);

    form.slideUp({
        duration: ANIMATION_DURATION,
        queue: false,
        complete: afterAnimationCallback
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

    return Templates.render('mod_forum/inpage_reply_modern', context);
};

/**
 * Create all of the event listeners for the discussion.
 *
 * @param {Object} root jQuery element for the discussion container
 * @param {Object} additionalTemplateContext Additional render context for the in page reply template
 */
const registerEventListeners = (root, additionalTemplateContext) => {
    CustomEvents.define(root, [CustomEvents.events.activate]);
    // Auto expanding text area for in page reply.
    AutoRows.init(root);

    // Reply button is clicked.
    root.on(CustomEvents.events.activate, Selectors.post.inpageReplyCreateButton, async (e, data) => {
        data.originalEvent.preventDefault();

        const button = $(e.currentTarget);
        const postContainer = getPostContainer(button);
        const inPageReplyContainer = getInPageReplyContainer(postContainer);

        if (!hasInPageReplyForm(inPageReplyContainer)) {
            try {
                const html = await renderInPageReplyTemplate(additionalTemplateContext, button, postContainer);
                Templates.appendNodeContents(inPageReplyContainer, html, '');
            } catch (e) {
                Notification.exception(e);
            }
        }

        button.fadeOut(ANIMATION_DURATION, () => {
            showInPageReplyForm(inPageReplyContainer, () => {
                inPageReplyContainer.find(Selectors.post.inpageReplyContent).find('textarea').focus();
            });
        });
    });

    // Cancel in page reply button.
    root.on(CustomEvents.events.activate, Selectors.post.inpageReplyCancelButton, (e, data) => {
        data.originalEvent.preventDefault();

        const button = $(e.currentTarget);
        const postContainer = getPostContainer(button);
        const postContentContainer = getPostContentContainer(postContainer);
        const inPageReplyContainer = getInPageReplyContainer(postContainer);
        const inPageReplyCreateButton = postContentContainer.find(Selectors.post.inpageReplyCreateButton);
        hideInPageReplyForm(inPageReplyContainer, () => {
            inPageReplyCreateButton.fadeIn(ANIMATION_DURATION);
        });
    });
};

/**
 * Initialise the javascript for the discussion in modern display mode.
 *
 * @param {Object} root jQuery element for the discussion container
 * @param {Object} context Additional render context for the in page reply template
 */
export const init = (root, context) => {
    // Add discussion event listeners.
    registerEventListeners(root, context);
    // Add default discussion javascript (keyboard nav etc).
    Discussion.init(root);
    // Add in page reply javascript.
    InPageReply.init(root);

    // Initialise the settings menu javascript.
    const discussionToolsContainer = root.find('[data-container="discussion-tools"]');
    LockToggle.init(discussionToolsContainer);
    FavouriteToggle.init(discussionToolsContainer);
    Pin.init(discussionToolsContainer);
};
