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
 * PayPal repository module to encapsulate all of the AJAX requests that can be sent for PayPal.
 *
 * @module     pg_paypal/repository
 * @package    pg_paypal
 * @copyright  2020 Shamim Rezaie <shamim@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import Ajax from 'core/ajax';

/**
 * Return the PayPal JavaScript SDK URL.
 *
 * @param {string} component Name of the component that the componentid belongs to
 * @param {string} paymentArea The area of the component that the componentid belongs to
 * @param {number} componentId An internal identifier that is used by the component
 * @returns {Promise<{clientid: string, brandname: string}>}
 */
export const getConfigForJs = (component, paymentArea, componentId) => {
    const request = {
        methodname: 'pg_paypal_get_config_for_js',
        args: {
            component,
            paymentarea: paymentArea,
            componentid: componentId,
        },
    };

    return Ajax.call([request])[0];
};
