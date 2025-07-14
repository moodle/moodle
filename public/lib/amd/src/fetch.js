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
 * @copyright  Andrew Lyons <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @example <caption>Perform a single GET request</caption>
 * import Fetch from 'core/fetch';
 *
 * const result = Fetch.performGet('mod_example', 'animals', { params: { type: 'mammal' } });
 *
 * result.then((response) => {
 *    // Do something with the Response object.
 * })
 * .catch((error) => {
 *     // Handle the error
 * });
 */

import * as Cfg from 'core/config';
import PendingPromise from './pending';

/**
 * A wrapper around the Request, including a Promise that is resolved when the request is complete.
 *
 * @class RequestWrapper
 * @private
 */
class RequestWrapper {
    /** @var {Request} */
    #request = null;

    /** @var {Promise} */
    #promise = null;

    /** @var {Function} */
    #resolve = null;

    /** @var {Function} */
    #reject = null;

    /**
     * Create a new RequestWrapper.
     *
     * @param {Request} request The request object that is wrapped
     */
    constructor(request) {
        this.#request = request;
        this.#promise = new Promise((resolve, reject) => {
            this.#resolve = resolve;
            this.#reject = reject;
        });
    }

    /**
     * Get the wrapped Request.
     *
     * @returns {Request}
     * @private
     */
    get request() {
        return this.#request;
    }

    /**
     * Get the Promise link to this request.
     *
     * @return {Promise}
     * @private
     */
    get promise() {
        return this.#promise;
    }

    /**
     * Handle the response from the request.
     *
     * @param {Response} response
     * @private
     */
    handleResponse(response) {
        if (response.ok) {
            this.#resolve(response);
        } else {
            this.#reject(response.statusText);
        }
    }
}

/**
 * A class to handle requests to the Moodle REST API.
 *
 * @class Fetch
 */
export default class Fetch {
    /**
     * Make a single request to the Moodle API.
     *
     * @param {string} component The frankenstyle component name
     * @param {string} action The component action to perform
     * @param {object} params
     * @param {object} [params.params = {}] The parameters to pass to the API
     * @param {string|Object|FormData} [params.body = null] The HTTP method to use
     * @param {string} [params.method = "GET"] The HTTP method to use
     * @returns {Promise<Response>} A promise that resolves to the Response object for the request
     */
    static async request(
        component,
        action,
        {
            params = {},
            body = null,
            method = 'GET',
        } = {},
    ) {
        const pending = new PendingPromise(`Requesting ${component}/${action} with ${method}`);
        const requestWrapper = Fetch.#getRequest(
            Fetch.#normaliseComponent(component),
            action,
            { params, method, body },
        );
        const result = await fetch(requestWrapper.request);

        pending.resolve();

        requestWrapper.handleResponse(result);

        return requestWrapper.promise;
    }

    /**
     * Make a request to the Moodle API.
     *
     * @param {string} component The frankenstyle component name
     * @param {string} action The component action to perform
     * @param {object} params
     * @param {object} [params.params = {}] The parameters to pass to the API
     * @returns {Promise<Response>} A promise that resolves to the Response object for the request
     */
    static performGet(
        component,
        action,
        {
            params = {},
        } = {},
    ) {
        return this.request(
            component,
            action,
            { params, method: 'GET' },
        );
    }

    /**
     * Make a request to the Moodle API.
     *
     * @param {string} component The frankenstyle component name
     * @param {string} action The component action to perform
     * @param {object} params
     * @param {object} [params.params = {}] The parameters to pass to the API
     * @returns {Promise<Response>} A promise that resolves to the Response object for the request
     */
    static performHead(
        component,
        action,
        {
            params = {},
        } = {},
    ) {
        return this.request(
            component,
            action,
            { params, method: 'HEAD' },
        );
    }

    /**
     * Make a request to the Moodle API.
     *
     * @param {string} component The frankenstyle component name
     * @param {string} action The component action to perform
     * @param {object} params
     * @param {string|Object|FormData} params.body The HTTP method to use
     * @returns {Promise<Response>} A promise that resolves to the Response object for the request
     */
    static performPost(
        component,
        action,
        {
            body,
        } = {},
    ) {
        return this.request(
            component,
            action,
            { body, method: 'POST' },
        );
    }

    /**
     * Make a request to the Moodle API.
     *
     * @param {string} component The frankenstyle component name
     * @param {string} action The component action to perform
     * @param {object} params
     * @param {string|Object|FormData} params.body The HTTP method to use
     * @returns {Promise<Response>} A promise that resolves to the Response object for the request
     */
    static performPut(
        component,
        action,
        {
            body,
        } = {},
    ) {
        return this.request(
            component,
            action,
            { body, method: 'PUT' },
        );
    }

    /**
     * Make a PATCH request to the Moodle API.
     *
     * @param {string} component The frankenstyle component name
     * @param {string} action The component action to perform
     * @param {object} params
     * @param {string|Object|FormData} params.body The HTTP method to use
     * @returns {Promise<Response>} A promise that resolves to the Response object for the request
     */
    static performPatch(
        component,
        action,
        {
            body,
        } = {},
    ) {
        return this.request(
            component,
            action,
            { body, method: 'PATCH' },
        );
    }

    /**
     * Make a request to the Moodle API.
     *
     * @param {string} component The frankenstyle component name
     * @param {string} action The component action to perform
     * @param {object} params
     * @param {object} [params.params = {}] The parameters to pass to the API
     * @param {string|Object|FormData} [params.body = null] The HTTP method to use
     * @returns {Promise<Response>} A promise that resolves to the Response object for the request
     */
    static performDelete(
        component,
        action,
        {
            params = {},
            body = null,
        } = {},
    ) {
        return this.request(
            component,
            action,
            {
                body,
                params,
                method: 'DELETE',
            },
        );
    }

    /**
     * Normalise the component name to remove the core_ prefix.
     *
     * @param {string} component
     * @returns {string}
     */
    static #normaliseComponent(component) {
        return component.replace(/^core_/, '');
    }

    /**
     * Get the Request for a given API request.
     *
     * @param {string} component The frankenstyle component name
     * @param {string} endpoint The endpoint within the componet to call
     * @param {object} params
     * @param {object} [params.params = {}] The parameters to pass to the API
     * @param {string|Object|FormData} [params.body = null] The HTTP method to use
     * @param {string} [params.method = "GET"] The HTTP method to use
     * @returns {RequestWrapper}
     */
    static #getRequest(
        component,
        endpoint,
        {
            params = {},
            body = null,
            method = 'GET',
        }
    ) {
        const url = new URL(`${Cfg.apibase}/rest/v2/${component}/${endpoint}`);
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

        return new RequestWrapper(new Request(url, options));
    }
}
