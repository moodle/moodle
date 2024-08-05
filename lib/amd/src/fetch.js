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
 * The core/fetch module allows you to make web service requests to the Moodle API.
 *
 * @module     core/fetch
 * @copyright  2023 Andrew Lyons <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import Cfg from 'core/config';
import PendingPromise from './pending';

/**
 * Normalise the component name to remove the core_ prefix.
 *
 * @param {string} component
 * @returns {string}
 */
const normaliseComponent = (component) => component.replace(/^core_/, '');

/**
 * Get the Request object for a given API request.
 *
 * @param {string} component The frankenstyle component name
 * @param {string} endpoint The endpoint within the componet to call
 * @param {object} params
 * @param {object} [params.params = {}] The parameters to pass to the API
 * @param {string|Object|FormData} [params.body = null] The HTTP method to use
 * @param {string} [params.method = "GET"] The HTTP method to use
 * @returns {Request}
 */
const getRequest = (
    component,
    endpoint,
    {
        params = {},
        body = null,
        method = 'GET',
    }
) => {
    const url = new URL(`${Cfg.apibase}rest/v2/${component}/${endpoint}`);
    const options = {
        method,
        headers: {
            'Accept': 'application/json',
            'Content-Type': 'application/json',
        },
    };

    Object.entries(params).forEach(([key, value]) => {
        url.searchParams.append(key, value);
    });

    if (body) {
        if (body instanceof FormData) {
            options.body = body;
        } else if (body instanceof Object) {
            options.body = JSON.stringify(body);
        } else {
            options.body = body;
        }
    }

    return new Request(url, options);
};

/**
 * Make a request to the Moodle API.
 *
 * @param {string} component The frankenstyle component name
 * @param {string} action The component action to perform
 * @param {object} params
 * @param {object} [params.params = {}] The parameters to pass to the API
 * @param {string|Object|FormData} [params.body = null] The HTTP method to use
 * @param {string} [params.method = "GET"] The HTTP method to use
 * @returns {Promise<object>}
 */
const request = async(
    component,
    action,
    {
        params = {},
        body = null,
        method = 'GET',
    } = {},
) => {
    const pending = new PendingPromise(`Requesting ${component}/${action} with ${method}`);
    const result = await fetch(
        getRequest(
            normaliseComponent(component),
            action,
            {params, method, body},
        ),
    );

    pending.resolve();

    if (result.ok) {
        return result.json();
    }

    throw new Error(result.statusText);
};

/**
 * Make a request to the Moodle API.
 *
 * @param {string} component The frankenstyle component name
 * @param {string} action The component action to perform
 * @param {object} params
 * @param {object} [params.params = {}] The parameters to pass to the API
 * @returns {Promise<object>}
 */
const performGet = (
    component,
    action,
    {
        params = {},
    } = {},
) => request(
    component,
    action,
    {params, method: 'GET'},
);

/**
 * Make a request to the Moodle API.
 *
 * @param {string} component The frankenstyle component name
 * @param {string} action The component action to perform
 * @param {object} params
 * @param {object} [params.params = {}] The parameters to pass to the API
 * @returns {Promise<object>}
 */
const performHead = (
    component,
    action,
    {
        params = {},
    } = {},
) => request(
    component,
    action,
    {params, method: 'HEAD'},
);

/**
 * Make a request to the Moodle API.
 *
 * @param {string} component The frankenstyle component name
 * @param {string} action The component action to perform
 * @param {object} params
 * @param {string|Object|FormData} params.body The HTTP method to use
 * @returns {Promise<object>}
 */
const performPost = (
    component,
    action,
    {
        body,
    } = {},
) => request(
    component,
    action,
    {body, method: 'POST'},
);

/**
 * Make a request to the Moodle API.
 *
 * @param {string} component The frankenstyle component name
 * @param {string} action The component action to perform
 * @param {object} params
 * @param {string|Object|FormData} params.body The HTTP method to use
 * @returns {Promise<object>}
 */
const performPut = (
    component,
    action,
    {
        body,
    } = {},
) => request(
    component,
    action,
    {body, method: 'POST'},
);

/**
 * Make a request to the Moodle API.
 *
 * @param {string} component The frankenstyle component name
 * @param {string} action The component action to perform
 * @param {object} params
 * @param {object} [params.params = {}] The parameters to pass to the API
 * @param {string|Object|FormData} [params.body = null] The HTTP method to use
 * @returns {Promise<object>}
 */
const performDelete = (
    component,
    action,
    {
        params = {},
        body = null,
    } = {},
) => request(
    component,
    action,
    {
        body,
        params,
        method: 'DELETE',
    },
);

export {
    request,
    performGet,
    performHead,
    performPost,
    performPut,
    performDelete,
};
