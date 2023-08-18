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
 * Javascript module for importing presets.
 *
 * @module      mod_bigbluebuttonbn/guest_access_modal
 * @copyright   2022 Blindside Networks Inc
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
import {getString} from 'core/str';
import ModalForm from 'core_form/modalform';
import {add as toastAdd, addToastRegion} from 'core/toast';
import {
    exception as displayException,
} from 'core/notification';
const selectors = {
    showGuestAccessButton: '[data-action="show-guest-access"]',
};

/**
 * Intialise the object and click event to show the popup form
 *
 * @param {object} guestInfo
 * @param {string} guestInfo.id
 * @param {string} guestInfo.groupid
 * @param {string} guestInfo.guestjoinurl
 * @param {string} guestInfo.guestpassword
 */
export const init = (guestInfo) => {
    const showGuestAccessButton = document.querySelector(selectors.showGuestAccessButton);
    if (showGuestAccessButton === null) {
        return;
    }

    const modalForm = new ModalForm({
        modalConfig: {
            title: getString('guestaccess_title', 'mod_bigbluebuttonbn'),
            large: true,
        },
        args: guestInfo,
        saveButtonText: getString('ok', 'core_moodle'),
        formClass: 'mod_bigbluebuttonbn\\form\\guest_add',
    });
    showGuestAccessButton.addEventListener('click', event => {
        modalForm.show().then(() => {
            addToastRegion(modalForm.modal.getRoot()[0]);
            return true;
        }).catch(displayException);
        modalForm.addEventListener(modalForm.events.FORM_SUBMITTED, (e) => {
            // Remove toast region as if not it will be displayed on the closed modal.
            const modalElement = modalForm.modal.getRoot()[0];
            const regions = modalElement.querySelectorAll('.toast-wrapper');
            regions.forEach((reg) => reg.remove());
            if (e.detail.result) {
                if (e.detail.emailcount > 0) {
                    toastAdd(getString('guestaccess_invite_success', 'mod_bigbluebuttonbn', e.detail),
                        {
                            type: 'success',
                        }
                    );
                }
            } else {
                toastAdd(getString('guestaccess_invite_failure', 'mod_bigbluebuttonbn', e.detail),
                    {
                        type: 'warning',
                    }
                );
            }
        }, {once: true});
        event.stopPropagation();
    });
};
