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
 * @module     paygw_paypal/gateway_modal
 * @copyright  2020 Shamim Rezaie <shamim@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import * as Repository from './repository';
import Templates from 'core/templates';
import Truncate from 'core/truncate';
import Ajax from 'core/ajax';
import ModalFactory from 'core/modal_factory';
import ModalEvents from 'core/modal_events';
import {get_string as getString} from 'core/str';

/**
 * Creates and shows a modal that contains a placeholder.
 *
 * @returns {Promise<Modal>}
 */
const showModalWithPlaceholder = async() => {
    const modal = await ModalFactory.create({
        body: await Templates.render('paygw_paypal/paypal_button_placeholder', {})
    });
    modal.show();
    return modal;
};

/**
 * Process the payment.
 *
 * @param {string} component Name of the component that the itemId belongs to
 * @param {string} paymentArea The area of the component that the itemId belongs to
 * @param {number} itemId An internal identifier that is used by the component
 * @param {string} description Description of the payment
 * @param {processCallback} callback The callback function to call when processing is finished
 * @returns {Promise<void>}
 */
export const process = async(component, paymentArea, itemId, description, callback) => {

    const [
        modal,
        paypalConfig,
    ] = await Promise.all([
        showModalWithPlaceholder(),
        Repository.getConfigForJs(component, paymentArea, itemId),
    ]);
    const currency = paypalConfig.currency;
    const amount = paypalConfig.cost; // Cost with surcharge.

    modal.getRoot().on(ModalEvents.hidden, () => {
        // Destroy when hidden.
        modal.destroy();
    });

    const paypalScript = `https://www.paypal.com/sdk/js?client-id=${paypalConfig.clientid}&currency=${currency}`;

    callExternalFunction(paypalScript, () => {
        modal.setBody(''); // We have to clear the body. The render method in paypal.Buttons will render everything.

        paypal.Buttons({ // eslint-disable-line
            // Set up the transaction.
            createOrder: function(data, actions) {
                return actions.order.create({
                    purchase_units: [{ // eslint-disable-line
                        amount: {
                            currency_code: currency, // eslint-disable-line
                            value: amount
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

                // Call server to validate and capture payment for order.
                return Ajax.call([{
                    methodname: 'paygw_paypal_create_transaction_complete',
                    args: {
                        component,
                        paymentarea: paymentArea,
                        itemid: itemId,
                        orderid: data.orderID,
                    },
                }])[0]
                .then(function(res) {
                    modal.hide();
                    return callback(res);
                });
            }
        }).render(modal.getBody()[0]);
    });
};

/**
 * The callback definition for process.
 *
 * @callback processCallback
 * @param {bool} success
 * @param {string} message
 */

/**
 * Calls a function from an external javascript file.
 *
 * @param {string} jsFile URL of the external JavaScript file
 * @param {function} func The function to call
 */
const callExternalFunction = (jsFile, func) => {
    // Check to see if this file has already been loaded. If so just go straight to the func.
    if (callExternalFunction.currentlyloaded == jsFile) {
        func();
        return;
    }

    // PayPal can only work with one currency at the same time. We have to unload the previously loaded script
    // if it was loaded for a different currency. Weird way indeed, but the only way.
    // See: https://github.com/paypal/paypal-checkout-components/issues/1180
    if (callExternalFunction.currentlyloaded) {
        const suspectedScript = document.querySelector(`script[src="${callExternalFunction.currentlyloaded}"]`);
        if (suspectedScript) {
            suspectedScript.parentNode.removeChild(suspectedScript);
        }
    }

    const script = document.createElement('script');

    if (script.readyState) {
        script.onreadystatechange = function() {
            if (this.readyState == 'complete' || this.readyState == 'loaded') {
                this.onreadystatechange = null;
                func();
            }
        };
    } else {
        script.onload = function() {
            func();
        };
    }

    script.setAttribute('src', jsFile);
    document.head.appendChild(script);

    callExternalFunction.currentlyloaded = jsFile;
};

/**
 * Holds the full url of loaded external JavaScript file.
 *
 * @static
 * @type {string}
 */
callExternalFunction.currentlyloaded = '';
