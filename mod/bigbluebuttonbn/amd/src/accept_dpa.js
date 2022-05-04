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
 * Javascript module for confirming the acceptance of the current data processing agreement before enabling
 * the BigBlueButton activity module.
 *
 * @module      mod_bigbluebuttonbn/accept_dpa
 * @copyright   2022 Mihail Geshoski <mihail@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import ModalForm from 'core_form/modalform';
import Notification from 'core/notification';
import {get_string as getString} from 'core/str';

/**
 * Initialize module.
 */
export const init = () => {

    const modalForm = new ModalForm({
        modalConfig: {
            title: getString('enablingbigbluebutton', 'mod_bigbluebuttonbn'),
            large: false,
        },
        formClass: 'mod_bigbluebuttonbn\\form\\accept_dpa',
        saveButtonText: getString('enable'),
    });

    // Once the form has been submitted and successfully processed, reload the page to enable the activity module.
    modalForm.addEventListener(modalForm.events.FORM_SUBMITTED, event => {
        if (event.detail.result) {
            window.location.reload();
        } else {
            Notification.addNotification({
                type: 'error',
                message:  event.detail.errors.join('<br>')
            });
        }
    });

    modalForm.show();
};
