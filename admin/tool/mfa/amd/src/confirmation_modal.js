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
 * Modal for confirming factor actions.
 *
 * @module     tool_mfa/confirmation_modal
 * @copyright  2023 David Woloszyn <david.woloszyn@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import ModalEvents from 'core/modal_events';
import ModalSaveCancel from 'core/modal_save_cancel';
import Notification from 'core/notification';
import {getString} from 'core/str';
import Url from 'core/url';
import Fragment from 'core/fragment';
import * as Prefetch from 'core/prefetch';

const SELECTORS = {
    ACTION: '.mfa-action-button',
};

/**
 * Entrypoint of the js.
 *
 * @method init
 * @param {Integer} contextId Context ID of the user.
 */
export const init = (contextId) => {
    // Prefetch the language strings.
    Prefetch.prefetchStrings('tool_mfa', [
        'yesremove',
        'yesreplace',
    ]);
    registerEventListeners(contextId);
};

/**
 * Register event listeners.
 *
 * @method registerEventListeners
 * @param {Integer} contextId Context ID of the user.
 */
const registerEventListeners = (contextId) => {
    document.addEventListener('click', (e) => {
        const action = e.target.closest(SELECTORS.ACTION);
        if (action) {
            buildModal(action, contextId).catch(Notification.exception);
        }
    });
};

/**
 * Build the modal with the provided data.
 *
 * @method buildModal
 * @param {HTMLElement} element The button element.
 * @param {Number} contextId Context ID of the user.
 */
const buildModal = async(element, contextId) => {

    // Prepare data for modal.
    const data = {
        action: element.getAttribute('data-action'),
        factor: element.getAttribute('data-factor'),
        factorid: element.getAttribute('data-factorid'),
        devicename: element.getAttribute('data-devicename'),
        actionurl: Url.relativeUrl('/admin/tool/mfa/action.php'),
    };

    // Customise modal depending on action being performed.
    if (data.action === 'revoke') {
        data.title = await getString('revokefactorconfirmation', 'factor_' + data.factor, data.devicename);
        data.buttontext = await getString('yesremove', 'tool_mfa');

    } else if (data.action === 'replace') {
        data.title = await getString('replacefactorconfirmation', 'factor_' + data.factor, data.devicename);
        data.buttontext = await getString('yesreplace', 'tool_mfa');
    }

    const modal = await ModalSaveCancel.create({
        title: data.title,
        body: Fragment.loadFragment('tool_mfa', 'factor_action_confirmation_form', contextId, data),
        show: true,
        buttons: {
            'save': data.buttontext,
            'cancel': getString('cancel', 'moodle'),
        },
    });

    modal.getRoot().on(ModalEvents.save, () => {
        modal.getRoot().find('form').submit();
    });

};
