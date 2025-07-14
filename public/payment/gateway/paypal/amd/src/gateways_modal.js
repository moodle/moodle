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
 * This module is responsible for PayPal content in the gateways modal.
 *
 * @module     paygw_paypal/gateways_modal
 * @copyright  2020 Shamim Rezaie <shamim@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import * as Repository from './repository';
import Templates from 'core/templates';
import Truncate from 'core/truncate';
import Modal from 'core/modal';
import ModalEvents from 'core/modal_events';
import {getString} from 'core/str';

/**
 * Creates and shows a modal that contains a placeholder.
 *
 * @returns {Promise<Modal>}
 */
const showModalWithPlaceholder = async() => await Modal.create({
    body: await Templates.render('paygw_paypal/paypal_button_placeholder', {}),
    show: true,
    removeOnClose: true,
});

/**
 * Process the payment.
 *
 * @param {string} component Name of the component that the itemId belongs to
 * @param {string} paymentArea The area of the component that the itemId belongs to
 * @param {number} itemId An internal identifier that is used by the component
 * @param {string} description Description of the payment
 * @returns {Promise<string>}
 */
export const process = (component, paymentArea, itemId, description) => {
    return Promise.all([
        showModalWithPlaceholder(),
        Repository.getConfigForJs(component, paymentArea, itemId),
    ])
    .then(([modal, paypalConfig]) => {
        return Promise.all([
            modal,
            paypalConfig,
            switchSdk(paypalConfig.clientid, paypalConfig.currency),
        ]);
    })
    .then(([modal, paypalConfig]) => {
        // We have to clear the body. The render method in paypal.Buttons will render everything.
        modal.setBody('');

        return new Promise(resolve => {
            window.paypal.Buttons({
                // Set up the transaction.
                createOrder: function(data, actions) {
                    return actions.order.create({
                        purchase_units: [{ // eslint-disable-line
                            amount: {
                                currency_code: paypalConfig.currency_code, // eslint-disable-line
                                value: paypalConfig.cost,
                            },
                            description: Truncate.truncate(description, {length: 127, stripTags: true}),
                        }],
                        application_context: { // eslint-disable-line
                            shipping_preference: 'NO_SHIPPING', // eslint-disable-line
                            brand_name: Truncate.truncate(paypalConfig.brandname, {length: 127, stripTags: true}), // eslint-disable-line
                        },
                    });
                },
                // Finalise the transaction.
                onApprove: function(data) {
                    modal.getRoot().on(ModalEvents.outsideClick, (e) => {
                        // Prevent closing the modal when clicking outside of it.
                        e.preventDefault();
                    });

                    modal.setBody(getString('authorising', 'paygw_paypal'));

                    Repository.markTransactionComplete(component, paymentArea, itemId, data.orderID)
                    .then(res => {
                        modal.hide();
                        return res;
                    })
                    .then(resolve);
                }
            }).render(modal.getBody()[0]);
        });
    })
    .then(res => {
        if (res.success) {
            return Promise.resolve(res.message);
        }

        return Promise.reject(res.message);
    });
};

/**
 * Unloads the previously loaded PayPal JavaScript SDK, and loads a new one.
 *
 * @param {string} clientId PayPal client ID
 * @param {string} currency The currency
 * @returns {Promise}
 */
const switchSdk = (clientId, currency) => {
    const sdkUrl = `https://www.paypal.com/sdk/js?client-id=${clientId}&currency=${currency}`;

    // Check to see if this file has already been loaded. If so just go straight to the func.
    if (switchSdk.currentlyloaded === sdkUrl) {
        return Promise.resolve();
    }

    // PayPal can only work with one currency at the same time. We have to unload the previously loaded script
    // if it was loaded for a different currency. Weird way indeed, but the only way.
    // See: https://github.com/paypal/paypal-checkout-components/issues/1180
    if (switchSdk.currentlyloaded) {
        const suspectedScript = document.querySelector(`script[src="${switchSdk.currentlyloaded}"]`);
        if (suspectedScript) {
            suspectedScript.parentNode.removeChild(suspectedScript);
        }
    }

    const script = document.createElement('script');

    return new Promise(resolve => {
        if (script.readyState) {
            script.onreadystatechange = function() {
                if (this.readyState == 'complete' || this.readyState == 'loaded') {
                    this.onreadystatechange = null;
                    resolve();
                }
            };
        } else {
            script.onload = function() {
                resolve();
            };
        }

        script.setAttribute('src', sdkUrl);
        document.head.appendChild(script);

        switchSdk.currentlyloaded = sdkUrl;
    });
};

/**
 * Holds the full url of loaded PayPal JavaScript SDK.
 *
 * @static
 * @type {string}
 */
switchSdk.currentlyloaded = '';
