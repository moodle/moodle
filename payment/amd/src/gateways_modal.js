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
import {getGatewaysSupportingCurrency} from 'core_payment/repository';
import Selectors from './selectors';
import * as ModalEvents from 'core/modal_events';
import {add as addToast, addToastRegion} from 'core/toast';

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
const show = (rootNode, {
    focusOnClose = null,
} = {}) => {
    Templates.render('core_payment/gateways_modal', {})
    .done(content => {
        ModalFactory.create({
            type: ModalFactory.types.SAVE_CANCEL,
            title: getString('selectpaymenttype', 'core_payment'),
            body: content,
        })
        .done(function(modal) {
            addToastRegion(modal.getRoot()[0]);
            const currency = rootNode.dataset.currency;
            getGatewaysSupportingCurrency(currency)
            .done(gateways => {
                const context = {
                    gateways: []
                };

                for (let gateway of gateways) {
                    context.gateways.push(gateway);
                }

                Templates.render('core_payment/gateways', context)
                    .done((html, js) => {
                        Templates.replaceNodeContents(modal.getRoot().find(Selectors.regions.gatewaysContainer),
                            html, js);
                    });
            });

            modal.getRoot().on(ModalEvents.hidden, function() {
                // Destroy when hidden.
                modal.destroy();
                try {
                    focusOnClose.focus();
                } catch (e) {
                    // eslint-disable-line
                }
            });

            modal.getRoot().on(ModalEvents.save, function(e) {
                const root = modal.getRoot()[0];
                const gateway = (root.querySelector(Selectors.values.gateway) || {value: ''}).value;

                        if (gateway) {
                            processPayment(
                                root,
                                gateway,
                                rootNode.dataset.amount,
                                rootNode.dataset.currency,
                                rootNode.dataset.component,
                                rootNode.dataset.componentid,
                                rootNode.dataset.description,
                            );
                        } else {
                            getString('nogatewayselected', 'core_payment').then(message => {
                                return addToast(message);
                            });
                        }

                e.preventDefault();
            });

            modal.show();
        });
    });
};

/**
 * Process payment using the selected gateway.
 *
 * @param {HTMLElement} rootElement The root element of the main modal
 * @param {string} gateway The gateway to be used for payment
 * @param {number} amount Amount of payment
 * @param {string} currency The currency in the three-character ISO-4217 format
 * @param {string} component Name of the component that the componentid belongs to
 * @param {number} componentid An internal identifier that is used by the component
 * @param {string} description Description of the payment
 * @returns {Promise<void>}
 */
const processPayment = async(rootElement, gateway, amount, currency, component, componentid, description) => {
    const paymentMethod = await import(`pg_${gateway}/gateways_modal`);

    paymentMethod.process(rootElement, amount, currency, component, componentid, description);
};
