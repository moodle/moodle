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
 * Module containing the selectors for user filters.
 *
 * @module     core/datafilter/selectors
 * @copyright  2020 Michael Hawkins <michaelh@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

const getFilterRegion = region => `[data-filterregion="${region}"]`;
const getFilterAction = action => `[data-filteraction="${action}"]`;
const getFilterField = field => `[data-filterfield="${field}"]`;

export default {
    filter: {
        region: getFilterRegion('filter'),
        actions: {
            remove: getFilterAction('remove'),
        },
        fields: {
            join: getFilterField('join'),
            type: getFilterField('type'),
        },
        regions: {
            values: getFilterRegion('value'),
        },
        byName: name => `${getFilterRegion('filter')}[data-filter-type="${name}"]`,
    },
    filterset: {
        region: getFilterRegion('actions'),
        actions: {
            addRow: getFilterAction('add'),
            applyFilters: getFilterAction('apply'),
            resetFilters: getFilterAction('reset'),
        },
        regions: {
            filtermatch: getFilterRegion('filtermatch'),
            filterlist: getFilterRegion('filters'),
            datasource: getFilterRegion('filtertypedata'),
            emptyFilterRow: `${getFilterRegion('filter')}[data-filter-type=""]`,
        },
        fields: {
            join: `${getFilterRegion('filtermatch')} ${getFilterField('join')}`,
        },
    },
    data: {
        fields: {
            byName: name => `[data-field-name="${name}"]`,
            all: `${getFilterRegion('filtertypedata')} [data-field-name]`,
        },
        typeList: getFilterRegion('filtertypelist'),
        typeListSelect: `select${getFilterRegion('filtertypelist')}`,
        required: `${getFilterRegion('value')} > [data-required="1"]`,
    },
};
