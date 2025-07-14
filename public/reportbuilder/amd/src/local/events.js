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
 * Report builder events
 *
 * @module      core_reportbuilder/local/events
 * @copyright   2021 Paul Holden <paulh@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Events for the Report builder subsystem
 *
 * @constant
 * @property {String} tableReload See {@link event:tableReload}
 */
export default {
    /**
     * Trigger table reloading
     *
     * @event tableReload
     * @type {CustomEvent}
     * @property {object} detail
     * @property {Boolean} detail.preservePagination Whether current pagination should be preserved (default false)
     * @property {String} detail.preserveTriggerElement Element selector that should be focused after table reload (default null)
     *
     * @example <caption>Triggering table reload</caption>
     * import {dispatchEvent} from 'core/event_dispatcher';
     * import * as reportEvents from 'core_reportbuilder/local/events';
     *
     * dispatchEvent(reportEvents.tableReload, {}, document.querySelector(...));
     */
    tableReload: 'core_reportbuilder_table_reload',
    publish: {
        reportColumnsUpdated: 'core_reportbuilder_report_columns_updated',
    },
};
