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
 * Helper to get external content for Tiny Premium plugin.
 *
 * @module      tiny_premium/external
 * @copyright   2023 David Woloszyn <david.woloszyn@moodle.com>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import Ajax from 'core/ajax';

/**
 * Due to the order items are initialise, options.js will not work in getting the API key.
 * This external method is used to get the API key in time.
 *
 * @param {Number} contextId The context id
 * @return {Promise}
 */
export const getApiKey = (contextId) => {
    const request = {
        methodname: 'tiny_premium_get_api_key',
        args: {
            contextid: contextId
        }
    };
    return Ajax.call([request])[0];
};
