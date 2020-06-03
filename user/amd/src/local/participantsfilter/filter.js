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
 * Base Filter class for a filter type in the participants filter UI.
 *
 * @module     core_user/local/participantsfilter/filter
 * @package    core_user
 * @copyright  2020 Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
import Autocomplete from 'core/form-autocomplete';
import Selectors from './selectors';
import {get_string as getString} from 'core/str';

/**
 * Fetch all checked options in the select.
 *
 * This is a poor-man's polyfill for select.selectedOptions, which is not available in IE11.
 *
 * @param {HTMLSelectElement} select
 * @returns {HTMLOptionElement[]} All selected options
 */
const getOptionsForSelect = select => {
    return select.querySelectorAll(':checked');
};

export default class {

    /**
     * Constructor for a new filter.
     *
     * @param {String} filterType The type of filter that this relates to
     * @param {HTMLElement} rootNode The root node for the participants filterset
     * @param {Array} initialValues The initial values for the selector
     */
    constructor(filterType, rootNode, initialValues) {
        this.filterType = filterType;
        this.rootNode = rootNode;

        this.addValueSelector(initialValues);
    }

    /**
     * Perform any tear-down for this filter type.
     */
    tearDown() {
        // eslint-disable-line no-empty-function
    }

    /**
     * Get the placeholder to use when showing the value selector.
     *
     * @return {Promise} Resolving to a String
     */
    get placeholder() {
        return getString('placeholdertypeorselect', 'core_user');
    }

    /**
     * Whether to show suggestions in the autocomplete.
     *
     * @return {Boolean}
     */
    get showSuggestions() {
        return true;
    }

    /**
     * Add the value selector to the filter row.
     *
     * @param {Array} initialValues
     */
    async addValueSelector(initialValues = []) {
        const filterValueNode = this.getFilterValueNode();

        // Copy the data in place.
        filterValueNode.innerHTML = this.getSourceDataForFilter().outerHTML;

        const dataSource = filterValueNode.querySelector('select');

        // If there are any initial values then attempt to apply them.
        initialValues.forEach(filterValue => {
            let selectedOption = dataSource.querySelector(`option[value="${filterValue}"]`);
            if (selectedOption) {
                selectedOption.selected = true;
            } else if (!this.showSuggestions) {
                selectedOption = document.createElement('option');
                selectedOption.value = filterValue;
                selectedOption.innerHTML = filterValue;
                selectedOption.selected = true;

                dataSource.append(selectedOption);
            }
        });

        Autocomplete.enhance(
            // The source select element.
            dataSource,

            // Whether to allow 'tags' (custom entries).
            dataSource.dataset.allowCustom == "1",

            // We do not require AJAX at all as standard.
            null,

            // The string to use as a placeholder.
            await this.placeholder,

            // Disable case sensitivity on searches.
            false,

            // Show suggestions.
            this.showSuggestions,

            // Do not override the 'no suggestions' string.
            null,

            // Close the suggestions if this is not a multi-select.
            !dataSource.multiple,

            // Template overrides.
            {
                items: 'core_user/local/participantsfilter/autocomplete_selection_items',
                layout: 'core_user/local/participantsfilter/autocomplete_layout',
                selection: 'core_user/local/participantsfilter/autocomplete_selection',
            }
        );
    }

    /**
     * Get the root node for this filter.
     *
     * @returns {HTMLElement}
     */
    get filterRoot() {
        return this.rootNode.querySelector(Selectors.filter.byName(this.filterType));
    }

    /**
     * Get the possible data for this filter type.
     *
     * @returns {Array}
     */
    getSourceDataForFilter() {
        const filterDataNode = this.rootNode.querySelector(Selectors.filterset.regions.datasource);

        return filterDataNode.querySelector(Selectors.data.fields.byName(this.filterType));
    }

    /**
     * Get the HTMLElement which contains the value selector.
     *
     * @returns {HTMLElement}
     */
    getFilterValueNode() {
        return this.filterRoot.querySelector(Selectors.filter.regions.values);
    }

    /**
     * Get the name of this filter.
     *
     * @returns {String}
     */
    get name() {
        return this.filterType;
    }

    /**
     * Get the type of join specified.
     *
     * @returns {Number}
     */
    get jointype() {
        return this.filterRoot.querySelector(Selectors.filter.fields.join).value;
    }

    /**
     * Get the list of raw values for this filter type.
     *
     * @returns {Array}
     */
    get rawValues() {
        const filterValueNode = this.getFilterValueNode();
        const filterValueSelect = filterValueNode.querySelector('select');

        return Object.values(getOptionsForSelect(filterValueSelect)).map(option => option.value);
    }

    /**
     * Get the list of values for this filter type.
     *
     * @returns {Array}
     */
    get values() {
        return this.rawValues.map(option => parseInt(option, 10));
    }

    /**
     * Get the composed value for this filter.
     *
     * @returns {Object}
     */
    get filterValue() {
        return {
            name: this.name,
            jointype: this.jointype,
            values: this.values,
        };
    }
}
