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
 * Report builder reports list management
 *
 * @module      core_reportbuilder/reports_list
 * @copyright   2021 David Matamoros <davidmc@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

"use strict";

import {dispatchEvent} from 'core/event_dispatcher';
import Notification from 'core/notification';
import Pending from 'core/pending';
import {prefetchStrings} from 'core/prefetch';
import {getString} from 'core/str';
import {add as addToast} from 'core/toast';
import * as reportEvents from 'core_reportbuilder/local/events';
import * as reportSelectors from 'core_reportbuilder/local/selectors';
import {deleteReport} from 'core_reportbuilder/local/repository/reports';
import {createDuplicateReportModal, createReportModal} from 'core_reportbuilder/local/repository/modals';

/**
 * Initialise module
 */
export const init = () => {
    prefetchStrings('core_reportbuilder', [
        'deletereport',
        'deletereportconfirm',
        'duplicatereport',
        'editreportdetails',
        'newreport',
        'reportdeleted',
        'reportupdated',
    ]);

    prefetchStrings('core', [
        'delete',
    ]);

    document.addEventListener('click', event => {
        const reportCreate = event.target.closest(reportSelectors.actions.reportCreate);
        if (reportCreate) {
            event.preventDefault();

            // Redirect user to editing interface for the report after submission.
            const reportModal = createReportModal(event.target, getString('newreport', 'core_reportbuilder'));
            reportModal.addEventListener(reportModal.events.FORM_SUBMITTED, event => {
                window.location.href = event.detail;
            });

            reportModal.show();
        }

        const reportEdit = event.target.closest(reportSelectors.actions.reportEdit);
        if (reportEdit) {
            event.preventDefault();

            // Reload current report page after submission.
            // Use triggerElement to return focus to the action menu toggle.
            const triggerElement = reportEdit.closest('.dropdown').querySelector('.dropdown-toggle');
            const reportModal = createReportModal(triggerElement, getString('editreportdetails', 'core_reportbuilder'),
                reportEdit.dataset.reportId);
            reportModal.addEventListener(reportModal.events.FORM_SUBMITTED, () => {
                const reportElement = event.target.closest(reportSelectors.regions.report);

                getString('reportupdated', 'core_reportbuilder')
                    .then(addToast)
                    .then(() => {
                        dispatchEvent(reportEvents.tableReload, {preservePagination: true}, reportElement);
                        return;
                    })
                    .catch(Notification.exception);
            });

            reportModal.show();
        }

        const reportDuplicate = event.target.closest(reportSelectors.actions.reportDuplicate);
        if (reportDuplicate) {
            event.preventDefault();

            const strDuplicateReport = getString('duplicatereport', 'core_reportbuilder');
            const {reportId, reportName} = reportDuplicate.dataset;

            const reportModal = createDuplicateReportModal(event.target, strDuplicateReport, reportId, reportName);
            reportModal.addEventListener(reportModal.events.FORM_SUBMITTED, event => {
                window.location.href = event.detail;
            });

            reportModal.show();
        }

        const reportDelete = event.target.closest(reportSelectors.actions.reportDelete);
        if (reportDelete) {
            event.preventDefault();

            // Use triggerElement to return focus to the action menu toggle.
            const triggerElement = reportDelete.closest('.dropdown').querySelector('.dropdown-toggle');
            Notification.saveCancelPromise(
                getString('deletereport', 'core_reportbuilder'),
                getString('deletereportconfirm', 'core_reportbuilder', reportDelete.dataset.reportName),
                getString('delete', 'core'),
                {triggerElement}
            ).then(() => {
                const pendingPromise = new Pending('core_reportbuilder/reports:delete');
                const reportElement = event.target.closest(reportSelectors.regions.report);

                return deleteReport(reportDelete.dataset.reportId)
                    .then(() => addToast(getString('reportdeleted', 'core_reportbuilder')))
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
