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
 * JS actions.
 *
 * @module      mod_bigbluebuttonbn/actions
 * @copyright   2021 Blindside Networks Inc
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import {endMeeting as requestEndMeeting} from './repository';
import {
    exception as displayException,
    saveCancel,
} from 'core/notification';
import {notifySessionEnded} from './events';
import {get_string as getString} from 'core/str';

const confirmedPromise = (title, question, saveLabel) => new Promise(resolve => {
    saveCancel(title, question, saveLabel, resolve);
});

const registerEventListeners = () => {
    document.addEventListener('click', e => {
        const actionButton = e.target.closest('.bbb-btn-action[data-action="end"]');
        if (!actionButton) {
            return;
        }

        e.preventDefault();

        const bbbId = actionButton.dataset.bbbId;
        const groupId = actionButton.dataset.groupId ? actionButton.dataset.groupId : 0;

        confirmedPromise(
            getString('end_session_confirm_title', 'mod_bigbluebuttonbn'),
            getString('end_session_confirm', 'mod_bigbluebuttonbn'),
            getString('yes', 'moodle')
        )
        .then(() => requestEndMeeting(bbbId, groupId))
        .then(() => {
            notifySessionEnded(bbbId, groupId);

            return;
        })
        .catch(displayException);
    });
};

let listening = false;
if (!listening) {
    registerEventListeners();
    listening = true;
}
