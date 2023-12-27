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
 * Column selector js.
 *
 * @module    qbank_comment/comment
 * @copyright 2021 Catalyst IT Australia Pty Ltd
 * @author    Safat Shahin <safatshahin@catalyst-au.net>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import Fragment from 'core/fragment';
import {get_string as getString} from 'core/str';
import ModalEvents from 'core/modal_events';
import SaveCancelModal from 'core/modal_save_cancel';

/**
 * Event listeners for the module.
 *
 * @method clickEvent
 * @param {Number} questionId
 * @param {Number} courseID
 * @param {Number} contextId
 */
const commentEvent = async(questionId, courseID, contextId) => {
    const args = {
        questionid: questionId,
        courseid: courseID
    };
    const modal = await SaveCancelModal.create({
        title: getString('commentheader', 'qbank_comment'),
        body: Fragment.loadFragment('qbank_comment', 'question_comment', contextId, args),
        large: true,
        show: true,
        buttons: {
            save: getString('addcomment', 'qbank_comment'),
            cancel: getString('close', 'qbank_comment'),
        },
        removeOnClose: true,
    });
    const root = modal.getRoot();

    // Don't display the default add comment link in the modal.
    root.on(ModalEvents.bodyRendered, function() {
        const submitlink = document.querySelectorAll("div.comment-area a")[0];
        submitlink.style.display = 'none';
    });

    // Version selection event.
    root.on('change', '#question_comment_version_dropdown', (e) =>{
        args.questionid = e.target.value;
        modal.setBody(Fragment.loadFragment('qbank_comment', 'question_comment', contextId, args));
    });

    // Reload the page when the modal is closed.
    root.on(ModalEvents.hidden, () => location.reload());

    // Handle adding the comment when the button in the modal is clicked.
    root.on(ModalEvents.save, function(e) {
        e.preventDefault();
        const submitlink = document.querySelectorAll("div.comment-area a")[0];
        const textarea = document.querySelectorAll("div.comment-area textarea")[0];

        // Check there is a valid comment to add, and trigger adding if there is.
        if (textarea.value != textarea.getAttribute('aria-label') && textarea.value != '') {
            submitlink.click();
        }

    });
};

/**
 * Entrypoint of the js.
 *
 * @method init
 */
export const init = () => {
    const target = document.querySelector('#categoryquestions');
    if (target !== null) {
        target.addEventListener('click', (e) => {
            if (e.target.dataset.target && e.target.dataset.target.includes('questioncommentpreview')) {
                // Call for the event listener to listed for clicks in any comment count row.
                commentEvent(e.target.dataset.questionid, e.target.dataset.courseid, e.target.dataset.contextid);
            }
        });
    }
};
