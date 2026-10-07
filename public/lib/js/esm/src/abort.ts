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
 * Global Abort Controller used in the Fetch API.
 *
 * @copyright  Andrew Lyons <andrew@nicols.co.uk>
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

declare global {
    /* eslint-disable-next-line @typescript-eslint/consistent-type-definitions -- Window merging needs an interface. */
    interface Window {
        globalAbortController: AbortController | undefined;
    }

    // Declares the same global for globalThis access; only var reaches globalThis.
    var globalAbortController: AbortController | undefined;
}

/**
 * The signal every global fetch is issued with, so that they can be cancelled together.
 *
 * @returns The signal of the current global abort controller.
 */
export const getGlobalAbortSignal = (): AbortSignal => globalThis.globalAbortController!.signal;

/**
 * Cancel every request issued with the signal from {@link getGlobalAbortSignal}.
 */
export const abortGlobalFetches = (): void => {
    globalThis.globalAbortController?.abort();
};

/**
 * Replace the controller, so that requests made after an abort are not cancelled by it.
 */
export const resetGlobalAbortController = (): void => {
    /* eslint-disable-next-line unicorn/no-global-object-property-assignment -- Behat aborts via this global. */
    globalThis.globalAbortController = new AbortController();
};

/* eslint-disable-next-line unicorn/no-top-level-side-effects -- Must exist before the first request. */
resetGlobalAbortController();

export default getGlobalAbortSignal;
