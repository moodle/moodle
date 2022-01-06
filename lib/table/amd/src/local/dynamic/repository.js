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
 * A javascript module to handle calendar ajax actions.
 *
 * @module     core_calendar/repository
 * @copyright  2017 Simey Lameze <lameze@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
import {call as fetchMany} from 'core/ajax';

/**
 * Fetch table view.
 *
 * @method fetch
 * @param {String} component The component
 * @param {String} handler The name of the handler
 * @param {String} uniqueid The unique id of the table
 * @param {Object} options The options to use when updating the table
 * @param {Array} options.sortData The list of columns to sort by
 * @param {Number} options.joinType The filterset join type
 * @param {Object} options.filters The filters to apply when searching
 * @param {String} options.firstinitial The first name initial to filter on
 * @param {String} options.lastinitial The last name initial to filter on
 * @param {String} options.pageNumber The page number
 * @param {Number} options.pageSize The page size
 * @param {Object} options.hiddenColumns The columns to hide
 * @param {Bool} resetPreferences
 * @return {Promise} Resolved with requested table view
 */
export const fetch = (component, handler, uniqueid, {
    sortData = [],
    joinType = null,
    filters = {},
    firstinitial = null,
    lastinitial = null,
    pageNumber = null,
    pageSize = null,
    hiddenColumns = {}
} = {}, resetPreferences = false) => fetchMany([{
    methodname: `core_table_get_dynamic_table_content`,
    args: {
        component,
        handler,
        uniqueid,
        sortdata: sortData,
        jointype: joinType,
        filters,
        firstinitial,
        lastinitial,
        pagenumber: pageNumber,
        pagesize: pageSize,
        hiddencolumns: hiddenColumns,
        resetpreferences: resetPreferences
    },
}])[0];
