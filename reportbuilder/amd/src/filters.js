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
 * Report builder filter management
 *
 * @module      core_reportbuilder/filters
 * @copyright   2021 Paul Holden <paulh@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import {dispatchEvent} from 'core/event_dispatcher';
import {loadFragment} from 'core/fragment';
import Notification from 'core/notification';
import Pending from 'core/pending';
import {get_string as getString} from 'core/str';
import Templates from 'core/templates';
import {add as addToast} from 'core/toast';
import DynamicForm from 'core_form/dynamicform';
import * as reportEvents from 'core_reportbuilder/local/events';
import * as reportSelectors from 'core_reportbuilder/local/selectors';
import {resetFilters} from 'core_reportbuilder/local/repository/filters';

/**
 * Update filter button text to indicate applied filter count
 *
 * @param {Element} reportElement
 * @param {Number} filterCount
 */
const setFilterButtonCount = async(reportElement, filterCount) => {
    const filterButtonLabel = reportElement.querySelector(reportSelectors.regions.filterButtonLabel);

    if (filterCount > 0) {
        filterButtonLabel.textContent = await getString('filtersappliedx', 'core_reportbuilder', filterCount);
    } else {
        filterButtonLabel.textContent = await getString('filters', 'moodle');
    }
};

/**
 * Initialise module for given report
 *
 * @method
 * @param {Number} reportId
 * @param {Number} contextId
 */
export const init = (reportId, contextId) => {
    const reportElement = document.querySelector(reportSelectors.forReport(reportId));
    const filterFormContainer = reportElement.querySelector(reportSelectors.regions.filtersForm);

    // Ensure we only add our listeners once (can be called multiple times by mustache template).
    if (filterFormContainer.dataset.initialized) {
        return;
    }
    filterFormContainer.dataset.initialized = true;

    const filterForm = new DynamicForm(filterFormContainer, '\\core_reportbuilder\\form\\filter');

    // Submit report filters.
    filterForm.addEventListener(filterForm.events.FORM_SUBMITTED, event => {
        event.preventDefault();

        // After the form has been submitted, we should trigger report table reload.
        dispatchEvent(reportEvents.tableReload, {}, reportElement);
        setFilterButtonCount(reportElement, event.detail);

        getString('filtersapplied', 'core_reportbuilder')
            .then(addToast)
            .catch(Notification.exception);
    });

    // Reset report filters.
    filterForm.addEventListener(filterForm.events.NOSUBMIT_BUTTON_PRESSED, event => {
        event.preventDefault();

        const pendingPromise = new Pending('core_reportbuilder/filters:reset');

        resetFilters(reportId)
            .then(() => getString('filtersreset', 'core_reportbuilder'))
            .then(addToast)
            .then(() => loadFragment('core_reportbuilder', 'filters_form', contextId, {
                reportid: reportId,
                parameters: reportElement.dataset.parameter,
            }))
            .then((html, js) => {
                Templates.replaceNodeContents(filterFormContainer, html, js);

                dispatchEvent(reportEvents.tableReload, {}, reportElement);
                setFilterButtonCount(reportElement, 0);

                return pendingPromise.resolve();
            })
            .catch(Notification.exception);
    });

    // Modify "region-main" overflow for big filter forms.
    document.querySelector('#region-main').style.overflowX = "visible";
};
