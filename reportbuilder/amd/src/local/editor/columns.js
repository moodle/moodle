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
 * Report builder columns editor
 *
 * @module      core_reportbuilder/local/editor/columns
 * @copyright   2021 Paul Holden <paulh@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

"use strict";

import {dispatchEvent} from 'core/event_dispatcher';
import 'core/inplace_editable';
import {eventTypes as inplaceEditableEvents} from 'core/local/inplace_editable/events';
import Notification from 'core/notification';
import Pending from 'core/pending';
import {prefetchStrings} from 'core/prefetch';
import {publish} from 'core/pubsub';
import SortableList from 'core/sortable_list';
import {getString} from 'core/str';
import {add as addToast} from 'core/toast';
import * as reportEvents from 'core_reportbuilder/local/events';
import * as reportSelectors from 'core_reportbuilder/local/selectors';
import {addColumn, deleteColumn, reorderColumn} from 'core_reportbuilder/local/repository/columns';
import {getColumnSorting} from 'core_reportbuilder/local/repository/sorting';

/**
 * Initialise module, prefetch all required strings
 *
 * @param {Boolean} initialized Ensure we only add our listeners once
 */
export const init = initialized => {
    prefetchStrings('core_reportbuilder', [
        'columnadded',
        'columnaggregated',
        'columndeleted',
        'columnmoved',
        'deletecolumn',
        'deletecolumnconfirm',
    ]);

    prefetchStrings('core', [
        'delete',
    ]);

    if (initialized) {
        return;
    }

    document.addEventListener('click', event => {

        // Add column to report.
        const reportAddColumn = event.target.closest(reportSelectors.actions.reportAddColumn);
        if (reportAddColumn) {
            event.preventDefault();

            const pendingPromise = new Pending('core_reportbuilder/columns:add');
            const reportElement = reportAddColumn.closest(reportSelectors.regions.report);

            addColumn(reportElement.dataset.reportId, reportAddColumn.dataset.uniqueIdentifier)
                .then(data => publish(reportEvents.publish.reportColumnsUpdated, data))
                .then(() => getString('columnadded', 'core_reportbuilder', reportAddColumn.dataset.name))
                .then(addToast)
                .then(() => {
                    dispatchEvent(reportEvents.tableReload, {preservePagination: true}, reportElement);
                    return pendingPromise.resolve();
                })
                .catch(Notification.exception);
        }

        // Remove column from report.
        const reportRemoveColumn = event.target.closest(reportSelectors.actions.reportRemoveColumn);
        if (reportRemoveColumn) {
            event.preventDefault();

            const reportElement = reportRemoveColumn.closest(reportSelectors.regions.report);
            const columnHeader = reportRemoveColumn.closest(reportSelectors.regions.columnHeader);
            const columnName = columnHeader.dataset.columnName;

            Notification.saveCancelPromise(
                getString('deletecolumn', 'core_reportbuilder', columnName),
                getString('deletecolumnconfirm', 'core_reportbuilder', columnName),
                getString('delete', 'core'),
                {triggerElement: reportRemoveColumn}
            ).then(() => {
                const pendingPromise = new Pending('core_reportbuilder/columns:remove');

                return deleteColumn(reportElement.dataset.reportId, columnHeader.dataset.columnId)
                    .then(data => publish(reportEvents.publish.reportColumnsUpdated, data))
                    .then(() => addToast(getString('columndeleted', 'core_reportbuilder', columnName)))
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

    // Initialize sortable list to handle column moving.
    const columnHeadingSelector = `${reportSelectors.regions.reportTable} thead tr`;
    const columnHeadingSortableList = new SortableList(columnHeadingSelector, {isHorizontal: true});
    columnHeadingSortableList.getElementName = element => Promise.resolve(element.data('columnName'));

    document.addEventListener(SortableList.EVENTS.elementDrag, event => {
        const reportOrderColumn = event.target.closest(`${columnHeadingSelector} ${reportSelectors.regions.columnHeader}`);
        if (reportOrderColumn) {
            const reportElement = event.target.closest(reportSelectors.regions.report);
            const {columnPosition} = reportOrderColumn.dataset;

            // Select target position, shift table columns to match.
            const targetColumnPosition = event.detail.targetNextElement.data('columnPosition');

            const reportTableRows = reportElement.querySelectorAll(`${reportSelectors.regions.reportTable} tbody tr`);
            reportTableRows.forEach(reportTableRow => {
                const reportTableRowCell = reportTableRow.querySelector(`td.c${columnPosition - 1}`);
                if (targetColumnPosition) {
                    const reportTableRowCellTarget = reportTableRow.querySelector(`td.c${targetColumnPosition - 1}`);
                    reportTableRow.insertBefore(reportTableRowCell, reportTableRowCellTarget);
                } else {
                    reportTableRow.appendChild(reportTableRowCell);
                }
            });
        }
    });

    document.addEventListener(SortableList.EVENTS.elementDrop, event => {
        const reportOrderColumn = event.target.closest(`${columnHeadingSelector} ${reportSelectors.regions.columnHeader}`);
        if (reportOrderColumn && event.detail.positionChanged) {
            const pendingPromise = new Pending('core_reportbuilder/columns:reorder');

            const reportElement = reportOrderColumn.closest(reportSelectors.regions.report);
            const {columnId, columnPosition, columnName} = reportOrderColumn.dataset;

            // Select target position, if moving to the end then count number of element siblings.
            let targetColumnPosition = event.detail.targetNextElement.data('columnPosition')
                || event.detail.element.siblings().length + 2;
            if (targetColumnPosition > columnPosition) {
                targetColumnPosition--;
            }

            // Re-order column, giving drop event transition time to finish.
            const reorderPromise = reorderColumn(reportElement.dataset.reportId, columnId, targetColumnPosition);
            Promise.all([reorderPromise, new Promise(resolve => setTimeout(resolve, 1000))])
                .then(() => getString('columnmoved', 'core_reportbuilder', columnName))
                .then(addToast)
                .then(() => {
                    dispatchEvent(reportEvents.tableReload, {preservePagination: true}, reportElement);
                    return pendingPromise.resolve();
                })
                .catch(Notification.exception);
        }
    });

    // Initialize inplace editable listeners for column aggregation.
    document.addEventListener(inplaceEditableEvents.elementUpdated, event => {
        const columnAggregation = event.target.closest('[data-itemtype="columnaggregation"]');
        if (columnAggregation) {
            const pendingPromise = new Pending('core_reportbuilder/columns:aggregate');
            const reportElement = columnAggregation.closest(reportSelectors.regions.report);
            const columnHeader = columnAggregation.closest(reportSelectors.regions.columnHeader);

            getString('columnaggregated', 'core_reportbuilder', columnHeader.dataset.columnName)
                .then(addToast)
                .then(() => {
                    // Pass preserveTriggerElement parameter so columnAggregationLink will be focused after the report reload.
                    const columnAggregationLink = `[data-itemtype="columnaggregation"][data-itemid="`
                        + `${columnAggregation.dataset.itemid}"] > a`;

                    // Now reload the table, and notify listeners that columns have been updated.
                    dispatchEvent(reportEvents.tableReload, {preserveTriggerElement: columnAggregationLink}, reportElement);
                    return getColumnSorting(reportElement.dataset.reportId);
                })
                .then(data => publish(reportEvents.publish.reportColumnsUpdated, data))
                .then(() => pendingPromise.resolve())
                .catch(Notification.exception);
        }
    });
};
