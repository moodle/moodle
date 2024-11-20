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
 * Base filter for binary selector ie: (Yes / No).
 *
 * @module     core/datafilter/filtertypes/binary
 * @author     2022 Ghaly Marc-Alexandre <marc-alexandreghaly@catalyst-ca.net>
 * @copyright  2022 Catalyst IT Australia Pty Ltd
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import Filter from 'core/datafilter/filtertype';
import Selectors from 'core/datafilter/selectors';
import Templates from 'core/templates';
import {get_strings as getStrings} from 'core/str';

export default class extends Filter {

    /**
     * Text string for the first binary option.
     *
     * This option (and {@see optionTwo}) are set by {@see getTextValues()}. The base class will set default values,
     * a subclass can override the method to define its own option.
     *
     * @type {String}
     */
    optionOne;

    /**
     * Text string for the second binary option.
     *
     * @type {String}
     */
    optionTwo;

    /**
     * Add the value selector to the filter row.
     *
     * @param {Array} initialValues The default value for the filter.
     */
    async addValueSelector(initialValues) {
        [this.optionOne, this.optionTwo] = await this.getTextValues();
        return this.displayBinarySelection(initialValues[0]);
    }

    /**
     * Fetch text values for select options.
     *
     * Subclasses should override this method to set their own options.
     *
     * @returns {Promise}
     */
    getTextValues() {
        return getStrings([{key: 'no'}, {key: 'yes'}]);
    }

    /**
     * Renders yes/no select input with proper selection.
     *
     * @param {Number} initialValue The default value for the filter.
     */
    async displayBinarySelection(initialValue = 0) {
        // We specify a specific filterset in case there are multiple filtering condition - avoiding glitches.
        const specificFilterSet = this.rootNode.querySelector(Selectors.filter.byName(this.filterType));
        const sourceDataNode = this.getSourceDataForFilter();
        const context = {
            filtertype: this.filterType,
            title: sourceDataNode.getAttribute('data-field-title'),
            required: sourceDataNode.dataset.required,
            options: [
                {
                    text: this.optionOne,
                    value: 0,
                    selected: initialValue === 0,
                },
                {
                    text: this.optionTwo,
                    value: 1,
                    selected: initialValue === 1,
                },
            ]
        };
        return Templates.render('core/datafilter/filtertypes/binary_selector', context)
        .then((binaryUi, js) => {
            return Templates.replaceNodeContents(specificFilterSet.querySelector(Selectors.filter.regions.values), binaryUi, js);
        });
    }

    /**
     * Get the list of raw values for this filter type.
     *
     * @returns {Array}
     */
    get values() {
        return [parseInt(this.filterRoot.querySelector(`[data-filterfield="${this.name}"]`).value)];
    }

}
