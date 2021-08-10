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
 * @property {String} regions.filtersForm Filters form page region
 */
const SELECTORS = {
    regions: {
        systemReport: '[data-region="core_reportbuilder/system-report"]',
        filtersForm: '[data-region="filters-form"]',
    },
};

/**
 * Selector for given report
 *
 * @method forSystemReport
 * @param {Number} reportId
 * @return {String}
 */
SELECTORS.forSystemReport = reportId => `${SELECTORS.regions.systemReport}[data-reportid="${reportId}"]`;

export default SELECTORS;
