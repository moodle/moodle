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
    interface Window {
        globalAbortController: AbortController | undefined;
    }
}

/**
 * Get the Global Abort Signal.
 */
export const getGlobalAbortSignal = (): AbortSignal => {
    return window.globalAbortController!.signal;
};

/**
 * Abort all ongoing global fetches.
 */
export const abortGlobalFetches = (): void => {
    window.globalAbortController?.abort();
};

/**
 * Reset the Global Abort Controller.
 */
export const resetGlobalAbortController = (): void => {
    window.globalAbortController = new AbortController();
};

// Initialize the Global Abort Controller on module load.
resetGlobalAbortController();

export default getGlobalAbortSignal;
