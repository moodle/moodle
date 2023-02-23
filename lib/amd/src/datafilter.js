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
 * Data filter management.
 *
 * @module     core/datafilter
 * @copyright  2020 Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import CourseFilter from 'core/datafilter/filtertypes/courseid';
import GenericFilter from 'core/datafilter/filtertype';
import {get_strings as getStrings} from 'core/str';
import Notification from 'core/notification';
import Pending from 'core/pending';
import Selectors from 'core/datafilter/selectors';
import Templates from 'core/templates';
import CustomEvents from 'core/custom_interaction_events';
import jQuery from 'jquery';

export default class {

    /**
     * Initialise the filter on the element with the given filterSet and callback.
     *
     * @param {HTMLElement} filterSet The filter element.
     * @param {Function} applyCallback Callback function when updateTableFromFilter
     */
    constructor(filterSet, applyCallback) {

        this.filterSet = filterSet;
        this.applyCallback = applyCallback;
        // Keep a reference to all of the active filters.
        this.activeFilters = {
            courseid: new CourseFilter('courseid', filterSet),
        };
    }

    /**
     * Initialise event listeners to the filter.
     */
    init() {
        // Add listeners for the main actions.
        this.filterSet.querySelector(Selectors.filterset.region).addEventListener('click', e => {
            if (e.target.closest(Selectors.filterset.actions.addRow)) {
                e.preventDefault();

                this.addFilterRow();
            }

            if (e.target.closest(Selectors.filterset.actions.applyFilters)) {
                e.preventDefault();

                this.updateTableFromFilter();
            }

            if (e.target.closest(Selectors.filterset.actions.resetFilters)) {
                e.preventDefault();

                this.removeAllFilters();
            }
        });

        // Add the listener to remove a single filter.
        this.filterSet.querySelector(Selectors.filterset.regions.filterlist).addEventListener('click', e => {
            if (e.target.closest(Selectors.filter.actions.remove)) {
                e.preventDefault();

                this.removeOrReplaceFilterRow(e.target.closest(Selectors.filter.region), true);
            }
        });

        // Add listeners for the filter type selection.
        let filterRegion = jQuery(this.getFilterRegion());
        CustomEvents.define(filterRegion, [CustomEvents.events.accessibleChange]);
        filterRegion.on(CustomEvents.events.accessibleChange, e => {
            const typeField = e.target.closest(Selectors.filter.fields.type);
            if (typeField && typeField.value) {
                const filter = e.target.closest(Selectors.filter.region);

                this.addFilter(filter, typeField.value);
            }
        });

        this.filterSet.querySelector(Selectors.filterset.fields.join).addEventListener('change', e => {
            this.filterSet.dataset.filterverb = e.target.value;
        });
    }

    /**
     * Get the filter list region.
     *
     * @return {HTMLElement}
     */
    getFilterRegion() {
        return this.filterSet.querySelector(Selectors.filterset.regions.filterlist);
    }

    /**
     * Add an unselected filter row.
     *
     * @return {Promise}
     */
    addFilterRow() {
        const pendingPromise = new Pending('core/datafilter:addFilterRow');
        const rownum = 1 + this.getFilterRegion().querySelectorAll(Selectors.filter.region).length;
        return Templates.renderForPromise('core/datafilter/filter_row', {"rownumber": rownum})
            .then(({html, js}) => {
                const newContentNodes = Templates.appendNodeContents(this.getFilterRegion(), html, js);

                return newContentNodes;
            })
            .then(filterRow => {
                // Note: This is a nasty hack.
                // We should try to find a better way of doing this.
                // We do not have the list of types in a readily consumable format, so we take the pre-rendered one and copy
                // it in place.
                const typeList = this.filterSet.querySelector(Selectors.data.typeList);

                filterRow.forEach(contentNode => {
                    const contentTypeList = contentNode.querySelector(Selectors.filter.fields.type);

                    if (contentTypeList) {
                        contentTypeList.innerHTML = typeList.innerHTML;
                    }
                });

                return filterRow;
            })
            .then(filterRow => {
                this.updateFiltersOptions();

                return filterRow;
            })
            .then(result => {
                pendingPromise.resolve();

                return result;
            })
            .catch(Notification.exception);
    }

    /**
     * Get the filter data source node fro the specified filter type.
     *
     * @param {String} filterType
     * @return {HTMLElement}
     */
    getFilterDataSource(filterType) {
        const filterDataNode = this.filterSet.querySelector(Selectors.filterset.regions.datasource);

        return filterDataNode.querySelector(Selectors.data.fields.byName(filterType));
    }

    /**
     * Add a filter to the list of active filters, performing any necessary setup.
     *
     * @param {HTMLElement} filterRow
     * @param {String} filterType
     * @param {Array} initialFilterValues The initially selected values for the filter
     * @returns {Filter}
     */
    async addFilter(filterRow, filterType, initialFilterValues) {
        // Name the filter on the filter row.
        filterRow.dataset.filterType = filterType;

        const filterDataNode = this.getFilterDataSource(filterType);

        // Instantiate the Filter class.
        let Filter = GenericFilter;
        if (filterDataNode.dataset.filterTypeClass) {
            Filter = await import(filterDataNode.dataset.filterTypeClass);
        }
        this.activeFilters[filterType] = new Filter(filterType, this.filterSet, initialFilterValues);

        // Disable the select.
        const typeField = filterRow.querySelector(Selectors.filter.fields.type);
        typeField.value = filterType;
        typeField.disabled = 'disabled';

        // Update the list of available filter types.
        this.updateFiltersOptions();

        return this.activeFilters[filterType];
    }

    /**
     * Get the registered filter class for the named filter.
     *
     * @param {String} name
     * @return {Object} See the Filter class.
     */
    getFilterObject(name) {
        return this.activeFilters[name];
    }

    /**
     * Remove or replace the specified filter row and associated class, ensuring that if there is only one filter row,
     * that it is replaced instead of being removed.
     *
     * @param {HTMLElement} filterRow
     * @param {Bool} refreshContent Whether to refresh the table content when removing
     */
    removeOrReplaceFilterRow(filterRow, refreshContent) {
        const filterCount = this.getFilterRegion().querySelectorAll(Selectors.filter.region).length;
        if (filterCount === 1) {
            this.replaceFilterRow(filterRow, refreshContent);
        } else {
            this.removeFilterRow(filterRow, refreshContent);
        }
    }

    /**
     * Remove the specified filter row and associated class.
     *
     * @param {HTMLElement} filterRow
     * @param {Bool} refreshContent Whether to refresh the table content when removing
     */
    async removeFilterRow(filterRow, refreshContent = true) {
        const filterType = filterRow.querySelector(Selectors.filter.fields.type);
        const hasFilterValue = !!filterType.value;

        // Remove the filter object.
        this.removeFilterObject(filterRow.dataset.filterType);

        // Remove the actual filter HTML.
        filterRow.remove();

        // Update the list of available filter types.
        this.updateFiltersOptions();

        if (hasFilterValue && refreshContent) {
            // Refresh the table if there was any content in this row.
            this.updateTableFromFilter();
        }

        // Update filter fieldset legends.
        const filterLegends = await this.getAvailableFilterLegends();

        this.getFilterRegion().querySelectorAll(Selectors.filter.region).forEach((filterRow, index) => {
            filterRow.querySelector('legend').innerText = filterLegends[index];
        });

    }

    /**
     * Replace the specified filter row with a new one.
     *
     * @param {HTMLElement} filterRow
     * @param {Bool} refreshContent Whether to refresh the table content when removing
     * @param {Number} rowNum The number used to label the filter fieldset legend (eg Row 1). Defaults to 1 (the first filter).
     * @return {Promise}
     */
    replaceFilterRow(filterRow, refreshContent = true, rowNum = 1) {
        // Remove the filter object.
        this.removeFilterObject(filterRow.dataset.filterType);

        return Templates.renderForPromise('core/datafilter/filter_row', {"rownumber": rowNum})
            .then(({html, js}) => {
                const newContentNodes = Templates.replaceNode(filterRow, html, js);

                return newContentNodes;
            })
            .then(filterRow => {
                // Note: This is a nasty hack.
                // We should try to find a better way of doing this.
                // We do not have the list of types in a readily consumable format, so we take the pre-rendered one and copy
                // it in place.
                const typeList = this.filterSet.querySelector(Selectors.data.typeList);

                filterRow.forEach(contentNode => {
                    const contentTypeList = contentNode.querySelector(Selectors.filter.fields.type);

                    if (contentTypeList) {
                        contentTypeList.innerHTML = typeList.innerHTML;
                    }
                });

                return filterRow;
            })
            .then(filterRow => {
                this.updateFiltersOptions();

                return filterRow;
            })
            .then(filterRow => {
                // Refresh the table.
                if (refreshContent) {
                    return this.updateTableFromFilter();
                } else {
                    return filterRow;
                }
            })
            .catch(Notification.exception);
    }

    /**
     * Remove the Filter Object from the register.
     *
     * @param {string} filterName The name of the filter to be removed
     */
    removeFilterObject(filterName) {
        if (filterName) {
            const filter = this.getFilterObject(filterName);
            if (filter) {
                filter.tearDown();

                // Remove from the list of active filters.
                delete this.activeFilters[filterName];
            }
        }
    }

    /**
     * Remove all filters.
     *
     * @returns {Promise}
     */
    removeAllFilters() {
        const filters = this.getFilterRegion().querySelectorAll(Selectors.filter.region);
        filters.forEach(filterRow => this.removeOrReplaceFilterRow(filterRow, false));

        // Refresh the table.
        return this.updateTableFromFilter();
    }

    /**
     * Remove any empty filters.
     */
    removeEmptyFilters() {
        const filters = this.getFilterRegion().querySelectorAll(Selectors.filter.region);
        filters.forEach(filterRow => {
            const filterType = filterRow.querySelector(Selectors.filter.fields.type);
            if (!filterType.value) {
                this.removeOrReplaceFilterRow(filterRow, false);
            }
        });
    }

    /**
     * Update the list of filter types to filter out those already selected.
     */
    updateFiltersOptions() {
        const filters = this.getFilterRegion().querySelectorAll(Selectors.filter.region);
        filters.forEach(filterRow => {
            const options = filterRow.querySelectorAll(Selectors.filter.fields.type + ' option');
            options.forEach(option => {
                if (option.value === filterRow.dataset.filterType) {
                    option.classList.remove('hidden');
                    option.disabled = false;
                } else if (this.activeFilters[option.value]) {
                    option.classList.add('hidden');
                    option.disabled = true;
                } else {
                    option.classList.remove('hidden');
                    option.disabled = false;
                }
            });
        });

        // Configure the state of the "Add row" button.
        // This button is disabled when there is a filter row available for each condition.
        const addRowButton = this.filterSet.querySelector(Selectors.filterset.actions.addRow);
        const filterDataNode = this.filterSet.querySelectorAll(Selectors.data.fields.all);
        if (filterDataNode.length <= filters.length) {
            addRowButton.setAttribute('disabled', 'disabled');
        } else {
            addRowButton.removeAttribute('disabled');
        }

        if (filters.length === 1) {
            this.filterSet.querySelector(Selectors.filterset.regions.filtermatch).classList.add('hidden');
            this.filterSet.querySelector(Selectors.filterset.fields.join).value = 2;
            this.filterSet.dataset.filterverb = 2;
        } else {
            this.filterSet.querySelector(Selectors.filterset.regions.filtermatch).classList.remove('hidden');
        }
    }

    /**
     * Update the Dynamic table based upon the current filter.
     */
    updateTableFromFilter() {
        const pendingPromise = new Pending('core/datafilter:updateTableFromFilter');

        const filters = {};
        Object.values(this.activeFilters).forEach(filter => {
            filters[filter.filterValue.name] = filter.filterValue;
        });

        if (this.applyCallback) {
            this.applyCallback(filters, pendingPromise);
        }
    }

    /**
     * Fetch the strings used to populate the fieldset legends for the maximum number of filters possible.
     *
     * @return {array}
     */
    async getAvailableFilterLegends() {
        const maxFilters = document.querySelector(Selectors.data.typeListSelect).length - 1;
        let requests = [];

        [...Array(maxFilters)].forEach((_, rowIndex) => {
            requests.push({
                "key": "filterrowlegend",
                "component": "core",
                // Add 1 since rows begin at 1 (index begins at zero).
                "param": rowIndex + 1
            });
        });

        const legendStrings = await getStrings(requests)
            .then(fetchedStrings => {
                return fetchedStrings;
            })
            .catch(Notification.exception);

        return legendStrings;
    }

}
