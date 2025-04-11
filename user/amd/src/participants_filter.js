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
 * Participants filter management.
 *
 * @module     core_user/participants_filter
 * @copyright  2021 Tomo Tsuyuki <tomotsuyuki@catalyst-au.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import CoreFilter from 'core/datafilter';
import CourseFilter from 'core/datafilter/filtertypes/courseid';
import * as DynamicTable from 'core_table/dynamic';
import Selectors from 'core/datafilter/selectors';
import Notification from 'core/notification';
import Pending from 'core/pending';

/**
 * Initialise the participants filter on the element with the given id.
 *
 * @param {String} filterRegionId The id for the filter element.
 */
export const init = filterRegionId => {

    const filterSet = document.getElementById(filterRegionId);

    // Create and initialize filter.
    const coreFilter = new CoreFilter(filterSet, function(filters, pendingPromise) {
        DynamicTable.setFilters(
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
    });
    coreFilter.activeFilters.courseid = new CourseFilter('courseid', filterSet);
    coreFilter.init();

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
            return coreFilter.addFilterRow()
                .then(([filterRow]) => {
                    coreFilter.addFilter(filterRow, filterType, filterValues);
                    return;
                });
        }).filter(promise => promise);

        if (!filterPromises.length) {
            return Promise.resolve();
        }

        return Promise.all(filterPromises)
            .then(() => {
                return coreFilter.removeEmptyFilters();
            })
            .then(() => {
                coreFilter.updateFiltersOptions();
                return;
            })
            .then(() => {
                coreFilter.updateTableFromFilter();
                return;
            });
    };

    // Initialize DynamicTable for showing result.
    const tableRoot = DynamicTable.getTableFromId(filterSet.dataset.tableRegion);
    const initialFilters = DynamicTable.getFilters(tableRoot);
    if (initialFilters) {
        const initialFilterPromise = new Pending('core/filter:setFilterFromConfig');
        // Apply the initial filter configuration.
        setFilterFromConfig(initialFilters)
            .then(() => initialFilterPromise.resolve())
            .catch();
    }
};

