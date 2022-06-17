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
 * Report builder columns sorting editor
 *
 * @module      core_reportbuilder/local/editor/sorting
 * @copyright   2021 David Matamoros <davidmc@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

"use strict";

import $ from 'jquery';
import 'core/inplace_editable';
import Notification from 'core/notification';
import Pending from 'core/pending';
import {subscribe} from 'core/pubsub';
import SortableList from 'core/sortable_list';
import {get_string as getString} from 'core/str';
import {add as addToast} from 'core/toast';
import * as reportSelectors from 'core_reportbuilder/local/selectors';
import {reorderColumnSorting, toggleColumnSorting} from 'core_reportbuilder/local/repository/sorting';
import Templates from 'core/templates';
import {dispatchEvent} from 'core/event_dispatcher';
import * as reportEvents from 'core_reportbuilder/local/events';

// These constants match PHP consts SORT_ASC, SORT_DESC.
const SORTORDER = {
    ASCENDING: 4,
    DESCENDING: 3,
};

/**
 * Reload sorting settings region
 *
 * @param {Object} context
 * @return {Promise}
 */
const reloadSettingsSortingRegion = context => {
    const pendingPromise = new Pending('core_reportbuilder/sorting:reload');
    const settingsSortingRegion = document.querySelector(reportSelectors.regions.settingsSorting);

    return Templates.renderForPromise('core_reportbuilder/local/settings/sorting', {sorting: context})
        .then(({html, js}) => {
            Templates.replaceNode(settingsSortingRegion, html, js);
            return pendingPromise.resolve();
        });
};

/**
 * Updates column sorting
 *
 * @param {Element} reportElement
 * @param {Element} element
 * @param {Number} sortenabled
 * @param {Number} sortdirection
 * @return {Promise}
 */
const updateSorting = (reportElement, element, sortenabled, sortdirection) => {
    const reportId = reportElement.dataset.reportId;
    const listElement = element.closest('li');
    const columnId = listElement.dataset.columnSortId;
    const columnName = listElement.dataset.columnSortName;

    return toggleColumnSorting(reportId, columnId, sortenabled, sortdirection)
        .then(reloadSettingsSortingRegion)
        .then(() => getString('columnsortupdated', 'core_reportbuilder', columnName))
        .then(addToast)
        .then(() => {
            dispatchEvent(reportEvents.tableReload, {}, reportElement);
            return null;
        });
};

/**
 * Initialise module
 *
 * @param {Boolean} initialized Ensure we only add our listeners once
 */
export const init = (initialized) => {
    if (initialized) {
        return;
    }

    // Update sorting region each time report columns are updated (added or removed).
    subscribe(reportEvents.publish.reportColumnsUpdated, data => reloadSettingsSortingRegion(data)
        .catch(Notification.exception)
    );

    document.addEventListener('click', event => {

        // Enable/disable sorting on columns.
        const toggleSorting = event.target.closest(reportSelectors.actions.reportToggleColumnSort);
        if (toggleSorting) {
            event.preventDefault();

            const pendingPromise = new Pending('core_reportbuilder/sorting:toggle');
            const reportElement = toggleSorting.closest(reportSelectors.regions.report);
            const sortdirection = parseInt(toggleSorting.closest('li').dataset.columnSortDirection);

            updateSorting(reportElement, toggleSorting, toggleSorting.checked, sortdirection)
                .then(() => {
                    // Re-focus the toggle sorting element after reloading the region.
                    const toggleSortingElement = document.getElementById(toggleSorting.id);
                    toggleSortingElement?.focus();
                    return pendingPromise.resolve();
                })
                .catch(Notification.exception);
        }

        // Change column sort direction.
        const toggleSortDirection = event.target.closest(reportSelectors.actions.reportToggleColumnSortDirection);
        if (toggleSortDirection) {
            event.preventDefault();

            const pendingPromise = new Pending('core_reportbuilder/sorting:direction');
            const reportElement = toggleSortDirection.closest(reportSelectors.regions.report);
            const listElement = toggleSortDirection.closest('li');
            const toggleSorting = listElement.querySelector(reportSelectors.actions.reportToggleColumnSort);

            let sortdirection = parseInt(listElement.dataset.columnSortDirection);
            if (sortdirection === SORTORDER.ASCENDING) {
                sortdirection = SORTORDER.DESCENDING;
            } else if (sortdirection === SORTORDER.DESCENDING) {
                sortdirection = SORTORDER.ASCENDING;
            }

            updateSorting(reportElement, toggleSortDirection, toggleSorting.checked, sortdirection)
                .then(() => {
                    // Re-focus the toggle sort direction element after reloading the region.
                    const toggleSortDirectionElement = document.getElementById(toggleSortDirection.id);
                    toggleSortDirectionElement?.focus();
                    return pendingPromise.resolve();
                })
                .catch(Notification.exception);
        }
    });

    // Initialize sortable list to handle column sorting moving (note JQuery dependency, see MDL-72293 for resolution).
    var columnsSortingSortableList = new SortableList(`${reportSelectors.regions.settingsSorting} ul`, {isHorizontal: false});
    columnsSortingSortableList.getElementName = element => Promise.resolve(element.data('columnSortName'));

    $(document).on(SortableList.EVENTS.DROP, `${reportSelectors.regions.report} li[data-column-sort-id]`, (event, info) => {
        if (info.positionChanged) {
            const pendingPromise = new Pending('core_reportbuilder/sorting:reorder');
            const reportElement = event.target.closest(reportSelectors.regions.report);
            const columnId = info.element.data('columnSortId');
            const columnPosition = info.element.data('columnSortPosition');

            // Select target position, if moving to the end then count number of element siblings.
            let targetColumnSortPosition = info.targetNextElement.data('columnSortPosition') || info.element.siblings().length + 2;
            if (targetColumnSortPosition > columnPosition) {
                targetColumnSortPosition--;
            }

            reorderColumnSorting(reportElement.dataset.reportId, columnId, targetColumnSortPosition)
                .then(reloadSettingsSortingRegion)
                .then(() => getString('columnsortupdated', 'core_reportbuilder', info.element.data('columnSortName')))
                .then(addToast)
                .then(() => {
                    dispatchEvent(reportEvents.tableReload, {}, reportElement);
                    return null;
                })
                .then(() => pendingPromise.resolve())
                .catch(Notification.exception);
        }
    });
};
