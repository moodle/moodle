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
 * Allows to filter the plugin list on plugins overview page
 *
 * @module     core_admin/plugins_overview
 * @copyright  2024 Marina Glancy
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

const SELECTORS = {
    PLUGIN_FILTERS: '#plugins-overview-panel [data-filterby]',
    PLUGIN_ROWS: 'table#plugins-control-panel tbody tr:not(.plugintypeheader)',
    PLUGIN_TYPE_ROWS: 'table#plugins-control-panel tbody tr.plugintypeheader',
};

/**
 * Initialise filters for the "Plugins overview" page
 */
export function init() {
    const filters = document.querySelectorAll(SELECTORS.PLUGIN_FILTERS);
    const pluginRows = document.querySelectorAll(SELECTORS.PLUGIN_ROWS);
    const pluginTypeRows = document.querySelectorAll(SELECTORS.PLUGIN_TYPE_ROWS);

    const filterPlugins = (target) => {
        const filterBy = target.getAttribute('data-filterby');
        const headerVisibility = {};

        // Hide all plugin rows in the plugin table that do not match the filter and show all others.
        for (const row of pluginRows) {
            const type = [...row.classList].find(s => s.startsWith('type-'));
            const visible = filterBy === 'all' ? true : row.classList.contains(filterBy);
            row.style.display = visible ? null : 'none';
            if (visible && type) {
                headerVisibility[type] = true;
            }
        }

        // Hide all the plugin type headers that do not have any visible plugins and show all others.
        for (const row of pluginTypeRows) {
            const type = [...row.classList].find(s => s.startsWith('type-'));
            if (type) {
                const visible = filterBy === 'all' || headerVisibility[type];
                row.style.display = visible ? null : 'none';
            }
        }

        // Toggle 'active' class for the selected filter.
        filters.forEach(el => el.classList.remove('active'));
        target.classList.add('active');
    };

    // Add event listeners for the links changing plugins filters.
    filters
    .forEach(target => target.addEventListener('click', (e) => {
        e.preventDefault();
        window.history.replaceState({}, null, e.target.href);
        filterPlugins(target);
    }));

    // Pre-filter plugins based on the current url anchor.
    if (window.location.hash.length > 1) {
        const anchor = window.location.hash.substring(1);
        const target = [...filters].find(t => t.getAttribute('data-filterby') === anchor);
        if (target) {
            filterPlugins(target);
        }
    }
}
