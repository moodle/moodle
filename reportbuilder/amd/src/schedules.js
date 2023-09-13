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
 * Report builder audiences
 *
 * @module      core_reportbuilder/schedules
 * @copyright   2021 Paul Holden <paulh@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

"use strict";

import {dispatchEvent} from 'core/event_dispatcher';
import 'core/inplace_editable';
import Notification from 'core/notification';
import Pending from 'core/pending';
import {prefetchStrings} from 'core/prefetch';
import {getString} from 'core/str';
import {add as addToast} from 'core/toast';
import * as reportEvents from 'core_reportbuilder/local/events';
import * as reportSelectors from 'core_reportbuilder/local/selectors';
import {createScheduleModal} from 'core_reportbuilder/local/repository/modals';
import {deleteSchedule, sendSchedule, toggleSchedule} from 'core_reportbuilder/local/repository/schedules';

let initialized = false;

/**
 * Initialise schedules tab
 *
 * @param {Number} reportId
 */
export const init = reportId => {
    prefetchStrings('core_reportbuilder', [
        'deleteschedule',
        'deletescheduleconfirm',
        'disableschedule',
        'editscheduledetails',
        'enableschedule',
        'newschedule',
        'schedulecreated',
        'scheduledeleted',
        'schedulesent',
        'scheduleupdated',
        'sendschedule',
        'sendscheduleconfirm',
    ]);

    prefetchStrings('core', [
        'confirm',
        'delete',
    ]);

    if (initialized) {
        // We already added the event listeners (can be called multiple times by mustache template).
        return;
    }

    document.addEventListener('click', event => {

        // Create schedule.
        const scheduleCreate = event.target.closest(reportSelectors.actions.scheduleCreate);
        if (scheduleCreate) {
            event.preventDefault();

            const scheduleModal = createScheduleModal(event.target, getString('newschedule', 'core_reportbuilder'), reportId);
            scheduleModal.addEventListener(scheduleModal.events.FORM_SUBMITTED, () => {
                getString('schedulecreated', 'core_reportbuilder')
                    .then(addToast)
                    .then(() => {
                        const reportElement = document.querySelector(reportSelectors.regions.report);
                        dispatchEvent(reportEvents.tableReload, {}, reportElement);
                        return;
                    })
                    .catch(Notification.exception);
            });

            scheduleModal.show();
        }

        // Toggle schedule.
        const scheduleToggle = event.target.closest(reportSelectors.actions.scheduleToggle);
        if (scheduleToggle) {
            const pendingPromise = new Pending('core_reportbuilder/schedules:toggle');
            const scheduleStateToggle = +!Number(scheduleToggle.dataset.state);

            toggleSchedule(reportId, scheduleToggle.dataset.id, scheduleStateToggle)
                .then(() => {
                    const tableRow = scheduleToggle.closest('tr');
                    tableRow.classList.toggle('text-muted');

                    scheduleToggle.dataset.state = scheduleStateToggle;

                    const stringKey = scheduleStateToggle ? 'disableschedule' : 'enableschedule';
                    return getString(stringKey, 'core_reportbuilder');
                })
                .then(toggleLabel => {
                    const labelContainer = scheduleToggle.parentElement.querySelector(`label[for="${scheduleToggle.id}"] > span`);
                    labelContainer.innerHTML = toggleLabel;
                    return pendingPromise.resolve();
                })
                .catch(Notification.exception);
        }

        // Edit schedule.
        const scheduleEdit = event.target.closest(reportSelectors.actions.scheduleEdit);
        if (scheduleEdit) {
            event.preventDefault();

            // Use triggerElement to return focus to the action menu toggle.
            const triggerElement = scheduleEdit.closest('.dropdown').querySelector('.dropdown-toggle');
            const scheduleModal = createScheduleModal(triggerElement, getString('editscheduledetails', 'core_reportbuilder'),
                reportId, scheduleEdit.dataset.scheduleId);
            scheduleModal.addEventListener(scheduleModal.events.FORM_SUBMITTED, () => {
                getString('scheduleupdated', 'core_reportbuilder')
                    .then(addToast)
                    .then(() => {
                        const reportElement = scheduleEdit.closest(reportSelectors.regions.report);
                        dispatchEvent(reportEvents.tableReload, {}, reportElement);
                        return;
                    })
                    .catch(Notification.exception);
            });

            scheduleModal.show();
        }

        // Send schedule.
        const scheduleSend = event.target.closest(reportSelectors.actions.scheduleSend);
        if (scheduleSend) {
            event.preventDefault();

            // Use triggerElement to return focus to the action menu toggle.
            const triggerElement = scheduleSend.closest('.dropdown').querySelector('.dropdown-toggle');
            Notification.saveCancelPromise(
                getString('sendschedule', 'core_reportbuilder'),
                getString('sendscheduleconfirm', 'core_reportbuilder', scheduleSend.dataset.scheduleName),
                getString('confirm', 'core'),
                {triggerElement}
            ).then(() => {
                const pendingPromise = new Pending('core_reportbuilder/schedules:send');

                return sendSchedule(reportId, scheduleSend.dataset.scheduleId)
                    .then(addToast(getString('schedulesent', 'core_reportbuilder')))
                    .then(() => pendingPromise.resolve())
                    .catch(Notification.exception);
            }).catch(() => {
                return;
            });
        }

        // Delete schedule.
        const scheduleDelete = event.target.closest(reportSelectors.actions.scheduleDelete);
        if (scheduleDelete) {
            event.preventDefault();

            // Use triggerElement to return focus to the action menu toggle.
            const triggerElement = scheduleDelete.closest('.dropdown').querySelector('.dropdown-toggle');
            Notification.saveCancelPromise(
                getString('deleteschedule', 'core_reportbuilder'),
                getString('deletescheduleconfirm', 'core_reportbuilder', scheduleDelete.dataset.scheduleName),
                getString('delete', 'core'),
                {triggerElement}
            ).then(() => {
                const pendingPromise = new Pending('core_reportbuilder/schedules:delete');

                return deleteSchedule(reportId, scheduleDelete.dataset.scheduleId)
                    .then(addToast(getString('scheduledeleted', 'core_reportbuilder')))
                    .then(() => {
                        const reportElement = scheduleDelete.closest(reportSelectors.regions.report);
                        dispatchEvent(reportEvents.tableReload, {preservePagination: true}, reportElement);
                        return pendingPromise.resolve();
                    })
                    .catch(Notification.exception);
            }).catch(() => {
                return;
            });
        }
    });

    initialized = true;
};
