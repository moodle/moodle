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
 * @class      repository
 * @package    core_calendar
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
 * @param {Object} filters The filters to apply when searching
 * @param {String} firstinitial The first name initial to filter on
 * @param {String} lastinitial The last name initial to filter on
 * @param {String} pageNumber The page number
 * @param {Number} pageSize The page size
 * @param {Number} params parameters to request table
 * @return {Promise} Resolved with requested table view
 */
export const fetch = (component, handler, uniqueid, {
        sortBy = null,
        sortOrder = null,
        joinType = null,
        filters = {},
        firstinitial = null,
        lastinitial = null,
        pageNumber = null,
        pageSize = null,
    } = {}
) => {
    return fetchMany([{
        methodname: `core_table_dynamic_fetch`,
        args: {
            component,
            handler,
            uniqueid,
            sortby: sortBy,
            sortorder: sortOrder,
            jointype: joinType,
            filters,
            firstinitial,
            lastinitial,
            pagenumber: pageNumber,
            pagesize: pageSize,
        },
    }])[0];
};
