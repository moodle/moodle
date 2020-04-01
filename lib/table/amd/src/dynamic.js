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
 * Module to handle dynamic table features.
 *
 * @module     core_table/dynamic
 * @package    core_table
 * @copyright  2020 Simey Lameze <simey@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
import {fetch as fetchTableData} from 'core_table/local/dynamic/repository';
import * as Selectors from 'core_table/local/dynamic/selectors';

let watching = false;

/**
 * Ensure that a table is a dynamic table.
 *
 * @param {HTMLElement} tableRoot
 * @returns {Bool}
 */
const checkTableIsDynamic = tableRoot => {
    if (!tableRoot) {
        // The table is not a dynamic table.
        throw new Error("The table specified is not a dynamic table and cannot be updated");
    }

    if (!tableRoot.matches(Selectors.table.region)) {
        // The table is not a dynamic table.
        throw new Error("The table specified is not a dynamic table and cannot be updated");
    }

    return true;
};

/**
 * Get the filterset data from a known dynamic table.
 *
 * @param {HTMLElement} tableRoot
 * @returns {Object}
 */
const getFiltersetFromTable = tableRoot => {
    return JSON.parse(tableRoot.dataset.tableFilters);
};

/**
 * Update the specified table based on its current values.
 *
 * @param {HTMLElement} tableRoot
 * @returns {Promise}
 */
export const refreshTableContent = tableRoot => {
    const filterset = getFiltersetFromTable(tableRoot);

    return fetchTableData(
        tableRoot.dataset.tableHandler,
        tableRoot.dataset.tableUniqueid,
        {
            sortBy: tableRoot.dataset.tableSortBy,
            sortOrder: tableRoot.dataset.tableSortOrder,
            joinType: filterset.jointype,
            filters: filterset.filters,
        }
    )
    .then(data => {
        const placeholder = document.createElement('div');
        placeholder.innerHTML = data.html;
        tableRoot.replaceWith(...placeholder.childNodes);

        return data;
    });
};

export const updateTable = (tableRoot, {
    sortBy = null,
    sortOrder = null,
    filters = null,
} = {}, refreshContent = true) => {
    checkTableIsDynamic(tableRoot);

    // Update sort fields.
    if (sortBy && sortOrder) {
        tableRoot.dataset.tableSortBy = sortBy;
        tableRoot.dataset.tableSortOrder = sortOrder;
    }

    // Update filters.
    if (filters) {
        tableRoot.dataset.tableFilters = JSON.stringify(filters);
    }

    // Refresh.
    if (refreshContent) {
        return refreshTableContent(tableRoot);
    } else {
        return Promise.resolve();
    }
};

/**
 * Update the specified table using the new filters.
 *
 * @param {HTMLElement} tableRoot
 * @param {Object} filters
 * @param {Bool} refreshContent
 * @returns {Promise}
 */
export const setFilters = (tableRoot, filters, refreshContent = true) =>
    updateTable(tableRoot, {filters}, refreshContent);

/**
 * Update the sort order.
 *
 * @param {HTMLElement} tableRoot
 * @param {String} sortBy
 * @param {Number} sortOrder
 * @param {Bool} refreshContent
 * @returns {Promise}
 */
export const setSortOrder = (tableRoot, sortBy, sortOrder, refreshContent = true) =>
    updateTable(tableRoot, {sortBy, sortOrder}, refreshContent);

/**
 * Set up listeners to handle table updates.
 */
export const init = () => {
    if (watching) {
        // Already watching.
        return;
    }
    watching = true;

    document.addEventListener('click', e => {
        const tableRoot = e.target.closest(Selectors.table.region);

        if (!tableRoot) {
            return;
        }

        const sortableLink = e.target.closest(Selectors.table.links.sortableColumn);
        if (sortableLink) {
            e.preventDefault();

            setSortOrder(tableRoot, sortableLink.dataset.sortby, sortableLink.dataset.sortorder);
        }
    });
};
