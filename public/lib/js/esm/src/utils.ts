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
 * Utility functions.
 *
 * @module     core/utils
 * @copyright  2019 Ryan Wyllie <ryan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since      2.9
 */

import Pending from './pending';

/**
 * Create a wrapper function to throttle the execution of the given
 * function to at most once every specified period.
 *
 * If the function is attempted to be executed while it's in cooldown
 * (during the wait period) then it'll immediately execute again as
 * soon as the cooldown is over.
 *
 * @param func The function to throttle.
 * @param wait The number of milliseconds to wait between executions.
 * @returns The throttled function.
 */
export const throttle = <T extends unknown[]>(func: (...args: T) => void, wait: number): ((...args: T) => void) => {
    let isOnCooldown = false;
    let isRunAgain = false;
    let latestArgs: T;

    const run = function (this: unknown, ...args: T): void {
        latestArgs = args;

        if (isOnCooldown) {
            isRunAgain = true;
            return;
        }

        // Preserve caller context for throttled methods.

        func.apply(this, args);
        isOnCooldown = true;

        setTimeout(() => {
            const isRecurse = isRunAgain;
            isOnCooldown = false;
            isRunAgain = false;

            if (isRecurse) {
                run.apply(this, latestArgs);
            }
        }, wait);
    };

    return run;
};

/**
 * DebounceMap A map of functions to their debounced pending promises.
 */
const debounceMap = new Map<(...args: Array<DebouncedFunction<any[]>>) => void, Pending>();

type DebounceOptions = {
    pending?: boolean;
    cancel?: boolean;
};

type DebouncedFunction<T extends unknown[]> = ((...args: T) => void) & {
    cancel?: () => void;
};

/**
 * Create a wrapper function to debounce the execution of the given
 * function. Each attempt to execute the function will reset the cooldown
 * period.
 *
 * @param func The function to debounce.
 * @param wait The number of milliseconds to wait after the final attempt to execute.
 * @param options Optional debounce behavior toggles.
 * @returns The debounced function.
 */
export const debounce = <T extends unknown[]>(
    func: (...args: T) => unknown,
    wait: number,
    {
        pending = false,
        cancel = false,
    }: DebounceOptions = {},
): DebouncedFunction<any> => {
    let timeout: ReturnType<typeof setTimeout> | null = null;

    const returnedFunction: DebouncedFunction<any> = (...args: T): void => {
        if (pending && !debounceMap.has(returnedFunction)) {
            debounceMap.set(returnedFunction, new Pending('core/utils:debounce'));
        }

        if (timeout !== null) {
            clearTimeout(timeout);
        }

        const flush = async (): Promise<void> => {
            // Get the current pending promise and immediately empty it.
            // This is important to allow the function to be debounced again as soon as possible.
            // We do not resolve it until later - but that's fine because the promise is appropriately scoped.
            const pendingPromise = debounceMap.get(returnedFunction);
            debounceMap.delete(returnedFunction);

            // Allow the debounced function to return a Promise.
            // This ensures that Behat will not continue until the function has finished executing.
            await func(...args);

            // Resolve the pending promise if it exists.
            pendingPromise?.resolve();
        };

        timeout = setTimeout(() => {
            void flush();
        }, wait);
    };

    if (cancel) {
        returnedFunction.cancel = (): void => {
            const pendingPromise = debounceMap.get(returnedFunction);
            pendingPromise?.resolve();
            if (timeout !== null) {
                clearTimeout(timeout);
            }
        };
    }

    return returnedFunction;
};

/**
 * Normalise the provided component such that '', 'moodle', and 'core' are treated consistently.
 *
 * @param component The component name to normalise.
 * @returns The normalised component name.
 */
export const getNormalisedComponent = (component: string | null | undefined): string => {
    // AMD callers are untyped and pass null or nothing for core.
    if (component !== undefined && component !== null && !['', 'moodle', 'core'].includes(component)) {
        return component;
    }

    return 'core';
};

/** Groups the helpers for `import utils from '@moodle/lms/core/utils'`. */
const utils = {
    throttle,
    debounce,
    getNormalisedComponent,
};

export default utils;
