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
 * Keyword filter.
 *
 * @module     core/datafilter/filtertypes/keyword
 * @copyright  2020 Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
import Filter from 'core/datafilter/filtertype';
import {getString} from 'core/str';

export default class extends Filter {
    /**
     * For keywords the final value is an Array of strings.
     *
     * @returns {Object}
     */
    get values() {
        return this.rawValues;
    }

    /**
     * Get the placeholder to use when showing the value selector.
     *
     * @return {Promise} Resolving to a String
     */
    get placeholder() {
        return getString('placeholdertype', 'core_user');
    }

    /**
     * Whether to show suggestions in the autocomplete.
     *
     * @return {Boolean}
     */
    get showSuggestions() {
        return false;
    }
}
