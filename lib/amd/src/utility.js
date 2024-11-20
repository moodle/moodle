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
 * Javascript handling for HTML attributes. This module gets autoloaded on page load.
 *
 * With the appropriate HTML attributes, various functionalities defined in this module can be used such as a displaying
 * an alert or a confirmation modal, etc.
 *
 * @module     core/utility
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
 *         'data-modal' => 'confirmation', // Needed so this module will pick it up in the click handler.
 *         'data-modal-title-str' => json_encode(['deletecheck_modal', 'block']),
 *         'data-modal-content-str' => json_encode(['deleteblockcheck', 'block', $blocktitle]),
 *         'data-modal-yes-button-str' => json_encode(['delete', 'core']),
 *         'data-modal-toast' => 'true', // Can be set to inform the user that their action was a success.
 *         'data-modal-toast-confirmation-str' => json_encode(['deleteblockinprogress', 'block', $blocktitle]),
 *         'data-modal-destination' => $deleteconfirmationurl->out(false), // Where do you want to direct the user?
 *     ]
 * );
 */

import * as Str from 'core/str';
import Pending from 'core/pending';
import {add as addToast} from 'core/toast';
import {saveCancelPromise, deleteCancelPromise, exception} from 'core/notification';

// We want to ensure that we only initialize the listeners only once.
let registered = false;

/**
 * Either fetch the string or return it from the dom node.
 *
 * @method getConfirmationString
 * @private
 * @param {HTMLElement} dataset The page element to fetch dataset items in
 * @param {String} type The type of string to fetch
 * @param {String} field The dataset field name to fetch the contents of
 * @param {Array|null} [defaultValue=null] The default params to pass to get_string if no value is found in a dataset
 * @return {Promise}
 *
 */
const getModalString = (dataset, type, field, defaultValue = null) => {
    if (dataset[`${type}${field}Str`]) {
        return Str.get_string.apply(null, JSON.parse(dataset[`${type}${field}Str`]));
    }
    if (dataset[`${type}${field}`]) {
        return Promise.resolve(dataset[`${type}${field}`]);
    }

    if (defaultValue) {
        return Str.get_string.apply(null, defaultValue);
    }

    return null;
};

/**
 * Display a save/cancel confirmation.
 *
 * @private
 * @param {HTMLElement} source The title of the confirmation
 * @param {String} type The content of the confirmation
 * @returns {Promise}
 */
const displayConfirmation = (source, type) => {
    let confirmationPromise = null;
    if (`${type}Type` in source.dataset && source.dataset[`${type}Type`] === 'delete') {
        confirmationPromise = deleteCancelPromise(
            getModalString(source.dataset, type, 'Title', ['confirm', 'core']),
            getModalString(source.dataset, type, 'Content'),
            getModalString(source.dataset, type, 'YesButton', ['yes', 'core'])
        );
    } else {
        confirmationPromise = saveCancelPromise(
            getModalString(source.dataset, type, 'Title', ['confirm', 'core']),
            getModalString(source.dataset, type, 'Content'),
            getModalString(source.dataset, type, 'YesButton', ['yes', 'core'])
        );
    }
    return confirmationPromise.then(() => {
        if (source.dataset[`${type}Toast`] === 'true') {
            const stringForToast = getModalString(source.dataset, type, 'ToastConfirmation');
            if (typeof stringForToast === "string") {
                addToast(stringForToast);
            } else {
                stringForToast.then(str => addToast(str)).catch(e => exception(e));
            }
        }

        if (source.dataset[`${type}Destination`]) {
            window.location.href = source.dataset[`${type}Destination`];
            return;
        }

        if (source.closest('form')) {
            // Update the modal and confirmation data fields so that we don't loop.
            source.dataset.confirmation = 'none';
            source.dataset.modal = 'none';

            // Click on the button again.
            // Note: Do not use the form.submit() because it will not work for cancel buttons.
            source.click();
            return;
        }

        const link = source.closest('a');
        if (link && link.href && link.href !== '#') {
            window.location.href = link.href;
            return;
        }

        const button = source.closest('button, input[type="submit"], input[type="button"], input[type="reset"]');
        if (button) {
            source.dataset.modalSubmitting = true;
            source.click();
            return;
        }

        window.console.error(`No destination found for ${type} modal`);
        return;
    }).catch(() => {
        return;
    });
};

/**
 * Display an alert and return the promise from it.
 *
 * @private
 * @param {String} title The title of the alert
 * @param {String} body The content of the alert
 * @returns {Promise<ModalAlert>}
 */
const displayAlert = async(title, body) => {
    const pendingPromise = new Pending('core/confirm:alert');
    const AlertModal = await import('core/local/modal/alert');

    return AlertModal.create({
        title,
        body,
        removeOnClose: true,
        show: true,
    })
    .then((modal) => {
        pendingPromise.resolve();
        return modal;
    });
};

/**
 * Set up the listeners for the confirmation modal widget within the page.
 *
 * @method registerConfirmationListeners
 * @private
 */
const registerConfirmationListeners = () => {
    document.addEventListener('click', e => {
        if (e.target.closest('[data-modal-submitting]')) {
            return;
        }
        const confirmRequest = e.target.closest('[data-confirmation="modal"]');
        if (confirmRequest) {
            e.preventDefault();
            displayConfirmation(confirmRequest, 'confirmation');
        }

        const modalConfirmation = e.target.closest('[data-modal="confirmation"]');
        if (modalConfirmation) {
            e.preventDefault();
            displayConfirmation(modalConfirmation, 'modal');
        }

        const alertRequest = e.target.closest('[data-modal="alert"]');
        if (alertRequest) {
            e.preventDefault();
            displayAlert(
                getModalString(alertRequest.dataset, 'modal', 'Title'),
                getModalString(alertRequest.dataset, 'modal', 'Content'),
            );
        }
    });
};

if (!registered) {
    registerConfirmationListeners();
    registered = true;
}
