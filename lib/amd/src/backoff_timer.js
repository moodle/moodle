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
 * A timer that will execute a callback with decreasing frequency. Useful for
 * doing polling on the server without overwhelming it with requests.
 *
 * @module     core/backoff_timer
 * @copyright  2016 Ryan Wyllie <ryan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
define(function() {

    /**
     * Constructor for the back off timer.
     *
     * @class
     * @param {function} callback The function to execute after each tick
     * @param {function} backoffFunction The function to determine what the next timeout value should be
     */
    var BackoffTimer = function(callback, backoffFunction) {
        this.callback = callback;
        this.backOffFunction = backoffFunction;
    };

    /**
     * @property {function} callback The function to execute after each tick
     */
    BackoffTimer.prototype.callback = null;

    /**
     * @property {function} backoffFunction The function to determine what the next timeout value should be
     */
    BackoffTimer.prototype.backOffFunction = null;

    /**
     * @property {int} time The timeout value to use
     */
    BackoffTimer.prototype.time = null;

    /**
     * @property {numeric} timeout The timeout identifier
     */
    BackoffTimer.prototype.timeout = null;

    /**
     * Generate the next timeout in the back off time sequence
     * for the timer.
     *
     * The back off function is called to calculate the next value.
     * It is given the current value and an array of all previous values.
     *
     * @return {int} The new timeout value (in milliseconds)
     */
    BackoffTimer.prototype.generateNextTime = function() {
        var newTime = this.backOffFunction(this.time);
        this.time = newTime;

        return newTime;
    };

    /**
     * Stop the current timer and clear the previous time values
     *
     * @return {object} this
     */
    BackoffTimer.prototype.reset = function() {
        this.time = null;
        this.stop();

        return this;
    };

    /**
     * Clear the current timeout, if one is set.
     *
     * @return {object} this
     */
    BackoffTimer.prototype.stop = function() {
        if (this.timeout) {
            window.clearTimeout(this.timeout);
            this.timeout = null;
        }

        return this;
    };

    /**
     * Start the current timer by generating the new timeout value and
     * starting the ticks.
     *
     * This function recurses after each tick with a new timeout value
     * generated each time.
     *
     * The callback function is called after each tick.
     *
     * @return {object} this
     */
    BackoffTimer.prototype.start = function() {
        // If we haven't already started.
        if (!this.timeout) {
            var time = this.generateNextTime();
            this.timeout = window.setTimeout(function() {
                this.callback();
                // Clear the existing timer.
                this.stop();
                // Start the next timer.
                this.start();
            }.bind(this), time);
        }

        return this;
    };

    /**
     * Reset the timer and start it again from the initial timeout
     * values
     *
     * @return {object} this
     */
    BackoffTimer.prototype.restart = function() {
        return this.reset().start();
    };

    /**
     * Returns an incremental function for the timer.
     *
     * @param {int} minamount The minimum amount of time we wait before checking
     * @param {int} incrementamount The amount to increment the timer by
     * @param {int} maxamount The max amount to ever increment to
     * @param {int} timeoutamount The timeout to use once we reach the max amount
     * @return {function}
     */
     BackoffTimer.getIncrementalCallback = function(minamount, incrementamount, maxamount, timeoutamount) {

        /**
         * An incremental function for the timer.
         *
         * @param {(int|null)} time The current timeout value or null if none set
         * @return {int} The new timeout value
         */
        return function(time) {
            if (!time) {
                return minamount;
            }

            // Don't go over the max amount.
            if (time + incrementamount > maxamount) {
                return timeoutamount;
            }

            return time + incrementamount;
        };
    };

    return BackoffTimer;
});
