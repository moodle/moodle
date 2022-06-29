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
 * Dynamic table selectors.
 *
 * @module     core_table/selectors
 * @copyright  2020 Simey Lameze <simey@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
export default {
    main: {
        region: '[data-region="core_table/dynamic"]',
        fromRegionId: regionId => `[data-region="core_table/dynamic"][data-table-uniqueid="${regionId}"]`,
    },
    table: {
        links: {
            sortableColumn: 'a[data-sortable="1"]',
            hide: 'a[data-action="hide"]',
            show: 'a[data-action="show"]',
        },
    },
    initialsBar: {
        links: {
            firstInitial: '.firstinitial [data-initial]',
            lastInitial: '.lastinitial [data-initial]',
        },
    },
    paginationBar: {
        links: {
            pageItem: '.pagination [data-page-number]'
        }
    },
    showCount: {
        links: {
            toggle: '[data-action="showcount"]',
        },
    },
};
