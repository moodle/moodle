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
 * Filter managing display of subcategories questions.
 *
 * @module     qbank_managecategories/datafilter/filtertypes/categories
 * @author     Mark Johnson <mark.johnson@catalyst-eu.net>
 * @copyright  2023 Catalyst IT Europe Ltd.
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import GenericFilter from 'core/datafilter/filtertype';
import Templates from 'core/templates';
import {getUserPreference, setUserPreference} from 'core_user/repository';
import Notification from 'core/notification';
import {get_strings as getStrings} from 'core/str';

export default class extends GenericFilter {

    SELECTORS = {
        includeSubcategories: 'input[name=category-subcategories]',
        selectInput: 'select#filter-value-category',
        selectInputOption: 'select#filter-value-category option',
        validationInput: 'div[data-filter-type="category"] div.form-autocomplete-input input',
    };

    /**
     * Construct a new categoires filter
     *
     * @param {String} filterType The type of filter that this relates to (categories)
     * @param {HTMLElement} rootNode The root node for the participants filterset
     * @param {Array} initialValues The currently selected category IDs.
     * @param {Object} filterOptions An object containing the additional options for the filter, currently "includesubcategories"
     *     is supported, which if true will display the "Also show questions from subcategories" checkbox as checked.
     */
    constructor(filterType, rootNode, initialValues, filterOptions = {includesubcategories: false}) {
        super(filterType, rootNode, initialValues);
        this.addSubcategoryCheckbox(filterOptions.includesubcategories);
    }

    async addSubcategoryCheckbox(checked = null) {
        const filterValueNode = this.getFilterValueNode();
        if (checked === null || checked === undefined) {
            checked = await getUserPreference('qbank_managecategories_includesubcategories_filter_default');
        } else {
            setUserPreference('qbank_managecategories_includesubcategories_filter_default', checked);
        }
        const {html} = await Templates.renderForPromise('qbank_managecategories/include_subcategories_checkbox', {
            checked: checked && checked !== '0',
        });
        filterValueNode.insertAdjacentHTML('afterend', html);
    }

    get filterOptions() {
        return [
            {name: 'includesubcategories', value: this.filterRoot.querySelector(this.SELECTORS.includeSubcategories).checked}
        ];
    }

    get filterValue() {
        return {
            name: this.name,
            jointype: this.jointype,
            values: this.values,
            filteroptions: this.filterOptions,
        };
    }

    validate() {

        // Get the possible option values and filter out empty ones.
        const nodelist = this.filterRoot.querySelectorAll(this.SELECTORS.selectInputOption);

        // This gets all the values from the autocomplete's hidden select menu and filters out
        // any which are not positive integers.
        const validoptions = Array.from(nodelist).map(
            option => (parseInt(option.value) > 0) ? parseInt(option.value) : false
        ).filter(Boolean);

        // Get the value supplied.
        const value = parseInt(this.filterRoot.querySelector(this.SELECTORS.selectInput).value);

        // Reset validation.
        const node = this.filterRoot.querySelector(this.SELECTORS.validationInput);
        node.setCustomValidity('');

        // Make sure the value supplied is valid.
        if (!validoptions.includes(value)) {
            getStrings([
                {
                    key: 'error:category',
                    component: 'qbank_managecategories',
                },
            ]).then((strings) => {
                node.setCustomValidity(strings[0]);
                node.reportValidity();
                return strings;
            }).catch(Notification.exception);

            return false;
        }

        return true;

    }

}
