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
 * A module to handle Delete/Update operations of the manage subscription page.
 *
 * @module core_calendar/manage_subscriptions
 * @copyright 2021 Huong Nguyen <huongnv13@gmail.com>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since 4.0
 */

import * as CalendarSelectors from 'core_calendar/selectors';
import * as CalendarRepository from 'core_calendar/repository';
import * as Modal from 'core/modal_factory';
import * as ModalEvents from 'core/modal_events';
import {displayException, addNotification, fetchNotifications} from 'core/notification';
import Prefetch from 'core/prefetch';
import {get_string as getString} from 'core/str';
import {eventTypes} from 'core/local/inplace_editable/events';

/**
 * Get subscription id for given element.
 *
 * @param {HTMLElement} element update/delete link
 * @return {Number}
 */
const getSubscriptionId = element => {
    return parseInt(element.closest('tr').dataset.subid);
};

/**
 * Get subscription name for given element.
 *
 * @param {HTMLElement} element update/delete link
 * @return {String}
 */
const getSubscriptionName = element => {
    return element.closest('tr').dataset.subname;
};

/**
 * Get subscription table row for subscription id.
 *
 * @param {string} subscriptionId Subscription id
 * @return {Element}
 */
const getSubscriptionRow = subscriptionId => {
    return document.querySelector(`tr[data-subid="${subscriptionId}"]`);
};

/**
 * Create modal.
 *
 * @param {HTMLElement} element
 * @param {string} messageCode Message code.
 * @return {promise} Promise for modal
 */
const createModal = (element, messageCode) => {
    const subscriptionName = getSubscriptionName(element);
    return Modal.create({
        type: Modal.types.SAVE_CANCEL,
        title: getString('confirmation', 'admin'),
        body: getString(messageCode, 'calendar', subscriptionName),
        buttons: {
            save: getString('yes')
        },
    }).then(modal => {
        modal.getRoot().on(ModalEvents.hidden, () => {
            element.focus();
        });
        modal.show();
        return modal;
    });
};

/**
 * Response handler for delete action.
 *
 * @param {HTMLElement} element
 * @param {Object} data
 * @return {Promise}
 */
const responseHandlerForDelete = async(element, data) => {
    const subscriptionName = getSubscriptionName(element);
    const message = data.status ? await getString('subscriptionremoved', 'calendar', subscriptionName) : data.warnings[0].message;
    const type = data.status ? 'info' : 'error';
    return addNotification({message, type});
};

/**
 * Register events for update/delete links.
 */
const registerEventListeners = () => {
    document.addEventListener('click', e => {
        const deleteAction = e.target.closest(CalendarSelectors.actions.deleteSubscription);
        if (deleteAction) {
            e.preventDefault();
            const modalPromise = createModal(deleteAction, 'confirmsubscriptiondelete');
            modalPromise.then(modal => {
                modal.getRoot().on(ModalEvents.save, () => {
                    const subscriptionId = getSubscriptionId(deleteAction);
                    CalendarRepository.deleteSubscription(subscriptionId).then(data => {
                        const response = responseHandlerForDelete(deleteAction, data);
                        return response.then(() => {
                            const subscriptionRow = getSubscriptionRow(subscriptionId);
                            return subscriptionRow.remove();
                        });
                    }).catch(displayException);
                });

                return modal;
            }).catch(displayException);
        }
    });

    document.addEventListener(eventTypes.elementUpdated, e => {
        const inplaceEditable = e.target;
        if (inplaceEditable.getAttribute('data-component') == 'core_calendar') {
            fetchNotifications();
        }
    });
};

/**
 * Initialises.
 */
export const init = () => {
    Prefetch.prefetchStrings('moodle', ['yes']);
    Prefetch.prefetchStrings('core_admin', ['confirmation']);
    Prefetch.prefetchStrings('core_calendar', ['confirmsubscriptiondelete', 'subscriptionremoved']);
    registerEventListeners();
};
