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
 * This module has the code to make the Re-open attempt button work, if present.
 *
 * That is, it looks for buttons with HTML like
 * &lt;button type="button" data-action="reopen-attempt" data-attempt-id="227000" data-after-action-url="/mod/quiz/report.php">
 * and if that is clicked, it first shows an 'Are you sure' pop-up, and if they are sure,
 * the attempt is re-opened, and then the page reloads.
 *
 * @module    mod_quiz/reopen_attempt_ui
 * @copyright 2023 The Open University
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import {exception as displayException} from 'core/notification';
import {call as fetchMany} from 'core/ajax';
import {get_string as getString} from 'core/str';
import {saveCancelPromise} from 'core/notification';

/**
 * Handle a click if it is on one of our buttons.
 *
 * @param {MouseEvent} e the click event.
 */
const reopenButtonClicked = async(e) => {
    if (!(e.target instanceof HTMLElement) || !e.target.matches('button[data-action="reopen-attempt"]')) {
        return;
    }

    e.preventDefault();
    const attemptId = e.target.dataset.attemptId;

    try {
        // We fetch the confirmation message from the server now, so the message is based
        // on the latest state of the attempt, rather than when the containing page loaded.
        const messages = fetchMany([{
            methodname: 'mod_quiz_get_reopen_attempt_confirmation',
            args: {
                "attemptid": attemptId,
            },
        }]);

        await saveCancelPromise(
            getString('reopenattemptareyousuretitle', 'mod_quiz'),
            messages[0],
            getString('reopenattempt', 'mod_quiz'),
            {triggerElement: e.target},
       );

        await (fetchMany([{
            methodname: 'mod_quiz_reopen_attempt',
            args: {
                "attemptid": attemptId,
            },
        }])[0]);
        window.location = M.cfg.wwwroot + e.target.dataset.afterActionUrl;

    } catch (error) {
        if (error.type === 'modal-save-cancel:cancel') {
            // User clicked Cancel, so do nothing.
            return;
        }
        await displayException(error);
    }
};

export const init = () => {
    document.addEventListener('click', reopenButtonClicked);
};
