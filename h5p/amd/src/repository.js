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
 * Module to handle AJAX interactions.
 *
 * @module     core_h5p/repository
 * @copyright  2023 Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
import {call as fetchMany} from 'core/ajax';
import * as config from 'core/config';

/**
 * Send a xAPI statement to LMS.
 *
 * @param {string} component
 * @param {Object} statements
 * @returns {Promise}
 */
export const postStatement = (component, statements) => fetchMany([{
    methodname: 'core_xapi_statement_post',
    args: {
        component,
        requestjson: JSON.stringify(statements),
    }
}])[0];

/**
 * Send a xAPI state to LMS.
 *
 * @param {string} component
 * @param {string} activityId
 * @param {Object} agent
 * @param {string} stateId
 * @param {string} stateData
 */
export const postState = (
    component,
    activityId,
    agent,
    stateId,
    stateData,
) => {
    // Please note that we must use a Beacon send here.
    // The XHR is not guaranteed because it will be aborted on page transition.
    // https://developer.mozilla.org/en-US/docs/Web/API/Beacon_API
    // Note: Moodle does not currently have a sendBeacon API endpoint.
    const requestUrl = new URL(`${config.wwwroot}/lib/ajax/service.php`);
    requestUrl.searchParams.set('sesskey', config.sesskey);

    navigator.sendBeacon(requestUrl, JSON.stringify([{
        index: 0,
        methodname: 'core_xapi_post_state',
        args: {
            component,
            activityId,
            agent: JSON.stringify(agent),
            stateId,
            stateData,
        }
    }]));
};

/**
 * Delete a xAPI state from LMS.
 *
 * @param {string} component
 * @param {string} activityId
 * @param {Object} agent
 * @param {string} stateId
 * @returns {Promise}
 */
export const deleteState = (
    component,
    activityId,
    agent,
    stateId,
) => fetchMany([{
    methodname: 'core_xapi_delete_state',
    args: {
        component,
        activityId,
        agent: JSON.stringify(agent),
        stateId,
    },
}])[0];
