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
 * This module handles the creation of a Modal that shows the user's post in context of the entire discussion.
 *
 * @module     mod_forum/grades/expandconversation
 * @package    mod_forum
 * @copyright  2019 Mathew May <mathew.solutions>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
import * as ForumSelectors from './grader/selectors';
import Repository from 'mod_forum/repository';
import {exception as showException} from "core/notification";
import Templates from 'core/templates';
import * as Modal from 'core/modal_factory';
import * as ModalEvents from 'core/modal_events';

/**
 * Find the Node containing the gradable details from the provided node by searching up the tree.
 *
 * @param {HTMLElement} node
 * @returns {HTMLElement}
 */
const findGradableNode = node => node.closest(ForumSelectors.expandConversation);

/**
 * Show the post in context in a modal.
 *
 * @param {HTMLElement} rootNode The button that has been clicked
 */
const showPostInContext = async(rootNode) => {
    const postId = rootNode.dataset.postid;
    const discussionId = rootNode.dataset.discussionid;
    const discussionName = rootNode.dataset.name;

    const [
        allPosts,
        modal,
    ] = await Promise.all([
        Repository.getDiscussionPosts(parseInt(discussionId)),
        Modal.create({
            title: discussionName,
            large: true,
            type: Modal.types.CANCEL
        }),
    ]);

    const userPosts = allPosts.posts.map((post) => {
        post.subject = null;
        post.readonly = true;
        post.html.rating = null;

        return post;
    });

    // Handle hidden event.
    modal.getRoot().on(ModalEvents.hidden, function() {
        // Destroy when hidden.
        modal.destroy();
    });

    modal.show();

    // Note: We do not use await here because it messes with the Modal transitions.
    const templatePromise = Templates.render('mod_forum/grades/grader/discussion/post_modal', userPosts);
    modal.setBody(templatePromise);
    // eslint-disable-next-line promise/catch-or-return
    templatePromise.then(() => {
        const relevantPost = modal.getRoot()[0].querySelector(`#p${postId}`);
        if (relevantPost) {
            relevantPost.scrollIntoView({behavior: "smooth"});
        }

        return;
    });
};

/**
 * Register event listeners for the expand conversations button.
 *
 * @param {HTMLElement} rootNode The root to listen to.
 */
export const registerEventListeners = (rootNode) => {
    rootNode.addEventListener('click', (e) => {
        const rootNode = findGradableNode(e.target);

        if (rootNode) {
            e.preventDefault();

            try {
                showPostInContext(rootNode);
            } catch (err) {
                showException(err);
            }
        }
    });
};
