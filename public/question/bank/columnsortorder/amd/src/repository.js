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
 * External function calls for qbank_columnsortorder
 *
 * @module     qbank_columnsortorder/repository
 * @copyright  2023 Catalyst IT Europe Ltd.
 * @author     Mark Johnson <mark.johnson@catalyst-eu.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import {call as fetchMany} from 'core/ajax';

/**
 * Save the list of hidden columns
 *
 * @param {String[]} columns List of hidden column names
 * @param {Boolean} global Set global config setting, rather than user preference
 * @return {Promise}
 */
export const setHiddenColumns = (columns, global = false) => fetchMany([{
    methodname: 'qbank_columnsortorder_set_hidden_columns',
    args: {
        columns,
        global,
    },
}])[0];

/**
 * Save the order of columns
 *
 * @param {String[]} columns List of column names in the desired order
 * @param {Boolean} global Set global config setting, rather than user preference
 * @return {Promise}
 */
export const setColumnbankOrder = (columns, global = false) => fetchMany([{
    methodname: 'qbank_columnsortorder_set_columnbank_order',
    args: {
        columns,
        global,
    },
}])[0];

/**
 * Save the column widths
 *
 * @param {String} sizes JSON string encoding an array of objects with "column" and "width" properties.
 * @param {Boolean} global Set global config setting, rather than user preference
 * @return {Promise}
 */
export const setColumnSize = (sizes, global = false) => fetchMany([{
    methodname: 'qbank_columnsortorder_set_column_size',
    args: {
        sizes,
        global,
    },
}])[0];

/**
 * Reset all settings.
 *
 * @param {Boolean} global Reset global config settings, rather than user preference
 * @return {Promise}
 */
export const resetColumns = (global = false) => Promise.all(
    fetchMany([
        {
            methodname: 'qbank_columnsortorder_set_column_size',
            args: {
                global,
            },
        },
        {
            methodname: 'qbank_columnsortorder_set_columnbank_order',
            args: {
                global,
            },
        },
        {
            methodname: 'qbank_columnsortorder_set_hidden_columns',
            args: {
                global,
            },
        },
    ])
);
