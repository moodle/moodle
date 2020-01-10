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
import Selectors from './selectors';
import Truncate from 'core/truncate';
import Ajax from 'core/ajax';
import Notification from 'core/notification';

/**
 * Renders a placeholder in the modal.
 *
 * @param rootElement
 * @returns {Promise<void>}
 */
const showPlaceholder = async(rootElement) => {
    const {html, js} = await Templates.renderForPromise('pg_paypal/paypal_button_placeholder', {});
    Templates.replaceNodeContents(rootElement.querySelector(Selectors.regions.gatewaysContainer), html, js);
};

export const process = async(rootElement, amount, currency, component, componentid, description) => {

    const [
        ,
        paypalConfig,
    ] = await Promise.all([
        showPlaceholder(rootElement),
        Repository.getConfigForJs(),
    ]);

    const paypalScript = `https://www.paypal.com/sdk/js?client-id=${paypalConfig.clientid}&currency=${currency}&intent=authorize`;

    callExternalFunction(paypalScript, () => {
        rootElement.querySelector(Selectors.buttons.save).style.display = 'none';
        rootElement.querySelector(Selectors.regions.gatewaysContainer).innerHTML = '';
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
                        Notification.addNotification({
                            message: res.message,
                            type: res.success ? 'success' : 'error'
                        });
                    });
                });
            }
        }).render(Selectors.regions.gatewaysContainer);
    });
};

const callExternalFunction = (jsFile, callback) => {
    // Check to see if this file has already been loaded. If so just go straight to the callback.
    if (callExternalFunction.currentlyloaded.includes(jsFile)) {
        callback();
        return;
    }

    const script = document.createElement('script');

    if (script.readyState) {
        script.onreadystatechange = function() {
            if (this.readyState == 'complete' || this.readyState == 'loaded') {
                this.onreadystatechange = null;
                callback();
            }
        };
    } else {
        script.onload = function() {
            callback();
        };
    }

    script.setAttribute('src', jsFile);
    document.head.appendChild(script);

    callExternalFunction.currentlyloaded.push(jsFile);
};

callExternalFunction.currentlyloaded = [];
