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
 * @returns {Promise<{clientid: String, brandname: String}>}
 */
export const getConfigForJs = () => {
    const request = {
        methodname: 'pg_paypal_get_config_for_js',
        args: {},
    };

    return Ajax.call([request])[0];
};
