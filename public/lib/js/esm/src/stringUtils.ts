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

import config from './config';
import {localStore} from './Storage';
import {
    fetchMany,
} from '@moodle/lms/core/ajax';

// --- Global type declarations ---

declare const M: {
    str: Record<string, Record<string, string>>;
    util: {
        get_string: (key: string, component: string, parameter?: StringParams) => string;
    };
};

// --- Public types ---

/** Parameter types accepted for variable expansion in language strings. */
export type StringParams = Record<string, string | number> | string | number | null;

/** A request for a single language string. */
export type StringRequest = {
    /** The string identifier. */
    key: string;
    /** The component name (defaults to 'core'). */
    component?: string | null;
    /** Parameters for variable expansion. */
    param?: StringParams;
    /** The language code (defaults to current page language). */
    lang?: string;
};

/** An entry for pre-caching a resolved string value. */
export type CacheStringEntry = {
    /** The string identifier. */
    key: string;
    /** The component name (defaults to 'core'). */
    component?: string;
    /** The resolved (untranslated) string value. */
    value: string;
    /** The language code (defaults to current page language). */
    lang?: string;
};

// --- Internal state ---

/** Cache of promises for string fetch operations, keyed by `core_str/key/component/lang`. */
const promiseCache = new Map<string, Promise<string>>();

/** Cache for React's `use()` hook — ensures stable promise references across renders. */
const stringPromiseCache = new Map<string, Promise<string>>();

// --- Cache key helper ---

const getCacheKey = (key: string, component: string, lang: string): string =>
    `core_str/${key}/${component}/${lang}`;

/**
 * Create a resolved Promise that React's `use()` can read without suspending.
 *
 * @param value The resolved string.
 * @returns A Promise already marked as fulfilled.
 */
/* eslint-disable-next-line @typescript-eslint/promise-function-async --
   Not async: that would return a new promise without the status and value React reads. */
const getResolvedPromise = (value: string): Promise<string> =>
    // React reads `status` and `value` to return the result synchronously instead of suspending for a render.
    Object.assign(Promise.resolve(value), {status: 'fulfilled', value});

// --- Core string API ---

/**
 * Request a batch of language strings, returning one Promise per request.
 *
 * Strings already cached in `M.str` or `localStore` resolve immediately.
 * Uncached strings are fetched from the server in a single batched web-service call.
 *
 * @param requests List of string requests.
 * @returns An array of Promises, one per request, each resolving to the formatted string.
 */
export const getRequestedStrings = (requests: StringRequest[]): Array<Promise<string>> => {
    type PendingFetch = {
        request: {methodname: string; args: Record<string, unknown>};
        resolve: (value: string) => void;
        reject: (reason: unknown) => void;
    };

    const stringPromises: Array<Promise<string>> = Array.from({length: requests.length});
    const pendingFetches: PendingFetch[] = [];

    for (const [index, request] of requests.entries()) {
        const {key, param: parameter = null, lang = config.language} = request;
        // AMD callers are untyped and pass null or nothing for core.
        const requestedComponent = request.component ?? '';
        const component = requestedComponent === '' ? 'core' : requestedComponent;
        const cacheKey = getCacheKey(key, component, lang);

        // 1. Check M.str in-memory cache.
        if (M.str[component]?.[key] !== undefined) {
            const promise = getResolvedPromise(M.util.get_string(key, component, parameter));
            promiseCache.set(cacheKey, promise);
            stringPromises[index] = promise;
            continue;
        }

        // 2. Check browser localStore.
        const cached = localStore.get(cacheKey);
        if (cached !== null) {
            if (M.str[component] === undefined) {
                M.str[component] = {};
            }

            M.str[component][key] = cached;
            const promise = getResolvedPromise(M.util.get_string(key, component, parameter));
            promiseCache.set(cacheKey, promise);
            stringPromises[index] = promise;
            continue;
        }

        // 3. Check promise cache (another request already triggered a fetch for this string).
        if (promiseCache.has(cacheKey)) {
            stringPromises[index] = promiseCache.get(cacheKey)!.then(() => M.util.get_string(key, component, parameter));
            continue;
        }

        // 4. Need to fetch from server — create a deferred promise.
        const fetchPromise = new Promise<string>((resolve, reject) => {
            pendingFetches.push({
                request: {
                    methodname: 'core_get_string',
                    args: {
                        stringid: key, stringparams: [], component, lang,
                    },
                },
                resolve,
                reject,
            });
        });

        // Store immediately so duplicate keys in the same batch reuse this promise.
        promiseCache.set(cacheKey, fetchPromise);

        stringPromises[index] = fetchPromise.then(string_ => {
            if (M.str[component] === undefined) {
                M.str[component] = {};
            }

            M.str[component][key] = string_;
            localStore.set(cacheKey, string_);
            return M.util.get_string(key, component, parameter);
        });
    }

    if (pendingFetches.length > 0) {
        const ajaxRequests = pendingFetches.map(pf => pf.request);

        fetchMany<string>(ajaxRequests, {
            loginrequired: false,
            nosessionupdate: true,
            timeout: 0,
            cachekey: config.langrev,
        })
            .then(results => {
                for (const [index, result] of results.entries()) {
                    pendingFetches[index].resolve(result);
                }

                return results;
            })
            .catch((error: unknown) => {
                for (const pf of pendingFetches) {
                    pf.reject(error);
                }
            });
    }

    return stringPromises;
};

/**
 * Fetch a batch of language strings, returning a single Promise for all results.
 *
 * @param requests List of string requests.
 * @returns A Promise resolving to an array of formatted strings, in request order.
 */
export const getStrings = async (requests: StringRequest[]): Promise<string[]> =>
    Promise.all(getRequestedStrings(requests));

/**
 * Pre-populate the string caches with known values.
 *
 * This is typically called by core APIs (e.g. page bootstrap) to seed the cache
 * so that subsequent `getString` / `getStrings` calls resolve immediately.
 *
 * @param strings List of string entries to cache.
 */
export const cacheStrings = (strings: CacheStringEntry[]): void => {
    for (const {key, component = 'core', value, lang = config.language} of strings) {
        const cacheKey = getCacheKey(key, component, lang);

        if (M.str[component] === undefined) {
            M.str[component] = {};
        }

        if (M.str[component][key] === undefined) {
            M.str[component][key] = value;
        }

        localStore.set(cacheKey, value);

        if (!promiseCache.has(cacheKey)) {
            promiseCache.set(cacheKey, Promise.resolve(value));
        }
    }
};

/**
 * Fetch a single language string, with stable promise caching.
 *
 * Returns a stable Promise reference for the same (identifier, component, params) tuple,
 * making it safe to use with React's `use()` hook.
 */
/* eslint-disable @typescript-eslint/promise-function-async -- Returns the cached promise; use() needs it stable. */
export const getString = (
    identifier: string,
    component = 'core',
    parameters?: StringParams,
): Promise<string> => {
    const key = `${component}::${identifier}::${JSON.stringify(parameters)}`;
    if (!stringPromiseCache.has(key)) {
        stringPromiseCache.set(
            key,
            getRequestedStrings([{key: identifier, component, param: parameters}])[0],
        );
    }

    return stringPromiseCache.get(key)!;
};
/* eslint-enable @typescript-eslint/promise-function-async */

/**
 * Clear all internal caches. Intended for use in tests.
 */
export const resetStringCache = (): void => {
    stringPromiseCache.clear();
    promiseCache.clear();
};
