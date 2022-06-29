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
 * Javascript for sorting columns in question bank view.
 *
 * @copyright  2021 Catalyst IT Australia Pty Ltd
 * @author     Ghaly Marc-Alexandre <marc-alexandreghaly@catalyst-ca.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import {call as fetchMany} from 'core/ajax';
import {exception as displayException} from 'core/notification';
import SortableList from 'core/sortable_list';
import jQuery from 'jquery';

/**
 * Sets up sortable list in the column sort order page.
 * @param {Element} listRoot
 */
const setupSortableLists = (listRoot) => {
    new SortableList('.list', {
        moveHandlerSelector: '.item',
    });

    jQuery('.item').on(SortableList.EVENTS.DROP, () => {
        const columns = getColumnOrder(listRoot);
        setOrder(columns).catch(displayException);
        listRoot.querySelectorAll('.item').forEach(item => item.classList.remove('active'));
    });

    jQuery('.item').on(SortableList.EVENTS.DRAGSTART, (event) => {
        event.currentTarget.classList.add('active');
    });
};

/**
 * Call external function set_order - inserts the updated column in the config_plugins table.
 *
 * @param {String} columns String that contains column order.
 * @returns {Promise}
 */
const setOrder = columns => fetchMany([{
    methodname: 'qbank_columnsortorder_set_columnbank_order',
    args: {columns},
}])[0];

/**
 * Gets the newly reordered columns to display in the question bank view.
 * @param {Element} listRoot
 * @returns {Array}
 */
const getColumnOrder = listRoot => {
    const columns = Array.from(listRoot.querySelectorAll('[data-pluginname]'))
        .map(column => column.dataset.pluginname);

    return columns.filter((value, index) => columns.indexOf(value) === index);
};

/**
 * Initialize module
 * @param {String} id unique id for columns.
 */
export const init = id => {
    const listRoot = document.querySelector(`#${id}`);
    setupSortableLists(listRoot);
};
