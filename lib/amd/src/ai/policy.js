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
 * AI Subsystem policy functions.
 *
 * @module     core/ai/policy
 * @copyright   2024 Matt Porritt <matt.porritt@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since      4.5
 */

import Ajax from 'core/ajax';

/**
 * Get the policy status for a user.
 *
 * @param {int} userId The user ID.
 * @param {int} contextId Context id request is made in.
 * @return {array} The policy status.
 */
export const getPolicyStatus = async(userId, contextId) => {
    const request = {
        methodname: 'core_ai_get_policy_status',
        args: {userid: userId, contextid: contextId},
    };

    const response = await Ajax.call([request]);
    return response[0];
};

/**
 * Set the policy status for a user.
 * @param {int} userId The user ID.
 * @param {int} contextId Context id request is made in.
 * @return {promise} Promise resolved with the policy set status.
 */
export const setPolicyStatus = async(userId, contextId) => {
    const request = {
        methodname: 'core_ai_set_policy_status',
        args: {userid: userId, contextid: contextId},
    };

    const response = await Ajax.call([request]);
    return response[0];
};
