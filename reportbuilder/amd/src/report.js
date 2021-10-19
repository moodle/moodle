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
 * Report builder report management
 *
 * @module      core_reportbuilder/report
 * @copyright   2021 Paul Holden <paulh@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import * as reportEvents from 'core_reportbuilder/local/events';
import * as reportSelectors from 'core_reportbuilder/local/selectors';
import {setPageNumber, refreshTableContent} from 'core_table/dynamic';
import * as tableSelectors from 'core_table/local/dynamic/selectors';

let initialized = false;

/**
 * Initialise module for given report
 *
 * @method
 */
export const init = () => {

    if (initialized) {
        // We already added the event listeners (can be called multiple times by mustache template).
        return;
    }

    // Listen for the table reload event.
    document.addEventListener(reportEvents.tableReload, async(event) => {
        const reportElement = event.target.closest(reportSelectors.regions.report);
        if (reportElement === null) {
            return;
        }

        const tableRoot = reportElement.querySelector(tableSelectors.main.region);
        const pageNumber = event.detail?.preservePagination ? null : 1;

        await setPageNumber(tableRoot, pageNumber, false)
            .then(refreshTableContent);
    });

    // Listen for trigger popup events.
    document.addEventListener('click', event => {
        const reportActionPopup = event.target.closest(reportSelectors.actions.reportActionPopup);
        if (reportActionPopup === null) {
            return;
        }
        event.preventDefault();
        const popupAction = JSON.parse(reportActionPopup.dataset.popupAction);
        window.openpopup(event, popupAction.jsfunctionargs);
    });

    initialized = true;
};
