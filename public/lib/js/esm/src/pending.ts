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
 * This is useful in cases where the user interface may be updated and take some time to change — for example
 * where applying a transition.
 *
 * This data is used by Behat, but may also be consumed by other locations too.
 *
 * By informing Behat that an action is about to happen, and then that it is complete, allows
 * Behat to wait for that completion and avoid random failures in automated testing.
 *
 * Note: It is recommended that a descriptive key be used to aid in debugging where possible, but this is optional.
 *
 * @module     core/pending
 * @copyright  2018 Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since      3.6
 */

declare const M: {
    util: {
        js_pending(key: string): void;
        js_complete(key: string): void;
    };
};

/** A Promise extended with `resolve` and `reject` methods for external settlement. */
interface PendingPromise<T> extends Promise<T> {
    resolve: (value?: any) => void;
    reject: (reason?: any) => void;
}

/**
 * A helper used to register any long-running operations that are in-progress
 * and that Behat must wait for to complete.
 *
 * @example
 * import Pending from 'core/pending';
 * import {getString} from 'core/str';
 *
 * const stringPromise = new Pending('mod_myexample/setup');
 * const myString = getString('ok')
 *     .then(okay => {
 *         window.console.log(okay);
 *     })
 *     .then(okay => stringPromise.resolve(okay));
 */
export default class Pending {
    /** Resolve the pending promise, marking the operation as complete. */
    declare resolve: (value?: any) => void;
    /** Reject the pending promise. */
    declare reject: (reason?: any) => void;

    #internalPromise: Promise<void>;

    /**
     * Register a pending operation with Moodle's Behat integration.
     *
     * @param key A descriptive identifier for debugging.
     */
    static pending(key: string): void {
        M.util.js_pending(key);
    }

    /**
     * Mark a pending operation as complete.
     *
     * @param key The same identifier that was passed to {@link Pending.pending}.
     */
    static complete(key: string): void {
        M.util.js_complete(key);
    }

    /**
     * Request a new pendingPromise for later resolution.
     *
     * When the action you are performing is complete, simply call `resolve` on the returned Promise.
     *
     * @param pendingKey An identifier to help in debugging.
     * @returns A Promise with `resolve` and `reject` methods attached.
     */
    constructor(pendingKey = 'pendingPromise') {
        let resolver!: (value: void) => void;
        let rejector!: (reason?: void) => void;

        this.#internalPromise = Pending.Promise((resolve, reject) => {
            resolver = resolve;
            rejector = reject;
        }, pendingKey);

        this.resolve = resolver;
        this.reject = rejector;
    }

    then<TResult1 = void, TResult2 = never>(
        onfulfilled?: ((value: void) => TResult1 | PromiseLike<TResult1>) | undefined | null,
        onrejected?: ((reason: any) => TResult2 | PromiseLike<TResult2>) | undefined | null
    ): Promise<TResult1 | TResult2> {
        return this.#internalPromise.then(onfulfilled, onrejected);
    }

    /**
     * Attaches a callback for only the rejection of the Promise.
     * @param onrejected The callback to execute when the Promise is rejected.
     * @returns A Promise for the completion of the callback.
     */
    catch<TResult = never>(
        onrejected?: ((reason: any) => TResult | PromiseLike<TResult>) | undefined | null
    ): Promise<void | TResult> {
        return this.#internalPromise.catch(onrejected);
    }

    /**
     * Create a new Pending Promise with the same interface as a native Promise.
     *
     * @param fn A callable which takes the resolve and reject arguments as in a native Promise constructor.
     * @param pendingKey An identifier to help in debugging.
     * @returns A Promise that marks the pending operation as complete when resolved.
     * @since Moodle 4.2
     *
     * @example
     * import Pending from 'core/pending';
     * import {getString} from 'core/str';
     *
     * export const init = () => {
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
    static Promise<T>(
        fn: (resolve: (value: T) => void, reject: (reason?: unknown) => void) => void,
        pendingKey = 'pendingPromise',
    ): Promise<T> {
        const resolver = new Promise<T>((resolve, reject) => {
            Pending.pending(pendingKey);
            fn(resolve, reject);
        });

        resolver.then(() => {
            Pending.complete(pendingKey);
            return;
        }).catch(() => {
            // Intentionally empty — swallow rejection to avoid unhandled promise warnings.
            // The caller's own .catch() will handle the error.
        });

        return resolver;
    }
}
