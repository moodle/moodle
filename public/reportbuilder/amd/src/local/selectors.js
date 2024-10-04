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
        sidebarItem: '[data-region="sidebar-item"]',
        settingsConditions: '[data-region="settings-conditions"]',
        activeConditions: '[data-region="active-conditions"]',
        activeCondition: '[data-region="active-condition"]',
        settingsFilters: '[data-region="settings-filters"]',
        activeFilters: '[data-region="active-filters"]',
        activeFilter: '[data-region="active-filter"]',
        settingsSorting: '[data-region="settings-sorting"]',
        activeColumnSort: '[data-region="active-column-sort"]',
        audiencesContainer: '[data-region="audiences"]',
        audienceFormContainer: '[data-region="audience-form-container"]',
        audienceCard: '[data-region="audience-card"]',
        audienceHeading: '[data-region="audience-heading"]',
        audienceForm: '[data-region="audience-form"]',
        audienceEmptyMessage: '[data-region=no-instances-message]',
        audienceDescription: '[data-region=audience-description]',
        audienceNotSavedLabel: '[data-region=audience-not-saved]',
        settingsCardView: '[data-region="settings-cardview"]',
    },
    actions: {
        reportActionPopup: '[data-action="report-action-popup"]',
        reportCreate: '[data-action="report-create"]',
        reportEdit: '[data-action="report-edit"]',
        reportDelete: '[data-action="report-delete"]',
        reportDuplicate: '[data-action="report-duplicate"]',
        reportAddColumn: '[data-action="report-add-column"]',
        reportRemoveColumn: '[data-action="report-remove-column"]',
        reportAddCondition: '[data-action="report-add-condition"]',
        reportRemoveCondition: '[data-action="report-remove-condition"]',
        reportAddFilter: '[data-action="report-add-filter"]',
        reportRemoveFilter: '[data-action="report-remove-filter"]',
        reportToggleColumnSort: '[data-action="report-toggle-column-sorting"]',
        reportToggleColumnSortDirection: '[data-action="report-toggle-sort-direction"]',
        sidebarSearch: '[data-action="sidebar-search"]',
        toggleEditPreview: '[data-action="toggle-edit-preview"]',
        audienceAdd: '[data-action="add-audience"]',
        audienceEdit: '[data-action="edit-audience"]',
        audienceDelete: '[data-action="delete-audience"]',
        toggleCardView: '[data-action="toggle-card"]',
        scheduleCreate: '[data-action="schedule-create"]',
        scheduleToggle: '[data-action="schedule-toggle"]',
        scheduleEdit: '[data-action="schedule-edit"]',
        scheduleSend: '[data-action="schedule-send"]',
        scheduleDelete: '[data-action="schedule-delete"]',
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
