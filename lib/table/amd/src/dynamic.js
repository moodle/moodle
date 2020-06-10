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
import * as Selectors from 'core_table/local/dynamic/selectors';
import Events from './local/dynamic/events';
import Pending from 'core/pending';
import {addIconToContainer} from 'core/loadingicon';
import {fetch as fetchTableData} from 'core_table/local/dynamic/repository';
import Notification from 'core/notification';

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

    if (!tableRoot.matches(Selectors.main.region)) {
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
 * @param {Bool} resetContent
 * @returns {Promise}
 */
export const refreshTableContent = (tableRoot, resetContent = false) => {
    const filterset = getFiltersetFromTable(tableRoot);
    addIconToContainer(tableRoot);

    const pendingPromise = new Pending('core_table/dynamic:refreshTableContent');

    return fetchTableData(
        tableRoot.dataset.tableComponent,
        tableRoot.dataset.tableHandler,
        tableRoot.dataset.tableUniqueid,
        {
            sortData: JSON.parse(tableRoot.dataset.tableSortData),
            joinType: filterset.jointype,
            filters: filterset.filters,
            firstinitial: tableRoot.dataset.tableFirstInitial,
            lastinitial: tableRoot.dataset.tableLastInitial,
            pageNumber: tableRoot.dataset.tablePageNumber,
            pageSize: tableRoot.dataset.tablePageSize,
            hiddenColumns: JSON.parse(tableRoot.dataset.tableHiddenColumns),
        },
        resetContent,
    )
    .then(data => {
        const placeholder = document.createElement('div');
        placeholder.innerHTML = data.html;
        tableRoot.replaceWith(...placeholder.childNodes);

        // Update the tableRoot.
        return getTableFromId(tableRoot.dataset.tableUniqueid);
    }).then(tableRoot => {
        tableRoot.dispatchEvent(new CustomEvent(Events.tableContentRefreshed, {
            bubbles: true,
        }));

        return tableRoot;
    })
    .then(tableRoot => {
        pendingPromise.resolve();

        return tableRoot;
    });
};

export const updateTable = (tableRoot, {
    sortBy = null,
    sortOrder = null,
    filters = null,
    firstInitial = null,
    lastInitial = null,
    pageNumber = null,
    pageSize = null,
    hiddenColumns = null,
} = {}, refreshContent = true) => {
    checkTableIsDynamic(tableRoot);

    const pendingPromise = new Pending('core_table/dynamic:updateTable');
    let tableConfigChanged = false;

    // Update sort fields.
    if (sortBy && sortOrder) {
        // Always update the table if requested and there were sort fields.
        // These fields are only ever normalised in the backend.
        tableConfigChanged = true;

        const sortData = JSON.parse(tableRoot.dataset.tableSortData);
        sortData.unshift({
            sortby: sortBy,
            sortorder: parseInt(sortOrder, 10),
        });
        tableRoot.dataset.tableSortData = JSON.stringify(sortData);
    }

    // Update initials.
    if (firstInitial !== null) {
        if (tableRoot.dataset.tableFirstInitial !== firstInitial) {
            tableConfigChanged = true;
        }

        tableRoot.dataset.tableFirstInitial = firstInitial;
    }

    if (lastInitial !== null) {
        if (tableRoot.dataset.tableLastInitial !== lastInitial) {
            tableConfigChanged = true;
        }

        tableRoot.dataset.tableLastInitial = lastInitial;
    }

    if (pageNumber !== null) {
        if (tableRoot.dataset.tablePageNumber != pageNumber) {
            tableConfigChanged = true;
        }

        tableRoot.dataset.tablePageNumber = pageNumber;
    }

    if (pageSize !== null) {
        if (tableRoot.dataset.tablePageSize != pageSize) {
            tableConfigChanged = true;
        }

        tableRoot.dataset.tablePageSize = pageSize;
    }

    // Update filters.
    if (filters) {
        const filterJson = JSON.stringify(filters);

        if (tableRoot.dataset.tableFilters !== filterJson) {
            tableConfigChanged = true;
        }

        tableRoot.dataset.tableFilters = filterJson;
    }

    // Update hidden columns.
    if (hiddenColumns) {
        const columnJson = JSON.stringify(hiddenColumns);

        if (tableRoot.dataset.tableHiddenColumns !== columnJson) {
            tableConfigChanged = true;
        }

        tableRoot.dataset.tableHiddenColumns = columnJson;
    }

    // Refresh.
    if (refreshContent && tableConfigChanged) {
        return refreshTableContent(tableRoot)
        .then(tableRoot => {
            pendingPromise.resolve();
            return tableRoot;
        });
    } else {
        pendingPromise.resolve();
        return Promise.resolve(tableRoot);
    }
};

/**
 * Get the table dataset for the specified tableRoot, ensuring that the provided table is a dynamic table.
 *
 * @param {HTMLElement} tableRoot
 * @returns {DOMStringMap}
 */
const getTableData = tableRoot => {
    checkTableIsDynamic(tableRoot);

    return tableRoot.dataset;
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
 * Get the filter data for the specified table.
 *
 * @param {HTMLElement} tableRoot
 * @returns {Object}
 */
export const getFilters = tableRoot => {
    checkTableIsDynamic(tableRoot);

    return getFiltersetFromTable(tableRoot);
};

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
 * Set the page number.
 *
 * @param {HTMLElement} tableRoot
 * @param {String} pageNumber
 * @param {Bool} refreshContent
 * @returns {Promise}
 */
export const setPageNumber = (tableRoot, pageNumber, refreshContent = true) =>
    updateTable(tableRoot, {pageNumber}, refreshContent);

/**
 * Get the current page number.
 *
 * @param {HTMLElement} tableRoot
 * @returns {Number}
 */
export const getPageNumber = tableRoot => getTableData(tableRoot).tablePageNumber;

/**
 * Set the page size.
 *
 * @param {HTMLElement} tableRoot
 * @param {Number} pageSize
 * @param {Bool} refreshContent
 * @returns {Promise}
 */
export const setPageSize = (tableRoot, pageSize, refreshContent = true) =>
    updateTable(tableRoot, {pageSize, pageNumber: 1}, refreshContent);

/**
 * Get the current page size.
 *
 * @param {HTMLElement} tableRoot
 * @returns {Number}
 */
export const getPageSize = tableRoot => getTableData(tableRoot).tablePageSize;

/**
 * Update the first initial to show.
 *
 * @param {HTMLElement} tableRoot
 * @param {String} firstInitial
 * @param {Bool} refreshContent
 * @returns {Promise}
 */
export const setFirstInitial = (tableRoot, firstInitial, refreshContent = true) =>
    updateTable(tableRoot, {firstInitial}, refreshContent);

/**
 * Get the current first initial filter.
 *
 * @param {HTMLElement} tableRoot
 * @returns {String}
 */
export const getFirstInitial = tableRoot => getTableData(tableRoot).tableFirstInitial;

/**
 * Update the last initial to show.
 *
 * @param {HTMLElement} tableRoot
 * @param {String} lastInitial
 * @param {Bool} refreshContent
 * @returns {Promise}
 */
export const setLastInitial = (tableRoot, lastInitial, refreshContent = true) =>
    updateTable(tableRoot, {lastInitial}, refreshContent);

/**
 * Get the current last initial filter.
 *
 * @param {HTMLElement} tableRoot
 * @returns {String}
 */
export const getLastInitial = tableRoot => getTableData(tableRoot).tableLastInitial;

/**
 * Hide a column in the participants table.
 *
 * @param {HTMLElement} tableRoot
 * @param {String} columnToHide
 * @param {Bool} refreshContent
 */
export const hideColumn = (tableRoot, columnToHide, refreshContent = true) => {
    const hiddenColumns = JSON.parse(tableRoot.dataset.tableHiddenColumns);
    hiddenColumns.push(columnToHide);

    updateTable(tableRoot, {hiddenColumns}, refreshContent);
};

/**
 * Make a hidden column visible in the participants table.
 *
 * @param {HTMLElement} tableRoot
 * @param {String} columnToShow
 * @param {Bool} refreshContent
 */
export const showColumn = (tableRoot, columnToShow, refreshContent = true) => {
    let hiddenColumns = JSON.parse(tableRoot.dataset.tableHiddenColumns);
    hiddenColumns = hiddenColumns.filter(columnName => columnName !== columnToShow);

    updateTable(tableRoot, {hiddenColumns}, refreshContent);
};

/**
 * Reset table preferences.
 *
 * @param {HTMLElement} tableRoot
 * @returns {Promise}
 */
const resetTablePreferences = tableRoot => refreshTableContent(tableRoot, true);

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
        const tableRoot = e.target.closest(Selectors.main.region);

        if (!tableRoot) {
            return;
        }

        const sortableLink = e.target.closest(Selectors.table.links.sortableColumn);
        if (sortableLink) {
            e.preventDefault();

            setSortOrder(tableRoot, sortableLink.dataset.sortby, sortableLink.dataset.sortorder)
            .catch(Notification.exception);
        }

        const firstInitialLink = e.target.closest(Selectors.initialsBar.links.firstInitial);
        if (firstInitialLink !== null) {
            e.preventDefault();

            setFirstInitial(tableRoot, firstInitialLink.dataset.initial).catch(Notification.exception);
        }

        const lastInitialLink = e.target.closest(Selectors.initialsBar.links.lastInitial);
        if (lastInitialLink !== null) {
            e.preventDefault();

            setLastInitial(tableRoot, lastInitialLink.dataset.initial).catch(Notification.exception);
        }

        const pageItem = e.target.closest(Selectors.paginationBar.links.pageItem);
        if (pageItem) {
            e.preventDefault();

            setPageNumber(tableRoot, pageItem.dataset.pageNumber).catch(Notification.exception);
        }

        const hide = e.target.closest(Selectors.table.links.hide);
        if (hide) {
            e.preventDefault();

            hideColumn(tableRoot, hide.dataset.column).catch(Notification.exception);
        }

        const show = e.target.closest(Selectors.table.links.show);
        if (show) {
            e.preventDefault();

            showColumn(tableRoot, show.dataset.column).catch(Notification.exception);
        }

        const resetTablePreferencesLink = e.target.closest('.resettable a');
        if (resetTablePreferencesLink) {
            e.preventDefault();

            resetTablePreferences(tableRoot).catch(Notification.exception);
        }
    });
};

/**
 * Fetch the table via its table region id.
 *
 * @param {String} tableRegionId
 * @returns {HTMLElement}
 */
export const getTableFromId = tableRegionId => {
    const tableRoot = document.querySelector(Selectors.main.fromRegionId(tableRegionId));


    if (!tableRoot) {
        // The table is not a dynamic table.
        throw new Error("The table specified is not a dynamic table and cannot be updated");
    }

    return tableRoot;
};

export {
    Events
};
