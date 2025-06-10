/**
 * This file is part of Moodle - http://moodle.org/
 *
 * Moodle is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Moodle is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Moodle.  If not, see <http://www.gnu.org/licenses/>.
 *
 * Utility lib.
 *
 * @package
 * @author    Guy Thomas / Branden Visser
 * @copyright Copyright (c) 2017 Open LMS / 2023 Anthology Inc. and its affiliates
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
import $ from 'jquery';

class Util {

    /**
     * When evaluateFunction returns true.
     * @author Guy Thomas
     * @param {function} evaluateFunction
     * @param {integer} maxIterations
     * @returns {promise} jQuery promise
     */
    whenTrue(evaluateFunction, maxIterations) {

        maxIterations = !maxIterations ? 10 : maxIterations;

        const dfd = $.Deferred();
        let i = 0;

        // Maintains a handle to the interval timer, so it can be cleaned up when the element is removed.
        let intervalHandle = null;

        /**
         * The function that will be used to try the evaluation repeatedly.
         */
        const loop = function() {
            i++;
            if (i > maxIterations) {
                dfd.reject();
                if (intervalHandle) {
                    // Cleanup the interval.
                    clearInterval(intervalHandle);
                    intervalHandle = null;
                }
                return;
            }
            if (evaluateFunction()) {
                dfd.resolve();
                if (intervalHandle) {
                    // Cleanup the interval.
                    clearInterval(intervalHandle);
                    intervalHandle = null;
                }
                return;
            }
        };

        intervalHandle = setInterval(loop, 200);

        return dfd.promise();
    }

    /**
     * Listen for the offset/size of a given element to change. Whenever it changes, invoke the given function.
     * @author Branden Visser
     * @param  {jQuery}     $el                     The element to watch
     * @param  {Function}   callback                The function that is invoked when the coords change
     * @param  {Object}     callback.coords         The new set of coords
     * @param  {Number}     callback.coords.top     The top offset of the element
     * @param  {Number}     callback.coords.right   The right offset of the element
     * @param  {Number}     callback.coords.bottom  The bottom offset of the element
     * @param  {Number}     callback.coords.left    The left offset of the element
     * @api private
     */
    onCoordsChange($el, callback) {

        // Maintains the last known set of coords
        let lastCoords = {};

        // Maintains a handle to the interval timer, so it can be cleaned up when the element is removed
        let intervalHandle = null;

        /**
         * The function that is continuously run to determine if there was a change in coords
         */
        const _loop = () => {
            const offset = $el.offset();
            const width = $el.width();
            const height = $el.height();

            const currCoords = {
                'top': offset.top,
                'right': offset.left + width,
                'bottom': offset.top + height,
                'left': offset.left
            };

            // Only continue if the coordinates have changed. Otherwise we do nothing
            if (currCoords.top !== lastCoords.top || currCoords.right !== lastCoords.right ||
                currCoords.bottom !== lastCoords.bottom || currCoords.left !== lastCoords.left) {
                // Set the new set of coords
                lastCoords = currCoords;

                // First ensure the element is still on the DOM. If not, we're going to clean everything up here
                if (!$.contains(document.documentElement, $el[0])) {
                    if (intervalHandle) {
                        clearInterval(intervalHandle);
                        intervalHandle = null;
                    }
                    return;
                }

                // Finally, run the callback and exit.
                callback(lastCoords);
                return;
            }
        };

        // Start the interval timer
        intervalHandle = setInterval(_loop, 200);

        // Perform an immediate initial run
        _loop();
    }

    /**
     * Builds an object which contains all the parameters passed in a URL.
     * @param {string} url URL which has parameters
     * @returns {Object}
     */
    getQuery(url) {
        const query = {};

        url.replace(/[?&](.+?)=([^&#]*)/g, function(_, key, value) {
            query[key] = decodeURI(value).replace(/\+/g, ' ');
        });

        return query;
    }

    /**
     * Taken from underscore.js - debounce function to prevent function spamming on event triggers.
     * Modified by GThomas to implement deferred.
     * @param {function} func
     * @param {int} wait
     * @param {boolean} immediate
     * @returns {Promise}
     */
    debounce(func, wait, immediate) {
        let timeout;
        return function() {
            const dfd = $.Deferred();
            const context = this,
                args = arguments;
            const later = function() {
                timeout = null;
                if (!immediate) {
                    dfd.resolve(func.apply(context, args));
                }
            };
            const callNow = immediate && !timeout;
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
            if (callNow) {
                dfd.resolve(func.apply(context, args));
            }
            return dfd;
        };
    }
}

export default new Util();
