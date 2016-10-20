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
 * @class      backoff_timer
 * @package    core
 * @copyright  2016 Ryan Wyllie <ryan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
define(function() {

    // Default to one second.
    var DEFAULT_TIME = 1000;

    /**
     * The default back off function for the timer. It uses the Fibonacci
     * sequence to determine what the next timeout value should be.
     *
     * @param {(int|null)} time The current timeout value or null if none set
     * @param {array} previousTimes An array containing all previous timeout values
     * @return {int} The new timeout value
     */
    var fibonacciBackOff = function(time, previousTimes) {
        if (!time) {
            return DEFAULT_TIME;
        }

        if (previousTimes.length) {
            var lastTime = previousTimes[previousTimes.length - 1];
            return time + lastTime;
        } else {
            return DEFAULT_TIME;
        }
    };

    /**
     * Constructor for the back off timer.
     *
     * @param {function} callback The function to execute after each tick
     */
    var Timer = function(callback) {
        this.reset();
        this.setCallback(callback);
        // Set the default backoff function to be the Fibonacci sequence.
        this.setBackOffFunction(fibonacciBackOff);
    };

    /**
     * Set the callback function to be executed after each tick of the
     * timer.
     *
     * @method setCallback
     * @param {function} callback The callback function
     * @return {object} this
     */
    Timer.prototype.setCallback = function(callback) {
        this.callback = callback;

        return this;
    };

    /**
     * Get the callback function for this timer.
     *
     * @method getCallback
     * @return {function}
     */
    Timer.prototype.getCallback = function() {
        return this.callback;
    };

    /**
     * Set the function to be used when calculating the back off time
     * for each tick of the timer.
     *
     * The back off function will be given two parameters: the current
     * time and an array containing all previous times.
     *
     * @method setBackOffFunction
     * @param {function} backOffFunction The function to calculate back off times
     * @return {object} this
     */
    Timer.prototype.setBackOffFunction = function(backOffFunction) {
        this.backOffFunction = backOffFunction;

        return this;
    };

    /**
     * Get the current back off function.
     *
     * @method getBackOffFunction
     * @return {function}
     */
    Timer.prototype.getBackOffFunction = function() {
        return this.backOffFunction;
    };

    /**
     * Generate the next timeout in the back off time sequence
     * for the timer.
     *
     * The back off function is called to calculate the next value.
     * It is given the current value and an array of all previous values.
     *
     * @method generateNextTime
     * @return {int} The new timeout value (in milliseconds)
     */
    Timer.prototype.generateNextTime = function() {
        var newTime = this.getBackOffFunction().call(
            this.getBackOffFunction(),
            this.time,
            this.previousTimes
        );
        this.previousTimes.push(this.time);
        this.time = newTime;

        return newTime;
    };

    /**
     * Stop the current timer and clear the previous time values
     *
     * @method reset
     * @return {object} this
     */
    Timer.prototype.reset = function() {
        this.time = null;
        this.previousTimes = [];
        this.stop();

        return this;
    };

    /**
     * Clear the current timeout, if one is set.
     *
     * @method stop
     * @return {object} this
     */
    Timer.prototype.stop = function() {
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
     * @method start
     * @return {object} this
     */
    Timer.prototype.start = function() {
        // If we haven't already started.
        if (!this.timeout) {
            var time = this.generateNextTime();
            this.timeout = window.setTimeout(function() {
                this.getCallback().call();
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
     * @method restart
     * @return {object} this
     */
    Timer.prototype.restart = function() {
        return this.reset().start();
    };

    return Timer;
});
