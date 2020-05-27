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
 * Participants filter managemnet.
 *
 * @module     core_user/participants_filter
 * @package    core_user
 * @copyright  2020 Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import CourseFilter from './local/participantsfilter/filtertypes/courseid';
import * as DynamicTable from 'core_table/dynamic';
import GenericFilter from './local/participantsfilter/filter';
import Notification from 'core/notification';
import Selectors from './local/participantsfilter/selectors';
import Templates from 'core/templates';

/**
 * Initialise the participants filter on the element with the given id.
 *
 * @param {String} participantsRegionId
 */
export const init = participantsRegionId => {
    // Keep a reference to the filterset.
    const filterSet = document.querySelector(`#${participantsRegionId}`);

    // Keep a reference to all of the active filters.
    const activeFilters = {
        courseid: new CourseFilter('courseid', filterSet),
    };

    /**
     * Get the filter list region.
     *
     * @return {HTMLElement}
     */
    const getFilterRegion = () => filterSet.querySelector(Selectors.filterset.regions.filterlist);

    /**
     * Add an unselected filter row.
     *
     * @return {Promise}
     */
    const addFilterRow = () => {
        return Templates.renderForPromise('core_user/local/participantsfilter/filterrow', {})
        .then(({html, js}) => {
            const newContentNodes = Templates.appendNodeContents(getFilterRegion(), html, js);

            return newContentNodes;
        })
        .then(filterRow => {
            // Note: This is a nasty hack.
            // We should try to find a better way of doing this.
            // We do not have the list of types in a readily consumable format, so we take the pre-rendered one and copy
            // it in place.
            const typeList = filterSet.querySelector(Selectors.data.typeList);

            filterRow.forEach(contentNode => {
                const contentTypeList = contentNode.querySelector(Selectors.filter.fields.type);

                if (contentTypeList) {
                    contentTypeList.innerHTML = typeList.innerHTML;
                }
            });

            return filterRow;
        })
        .then(filterRow => {
            updateFiltersOptions();

            return filterRow;
        })
        .catch(Notification.exception);
    };

    /**
     * Get the filter data source node fro the specified filter type.
     *
     * @param {String} filterType
     * @return {HTMLElement}
     */
    const getFilterDataSource = filterType => {
        const filterDataNode = filterSet.querySelector(Selectors.filterset.regions.datasource);

        return filterDataNode.querySelector(Selectors.data.fields.byName(filterType));
    };

    /**
     * Add a filter to the list of active filters, performing any necessary setup.
     *
     * @param {HTMLElement} filterRow
     * @param {String} filterType
     */
    const addFilter = async(filterRow, filterType) => {
        // Name the filter on the filter row.
        filterRow.dataset.filterType = filterType;

        const filterDataNode = getFilterDataSource(filterType);

        // Instantiate the Filter class.
        let Filter = GenericFilter;
        if (filterDataNode.dataset.filterTypeClass) {
            Filter = await import(filterDataNode.dataset.filterTypeClass);
        }
        activeFilters[filterType] = new Filter(filterType, filterSet);

        // Disable the select.
        const typeField = filterRow.querySelector(Selectors.filter.fields.type);
        typeField.disabled = 'disabled';

        // Update the list of available filter types.
        updateFiltersOptions();
    };

    /**
     * Get the registered filter class for the named filter.
     *
     * @param {String} name
     * @return {Object} See the Filter class.
     */
    const getFilterObject = name => {
        return activeFilters[name];
    };

    /**
     * Remove or replace the specified filter row and associated class, ensuring that if there is only one filter row,
     * that it is replaced instead of being removed.
     *
     * @param {HTMLElement} filterRow
     */
    const removeOrReplaceFilterRow = filterRow => {
        const filterCount = getFilterRegion().querySelectorAll(Selectors.filter.region).length;

        if (filterCount === 1) {
            replaceFilterRow(filterRow);
        } else {
            removeFilterRow(filterRow);
        }
    };

    /**
     * Remove the specified filter row and associated class.
     *
     * @param {HTMLElement} filterRow
     */
    const removeFilterRow = filterRow => {
        // Remove the filter object.
        removeFilterObject(filterRow.dataset.filterType);

        // Remove the actual filter HTML.
        filterRow.remove();

        // Refresh the table.
        updateTableFromFilter();

        // Update the list of available filter types.
        updateFiltersOptions();
    };

    /**
     * Replace the specified filter row with a new one.
     *
     * @param {HTMLElement} filterRow
     * @return {Promise}
     */
    const replaceFilterRow = filterRow => {
        // Remove the filter object.
        removeFilterObject(filterRow.dataset.filterType);

        return Templates.renderForPromise('core_user/local/participantsfilter/filterrow', {})
        .then(({html, js}) => {
            const newContentNodes = Templates.replaceNode(filterRow, html, js);

            return newContentNodes;
        })
        .then(filterRow => {
            // Note: This is a nasty hack.
            // We should try to find a better way of doing this.
            // We do not have the list of types in a readily consumable format, so we take the pre-rendered one and copy
            // it in place.
            const typeList = filterSet.querySelector(Selectors.data.typeList);

            filterRow.forEach(contentNode => {
                const contentTypeList = contentNode.querySelector(Selectors.filter.fields.type);

                if (contentTypeList) {
                    contentTypeList.innerHTML = typeList.innerHTML;
                }
            });

            return filterRow;
        })
        .then(filterRow => {
            updateFiltersOptions();

            return filterRow;
        })
        .then(filterRow => {
            // Refresh the table.
            updateTableFromFilter();

            return filterRow;
        })
        .catch(Notification.exception);
    };

    /**
     * Remove the Filter Object from the register.
     *
     * @param {string} filterName The name of the filter to be removed
     */
    const removeFilterObject = filterName => {
        if (filterName) {
            const filter = getFilterObject(filterName);
            if (filter) {
                filter.tearDown();

                // Remove from the list of active filters.
                delete activeFilters[filterName];
            }
        }
    };

    /**
     * Remove all filters.
     */
    const removeAllFilters = async() => {
        const filters = getFilterRegion().querySelectorAll(Selectors.filter.region);
        filters.forEach((filterRow) => {
            removeOrReplaceFilterRow(filterRow);
        });

        // Refresh the table.
        updateTableFromFilter();
    };

    /**
     * Update the list of filter types to filter out those already selected.
     */
    const updateFiltersOptions = () => {
        const filters = getFilterRegion().querySelectorAll(Selectors.filter.region);
        filters.forEach(filterRow => {
            const options = filterRow.querySelectorAll(Selectors.filter.fields.type + ' option');
            options.forEach(option => {
                if (option.value === filterRow.dataset.filterType) {
                    option.classList.remove('hidden');
                    option.disabled = false;
                } else if (activeFilters[option.value]) {
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
        const addRowButton = filterSet.querySelector(Selectors.filterset.actions.addRow);
        const filterDataNode = filterSet.querySelectorAll(Selectors.data.fields.all);
        if (filterDataNode.length <= filters.length) {
            addRowButton.setAttribute('disabled', 'disabled');
        } else {
            addRowButton.removeAttribute('disabled');
        }

        if (filters.length === 1) {
            filterSet.querySelector(Selectors.filterset.regions.filtermatch).classList.add('hidden');
            filterSet.querySelector(Selectors.filterset.fields.join).value = 1;
        } else {
            filterSet.querySelector(Selectors.filterset.regions.filtermatch).classList.remove('hidden');
        }
    };

    /**
     * Update the Dynamic table based upon the current filter.
     *
     * @return {Promise}
     */
    const updateTableFromFilter = () => {
        return DynamicTable.setFilters(
            DynamicTable.getTableFromId(filterSet.dataset.tableRegion),
            {
                filters: Object.values(activeFilters).map(filter => filter.filterValue),
                jointype: filterSet.querySelector(Selectors.filterset.fields.join).value,
            }
        );
    };

    // Add listeners for the main actions.
    filterSet.querySelector(Selectors.filterset.region).addEventListener('click', e => {
        if (e.target.closest(Selectors.filterset.actions.addRow)) {
            e.preventDefault();

            addFilterRow();
        }

        if (e.target.closest(Selectors.filterset.actions.applyFilters)) {
            e.preventDefault();

            updateTableFromFilter();
        }

        if (e.target.closest(Selectors.filterset.actions.resetFilters)) {
            e.preventDefault();

            removeAllFilters();
        }
    });

    // Add the listener to remove a single filter.
    filterSet.querySelector(Selectors.filterset.regions.filterlist).addEventListener('click', e => {
        if (e.target.closest(Selectors.filter.actions.remove)) {
            e.preventDefault();

            removeOrReplaceFilterRow(e.target.closest(Selectors.filter.region));
        }
    });

    // Add listeners for the filter type selection.
    filterSet.querySelector(Selectors.filterset.regions.filterlist).addEventListener('change', e => {
        const typeField = e.target.closest(Selectors.filter.fields.type);
        if (typeField && typeField.value) {
            const filter = e.target.closest(Selectors.filter.region);

            addFilter(filter, typeField.value);
        }
    });

    filterSet.querySelector(Selectors.filterset.fields.join).addEventListener('change', e => {
        filterSet.dataset.filterverb = e.target.value;
    });
};
