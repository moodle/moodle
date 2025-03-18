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
 * Task indicator
 *
 * Watches the progress bar inside the task indicator for updates, and redirects when the progress is complete.
 *
 * @module     core/task_indicator
 * @copyright  2024 Catalyst IT Europe Ltd
 * @author     Mark Johnson <mark.johnson@catalyst-eu.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
export default class {
    /**
     * Watch the progress bar for updates.
     *
     * When the progress bar is updated to 100%, wait a couple of seconds so the user gets to see it if they are watching,
     * then redirect to the specified URL.
     *
     * @param {String} id The ID of the progress bar element.
     * @param {String} redirectUrl Optional URL to redirect to once the task is complete.
     */
    static init(id, redirectUrl) {
        const bar = document.getElementById(id);
        bar.addEventListener('update', (event) => {
            const percent = event?.detail?.percent;
            if (percent > 0) {
                // Once progress starts, display the progress bar and remove the run link.
                bar.classList.remove('stored-progress-notstarted');
                const runlink = document.querySelector(`.runlink[data-idnumber=${id}]`);
                if (runlink) {
                    runlink.remove();
                }
            }
            // Once the progress bar completes, redirect the page.
            if (redirectUrl !== '' && percent === 100) {
                window.setTimeout(() => window.location.assign(redirectUrl), 2000);
            }
        });
    }
}
