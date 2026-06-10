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
 * The core/fetch module allows you to make web service requests to the Moodle REST API.
 *
 * @module     core/fetch
 * @copyright  Andrew Lyons <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
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

import config from '@moodle/lms/core/config';
import Pending from '@moodle/lms/core/pending';
import {getGlobalAbortSignal} from './abort';

/** The body types accepted by write-method requests. */
type RequestBody = string | object | FormData;

/** Options for {@link Fetch.request}. */
interface RequestOptions {
    /** Any cache key to use for the request. */
    cachekey?: number | null;
    /** Additional headers to merge with the defaults. */
    headers?: Record<string, string>;
    /** Query-string parameters to append to the URL. */
    params?: Record<string, string>;
    /** The request body (for POST / PUT / PATCH / DELETE). */
    body?: RequestBody | null;
    /** The HTTP method to use. */
    method?: string;
}

/** Options for read-only convenience methods (GET / HEAD). */
interface ReadRequestOptions {
    /** Any cache key to use for the request. */
    cachekey?: number | null;
    /** Additional headers to merge with the defaults. */
    headers?: Record<string, string>;
    /** Query-string parameters to append to the URL. */
    params?: Record<string, string>;
}

/** Options for write convenience methods (POST / PUT / PATCH). */
interface WriteRequestOptions {
    /** Additional headers to merge with the defaults. */
    headers?: Record<string, string>;
    /** The request body. */
    body: RequestBody;
}

/** Options for the DELETE convenience method. */
interface DeleteRequestOptions {
    /** Additional headers to merge with the defaults. */
    headers?: Record<string, string>;
    /** Query-string parameters to append to the URL. */
    params?: Record<string, string>;
    /** An optional request body. */
    body?: RequestBody | null;
}

/**
 * A wrapper around a {@link Request} that pairs it with a {@link Promise}
 * which is resolved or rejected when the response arrives.
 */
class RequestWrapper {
    #request: Request;
    #promise: Promise<Response>;
    #resolve!: (value: Response) => void;
    #reject!: (reason: string) => void;

    constructor(request: Request) {
        this.#request = request;
        this.#promise = new Promise<Response>((resolve, reject) => {
            this.#resolve = resolve;
            this.#reject = reject;
        });
    }

    get request(): Request {
        return this.#request;
    }

    get promise(): Promise<Response> {
        return this.#promise;
    }

    handleResponse(response: Response): void {
        if (response.ok) {
            this.#resolve(response);
        } else {
            this.#reject(response.statusText);
        }
    }
}

/**
 * A class to handle requests to the Moodle REST API.
 */
export default class Fetch {
    /**
     * Make a single request to the Moodle API.
     *
     * @param component The frankenstyle component name.
     * @param action    The component action to perform.
     * @param options   Request options (params, body, method, headers, cachekey).
     * @returns A promise that resolves to the {@link Response}.
     */
    static async request(
        component: string,
        action: string,
        {
            cachekey = null,
            headers = {},
            params = {},
            body = null,
            method = 'GET',
        }: RequestOptions = {},
    ): Promise<Response> {
        const resolvePending = new Pending(`Requesting ${component}/${action} with ${method}`);
        const requestWrapper = Fetch.#getRequest(
            Fetch.#normaliseComponent(component),
            action,
            {headers, params, method, body, cachekey},
        );
        const result = await fetch(requestWrapper.request);

        resolvePending.resolve();
        requestWrapper.handleResponse(result);

        return requestWrapper.promise;
    }

    /**
     * Perform a GET request.
     *
     * @param component The frankenstyle component name.
     * @param action    The component action to perform.
     * @param options   Optional query-string parameters.
     */
    static performGet(
        component: string,
        action: string,
        {cachekey = null, headers = {}, params = {}}: ReadRequestOptions = {},
    ): Promise<Response> {
        return this.request(component, action, {cachekey, headers, params, method: 'GET'});
    }

    /**
     * Perform a HEAD request.
     *
     * @param component The frankenstyle component name.
     * @param action    The component action to perform.
     * @param options   Optional query-string parameters.
     */
    static performHead(
        component: string,
        action: string,
        {headers = {}, params = {}}: ReadRequestOptions = {},
    ): Promise<Response> {
        return this.request(component, action, {headers, params, method: 'HEAD'});
    }

    /**
     * Perform a POST request.
     *
     * @param component The frankenstyle component name.
     * @param action    The component action to perform.
     * @param options   The request body and optional headers.
     */
    static performPost(
        component: string,
        action: string,
        {headers = {}, body}: WriteRequestOptions,
    ): Promise<Response> {
        return this.request(component, action, {headers, body, method: 'POST'});
    }

    /**
     * Perform a PUT request.
     *
     * @param component The frankenstyle component name.
     * @param action    The component action to perform.
     * @param options   The request body and optional headers.
     */
    static performPut(
        component: string,
        action: string,
        {headers = {}, body}: WriteRequestOptions,
    ): Promise<Response> {
        return this.request(component, action, {headers, body, method: 'PUT'});
    }

    /**
     * Perform a PATCH request.
     *
     * @param component The frankenstyle component name.
     * @param action    The component action to perform.
     * @param options   The request body and optional headers.
     */
    static performPatch(
        component: string,
        action: string,
        {headers = {}, body}: WriteRequestOptions,
    ): Promise<Response> {
        return this.request(component, action, {headers, body, method: 'PATCH'});
    }

    /**
     * Perform a DELETE request.
     *
     * @param component The frankenstyle component name.
     * @param action    The component action to perform.
     * @param options   Optional query-string parameters and/or body.
     */
    static performDelete(
        component: string,
        action: string,
        {headers = {}, params = {}, body = null}: DeleteRequestOptions = {},
    ): Promise<Response> {
        return this.request(component, action, {headers, body, params, method: 'DELETE'});
    }

    /**
     * Normalise a component name by stripping the `core_` prefix.
     */
    static #normaliseComponent(component: string): string {
        return component.replace(/^core_/, '');
    }

    /**
     * Build a {@link RequestWrapper} for a given API call.
     *
     * @param component The normalised component name.
     * @param endpoint  The endpoint within the component.
     * @param options   Request options.
     * @returns A new {@link RequestWrapper}.
     */
    static #getRequest(
        component: string,
        endpoint: string,
        {
            cachekey = null,
            headers = {},
            params = {},
            body = null,
            method = 'GET',
        }: RequestOptions,
    ): RequestWrapper {
        const urlParts: string[] = ['rest', 'v2'];
        if (cachekey && cachekey > 1) {
            urlParts.push(`cachekey:${cachekey}`);
        }
        urlParts.push(component, endpoint);

        const url = new URL(`${config.apibase}/${urlParts.join('/').replaceAll('//', '/')}`);
        const options: RequestInit & {headers: Record<string, string>} = {
            method,
            headers: {
                ...headers,
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'pageparent': config.traceId || '',
            },
            signal: getGlobalAbortSignal(),
        };

        Object.entries(params).forEach(([key, value]) => {
            url.searchParams.append(key, value);
        });

        if (body) {
            if (body instanceof FormData) {
                options.body = body;
            } else if (typeof body === 'object') {
                options.body = JSON.stringify(body);
            } else {
                options.body = body;
            }
        }

        return new RequestWrapper(new Request(url, options));
    }
}
