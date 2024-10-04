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
 * Report builder filters editor
 *
 * @module      core_reportbuilder/local/editor/filters
 * @copyright   2021 David Matamoros <davidmc@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

"use strict";

import AutoComplete from 'core/form-autocomplete';
import 'core/inplace_editable';
import Notification from 'core/notification';
import Pending from 'core/pending';
import {prefetchStrings} from 'core/prefetch';
import SortableList from 'core/sortable_list';
import {getString} from 'core/str';
import Templates from 'core/templates';
import {add as addToast} from 'core/toast';
import * as reportSelectors from 'core_reportbuilder/local/selectors';
import {addFilter, deleteFilter, reorderFilter} from 'core_reportbuilder/local/repository/filters';

/**
 * Reload filters settings region
 *
 * @param {Element} reportElement
 * @param {Object} templateContext
 * @return {Promise}
 */
const reloadSettingsFiltersRegion = (reportElement, templateContext) => {
    const pendingPromise = new Pending('core_reportbuilder/filters:reload');
    const settingsFiltersRegion = reportElement.querySelector(reportSelectors.regions.settingsFilters);

    return Templates.renderForPromise('core_reportbuilder/local/settings/filters', {filters: templateContext})
        .then(({html, js}) => {
            Templates.replaceNode(settingsFiltersRegion, html, js);

            initFiltersForm();

            // Re-focus the add filter element after reloading the region.
            const reportAddFilter = reportElement.querySelector(reportSelectors.actions.reportAddFilter);
            reportAddFilter?.focus();

            return pendingPromise.resolve();
        });
};

/**
 * Initialise filters form, must be called on each init because the form container is re-created when switching editor modes
 */
const initFiltersForm = () => {
    const reportElement = document.querySelector(reportSelectors.regions.report);

    // Enhance filter selector.
    const reportAddFilter = reportElement.querySelector(reportSelectors.actions.reportAddFilter);
    AutoComplete.enhanceField(reportAddFilter, false, '', getString('selectafilter', 'core_reportbuilder'))
        .catch(Notification.exception);
};

/**
 * Initialise module, prefetch all required strings
 *
 * @param {Boolean} initialized Ensure we only add our listeners once
 */
export const init = initialized => {
    prefetchStrings('core_reportbuilder', [
        'deletefilter',
        'deletefilterconfirm',
        'filteradded',
        'filterdeleted',
        'filtermoved',
        'selectafilter',
    ]);

    prefetchStrings('core', [
        'delete',
    ]);

    initFiltersForm();
    if (initialized) {
        return;
    }

    // Add filter to report.
    document.addEventListener('change', event => {
        const reportAddFilter = event.target.closest(reportSelectors.actions.reportAddFilter);
        if (reportAddFilter) {
            event.preventDefault();

            // Check if dropdown is closed with no filter selected.
            if (reportAddFilter.value === "" || reportAddFilter.value === "0") {
                return;
            }

            const reportElement = reportAddFilter.closest(reportSelectors.regions.report);
            const pendingPromise = new Pending('core_reportbuilder/filters:add');

            addFilter(reportElement.dataset.reportId, reportAddFilter.value)
                .then(data => reloadSettingsFiltersRegion(reportElement, data))
                .then(() => getString('filteradded', 'core_reportbuilder',
                    reportAddFilter.options[reportAddFilter.selectedIndex].text))
                .then(addToast)
                .then(() => pendingPromise.resolve())
                .catch(Notification.exception);
        }
    });

    document.addEventListener('click', event => {

        // Remove filter from report.
        const reportRemoveFilter = event.target.closest(reportSelectors.actions.reportRemoveFilter);
        if (reportRemoveFilter) {
            event.preventDefault();

            const reportElement = reportRemoveFilter.closest(reportSelectors.regions.report);
            const filterContainer = reportRemoveFilter.closest(reportSelectors.regions.activeFilter);
            const filterName = filterContainer.dataset.filterName;

            Notification.saveCancelPromise(
                getString('deletefilter', 'core_reportbuilder', filterName),
                getString('deletefilterconfirm', 'core_reportbuilder', filterName),
                getString('delete', 'core'),
                {triggerElement: reportRemoveFilter}
            ).then(() => {
                const pendingPromise = new Pending('core_reportbuilder/filters:remove');

                return deleteFilter(reportElement.dataset.reportId, filterContainer.dataset.filterId)
                    .then(data => reloadSettingsFiltersRegion(reportElement, data))
                    .then(() => addToast(getString('filterdeleted', 'core_reportbuilder', filterName)))
                    .then(() => pendingPromise.resolve())
                    .catch(Notification.exception);
            }).catch(() => {
                return;
            });
        }
    });

    // Initialize sortable list to handle active filters moving.
    const activeFiltersSelector = `${reportSelectors.regions.activeFilters} ul`;
    const activeFiltersSortableList = new SortableList(activeFiltersSelector, {isHorizontal: false});
    activeFiltersSortableList.getElementName = element => Promise.resolve(element.data('filterName'));

    document.addEventListener(SortableList.EVENTS.elementDrop, event => {
        const reportOrderFilter = event.target.closest(`${activeFiltersSelector} ${reportSelectors.regions.activeFilter}`);
        if (reportOrderFilter && event.detail.positionChanged) {
            const pendingPromise = new Pending('core_reportbuilder/filters:reorder');

            const reportElement = reportOrderFilter.closest(reportSelectors.regions.report);
            const {filterId, filterPosition, filterName} = reportOrderFilter.dataset;

            // Select target position, if moving to the end then count number of element siblings.
            let targetFilterPosition = event.detail.targetNextElement.data('filterPosition')
                || event.detail.element.siblings().length + 2;
            if (targetFilterPosition > filterPosition) {
                targetFilterPosition--;
            }

            // Re-order filter, giving drop event transition time to finish.
            const reorderPromise = reorderFilter(reportElement.dataset.reportId, filterId, targetFilterPosition);
            Promise.all([reorderPromise, new Promise(resolve => setTimeout(resolve, 1000))])
                .then(([data]) => reloadSettingsFiltersRegion(reportElement, data))
                .then(() => getString('filtermoved', 'core_reportbuilder', filterName))
                .then(addToast)
                .then(() => pendingPromise.resolve())
                .catch(Notification.exception);
        }
    });
};
