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
 * A helper used to inform Behat that an operation is in progress and that Behat must wait for it to complete.
 *
 * @module     core/pending
 * @copyright  2018 Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since      3.6
 */

/**
 * A helper used to register any long-running operations that are in-progress and that Behat must wait for it to complete.
 *
 * This is useful in cases where the user interface may be updated and take some time to change - for example where
 * applying a transition.
 *
 * This data is used by Behat, but may also be consumed by other location too.
 *
 * By informing Behat that an action is about to happen, and then that it is complete, allows
 * Behat to wait for that completion and avoid random failures in automated testing.
 *
 * Note: It is recommended that a descriptive key be used to aid in debugging where possible, but this is optional.
 */
export default class {

    /**
     * Create a new Pending Promise statically.
     *
     * @param {String} pendingKey An identifier to help in debugging
     * @return {Promise} A Native Promise
     * @deprecated since Moodle 4.2
     */
    static request(pendingKey) {
        window.console.error(
            `The core/pending::request method has been deprecated. ` +
            `Please use one of the alternative calls to core/pending, for example "new Pending('${pendingKey}')". ` +
            `Called with ${pendingKey}`
        );
        return new this(pendingKey);
    }

    /**
     * Request a new pendingPromise for later resolution.
     *
     * When the action you are performing is complete, simply call resolve on the returned Promise.
     *
     * @param {String} [pendingKey='pendingPromise'] An identifier to help in debugging
     * @return {Promise} A Native Promise
     * @example
     * import Pending from 'core/pending';
     * import {get_string as getString} from 'core/str';
     *
     * const stringPromise = new Pending('mod_myexample/setup');
     * const myString = getString('ok')
     *     .then(okay => {
     *         window.console.log(okay);
     *     })
     *     .then(okay => stringPromise.resolve(okay));
     */
    constructor(pendingKey = 'pendingPromise') {
        let resolver;
        let rejector;
        const pendingPromise = this.constructor.Promise((resolve, reject) => {
            resolver = resolve;
            rejector = reject;
        }, pendingKey);
        pendingPromise.resolve = resolver;
        pendingPromise.reject = rejector;

        return pendingPromise;
    }

    /**
     * Create a new Pending Promise with the same interface as a native Promise.
     *
     * @param {Callable} fn A callable which takes the resolve and reject arguments as in a Native Promise constructor.
     * @param {String} [pendingKey='pendingPromise'] An identifier to help in debugging
     * @returns {Promise}
     * @since Moodle 4.2
     * @example
     * // Use the Pending class in the same way that you would a Native Promise.
     * import Pending from 'core/pending';
     * import {get_string as getString} from 'core/str';
     *
     * export const init => {
     *     Pending.Promise((resolve, reject) => {
     *         getString('ok')
     *             .then(okay => {
     *                 window.console.log(okay);
     *                 return okay;
     *             })
     *             .then(resolve)
     *             .catch(reject);
     *     }, 'mod_myexample/setup:init');
     * };
     */
    static Promise(fn, pendingKey = 'pendingPromise') {
        const resolver = new Promise((resolve, reject) => {
            M.util.js_pending(pendingKey);

            fn(resolve, reject);
        });

        resolver.then(() => {
            M.util.js_complete(pendingKey);
            return;
        }).catch();

        return resolver;
    }
}
