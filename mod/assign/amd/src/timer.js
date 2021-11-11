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

import Notification from 'core/notification';
import {get_string as getString} from 'core/str';

/**
 * A javascript module for the time in the assign module.
 *
 * @copyright  2020 Matt Porritt <mattp@catalyst-au.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Timestamp at which time runs out.
 *
 * @property {Number} endTime
 */
let endTime = 0;

/**
 * ID of the timeout that updates the clock.
 *
 * @property {Number} timeoutId
 */
let timeoutId = null;

/**
 * The timer element.
 *
 * @property {Element} timer
 */
let timer = null;

/**
 * Helper method to convert time remaining in seconds into HH:MM:SS format.
 *
 * @method formatSeconds
 * @param {Number} secs Time remaining in seconds to get value for.
 * @return {String} Time remaining in HH:MM:SS format.
 */
const formatSeconds = (secs) => {
    const hours = Math.floor(secs / 3600);
    const minutes = Math.floor(secs / 60) % 60;
    const seconds = secs % 60;

    return [hours, minutes, seconds]
        // Remove the hours column if there is less than 1 hour left.
        .filter((value, index) => value !== 0 || index > 0)
        // Ensure that all fields are two digit numbers.
        .map(value => `${value}`.padStart(2, '0'))
        .join(":");
};

/**
 * Stop the timer, if it is running.
 *
 * @method stop
 */
const stop = () => {
    if (timeoutId) {
        clearTimeout(timeoutId);
    }
};

/**
 * Function to update the clock with the current time left.
 *
 * @method update
 */
const update = () => {
    const now = new Date().getTime();
    const secondsLeft = Math.floor((endTime - now) / 1000);

    // If time has expired, set the hidden form field that says time has expired.
    if (secondsLeft <= 0) {
        timer.classList.add('alert', 'alert-danger');
        timer.innerHTML = '00:00:00';

        // Only add a notification on the assign submission page.
        if (document.getElementById("mod_assign_timelimit_block")) {
            getString('caneditsubmission', 'mod_assign')
                .then(message => Notification.addNotification({message}))
                .catch(Notification.exception);
        }

        stop();
        return;
    } else if (secondsLeft < 300) { // Add danger style when less than 5 minutes left.
        timer.classList.remove('alert-warning');
        timer.classList.add('alert', 'alert-danger');
    } else if (secondsLeft < 900) { // Add warning style when less than 15 minutes left.
        timer.classList.remove('alert-danger');
        timer.classList.add('alert', 'alert-warning');
    }

    // Update the time display.
    timer.innerHTML = formatSeconds(secondsLeft);

    // Arrange for this method to be called again soon.
    timeoutId = setTimeout(update, 500);
};

/**
 * Set up the submission timer.
 *
 * @method init
 * @param {Number} timerId Unique ID of the timer element.
 */
export const init = (timerId) => {
    timer = document.getElementById(timerId);
    endTime = M.pageloadstarttime.getTime() + (timer.dataset.starttime * 1000);
    update();
};
