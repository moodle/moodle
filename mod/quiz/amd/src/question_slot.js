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
 * Render the question slot template for each question in the quiz edit view.
 *
 * @module     mod_quiz/question_slot
 * @copyright  2021 Catalyst IT Australia Pty Ltd
 * @author     Guillermo Gomez Arias <guillermogomez@catalyst-au.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import {call as fetchMany} from 'core/ajax';
import Notification from 'core/notification';

/**
 * Set the question version for the slot.
 *
 * @param {Number} slotId
 * @param {Number} newVersion
 * @return {Array} The modified question version
 */
const setQuestionVersion = (slotId, newVersion) => fetchMany([{
    methodname: 'mod_quiz_set_question_version',
    args: {
        slotid: slotId,
        newversion: newVersion,
    }
}])[0];

/**
 * Replace the container with a new version.
 */
const registerEventListeners = () => {
    document.addEventListener('change', e => {
        if (!e.target.matches('[data-action="mod_quiz-select_slot"][data-slot-id]')) {
            return;
        }

        const slotId = e.target.dataset.slotId;
        const newVersion = parseInt(e.target.value);

        setQuestionVersion(slotId, newVersion)
            .then(() => {
                location.reload();
                return;
            })
            .catch(Notification.exception);
    });
};

/** @property {Boolean} eventsRegistered If the event has been registered or not */
let eventsRegistered = false;

/**
 * Entrypoint of the js.
 */
export const init = () => {
    if (eventsRegistered) {
        return;
    }

    registerEventListeners();
};
