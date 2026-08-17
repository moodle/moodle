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
 * Display a confirmation modal for bulk-deleting questions.
 *
 * @module     qbank_deletequestion/bulk_delete
 * @copyright  2026 onwards Catalyst IT EU {@link https://catalyst-eu.net}
 * @author     Mark Johnson <mark.johnson@catalyst-eu.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import * as Fragment from 'core/fragment';
import {getString} from 'core/str';
import Notification from 'core/notification';
import Pending from 'core/pending';

export default class {
    static SELECTORS = {
        QUESTION_FORM: '#questionsubmit',
        SELECTED_QUESTIONS: 'input[id^="checkq"]:checked',
        CONFIRM_BUTTON: '.bulk-move-footer button[data-action="save"]',
        CANCEL_BUTTON: '.bulk-move-footer button[data-action="cancel"]'
    };

    static init(contextId) {
        const form = document.querySelector(this.SELECTORS.QUESTION_FORM);
        form.addEventListener('click', async(e) => {
            const trigger = e.target;
            if (!trigger.classList.contains('dropdown-item') || trigger.getAttribute('name') !== 'deleteselected') {
                return;
            }
            e.preventDefault();
            const actionUrl = new URL(trigger.getAttribute('formaction'));
            const deleteAll = !!parseInt(actionUrl.searchParams.get('deleteall'));
            const selectedQuestions = form.querySelectorAll(this.SELECTORS.SELECTED_QUESTIONS);
            if (selectedQuestions.length === 0) {
                return;
            }
            let title;
            if (deleteAll) {
                title = selectedQuestions.length > 1 ? 'deletequestiontitle_plural' : 'deletequestiontitle';
            } else {
                title = selectedQuestions.length > 1 ? 'deleteversiontitle_plural' : 'deleteversiontitle';
            }
            const questionIds = [];
            for (const question of selectedQuestions) {
                questionIds.push(question.name.substring(1));
            }
            const message = Fragment.loadFragment(
                'qbank_deletequestion',
                'bulk_delete',
                contextId,
                {
                    'questionids': JSON.stringify(questionIds),
                    'deleteall': deleteAll,
                }
            );
            await Notification.deleteCancelPromise(
                getString(title, 'question'),
                message
            );
            const pendingPromise = new Pending('qbank_deletequestion/bulk_delete:submit');
            // Once the user confirms, submit the form with confirmation parameters.
            actionUrl.searchParams.append('confirm', 1);
            actionUrl.searchParams.append('deleteselected', questionIds.join(','));
            form.action = actionUrl.toString();
            form.submit();
            pendingPromise.resolve();
        });
    }
}
