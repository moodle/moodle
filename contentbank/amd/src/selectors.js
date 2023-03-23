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
 * Define all of the selectors we will be using on the contentbank interface.
 *
 * @module     core_contentbank/selectors
 * @copyright  2020 Sara Arjona <sara@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * A small helper function to build queryable data selectors.
 *
 * @method getDataSelector
 * @param {String} name
 * @param {String} value
 * @return {string}
 */
const getDataSelector = (name, value) => {
    return `[data-${name}="${value}"]`;
};

export default {
    regions: {
        cbcontentname: getDataSelector('region', 'cb-content-name'),
        contentbank: getDataSelector('region', 'contentbank'),
        filearea: getDataSelector('region', 'filearea')
    },
    actions: {
        search: getDataSelector('action', 'searchcontent'),
        clearSearch: getDataSelector('action', 'clearsearch'),
        viewgrid: getDataSelector('action', 'viewgrid'),
        viewlist: getDataSelector('action', 'viewlist'),
        sortname: getDataSelector('action', 'sortname'),
        sortuses: getDataSelector('action', 'sortuses'),
        sortdate: getDataSelector('action', 'sortdate'),
        sortsize: getDataSelector('action', 'sortsize'),
        sorttype: getDataSelector('action', 'sorttype'),
        sortauthor: getDataSelector('action', 'sortauthor'),
    },
    elements: {
        listitem: '.cb-listitem',
        heading: '.cb-heading',
        cell: '.cb-column',
        cbnavbarbreadcrumb: '.cb-navbar-breadbrumb',
        cbnavbartotalsearch: '.cb-navbar-totalsearch',
        searchinput: '[data-action="search"]',
        sortbutton: '.cb-btnsort'
    },
};
