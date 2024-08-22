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
 * Tiny AI loading screen handling.
 *
 * @module      tiny_aiplacement/loading
 * @copyright   2024 Matt Porritt <matt.porritt@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import * as coreStr from 'core/str';

/**
 * Display a series of messages one by one with a specified delay between each message.
 * Returns a promise that resolves when the final message is displayed.
 *
 * @param {HTMLElement} element The element to display the messages in.
 * @param {number} delay The delay between each message in milliseconds.
 * @returns {Promise<function(): void>} A function to stop the message cycling.
 */
export async function loadingMessages(element, delay = 6000) {
    let stop = false;

    /**
     * Stop the message cycling.
     */
    function stopMessages() {
        stop = true;
    }

    // Retrieve the messages using the async/await pattern.
    const messages = await coreStr.get_strings([
        {
            key: 'loading_processing',
            component: 'tiny_aiplacement',
        },
        {
            key: 'loading_generating',
            component: 'tiny_aiplacement',
        },
        {
            key: 'loading_applying',
            component: 'tiny_aiplacement',
        },
        {
            key: 'loading_almostdone',
            component: 'tiny_aiplacement',
        }
    ]);

    // eslint-disable-next-line no-async-promise-executor
    return new Promise(async(resolve) => {
        for (let i = 0; i < messages.length; i++) {
            if (stop) {
                break;
            }

            element.textContent = messages[i];
            await new Promise((resolve) => setTimeout(resolve, delay));
        }

        resolve(stopMessages);
    });
}
