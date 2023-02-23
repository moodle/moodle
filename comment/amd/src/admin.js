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
 * Comments admin management
 *
 * @module      core_comment/admin
 * @copyright   2022 Paul Holden <paulh@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

"use strict";

import {dispatchEvent} from 'core/event_dispatcher';
import Notification from 'core/notification';
import Pending from 'core/pending';
import {prefetchStrings} from 'core/prefetch';
import {get_string as getString} from 'core/str';
import {deleteComment, deleteComments} from 'core_comment/repository';
import * as reportEvents from 'core_reportbuilder/local/events';
import * as reportSelectors from 'core_reportbuilder/local/selectors';

const Selectors = {
    commentDelete: '[data-action="comment-delete"]',
    commentDeleteChecked: '[data-togglegroup="report-select-all"][data-toggle="slave"]:checked',
    commentDeleteSelected: '[data-action="comment-delete-selected"]',
};

/**
 * Initialise module
 */
export const init = () => {
    prefetchStrings('core_admin', [
        'confirmdeletecomments',
    ]);

    prefetchStrings('core', [
        'delete',
        'deleteselected'
    ]);

    document.addEventListener('click', event => {
        const commentDelete = event.target.closest(Selectors.commentDelete);
        if (commentDelete) {
            event.preventDefault();

            // Use triggerElement to return focus to the action menu toggle.
            const triggerElement = commentDelete.closest('.dropdown').querySelector('.dropdown-toggle');
            Notification.saveCancelPromise(
                getString('delete', 'core'),
                getString('confirmdeletecomments', 'core_admin'),
                getString('delete', 'core'),
                {triggerElement}
            ).then(() => {
                const pendingPromise = new Pending('core_comment/comment:delete');
                const reportElement = event.target.closest(reportSelectors.regions.report);

                return deleteComment(commentDelete.dataset.commentId)
                    .then(() => {
                        dispatchEvent(reportEvents.tableReload, {preservePagination: true}, reportElement);
                        return pendingPromise.resolve();
                    })
                    .catch(Notification.exception);
            }).catch(() => {
                return;
            });
        }

        const commentDeleteSelected = event.target.closest(Selectors.commentDeleteSelected);
        if (commentDeleteSelected) {
            event.preventDefault();

            const reportElement = document.querySelector(reportSelectors.regions.report);
            const commentDeleteChecked = reportElement.querySelectorAll(Selectors.commentDeleteChecked);
            if (commentDeleteChecked.length === 0) {
                return;
            }

            Notification.saveCancelPromise(
                getString('deleteselected', 'core'),
                getString('confirmdeletecomments', 'core_admin'),
                getString('delete', 'core'),
                {triggerElement: commentDeleteSelected}
            ).then(() => {
                const pendingPromise = new Pending('core_comment/comments:delete');
                const deleteCommentIds = [...commentDeleteChecked].map(check => check.value);

                return deleteComments(deleteCommentIds)
                    .then(() => {
                        dispatchEvent(reportEvents.tableReload, {preservePagination: true}, reportElement);
                        return pendingPromise.resolve();
                    })
                    .catch(Notification.exception);
            }).catch(() => {
                return;
            });
        }
    });
};
