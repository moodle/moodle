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
 * Javascript events for the `core_confirm` modal.
 *
 * @module     core/confirm
 * @copyright  2021 Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since      4.0
 *
 * @example <caption>Calling the confirmation modal to delete a block</caption>
 *
 * // The following is an example of how to use this module via an indirect PHP call with a button.
 *
 * $controls[] = new action_menu_link_secondary(
 *     $deleteactionurl,
 *     new pix_icon('t/delete', $str, 'moodle', array('class' => 'iconsmall', 'title' => '')),
 *     $str,
 *     [
 *         'class' => 'editing_delete',
 *         'data-confirmation' => 'modal', // Needed so this module will pick it up in the click handler.
 *         'data-confirmation-title-str' => json_encode(['deletecheck_modal', 'block']),
 *         'data-confirmation-question-str' => json_encode(['deleteblockcheck', 'block', $blocktitle]),
 *         'data-confirmation-yes-button-str' => json_encode(['delete', 'core']),
 *         'data-confirmation-toast' => 'true', // Can be set to inform the user that their action was a success.
 *         'data-confirmation-toast-confirmation-str' => json_encode(['deleteblockinprogress', 'block', $blocktitle]),
 *         'data-confirmation-destination' => $deleteconfirmationurl->out(false), // Where do you want to direct the user?
 *     ]
 * );
 */

import {saveCancelPromise, exception} from 'core/notification';
import * as Str from 'core/str';
import {add as addToast} from 'core/toast';

// We want to ensure that we only initialize the listeners only once.
let registered = false;

/**
 * Either fetch the string or return it from the dom node.
 *
 * @method getConfirmationString
 * @private
 * @param {HTMLElement} dataset The page element to fetch dataset items in
 * @param {String} field The dataset field name to fetch the contents of
 * @return {Promise}
 *
 */
const getConfirmationString = (dataset, field) => {
    if (dataset[`confirmation${field}Str`]) {
        return Str.get_string.apply(null, JSON.parse(dataset[`confirmation${field}Str`]));
    }
    return Promise.resolve(dataset[`confirmation${field}`]);
};

/**
 * Set up the listeners for the confirmation modal widget within the page.
 *
 * @method registerConfirmationListeners
 * @private
 */
const registerConfirmationListeners = () => {
    document.addEventListener('click', e => {
        const confirmRequest = e.target.closest('[data-confirmation="modal"]');
        if (confirmRequest) {
            e.preventDefault();
            saveCancelPromise(
                getConfirmationString(confirmRequest.dataset, 'Title'),
                getConfirmationString(confirmRequest.dataset, 'Question'),
                getConfirmationString(confirmRequest.dataset, 'YesButton'),
            )
            .then(() => {
                if (confirmRequest.dataset.confirmationToast === 'true') {
                    const stringForToast = getConfirmationString(confirmRequest.dataset, 'ToastConfirmation');
                    if (typeof stringForToast === "string") {
                        addToast(stringForToast);
                    } else {
                        stringForToast.then(str => addToast(str)).catch(e => exception(e));
                    }
                }
                window.location.href = confirmRequest.dataset.confirmationDestination;
                return;
            }).catch(() => {
                return;
            });
        }
    });
};

if (!registered) {
    registerConfirmationListeners();
    registered = true;
}
