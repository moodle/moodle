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
 * Base filter for a date/time selector
 *
 * @module     core/datafilter/filtertypes/datetime
 * @author     Mark Johnson <mark.johnson@catalyst-eu.net>
 * @copyright  2024 Catalyst IT Europe Ltd
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import Filter from 'core/datafilter/filtertype';
import Selectors from 'core/datafilter/selectors';
import Templates from 'core/templates';
import Notification from 'core/notification';
import {get_strings as getStrings} from 'core/str';

const MODES = {
    before: 'before',
    after: 'after',
    between: 'between',
};

export default class extends Filter {

    SELECTORS = {
        afterwrapper: `.${this.filterType}-afterwrapper`,
        beforewrapper: `.${this.filterType}-beforewrapper`,
        betweenwrapper: `.${this.filterType}-betweenwrapper`,
        mode: `[data-filterfield=${this.filterType}-mode]`,
    };

    mode = null;

    constructor(filterType, rootNode, initialValues, filterOptions = {mode: MODES.before}) {
        super(filterType, rootNode, initialValues);
        this.addModeSelector(filterOptions.mode);
    }

    /**
     * Get the context object to be sent through to the mustache template.
     * This can be overridden by any filters which inherit from datetime to add/exclude data.
     *
     * @param {array} initialValues
     * @returns {Promise<{filtertype: *, afterlabel: *, beforelabel: *, required, aftervalue: *, beforevalue: *}>}
     */
    async getContext(initialValues) {
        const sourceDataNode = this.getSourceDataForFilter();
        const defaultBefore = sourceDataNode.getElementsByTagName('option')[0].value;
        const defaultAfter = sourceDataNode.getElementsByTagName('option')[1].value;
        const title = sourceDataNode.getAttribute('data-field-title');
        const labels = await getStrings([
            {
                key: 'datetimefilterafter',
                component: 'core',
                param: {title},
            },
            {
                key: 'datetimefilterbefore',
                component: 'core',
                param: {title},
            },
        ]);
        return {
            filtertype: this.filterType,
            afterlabel: labels[0],
            beforelabel: labels[1],
            required: sourceDataNode.dataset.required,
            aftervalue: initialValues[0] ?? defaultAfter,
            beforevalue: initialValues[1] ?? defaultBefore,
        };
    }

    async addValueSelector(initialValues = []) {
        // We specify a specific filterset in case there are multiple filtering condition - avoiding glitches.
        const specificFilterSet = this.rootNode.querySelector(Selectors.filter.byName(this.filterType));
        const context = await this.getContext(initialValues);
        const datetimeUi = await Templates.renderForPromise('core/datafilter/filtertypes/datetime_selector', context);
        return Templates.replaceNodeContents(
            specificFilterSet.querySelector(Selectors.filter.regions.values),
            datetimeUi.html,
            datetimeUi.js
        );
    }
    async addModeSelector(mode) {
        const modeStrings = await getStrings([
            {key: 'selectdates'},
            {key: 'filterdatebefore', component: 'reportbuilder'},
            {key: 'filterdateafter', component: 'reportbuilder'},
            {key: 'between'}
        ]);
        const context = {
            label: modeStrings[0],
            filtertype: this.filterType,
            modeoptions: [
                {
                    value: MODES.before,
                    label: modeStrings[1],
                    selected: mode === MODES.before ? 'selected' : '',
                },
                {
                    value: MODES.after,
                    label: modeStrings[2],
                    selected: mode === MODES.after ? 'selected' : '',
                },
                {
                    value: MODES.between,
                    label: modeStrings[3],
                    selected: mode === MODES.between ? 'selected' : '',
                },
            ],
        };
        const modeUi = await Templates.renderForPromise('core/datafilter/filtertypes/datetime_mode', context);
        const filterValueNode = this.getFilterValueNode();
        filterValueNode.insertAdjacentHTML('beforebegin', modeUi.html);
        const modeSelect = this.filterRoot.querySelector(this.SELECTORS.mode);
        modeSelect.addEventListener('change', this.updateFieldVisibility.bind(this));
        modeSelect.dispatchEvent(new Event('change')); // Update field visibility based on initial mode.
    }

    updateFieldVisibility(event) {
        const filterValueNode = this.getFilterValueNode();
        const afterWrapper = filterValueNode.querySelector(this.SELECTORS.afterwrapper);
        const beforeWrapper = filterValueNode.querySelector(this.SELECTORS.beforewrapper);
        const betweenWrapper = filterValueNode.querySelector(this.SELECTORS.betweenwrapper);
        const value = event.target.value;
        if (value === MODES.between) {
            betweenWrapper.classList.remove('d-none');
        } else {
            betweenWrapper.classList.add('d-none');
        }
        if (value === MODES.after || value === MODES.between) {
            afterWrapper.classList.remove('d-none');
        } else {
            afterWrapper.classList.add('d-none');
        }
        if (value === MODES.before || value === MODES.between) {
            beforeWrapper.classList.remove('d-none');
        } else {
            beforeWrapper.classList.add('d-none');
        }
    }

    get values() {
        return [
            this.filterRoot.querySelector(`[data-filterfield="${this.name}1"]`).value,
            this.filterRoot.querySelector(`[data-filterfield="${this.name}2"]`).value,
        ];
    }

    get filterOptions() {
        return [
            {name: 'mode', value: this.filterRoot.querySelector(this.SELECTORS.mode).value}
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
        const mode = document.querySelector(this.SELECTORS.mode).value;
        const before = document.querySelector(this.SELECTORS.beforewrapper + ' input');
        const after = document.querySelector(this.SELECTORS.afterwrapper + ' input');
        after.setCustomValidity('');
        if (mode === MODES.between) {
            if (after.value >= before.value) {
                getStrings([
                    {
                        key: 'invaliddatetimebetween',
                        component: 'error',
                        param: {
                            before: before.value,
                            after: after.value,
                        },
                    },
                ]).then((strings) => {
                    after.setCustomValidity(strings[0]);
                    after.reportValidity();
                    return strings;
                }).catch(Notification.exception);
                return false;
            }
        }

        return true;
    }
}