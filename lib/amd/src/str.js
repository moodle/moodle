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
 * Fetch and render language strings.
 * Hooks into the old M.str global - but can also fetch missing strings on the fly.
 *
 * @module     core/str
 * @class      str
 * @package    core
 * @copyright  2015 Damyon Wiese <damyon@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since      2.9
 */
import $ from 'jquery';
import Ajax from 'core/ajax';
import LocalStorage from 'core/localstorage';

// Module cache for the promises so that we don't make multiple
// unnecessary requests.
let promiseCache = [];

/**
 * Return a promise object that will be resolved into a string eventually (maybe immediately).
 *
 * @method get_string
 * @param {string} key The language string key
 * @param {string} component The language string component
 * @param {string} param The param for variable expansion in the string.
 * @param {string} lang The users language - if not passed it is deduced.
 * @return {Promise}
 */
export const get_string = (key, component, param, lang) => {
    return get_strings([{key, component, param, lang}])
        .then(results => results[0]);
};

/**
 * Make a batch request to load a set of strings
 *
 * @method get_strings
 * @param {Object[]} requests Array of { key: key, component: component, param: param, lang: lang };
 *                                      See get_string for more info on these args.
 * @return {Promise}
 */
export const get_strings = (requests) => {
    let requestData = [];
    const pageLang = $('html').attr('lang').replace(/-/g, '_');
    // Helper function to construct the cache key.
    const getCacheKey = ({key, component, lang = pageLang}) => `core_str/${key}/${component}/${lang}`;

    const stringPromises = requests.map((request) => {
        const cacheKey = getCacheKey(request);
        const {component, key, param, lang = pageLang} = request;
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
 * @method cache_strings
 * @param {Object[]} strings Array of { key: key, component: component, lang: lang, value: value }
 */
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
