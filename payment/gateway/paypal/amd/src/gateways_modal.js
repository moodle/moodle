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
 * @module     pg_paypal/gateway_modal
 * @copyright  2020 Shamim Rezaie <shamim@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import * as Repository from './repository';
import Templates from 'core/templates';
import Truncate from 'core/truncate';
import Ajax from 'core/ajax';
import ModalFactory from 'core/modal_factory';
import ModalEvents from 'core/modal_events';

/**
 * Creates and shows a modal that contains a placeholder.
 *
 * @returns {Promise<Modal>}
 */
const showPlaceholder = async() => {
    const modal = await ModalFactory.create({
        type: ModalFactory.types.CANCEL,
        body: await Templates.render('pg_paypal/paypal_button_placeholder', {})
    });
    modal.show();
    return modal;
};

/**
 * Process the payment.
 *
 * @param {double} amount Amount of payment
 * @param {string} currency The currency in the three-character ISO-4217 format
 * @param {string} component Name of the component that the componentid belongs to
 * @param {number} componentid An internal identifier that is used by the component
 * @param {string} description Description of the payment
 * @param {processCallback} callback The callback function to call when processing is finished
 * @returns {Promise<void>}
 */
export const process = async(amount, currency, component, componentid, description, callback) => {

    const [
        modal,
        paypalConfig,
    ] = await Promise.all([
        showPlaceholder(),
        Repository.getConfigForJs(),
    ]);

    modal.getRoot().on(ModalEvents.hidden, () => {
        // Destroy when hidden.
        modal.destroy();
    });

    const paypalScript = `https://www.paypal.com/sdk/js?client-id=${paypalConfig.clientid}&currency=${currency}&intent=authorize`;

    callExternalFunction(paypalScript, () => {
        modal.setBody('<form></form>'); // This is a hack. Instead of emptying the body, we put an empty form there so the modal
                                        // is not closed when user clicks outside of modal.
        paypal.Buttons({ // eslint-disable-line
            createOrder: function(data, actions) {
                // This function sets up the details of the transaction, including the amount and line item details.
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
            onApprove: function(data, actions) {
                // Authorize the transaction.
                actions.order.authorize().then(function(authorization) {
                    // Get the authorization id.
                    const authorizationID = authorization.purchase_units[0].payments.authorizations[0].id;

                    // Call your server to validate and capture the transaction.
                    return Ajax.call([{
                        methodname: 'pg_paypal_transaction_complete',
                        args: {
                            component,
                            componentid,
                            orderid: data.orderID,
                            authorizationid: authorizationID,
                        },
                    }])[0]
                    .then(function(res) {
                        modal.hide();
                        return callback(res);
                    });
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
