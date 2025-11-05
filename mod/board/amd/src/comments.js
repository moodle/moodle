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

import Ajax from 'core/ajax';
import Notification from 'core/notification';
import Templates from 'core/templates';
import emojiPicker from 'core/emoji/picker';
import * as Str from 'core/str';

/**
 * Fetch the comments for a given post.
 *
 * @param {int} noteId Number of the page.
 * @param {Node} element The element to render the comments into.
 */
const fetchFor = (noteId, element) => {
    Ajax.call([
        {
            methodname: 'mod_board_get_comments',
            args: {
                noteid: noteId
            },
            done: (response) => {
                return renderComments(response, element);
            },
            fail: Notification.exception
        }
    ], false);
};

/**
 * Render the comments.
 *
 * @param {Object} response fetched from the server
 * @param {DomNode} element containing the comments
 * @returns {Promise} resolved when the comments are rendered
 */
const renderComments = (response, element) => {
    return Templates.renderForPromise('mod_board/commentcontainer', response)
        .then(({html, js}) => {
            Templates.replaceNodeContents(element, html, js);
            return;
        })
        .catch();
};

/**
 * Save a comment.
 *
 * @param {Int} noteId The note id this comment is for
 * @param {String} comment The comment text
 */
const saveComment = (noteId, comment) => {
    Ajax.call([
        {
            methodname: 'mod_board_add_comment',
            args: {
                noteid: noteId,
                content: comment
            },
            done: () => {
                fetchFor(noteId, document.querySelector('[data-region="comment-area"]'));
            },
            fail: Notification.exception
        }
    ], false);
};

/**
 * Delete a comment.
 *
 * @param {Int} noteId The note id this comment is for
 * @param {*} commentId The comment id to delete
 */
const deleteComment = (noteId, commentId) => {
    Ajax.call([
        {
            methodname: 'mod_board_delete_comment',
            args: {
                commentid: commentId
            },
            done: () => {
                fetchFor(noteId, document.querySelector('[data-region="comment-area"]'));
            },
            fail: Notification.exception
        }
    ], false);
};

/**
 * Initialise the comment area.
 *
 */
const init = () => {
    const board = document.querySelector('.mod_board');
    if (board.dataset.init) {
        return;
    }
    board.dataset.init = 1;

    Str.get_string('addcomment', 'mod_board').then(s => {
        const style = document.createElement('style');
        style.innerHTML = `
            .comment-input:empty:before {
                content: '${s}';
            }
        `;
        document.head.appendChild(style);
        return '';
    }).fail(Notification.exception);

    document.addEventListener('click', e => {
        const commentbox = e.target.closest('[data-region="commentbox"]');
        if (!commentbox) {
            return;
        }
        const noteId = parseInt(commentbox.dataset.noteid);
        const commentInput = commentbox.querySelector('[data-region="commentinput"]');
        const commentControls = commentbox.querySelector('[data-region="comment-controls"]');
        const emojiPickerContainer = commentbox.querySelector('[data-region="emoji-picker-container"]');

        const postCommentClick = e.target.closest('[data-action="postcomment"]');
        const cancelCommentClick = e.target.closest('[data-action="cancelcomment"]');
        const deleteCommentClick = e.target.closest('[data-action="deletecomment"]');
        const emojiClick = e.target.closest('[data-action="toggle-emoji-picker"]');

        if (postCommentClick) {
            e.preventDefault();
            if (!postCommentClick.dataset.disabled) {
                postCommentClick.dataset.disabled = 1;
                saveComment(noteId, commentInput.innerHTML);
            }
        }
        if (cancelCommentClick) {
            e.preventDefault();
            commentInput.innerHTML = '';
            commentControls.classList.remove('show');
        }
        if (deleteCommentClick) {
            e.preventDefault();
            if (!deleteCommentClick.dataset.disabled) {
                deleteCommentClick.dataset.disabled = 1;
                deleteComment(noteId, deleteCommentClick.dataset.commentid);
            }
        }
        if (emojiClick) {
            e.preventDefault();
            if (!emojiPickerContainer.dataset.init) {
                emojiPickerContainer.dataset.init = 1;
                emojiPicker(emojiPickerContainer, (emoji) => {
                    commentInput.innerHTML = commentInput.innerHTML + '&nbsp;' + emoji + '&nbsp;';
                });
            }
            emojiPickerContainer.classList.toggle('hidden');
        }
    });
    document.addEventListener('focusin', e => {
        const inputCommentClick = e.target.closest('[data-region="commentinput"]');
        if (inputCommentClick) {
            e.preventDefault();
            const commentbox = e.target.closest('[data-region="commentbox"]');
            const commentControls = commentbox.querySelector('[data-region="comment-controls"]');
            commentControls.classList.add('show');
        }
    });
};

export default {
    init: init,
    fetchFor: fetchFor
};
