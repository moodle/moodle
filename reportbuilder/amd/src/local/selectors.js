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
 * Report builder selectors
 *
 * @module      core_reportbuilder/local/selectors
 * @copyright   2021 Paul Holden <paulh@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Selectors for the Report builder subsystem
 *
 * @property {Object} regions
 * @property {String} regions.systemReport System report page region
 * @property {String} regions.filterButtonLabel Filters form toggle region
 * @property {String} regions.filtersForm Filters form page region
 */
const SELECTORS = {
    regions: {
        report: '[data-region="core_reportbuilder/report"]',
        reportTable: '[data-region="reportbuilder-table"]',
        columnHeader: '[data-region="column-header"]',
        filterButtonLabel: '[data-region="filter-button-label"]',
        filtersForm: '[data-region="filters-form"]',
        sidebarMenu: '[data-region="sidebar-menu"]',
        sidebarCard: '[data-region="sidebar-card"]',
    },
    actions: {
        reportActionPopup: '[data-action="report-action-popup"]',
        reportCreate: '[data-action="report-create"]',
        reportEdit: '[data-action="report-edit"]',
        reportDelete: '[data-action="report-delete"]',
        reportAddColumn: '[data-action="report-add-column"]',
        reportRemoveColumn: '[data-action="report-remove-column"]',
        sidebarSearch: '[data-action="sidebar-search"]',
    },
};

/**
 * Selector for given report
 *
 * @method forReport
 * @param {Number} reportId
 * @return {String}
 */
SELECTORS.forReport = reportId => `${SELECTORS.regions.report}[data-report-id="${reportId}"]`;

export default SELECTORS;
