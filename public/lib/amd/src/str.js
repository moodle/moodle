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
 * This is a backward-compatibility shim that delegates to the ESM String module.
 * New code should import from '@moodle/lms/core/String' directly.
 *
 * @module     core/str
 * @copyright  2015 Damyon Wiese <damyon@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since      2.9
 */
import $ from 'jquery';
import Str from 'core/esm!@moodle/lms/core/String';

/**
 * @typedef StringRequest
 * @type {object}
 * @param {string} requests.key The string identifer to fetch
 * @param {string} [requests.component='core'] The componet to fetch from
 * @param {string} [requests.lang] The language to fetch a string for. Defaults to current page language.
 * @param {object|string} [requests.param] The param for variable expansion in the string.
 */

/* eslint-disable no-restricted-properties */

/**
 * Return a jQuery Promise that resolves to a string.
 *
 * If the string has previously been cached, then the Promise will be resolved immediately, otherwise it will be fetched
 * from the server and resolved when available.
 *
 * @param {string} key The language string key
 * @param {string} [component='core'] The language string component
 * @param {object|string} [param] The param for variable expansion in the string.
 * @param {string} [lang] The users language - if not passed it is deduced.
 * @return {jQuery.Promise} A jQuery Promise containing the translated string
 *
 * @example <caption>Fetching a string</caption>
 *
 * import {get_string} from 'core/str';
 * get_string('cannotfindteacher', 'error')
 * .then((str) => window.console.log(str)); // Cannot find teacher
 */
// eslint-disable-next-line camelcase
export const get_string = (key, component, param, lang) => {
    return get_strings([{key, component, param, lang}])
        .then(results => results[0]);
};

/**
 * Return a Promise that resolves to a string.
 *
 * If the string has previously been cached, then the Promise will be resolved immediately, otherwise it will be fetched
 * from the server and resolved when available.
 *
 * @param {string} key The language string key
 * @param {string} [component='core'] The language string component
 * @param {object|string} [param] The param for variable expansion in the string.
 * @param {string} [lang] The users language - if not passed it is deduced.
 * @return {Promise<string>} A native Promise containing the translated string
 *
 * @example <caption>Fetching a string</caption>
 *
 * import {getString} from 'core/str';
 *
 * getString('cannotfindteacher', 'error')
 * .then((str) => window.console.log(str)); // Cannot find teacher
 */
export const getString = (key, component, param, lang) =>
    Str.getRequestedStrings([{key, component, param, lang}])[0];

/**
 * Make a batch request to load a set of strings.
 *
 * Any missing string will be fetched from the server.
 * The Promise will only be resolved once all strings are available, or an attempt has been made to fetch them.
 *
 * @param {Array.<StringRequest>} requests List of strings to fetch
 * @return {Promise<string[]>} A native promise containing an array of the translated strings
 */
export const getStrings = (requests) => Str.getStrings(requests);

/**
 * Internal function to perform the string requests.
 *
 * @param {Array.<StringRequest>} requests List of strings to fetch
 * @returns {Promise[]}
 */
const getRequestedStrings = (requests) => Str.getRequestedStrings(requests);

/**
 * Make a batch request to load a set of strings.
 *
 * Any missing string will be fetched from the server.
 * The Promise will only be resolved once all strings are available, or an attempt has been made to fetch them.
 *
 * @param {Array.<StringRequest>} requests List of strings to fetch
 * @return {jquery.Promise<string[]>} A jquery promise containing an array of the translated strings
 *
 * @example <caption>Fetching a set of strings</caption>
 *
 * import {get_strings} from 'core/str';
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
 * });
 */
// eslint-disable-next-line camelcase
export const get_strings = (requests) => {
    // We need to use jQuery here because some calling code uses the
    // .done handler instead of the .then handler.
    return $.when.apply($, getRequestedStrings(requests))
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
export const cache_strings = (strings) => Str.cacheStrings(strings);
/* eslint-enable no-restricted-properties */
