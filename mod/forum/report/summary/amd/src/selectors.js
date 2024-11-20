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
 * Module containing the selectors for the forum summary report.
 *
 * @module     forumreport_summary/selectors
 * @copyright  2019 Jun Pataleta
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

export default {
    filters: {
        group: {
            checkbox: '[data-region="filter-groups"] input[type="checkbox"]',
            clear: '[data-region="filter-groups"] .filter-clear',
            popover: '#filter-groups-popover',
            save: '[data-region="filter-groups"] .filter-save',
            selectall: '[data-region="filter-groups"] .select-all',
            trigger: '#filter-groups-button',
        },
        date: {
            calendar: '#dateselector-calendar-panel',
            calendariconfrom: '#id_filterdatefrompopover_calendar',
            calendariconto: '#id_filterdatetopopover_calendar',
            popover: '#filter-dates-popover',
            save: '[data-region="filter-dates"] .filter-save',
            trigger: '#filter-dates-button',
        },
        exportlink: {
            link: '#summaryreport #forumreport_summary_table button.export-link'
        }
    }
};
