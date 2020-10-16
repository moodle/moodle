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
import {getAvailableGateways} from './repository';
import Selectors from './selectors';
import ModalEvents from 'core/modal_events';
import PaymentEvents from 'core_payment/events';
import {add as addToast, addToastRegion} from 'core/toast';
import Notification from 'core/notification';
import ModalGateways from './modal_gateways';

/**
 * Register event listeners for the module.
 *
 * @param {string} nodeSelector The root to listen to.
 */
export const registerEventListenersBySelector = (nodeSelector) => {
    document.querySelectorAll(nodeSelector).forEach((element) => {
        registerEventListeners(element);
    });
};

/**
 * Register event listeners for the module.
 *
 * @param {HTMLElement} rootNode The root to listen to.
 */
export const registerEventListeners = (rootNode) => {
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
        type: ModalGateways.TYPE,
        title: await getString('selectpaymenttype', 'core_payment'),
        body: await Templates.render('core_payment/gateways_modal', {}),
    });

    const rootElement = modal.getRoot()[0];
    addToastRegion(rootElement);

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

    modal.getRoot().on(PaymentEvents.proceed, (e) => {
        const gateway = (rootElement.querySelector(Selectors.values.gateway) || {value: ''}).value;

        if (gateway) {
            processPayment(
                gateway,
                rootNode.dataset.component,
                rootNode.dataset.paymentarea,
                rootNode.dataset.itemid,
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

    // Re-calculate the cost when gateway is changed.
    rootElement.addEventListener('change', e => {
        if (e.target.matches(Selectors.elements.gateways)) {
            updateCostRegion(rootElement, rootNode.dataset.cost);
        }
    });

    const gateways = await getAvailableGateways(rootNode.dataset.component, rootNode.dataset.paymentarea, rootNode.dataset.itemid);
    const context = {
        gateways
    };

    const {html, js} = await Templates.renderForPromise('core_payment/gateways', context);
    Templates.replaceNodeContents(rootElement.querySelector(Selectors.regions.gatewaysContainer), html, js);
    selectSingleGateway(rootElement);
    await updateCostRegion(rootElement, rootNode.dataset.cost);
};

/**
 * Auto-select the gateway if there is only one gateway.
 *
 * @param {HTMLElement} root An HTMLElement that contains the cost region
 */
const selectSingleGateway = root => {
    const gateways = root.querySelectorAll(Selectors.elements.gateways);

    if (gateways.length == 1) {
        gateways[0].checked = true;
    }
};

/**
 * Shows the cost of the item the user is purchasing in the cost region.
 *
 * @param {HTMLElement} root An HTMLElement that contains the cost region
 * @param {string} defaultCost The default cost that is going to be displayed if no gateway is selected
 * @returns {Promise<void>}
 */
const updateCostRegion = async(root, defaultCost = '') => {
    const gatewayElement = root.querySelector(Selectors.values.gateway);
    const surcharge = parseInt((gatewayElement || {dataset: {surcharge: 0}}).dataset.surcharge);
    const cost = (gatewayElement || {dataset: {cost: defaultCost}}).dataset.cost;

    const {html, js} = await Templates.renderForPromise('core_payment/fee_breakdown', {fee: cost, surcharge});
    Templates.replaceNodeContents(root.querySelector(Selectors.regions.costContainer), html, js);
};

/**
 * Process payment using the selected gateway.
 *
 * @param {string} gateway The gateway to be used for payment
 * @param {string} component Name of the component that the itemId belongs to
 * @param {string} paymentArea Name of the area in the component that the itemId belongs to
 * @param {number} itemId An internal identifier that is used by the component
 * @param {string} description Description of the payment
 * @param {processPaymentCallback} callback The callback function to call when processing is finished
 * @returns {Promise<void>}
 */
const processPayment = async(gateway, component, paymentArea, itemId, description, callback) => {
    const paymentMethod = await import(`paygw_${gateway}/gateways_modal`);
    paymentMethod.process(component, paymentArea, itemId, description, callback);
};

/**
 * The callback definition for processPayment.
 *
 * @callback processPaymentCallback
 * @param {bool} success
 * @param {string} message
 */
