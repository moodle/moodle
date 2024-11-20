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
 * Cohorts actions.
 *
 * @module     core_cohort/actions
 * @copyright  2024 David Woloszyn <david.woloszyn@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import {dispatchEvent} from 'core/event_dispatcher';
import Notification from 'core/notification';
import Pending from 'core/pending';
import {prefetchStrings} from 'core/prefetch';
import {getString} from 'core/str';
import {add as addToast} from 'core/toast';
import {deleteCohort, deleteCohorts} from 'core_cohort/repository';
import * as reportEvents from 'core_reportbuilder/local/events';
import * as reportSelectors from 'core_reportbuilder/local/selectors';
import {eventTypes} from 'core/local/inplace_editable/events';

const SELECTORS = {
    CHECKBOXES: '[data-togglegroup="report-select-all"][data-toggle="slave"]:checked',
    DELETE: '[data-action="cohort-delete"]',
    DELETEBUTTON: '[data-action="cohort-delete-selected"]',
    EDITNAME: '[data-itemtype="cohortname"]',
};

/**
 * Initialise module.
 */
export const init = () => {

    prefetchStrings('core_cohort', [
        'delcohortsconfirm',
        'delcohortssuccess',
        'delconfirm',
        'delsuccess',
    ]);

    prefetchStrings('core', [
        'delete',
        'deleteselected',
        'selectitem',
    ]);

    registerEventListeners();
};

/**
 * Register event listeners.
 */
export const registerEventListeners = () => {

    // Edit cohort name inplace.
    document.addEventListener(eventTypes.elementUpdated, async(event) => {
        const editCohortName = event.target.closest(SELECTORS.EDITNAME);
        if (editCohortName) {
            const cohortId = event.target.dataset.itemid;
            const checkbox = document.querySelector(`input[value="${cohortId}"][type="checkbox"]`);
            const label = document.querySelector(`label[for="${checkbox.id}"]`);
            if (label) {
                label.innerHTML = await getString('selectitem', 'core', event.target.dataset.value);
            }
        }
    });

    document.addEventListener('click', event => {

        // Delete single cohort.
        const cohortDeleteSingle = event.target.closest(SELECTORS.DELETE);
        if (cohortDeleteSingle) {
            event.preventDefault();

            const {cohortId, cohortName} = cohortDeleteSingle.dataset;

            Notification.saveCancelPromise(
                getString('deleteselected', 'core'),
                getString('delconfirm', 'core_cohort', cohortName),
                getString('delete', 'core'),
                {triggerElement: cohortDeleteSingle}
            ).then(() => {
                const pendingPromise = new Pending('core_cohort/cohort:delete');
                const reportElement = event.target.closest(reportSelectors.regions.report);

                // eslint-disable-next-line promise/no-nesting
                return deleteCohort(cohortId)
                    .then(() => addToast(getString('delsuccess', 'core_cohort')))
                    .then(() => {
                        dispatchEvent(reportEvents.tableReload, {preservePagination: true}, reportElement);
                        return pendingPromise.resolve();
                    })
                    .catch(Notification.exception);
            }).catch(() => {
                return;
            });
        }

        // Delete multiple cohorts.
        const cohortDeleteMultiple = event.target.closest(SELECTORS.DELETEBUTTON);
        if (cohortDeleteMultiple) {
            event.preventDefault();

            const reportElement = document.querySelector(reportSelectors.regions.report);
            const cohortDeleteChecked = reportElement.querySelectorAll(SELECTORS.CHECKBOXES);
            if (cohortDeleteChecked.length === 0) {
                return;
            }

            Notification.saveCancelPromise(
                getString('deleteselected', 'core'),
                getString('delcohortsconfirm', 'core_cohort'),
                getString('delete', 'core'),
                {triggerElement: cohortDeleteMultiple}
            ).then(() => {
                const pendingPromise = new Pending('core_cohort/cohorts:delete');
                const deleteCohortIds = [...cohortDeleteChecked].map(check => check.value);

                // eslint-disable-next-line promise/no-nesting
                return deleteCohorts(deleteCohortIds)
                    .then(() => addToast(getString('delcohortssuccess', 'core_cohort')))
                    .then(() => {
                        dispatchEvent(reportEvents.tableReload, {preservePagination: true}, reportElement);
                        return pendingPromise.resolve();
                    })
                    .catch(Notification.exception);
            }).catch(() => {
                return;
            });
        }
    });
};
