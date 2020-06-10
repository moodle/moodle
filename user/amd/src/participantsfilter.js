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
import {get_strings as getStrings} from 'core/str';
import Notification from 'core/notification';
import Pending from 'core/pending';
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
        const pendingPromise = new Pending('core_user/participantsfilter:addFilterRow');

        const rownum = 1 + getFilterRegion().querySelectorAll(Selectors.filter.region).length;
        return Templates.renderForPromise('core_user/local/participantsfilter/filterrow', {"rownumber": rownum})
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
        .then(result => {
            pendingPromise.resolve();

            return result;
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
     * @param {Array} initialFilterValues The initially selected values for the filter
     * @returns {Filter}
     */
    const addFilter = async(filterRow, filterType, initialFilterValues) => {
        // Name the filter on the filter row.
        filterRow.dataset.filterType = filterType;

        const filterDataNode = getFilterDataSource(filterType);

        // Instantiate the Filter class.
        let Filter = GenericFilter;
        if (filterDataNode.dataset.filterTypeClass) {
            Filter = await import(filterDataNode.dataset.filterTypeClass);
        }
        activeFilters[filterType] = new Filter(filterType, filterSet, initialFilterValues);

        // Disable the select.
        const typeField = filterRow.querySelector(Selectors.filter.fields.type);
        typeField.value = filterType;
        typeField.disabled = 'disabled';

        // Update the list of available filter types.
        updateFiltersOptions();

        return activeFilters[filterType];
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
     * @param {Bool} refreshContent Whether to refresh the table content when removing
     */
    const removeOrReplaceFilterRow = (filterRow, refreshContent) => {
        const filterCount = getFilterRegion().querySelectorAll(Selectors.filter.region).length;

        if (filterCount === 1) {
            replaceFilterRow(filterRow, refreshContent);
        } else {
            removeFilterRow(filterRow, refreshContent);
        }
    };

    /**
     * Remove the specified filter row and associated class.
     *
     * @param {HTMLElement} filterRow
     * @param {Bool} refreshContent Whether to refresh the table content when removing
     */
    const removeFilterRow = async(filterRow, refreshContent = true) => {
        const filterType = filterRow.querySelector(Selectors.filter.fields.type);
        const hasFilterValue = !!filterType.value;

        // Remove the filter object.
        removeFilterObject(filterRow.dataset.filterType);

        // Remove the actual filter HTML.
        filterRow.remove();

        // Update the list of available filter types.
        updateFiltersOptions();

        if (hasFilterValue && refreshContent) {
            // Refresh the table if there was any content in this row.
            updateTableFromFilter();
        }

        // Update filter fieldset legends.
        const filterLegends = await getAvailableFilterLegends();

        getFilterRegion().querySelectorAll(Selectors.filter.region).forEach((filterRow, index) => {
            filterRow.querySelector('legend').innerText = filterLegends[index];
        });

    };

    /**
     * Replace the specified filter row with a new one.
     *
     * @param {HTMLElement} filterRow
     * @param {Bool} refreshContent Whether to refresh the table content when removing
     * @param {Number} rowNum The number used to label the filter fieldset legend (eg Row 1). Defaults to 1 (the first filter).
     * @return {Promise}
     */
    const replaceFilterRow = (filterRow, refreshContent = true, rowNum = 1) => {
        // Remove the filter object.
        removeFilterObject(filterRow.dataset.filterType);

        return Templates.renderForPromise('core_user/local/participantsfilter/filterrow', {"rownumber": rowNum})
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
            if (refreshContent) {
                return updateTableFromFilter();
            } else {
                return filterRow;
            }
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
     *
     * @returns {Promise}
     */
    const removeAllFilters = () => {
        const pendingPromise = new Pending('core_user/participantsfilter:setFilterFromConfig');

        const filters = getFilterRegion().querySelectorAll(Selectors.filter.region);
        filters.forEach(filterRow => removeOrReplaceFilterRow(filterRow, false));

        // Refresh the table.
        return updateTableFromFilter()
        .then(result => {
            pendingPromise.resolve();

            return result;
        });
    };

    /**
     * Remove any empty filters.
     */
    const removeEmptyFilters = () => {
        const filters = getFilterRegion().querySelectorAll(Selectors.filter.region);
        filters.forEach(filterRow => {
            const filterType = filterRow.querySelector(Selectors.filter.fields.type);
            if (!filterType.value) {
                removeOrReplaceFilterRow(filterRow, false);
            }
        });
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
     * Set the current filter options based on a provided configuration.
     *
     * @param {Object} config
     * @param {Number} config.jointype
     * @param {Object} config.filters
     * @returns {Promise}
     */
    const setFilterFromConfig = config => {
        const filterConfig = Object.entries(config.filters);

        if (!filterConfig.length) {
            // There are no filters to set from.
            return Promise.resolve();
        }

        // Set the main join type.
        filterSet.querySelector(Selectors.filterset.fields.join).value = config.jointype;

        const filterPromises = filterConfig.map(([filterType, filterData]) => {
            if (filterType === 'courseid') {
                // The courseid is a special case.
                return false;
            }

            const filterValues = filterData.values;

            if (!filterValues.length) {
                // There are no values for this filter.
                // Skip it.
                return false;
            }

            return addFilterRow().then(([filterRow]) => addFilter(filterRow, filterType, filterValues));
        }).filter(promise => promise);

        if (!filterPromises.length) {
            return Promise.resolve();
        }

        return Promise.all(filterPromises).then(() => {
            return removeEmptyFilters();
        })
        .then(updateFiltersOptions)
        .then(updateTableFromFilter);
    };

    /**
     * Update the Dynamic table based upon the current filter.
     *
     * @return {Promise}
     */
    const updateTableFromFilter = () => {
        const pendingPromise = new Pending('core_user/participantsfilter:updateTableFromFilter');

        const filters = {};
        Object.values(activeFilters).forEach(filter => {
            filters[filter.filterValue.name] = filter.filterValue;
        });

        return DynamicTable.setFilters(
            DynamicTable.getTableFromId(filterSet.dataset.tableRegion),
            {
                jointype: parseInt(filterSet.querySelector(Selectors.filterset.fields.join).value, 10),
                filters,
            }
        )
        .then(result => {
            pendingPromise.resolve();

            return result;
        })
        .catch(Notification.exception);
    };

    /**
     * Fetch the strings used to populate the fieldset legends for the maximum number of filters possible.
     *
     * @return {array}
     */
    const getAvailableFilterLegends = async() => {
        const maxFilters = document.querySelector(Selectors.data.typeListSelect).length - 1;
        let requests = [];

        [...Array(maxFilters)].forEach((_, rowIndex) => {
            requests.push({
                "key": "filterrowlegend",
                "component": "core_user",
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

            removeOrReplaceFilterRow(e.target.closest(Selectors.filter.region), true);
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

    const tableRoot = DynamicTable.getTableFromId(filterSet.dataset.tableRegion);
    const initialFilters = DynamicTable.getFilters(tableRoot);
    if (initialFilters) {
        const initialFilterPromise = new Pending('core_user/participantsfilter:setFilterFromConfig');
        // Apply the initial filter configuration.
        setFilterFromConfig(initialFilters)
        .then(() => initialFilterPromise.resolve())
        .catch();
    }
};
