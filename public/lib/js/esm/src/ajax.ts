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
 * Standard Ajax wrapper for Moodle web service calls.
 *
 * Calls the central Ajax script which can invoke any existing web service
 * using the current session. Supports batching multiple requests into a
 * single HTTP call.
 *
 * @module     core/ajax
 * @copyright  2015 Damyon Wiese <damyon@moodle.com>
 * @copyright  2025 Andrew Lyons <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since      2.9
 */

import config from '@moodle/lms/core/config';
import Pending from '@moodle/lms/core/pending';
import log from '@moodle/lms/core/log';
import {redirect} from '@moodle/lms/core/location';
import {relativeUrl} from '@moodle/lms/core/url';
import {getGlobalAbortSignal} from './abort';

/** A single web service request descriptor. */
export interface AjaxRequest {
    methodname: string;
    args: Record<string, unknown>;
}

/** Options for web service calls. */
export interface AjaxOptions {
    /** When false, calls a no-login endpoint. Default true. */
    loginrequired?: boolean;
    /** When true, the request will not extend the session timer. Default false. */
    nosessionupdate?: boolean;
    /** Number of milliseconds to wait before aborting. 0 means no limit. Default 0. */
    timeout?: number;
    /** A cache key for browser-side caching (only with loginrequired=false). */
    cachekey?: number | null;
}

/** Shape of a Moodle web service error rejection. */
export interface MoodleAjaxError {
    message: string;
    errorcode: string;
    link?: string;
    moreinfourl?: string;
    debuginfo?: string;
}

/**
 * Type guard that narrows an unknown catch value to {@link MoodleAjaxError}.
 */
export function isMoodleAjaxError(err: unknown): err is MoodleAjaxError {
    return (
        typeof err === 'object' &&
        err !== null &&
        'message' in err &&
        'errorcode' in err
    );
}

/** Maximum URL length before falling back from GET to POST. */
const MAX_URL_LENGTH = 2000;

/** Tracks whether the page is unloading (to suppress errors during navigation). */
let unloading = false;

if (typeof window !== 'undefined') {
    window.addEventListener('beforeunload', () => {
        unloading = true;
    });
}

/** Internal request payload shape sent to service.php. */
export interface ServiceRequest {
    index: number;
    methodname: string;
    args: Record<string, unknown>;
}

/** Shape of a single response item from service.php. */
export interface ServiceResponse {
    error: boolean;
    data?: unknown;
    exception?: MoodleAjaxError;
}

/** Shape when the entire batch fails. */
export interface ServiceErrorResponse {
    error: true;
    exception?: MoodleAjaxError;
}

export type ServiceResult = ServiceResponse[] | ServiceErrorResponse;

// The shape of the fetch result is defined by the web service being called,
// so we can't be more specific than unknown here.
export type FetchResultSuccess = unknown;

export interface FetchResultError {
    error: true;
    exception?: MoodleAjaxError;
}

export type FetchResult = FetchResultSuccess[] | FetchResultError[];

/**
 * Build the URL and fetch options for a web service call.
 */
function buildRequest(
    requestData: ServiceRequest[],
    options: Required<AjaxOptions>,
): {url: string; init: RequestInit} {
    const {loginrequired, nosessionupdate, cachekey} = options;

    const methodInfo = requestData.map((r) => r.methodname);
    const requestInfo = methodInfo.length <= 5
        ? methodInfo.sort().join()
        : `${methodInfo.length}-method-calls`;

    const ajaxRequestData = JSON.stringify(requestData);

    let script: string;
    let url: string;
    let method = 'POST';

    if (!loginrequired) {
        script = 'service-nologin.php';
        url = `${config.wwwroot}/lib/ajax/${script}?info=${requestInfo}`;
        if (cachekey) {
            url += `&cachekey=${cachekey}`;
            method = 'GET';
        }
    } else {
        script = 'service.php';
        url = `${config.wwwroot}/lib/ajax/${script}?sesskey=${config.sesskey}&info=${requestInfo}`;
    }

    if (nosessionupdate) {
        url += '&nosessionupdate=true';
    }

    const headers: Record<string, string> = {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        'pageparent': config.traceId || '',
    };

    let body: string | undefined;

    if (method === 'POST') {
        body = ajaxRequestData;
    } else {
        // Try GET with args in URL; fall back to POST if too long.
        const urlWithArgs = `${url}&args=${encodeURIComponent(ajaxRequestData)}`;
        if (urlWithArgs.length > MAX_URL_LENGTH) {
            method = 'POST';
            body = ajaxRequestData;
        } else {
            url = urlWithArgs;
        }
    }

    const init: RequestInit = {
        method,
        headers,
        credentials: 'same-origin',
        signal: getGlobalAbortSignal(),
    };

    if (body) {
        init.body = body;
    }

    return {url, init};
}

/**
 * Process the response from service.php, resolving/rejecting individual request promises.
 */
function processResponse(
    responses: ServiceResult,
    resolvers: Array<{resolve: (value: unknown) => void; reject: (reason: unknown) => void}>,
    nosessionupdate: boolean,
): void {
    // Check for a whole-batch error.
    if ('error' in responses && responses.error && !Array.isArray(responses)) {
        for (const {reject} of resolvers) {
            reject(responses);
        }
        return;
    }

    const items = responses as ServiceResponse[];
    let exception: MoodleAjaxError | Error | null = null;

    for (let i = 0; i < resolvers.length; i++) {
        const response = items[i];
        if (typeof response === 'undefined') {
            exception = new Error('missing response');
            break;
        }
        if (response.error === false) {
            resolvers[i].resolve(response.data);
        } else {
            exception = response.exception || new Error('Unknown error');
            break;
        }
    }

    if (exception !== null) {
        if (isMoodleAjaxError(exception) && exception.errorcode === 'servicerequireslogin' && !nosessionupdate) {
            redirect(relativeUrl('/login/index.php'));
        } else {
            for (const {reject} of resolvers) {
                reject(exception);
            }
        }
    }
}

/**
 * Execute multiple web service requests in a single batched HTTP call.
 *
 * Returns an array of individual Promises — one per request — that each
 * resolve/reject independently as the server response is processed.
 *
 * @internal Used by the AMD backward-compatibility wrapper. Prefer
 *           {@link fetchOne} or {@link fetchMany} in new code.
 * @param requests Array of web service request descriptors.
 * @param options Call options.
 * @returns An array of Promises, one per request, in the same order.
 */
export function performFetch(
    requests: AjaxRequest[],
    options: AjaxOptions = {},
): Promise<unknown>[] {
    const {
        loginrequired = true,
        nosessionupdate = false,
        timeout = 0,
        cachekey = null,
    } = options;

    const resolvedOptions: Required<AjaxOptions> = {
        loginrequired,
        nosessionupdate,
        timeout,
        cachekey: cachekey && Number(cachekey) > 0 ? Number(cachekey) : null,
    };

    const requestData: ServiceRequest[] = requests.map((req, index) => ({
        index,
        methodname: req.methodname,
        args: req.args,
    }));

    const resolvers: Array<{resolve: (value: unknown) => void; reject: (reason: unknown) => void}> = [];
    const promises: Promise<unknown>[] = requests.map(() => {
        let outerResolve!: (value: unknown) => void;
        let outerReject!: (reason: unknown) => void;
        const promise = new Promise<unknown>((resolve, reject) => {
            outerResolve = resolve;
            outerReject = reject;
        });
        resolvers.push({resolve: outerResolve, reject: outerReject});
        return promise;
    });

    const {url, init} = buildRequest(requestData, resolvedOptions);

    const pendingPromise = new Pending('core/ajax:call');

    // Set up abort controller for timeout.
    let controller: AbortController | undefined;
    let timeoutId: ReturnType<typeof setTimeout> | undefined;
    if (timeout > 0) {
        controller = new AbortController();
        init.signal = controller.signal;
        timeoutId = setTimeout(() => controller!.abort(), timeout);

        // Also abort if the global abort controller is triggered (e.g. on page unload) to avoid hanging requests.
        getGlobalAbortSignal().addEventListener('abort', () => {
            controller!.abort();
        });
    }

    fetch(url, init)
        .then((response) => {
            if (!response.ok) {
                throw new Error(`HTTP ${response.status}: ${response.statusText}`);
            }
            return response.json() as Promise<ServiceResult>;
        })
        .then((data) => {
            processResponse(data, resolvers, nosessionupdate);

            return data;
        })
        .catch((error) => {
            if (unloading) {
                log.error('Page unloaded.');
                log.error(error);
            } else {
                for (const {reject} of resolvers) {
                    reject(error);
                }
            }
        })
        .finally(() => {
            if (timeoutId) {
                clearTimeout(timeoutId);
            }
            pendingPromise.resolve();
        });

    return promises;
}

/**
 * Execute a single web service request.
 *
 * @param request The web service request descriptor.
 * @param options Call options.
 * @returns A Promise that resolves with the web service response data.
 */
export function fetchOne<T = unknown>(
    request: AjaxRequest,
    options: AjaxOptions = {},
): Promise<T> {
    return performFetch([request], options)[0] as Promise<T>;
}

/**
 * Execute multiple web service requests in a single batched HTTP call.
 *
 * @param requests Array of web service request descriptors.
 * @param options Call options.
 * @returns A Promise that resolves to an array of responses in the same order as requests.
 */
export function fetchMany<T = unknown>(
    requests: AjaxRequest[],
    options: AjaxOptions = {},
): Promise<T[]> {
    return Promise.all(performFetch(requests, options)) as Promise<T[]>;
}
