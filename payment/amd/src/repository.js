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
 * Repository for payment subsystem.
 *
 * @module     core_payment/repository
 * @package    core_payment
 * @copyright  2020 Shamim Rezaie <shamim@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import Ajax from 'core/ajax';

/**
 * Returns the list of gateways that can process payments in the given currency.
 *
 * @param {string} component
 * @param {string} paymentArea
 * @param {number} componentId
 * @returns {Promise<{shortname: string, name: string, description: String}[]>}
 */
export const getAvailableGateways = (component, paymentArea, componentId) => {
    const request = {
        methodname: 'core_payment_get_available_gateways',
        args: {
            component,
            paymentarea: paymentArea,
            componentid: componentId,
        }
    };
    return Ajax.call([request])[0];
};
