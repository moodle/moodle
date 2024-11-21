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
 * Throttler.
 *
 * @copyright  2018 Frédéric Massart
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define([], function() {
    /**
     * Throttler.
     *
     * @param {Number} delay The delay.
     */
    function Throttler(delay) {
        this.delay = delay || 300;
        this.timeout = null;
        this.time = new Date();
    }

    Throttler.prototype.cancel = function() {
        clearTimeout(this.timeout);
    };

    Throttler.prototype.schedule = function(callback) {
        var now = new Date();
        if (this.time.getTime() + this.delay > now) {
            clearTimeout(this.timeout);
        }

        this.time = now;
        this.timeout = setTimeout(callback, this.delay);
    };

    return Throttler;
});
