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
 * @module core/utils
 * @copyright  2019 Ryan Wyllie <ryan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

 /**
  * Create a wrapper function to throttle the execution of the given
  *
  * function to at most once every specified period.
  *
  * If the function is attempted to be executed while it's in cooldown
  * (during the wait period) then it'll immediately execute again as
  * soon as the cooldown is over.
  *
  * @method
  * @param {Function} func The function to throttle
  * @param {Number} wait The number of milliseconds to wait between executions
  * @return {Function}
  */
export const throttle = (func, wait) => {
    let onCooldown = false;
    let runAgain = null;
    const run = function(...args) {
        if (runAgain === null) {
            // This is the first time the function has been called.
            runAgain = false;
        } else {
            // This function has been called a second time during the wait period
            // so re-run it once the wait period is over.
            runAgain = true;
        }

        if (onCooldown) {
            // Function has already run for this wait period.
            return;
        }

        func.apply(this, args);
        onCooldown = true;

        setTimeout(() => {
            const recurse = runAgain;
            onCooldown = false;
            runAgain = null;

            if (recurse) {
                run(args);
            }
        }, wait);
    };

    return run;
};

/**
 * Create a wrapper function to debounce the execution of the given
 * function. Each attempt to execute the function will reset the cooldown
 * period.
 *
 * @method
 * @param {Function} func The function to debounce
 * @param {Number} wait The number of milliseconds to wait after the final attempt to execute
 * @return {Function}
 */
export const debounce = (func, wait) => {
    let timeout = null;
    return function(...args) {
        clearTimeout(timeout);
        timeout = setTimeout(() => {
            func.apply(this, args);
        }, wait);
    };
};
