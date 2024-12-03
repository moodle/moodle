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
 * Filter managing hidden questions.
 *
 * @module     qbank_viewquestiontype/datafilter/filtertypes/type
 * @author     Mark Johnson <mark.johnson@catalyst-eu.net>
 * @copyright  2024 Catalyst IT Europe Ltd
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import Filter from 'core/datafilter/filtertype';

export default class extends Filter {
    /**
     * Get the list of values for this filter type.
     *
     * Overrides the default behaviour of running parseInt on the raw values, since we have textual
     * plugin identifiers and not numeric IDs.
     *
     * @returns {Array}
     */
    get values() {
        return this.rawValues;
    }
}
