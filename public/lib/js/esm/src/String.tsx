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

import {Suspense, use, type ReactNode} from 'react';
import {requireAsync} from '@moodle/lms/core/amd';
import config from './config';
import {localStore} from './Storage';

/* eslint-disable no-restricted-properties */

// --- Global type declarations ---

declare const M: {
    str: Record<string, Record<string, string>>;
    util: {
        get_string: (key: string, component: string, param?: StringParams) => string;
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
    component?: string;
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

// --- AMD Ajax type (for lazy loading) ---

type AmdAjaxThenable = {
    then: (resolve: (v: unknown) => void, reject: (e: unknown) => void) => void;
};

type AmdAjax = {
    call: (
        requests: Record<string, unknown>[],
        async_?: boolean,
        loginrequired?: boolean,
        nosessionupdate?: boolean,
        timeout?: number,
        cachekey?: number,
    ) => AmdAjaxThenable[];
};

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
export const getRequestedStrings = (requests: StringRequest[]): Promise<string>[] => {
    type PendingFetch = {
        request: { methodname: string; args: Record<string, unknown> };
        resolve: (value: string) => void;
        reject: (reason: unknown) => void;
    };

    const stringPromises: Promise<string>[] = new Array(requests.length);
    const pendingFetches: PendingFetch[] = [];

    for (let i = 0; i < requests.length; i++) {
        const {key, component: rawComponent = 'core', param = null, lang = config.language} = requests[i];
        const component = rawComponent || 'core';
        const cacheKey = getCacheKey(key, component, lang);

        // 1. Check M.str in-memory cache.
        if (M.str[component]?.[key] !== undefined) {
            const promise = Promise.resolve(M.util.get_string(key, component, param));
            promiseCache.set(cacheKey, promise);
            stringPromises[i] = promise;
            continue;
        }

        // 2. Check browser localStore.
        const cached = localStore.get(cacheKey);
        if (cached !== null) {
            if (!M.str[component]) {
                M.str[component] = {};
            }
            M.str[component][key] = cached;
            const promise = Promise.resolve(M.util.get_string(key, component, param));
            promiseCache.set(cacheKey, promise);
            stringPromises[i] = promise;
            continue;
        }

        // 3. Check promise cache (another request already triggered a fetch for this string).
        if (promiseCache.has(cacheKey)) {
            stringPromises[i] = promiseCache.get(cacheKey)!.then(
                () => M.util.get_string(key, component, param),
            );
            continue;
        }

        // 4. Need to fetch from server — create a deferred promise.
        const fetchPromise = new Promise<string>((resolve, reject) => {
            pendingFetches.push({
                request: {
                    methodname: 'core_get_string',
                    args: {stringid: key, stringparams: [], component, lang},
                },
                resolve,
                reject,
            });
        });

        // Store immediately so duplicate keys in the same batch reuse this promise.
        promiseCache.set(cacheKey, fetchPromise);

        stringPromises[i] = fetchPromise.then((str) => {
            if (!M.str[component]) {
                M.str[component] = {};
            }
            M.str[component][key] = str;
            localStore.set(cacheKey, str);
            return M.util.get_string(key, component, param);
        });
    }

    if (pendingFetches.length > 0) {
        const ajaxRequests = pendingFetches.map((pf) => pf.request);

        requireAsync<AmdAjax>('core/ajax').then(
            (ajax) => {
                const jqPromises = ajax.call(
                    ajaxRequests, true, false, false, 0, config.langrev,
                );
                jqPromises.forEach((jqp, j) => {
                    jqp.then( // eslint-disable-line promise/no-nesting
                        (str: unknown) => pendingFetches[j].resolve(str as string),
                        (err: unknown) => pendingFetches[j].reject(err),
                    );
                });

                return ajax;
            },
            (err) => {
                pendingFetches.forEach((pf) => pf.reject(err));
            },
        );
    }

    return stringPromises;
};

/**
 * Fetch a batch of language strings, returning a single Promise for all results.
 *
 * @param requests List of string requests.
 * @returns A Promise resolving to an array of formatted strings, in request order.
 */
export const getStrings = (requests: StringRequest[]): Promise<string[]> =>
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

        if (!M.str[component]) {
            M.str[component] = {};
        }
        if (!(key in M.str[component])) {
            M.str[component][key] = value;
        }

        localStore.set(cacheKey, value);

        if (!promiseCache.has(cacheKey)) {
            promiseCache.set(cacheKey, Promise.resolve(value));
        }
    }
};

// --- React-facing API ---

export interface StringProps {
    identifier: string;
    component?: string;
    params?: StringParams;
}

/**
 * Fetch a single language string, with React-compatible promise caching.
 *
 * Returns a stable Promise reference for the same (identifier, component, params) tuple,
 * making it safe to use with React's `use()` hook.
 */
export const getString = (
    identifier: string,
    component: string = 'core',
    params?: StringParams,
): Promise<string> => {
    const key = `${component}::${identifier}::${JSON.stringify(params)}`;
    if (!stringPromiseCache.has(key)) {
        stringPromiseCache.set(
            key,
            getRequestedStrings([{key: identifier, component, param: params}])[0],
        );
    }
    return stringPromiseCache.get(key)!;
};

/**
 * Clear all internal caches. Intended for use in tests.
 */
export const resetStringCache = (): void => {
    stringPromiseCache.clear();
    promiseCache.clear();
};

// --- React component ---

function StringInner({identifier, component, params}: StringProps) {
    return <>{use(getString(identifier, component, params))}</>;
}

function String({children, identifier, component = 'core', params}: StringProps & {children?: ReactNode}) {
    return (
        <Suspense fallback={children ?? `${identifier}, ${component}`}>
            <StringInner identifier={identifier} component={component} params={params} />
        </Suspense>
    );
}

export default String;
