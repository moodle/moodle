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
 * A list of human readable names for the keycodes.
 *
 * @module     core/key_codes
 * @copyright  2016 Ryan Wyllie <ryan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since      3.2
 */

define(function() {
    /**
     * @type {object}
     */
    return {
        'tab': 9,
        'enter': 13,
        'shift': 16,
        'ctrl': 17,
        'alt': 18,
        'escape': 27,
        'space': 32,
        'end': 35,
        'home': 36,
        'arrowLeft': 37,
        'arrowUp': 38,
        'arrowRight': 39,
        'arrowDown': 40,
        '8': 56,
        'asterix': 106,
        'pageUp': 33,
        'pageDown': 34,
    };
});
