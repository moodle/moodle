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
 * AMD backward-compatibility wrapper for core/ajax.
 *
 * This module wraps the ESM core/ajax module, converting native Promises
 * into jQuery Deferred/Promise objects for legacy AMD consumers.
 *
 * @module     core/ajax
 * @copyright  2015 Damyon Wiese <damyon@moodle.com>
 * @copyright  2025 Andrew Lyons <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since      2.9
 */

import jQuery from 'jquery';
import Log from 'core/log';
import {performFetch} from 'core/esm!@moodle/lms/core/ajax';

/**
 * Make a series of ajax requests and return all the responses.
 *
 * @param {Object[]} requests Array of requests with each containing methodname and args properties.
 *                   done and fail callbacks can be set for each element in the array, or the
 *                   can be attached to the promises returned by this function.
 * @param {Boolean} [async=true] Deprecated. If false, a warning is emitted. Synchronous requests
 *                  are no longer supported; the parameter is ignored.
 * @param {Boolean} [loginrequired=true] When false this function calls an endpoint which does not use the
 *                  session.
 * @param {Boolean} [nosessionupdate=false] If true, the timemodified for the session will not be updated.
 * @param {Number}  [timeout] number of milliseconds to wait for a response. Defaults to no limit.
 * @param {Number}  [cachekey] A cache key used to improve browser-side caching.
 * @return {Promise[]} The jQuery Promises for each of the supplied requests.
 */
export const call = (requests, async, loginrequired, nosessionupdate, timeout, cachekey) => {
    if (typeof async !== 'undefined' && async === false) {
        Log.warn(
            'core/ajax: The `async` parameter is deprecated and synchronous requests are no longer supported. '
            + 'The request will proceed asynchronously.'
        );
    }

    if (typeof loginrequired === 'undefined') {
        loginrequired = true;
    }
    if (typeof nosessionupdate === 'undefined') {
        nosessionupdate = false;
    }
    if (typeof timeout === 'undefined') {
        timeout = 0;
    }
    if (typeof cachekey === 'undefined') {
        cachekey = null;
    }

    const deferreds = requests.map(() => jQuery.Deferred());

    const nativePromises = performFetch(requests, {
        loginrequired,
        nosessionupdate,
        timeout,
        cachekey,
    });

    nativePromises.forEach((promise, index) => {
        promise.then((data) => {
            deferreds[index].resolve(data);
            if (typeof requests[index].done === 'function') {
                requests[index].done(data);
            }

            return data;
        })
        .catch((error) => {
            deferreds[index].reject(error);
            if (typeof requests[index].fail === 'function') {
                requests[index].fail(error);
            }
        });
    });

    return deferreds.map((deferred) => deferred.promise());
};

export default {call};
