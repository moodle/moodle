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
 * Contain the logic for the gateways modal.
 *
 * @module     core_payment/gateways_modal
 * @package    core_payment
 * @copyright  2019 Shamim Rezaie <shamim@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import ModalFactory from 'core/modal_factory';
import Templates from 'core/templates';
import {get_string as getString} from 'core/str';
import {getGatewaysSupportingCurrency} from './repository';
import Selectors from './selectors';
import ModalEvents from 'core/modal_events';
import {add as addToast, addToastRegion} from 'core/toast';
import Notification from 'core/notification';

/**
 * Register event listeners for the module.
 *
 * @param {string} nodeSelector The root to listen to.
 */
export const registerEventListeners = (nodeSelector) => {
    const rootNode = document.querySelector(nodeSelector);

    rootNode.addEventListener('click', (e) => {
        e.preventDefault();
        show(rootNode, {focusOnClose: e.target});
    });
};

/**
 * Shows the gateway selector modal.
 *
 * @param {HTMLElement} rootNode
 * @param {Object} options - Additional options
 * @param {HTMLElement} options.focusOnClose The element to focus on when the modal is closed.
 */
const show = async(rootNode, {
    focusOnClose = null,
} = {}) => {
    const modal = await ModalFactory.create({
        type: ModalFactory.types.SAVE_CANCEL,
        title: await getString('selectpaymenttype', 'core_payment'),
        body: await Templates.render('core_payment/gateways_modal', {}),
    });

    addToastRegion(modal.getRoot()[0]);

    modal.show();

    modal.getRoot().on(ModalEvents.hidden, () => {
        // Destroy when hidden.
        modal.destroy();
        try {
            focusOnClose.focus();
        } catch (e) {
            // eslint-disable-line
        }
    });

    modal.getRoot().on(ModalEvents.save, (e) => {
        const root = modal.getRoot()[0];
        const gateway = (root.querySelector(Selectors.values.gateway) || {value: ''}).value;

        if (gateway) {
            processPayment(
                gateway,
                rootNode.dataset.amount,
                rootNode.dataset.currency,
                rootNode.dataset.component,
                rootNode.dataset.componentid,
                rootNode.dataset.description,
                ({success, message = ''}) => {
                    modal.hide();
                    if (success) {
                        Notification.addNotification({
                            message: message,
                            type: 'success',
                        });
                        location.reload();
                    } else {
                        Notification.alert('', message);
                    }
                },
            );
        } else {
            // We cannot use await in the following line.
            // The reason is that we are preventing the default action of the save event being triggered,
            // therefore we cannot define the event handler function asynchronous.
            getString('nogatewayselected', 'core_payment').then(message => addToast(message));
        }

        e.preventDefault();
    });

    const currency = rootNode.dataset.currency;
    const gateways = await getGatewaysSupportingCurrency(currency);
    const context = {
        gateways
    };

    const {html, js} = await Templates.renderForPromise('core_payment/gateways', context);
    Templates.replaceNodeContents(modal.getRoot().find(Selectors.regions.gatewaysContainer), html, js);
};

/**
 * Process payment using the selected gateway.
 *
 * @param {string} gateway The gateway to be used for payment
 * @param {number} amount Amount of payment
 * @param {string} currency The currency in the three-character ISO-4217 format
 * @param {string} component Name of the component that the componentid belongs to
 * @param {number} componentid An internal identifier that is used by the component
 * @param {string} description Description of the payment
 * @param {processPaymentCallback} callback The callback function to call when processing is finished
 * @returns {Promise<void>}
 */
const processPayment = async(gateway, amount, currency, component, componentid, description, callback) => {
    const paymentMethod = await import(`pg_${gateway}/gateways_modal`);

    paymentMethod.process(amount, currency, component, componentid, description, callback);
};

/**
 * The callback definition for processPayment.
 *
 * @callback processPaymentCallback
 * @param {bool} success
 * @param {string} message
 */
