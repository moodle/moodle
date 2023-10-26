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
 * Fetch and return language strings.
 *
 * @module     core/str
 * @copyright  2015 Damyon Wiese <damyon@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since      2.9
 *
 */
import $ from 'jquery';
import Ajax from 'core/ajax';
import LocalStorage from 'core/localstorage';

// Module cache for the promises so that we don't make multiple
// unnecessary requests.
let promiseCache = [];

/* eslint-disable no-restricted-properties */

/**
 * Return a Promise that resolves to a string.
 *
 * If the string has previously been cached, then the Promise will be resolved immediately, otherwise it will be fetched
 * from the server and resolved when available.
 *
 * @method get_string
 * @param {string} key The language string key
 * @param {string} component The language string component
 * @param {string} param The param for variable expansion in the string.
 * @param {string} lang The users language - if not passed it is deduced.
 * @return {Promise}
 *
 * @example <caption>Fetching a string</caption>
 *
 * import {get_string as getString} from 'core/str';
 * get_string('cannotfindteacher', 'error')
 * .then(str => {
 *     window.console.log(str); // Cannot find teacher
 * })
 * .catch();
 */
// eslint-disable-next-line camelcase
export const get_string = (key, component, param, lang) => {
    return get_strings([{key, component, param, lang}])
        .then(results => results[0]);
};

/**
 * Make a batch request to load a set of strings.
 *
 * Any missing string will be fetched from the server.
 * The Promise will only be resolved once all strings are available, or an attempt has been made to fetch them.
 *
 * @method get_strings
 * @param {Object[]} requests List of strings to fetch
 * @param {string} requests.key The string identifer to fetch
 * @param {string} [requests.component='core'] The componet to fetch from
 * @param {string} [requests.lang] The language to fetch a string for. Defaults to current page language.
 * @param {object|string} [requests.param] The param for variable expansion in the string.
 * @return {Promise[]}
 *
 * @example <caption>Fetching a set of strings</caption>
 *
 * import {get_strings as getStrings} from 'core/str';
 * get_strings([
 *     {
 *         key: 'cannotfindteacher',
 *         component: 'error',
 *     },
 *     {
 *         key: 'yes',
 *         component: 'core',
 *     },
 *     {
 *         key: 'no',
 *         component: 'core',
 *     },
 * ])
 * .then((cannotFindTeacher, yes, no) => {
 *     window.console.log(cannotFindTeacher); // Cannot find teacher
 *     window.console.log(yes); // Yes
 *     window.console.log(no); // No
 * })
 * .catch();
 */
// eslint-disable-next-line camelcase
export const get_strings = (requests) => {
    let requestData = [];
    const pageLang = $('html').attr('lang').replace(/-/g, '_');

    // Helper function to construct the cache key.
    const getCacheKey = ({key, component, lang = pageLang}) => `core_str/${key}/${component}/${lang}`;

    const stringPromises = requests.map((request) => {
        let {component, key, param, lang = pageLang} = request;
        if (!component) {
            component = 'core';
        }

        const cacheKey = getCacheKey({key, component, lang});

        // Helper function to add the promise to cache.
        const buildReturn = (promise) => {
            // Make sure the promise cache contains our promise.
            promiseCache[cacheKey] = promise;
            return promise;
        };

        // Check if we can serve the string straight from M.str.
        if (component in M.str && key in M.str[component]) {
            return buildReturn(new Promise((resolve) => {
                resolve(M.util.get_string(key, component, param, lang));
            }));
        }

        // Check if the string is in the browser's local storage.
        const cached = LocalStorage.get(cacheKey);
        if (cached) {
            M.str[component] = {...M.str[component], [key]: cached};
            return buildReturn(new Promise((resolve) => {
                resolve(M.util.get_string(key, component, param, lang));
            }));
        }

        // Check if we've already loaded this string from the server.
        if (cacheKey in promiseCache) {
            return buildReturn(promiseCache[cacheKey]).then(() => {
                return M.util.get_string(key, component, param, lang);
            });
        } else {
            // We're going to have to ask the server for the string so
            // add this string to the list of requests to be sent.
            return buildReturn(new Promise((resolve, reject) => {
                requestData.push({
                    methodname: 'core_get_string',
                    args: {
                        stringid: key,
                        stringparams: [],
                        component,
                        lang,
                    },
                    done: (str) => {
                        // When we get the response from the server
                        // we should update M.str and the browser's
                        // local storage before resolving this promise.
                        M.str[component] = {...M.str[component], [key]: str};
                        LocalStorage.set(cacheKey, str);
                        resolve(M.util.get_string(key, component, param, lang));
                    },
                    fail: reject
                });
            }));
        }
    });

    if (requestData.length) {
        // If we need to load any strings from the server then send
        // off the request.
        Ajax.call(requestData, true, false, false, 0, M.cfg.langrev);
    }

    // We need to use jQuery here because some calling code uses the
    // .done handler instead of the .then handler.
    return $.when.apply($, stringPromises)
        .then((...strings) => strings);
};

/**
 * Add a list of strings to the caches.
 *
 * This function should typically only be called from core APIs to pre-cache values.
 *
 * @method cache_strings
 * @protected
 * @param {Object[]} strings List of strings to fetch
 * @param {string} strings.key The string identifer to fetch
 * @param {string} strings.value The string value
 * @param {string} [strings.component='core'] The componet to fetch from
 * @param {string} [strings.lang] The language to fetch a string for. Defaults to current page language.
 */
// eslint-disable-next-line camelcase
export const cache_strings = (strings) => {
    const defaultLang = $('html').attr('lang').replace(/-/g, '_');

    strings.forEach(({key, component, value, lang = defaultLang}) => {
        const cacheKey = ['core_str', key, component, lang].join('/');

        // Check M.str caching.
        if (!(component in M.str) || !(key in M.str[component])) {
            if (!(component in M.str)) {
                M.str[component] = {};
            }

            M.str[component][key] = value;
        }

        // Check local storage.
        if (!LocalStorage.get(cacheKey)) {
            LocalStorage.set(cacheKey, value);
        }

        // Check the promises cache.
        if (!(cacheKey in promiseCache)) {
            promiseCache[cacheKey] = $.Deferred().resolve(value).promise();
        }
    });
};
/* eslint-enable no-restricted-properties */
