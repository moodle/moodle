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
 * Report builder conditions editor
 *
 * @module      core_reportbuilder/local/editor/conditions
 * @copyright   2021 Paul Holden <paulh@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

"use strict";

import $ from 'jquery';
import {dispatchEvent} from 'core/event_dispatcher';
import 'core/inplace_editable';
import Notification from 'core/notification';
import Pending from 'core/pending';
import SortableList from 'core/sortable_list';
import {get_string as getString, get_strings as getStrings} from 'core/str';
import Templates from 'core/templates';
import {add as addToast} from 'core/toast';
import DynamicForm from 'core_form/dynamicform';
import * as reportEvents from 'core_reportbuilder/local/events';
import * as reportSelectors from 'core_reportbuilder/local/selectors';
import {addCondition, deleteCondition, reorderCondition, resetConditions} from 'core_reportbuilder/local/repository/conditions';

/**
 * Reload conditions settings region
 *
 * @param {Element} reportElement
 * @param {Object} templateContext
 * @return {Promise}
 */
const reloadSettingsConditionsRegion = (reportElement, templateContext) => {
    const pendingPromise = new Pending('core_reportbuilder/conditions:reload');
    const settingsConditionsRegion = reportElement.querySelector(reportSelectors.regions.settingsConditions);

    return Templates.renderForPromise('core_reportbuilder/local/settings/conditions', {conditions: templateContext})
        .then(({html, js}) => {
            Templates.replaceNode(settingsConditionsRegion, html, js + templateContext.javascript);
            initConditionsForm(reportElement);
            return pendingPromise.resolve();
        });
};

/**
 * Initialise conditions form, must be called on each init because the form container is re-created when switching editor modes
 */
const initConditionsForm = () => {
    // Handle dynamic conditions form.
    const reportElement = document.querySelector(reportSelectors.regions.report);
    const conditionFormContainer = reportElement.querySelector(reportSelectors.regions.settingsConditions);
    const conditionForm = new DynamicForm(conditionFormContainer, '\\core_reportbuilder\\form\\condition');

    // Submit report conditions.
    conditionForm.addEventListener(conditionForm.events.FORM_SUBMITTED, event => {
        event.preventDefault();

        getString('conditionsapplied', 'core_reportbuilder')
            .then(addToast)
            .catch(Notification.exception);

        // After the form has been submitted, we should trigger report table reload.
        dispatchEvent(reportEvents.tableReload, {}, reportElement);
    });

    // Reset report conditions.
    conditionForm.addEventListener(conditionForm.events.NOSUBMIT_BUTTON_PRESSED, event => {
        event.preventDefault();

        const pendingPromise = new Pending('core_reportbuilder/conditions:reset');

        resetConditions(reportElement.dataset.reportId)
            .then(data => reloadSettingsConditionsRegion(reportElement, data))
            .then(() => getString('conditionsreset', 'core_reportbuilder'))
            .then(addToast)
            .then(() => {
                dispatchEvent(reportEvents.tableReload, {}, reportElement);
                return pendingPromise.resolve();
            })
            .catch(Notification.exception);
    });
};

/**
 * Initialise module
 *
 * @param {Boolean} initialized Ensure we only add our listeners once
 */
export const init = (initialized) => {
    initConditionsForm();
    if (initialized) {
        return;
    }

    document.addEventListener('click', event => {

        // Add condition to report.
        const reportAddCondition = event.target.closest(reportSelectors.actions.reportAddCondition);
        if (reportAddCondition) {
            event.preventDefault();

            const reportElement = reportAddCondition.closest(reportSelectors.regions.report);

            // Check if dropdown is closed with no condition selected.
            if (reportAddCondition.value === '0') {
                return;
            }

            const pendingPromise = new Pending('core_reportbuilder/conditions:add');

            addCondition(reportElement.dataset.reportId, reportAddCondition.value)
                .then(data => reloadSettingsConditionsRegion(reportElement, data))
                .then(() => getString('conditionadded', 'core_reportbuilder',
                    reportAddCondition.options[reportAddCondition.selectedIndex].text))
                .then(addToast)
                .then(() => {
                    dispatchEvent(reportEvents.tableReload, {}, reportElement);
                    return pendingPromise.resolve();
                })
                .catch(Notification.exception);
        }

        // Remove condition from report.
        const reportRemoveCondition = event.target.closest(reportSelectors.actions.reportRemoveCondition);
        if (reportRemoveCondition) {
            event.preventDefault();

            const reportElement = reportRemoveCondition.closest(reportSelectors.regions.report);
            const conditionContainer = reportRemoveCondition.closest(reportSelectors.regions.activeCondition);
            const conditionName = conditionContainer.dataset.conditionName;

            getStrings([
                {key: 'deletecondition', component: 'core_reportbuilder', param: conditionName},
                {key: 'deleteconditionconfirm', component: 'core_reportbuilder', param: conditionName},
                {key: 'delete', component: 'moodle'},
            ]).then(([confirmTitle, confirmText, confirmButton]) => {
                Notification.confirm(confirmTitle, confirmText, confirmButton, null, () => {
                    const pendingPromise = new Pending('core_reportbuilder/conditions:remove');

                    deleteCondition(reportElement.dataset.reportId, conditionContainer.dataset.conditionId)
                        .then(data => reloadSettingsConditionsRegion(reportElement, data))
                        .then(() => getString('conditiondeleted', 'core_reportbuilder', conditionName))
                        .then(addToast)
                        .then(() => {
                            dispatchEvent(reportEvents.tableReload, {}, reportElement);
                            return pendingPromise.resolve();
                        })
                        .catch(Notification.exception);
                });
                return;
            }).catch(Notification.exception);
        }
    });

    // Initialize sortable list to handle active conditions moving (note JQuery dependency, see MDL-72293 for resolution).
    var activeConditionsSortableList = new SortableList(`${reportSelectors.regions.activeConditions}`,
        {isHorizontal: false});
    activeConditionsSortableList.getElementName = element => Promise.resolve(element.data('conditionName'));

    $(document).on(SortableList.EVENTS.DROP, reportSelectors.regions.activeCondition, (event, info) => {
        if (info.positionChanged) {
            const pendingPromise = new Pending('core_reportbuilder/conditions:reorder');
            const reportElement = event.target.closest(reportSelectors.regions.report);
            const conditionId = info.element.data('conditionId');
            const conditionPosition = info.element.data('conditionPosition');

            // Select target position, if moving to the end then count number of element siblings.
            let targetConditionPosition = info.targetNextElement.data('conditionPosition') || info.element.siblings().length + 2;
            if (targetConditionPosition > conditionPosition) {
                targetConditionPosition--;
            }

            reorderCondition(reportElement.dataset.reportId, conditionId, targetConditionPosition)
                .then(data => reloadSettingsConditionsRegion(reportElement, data))
                .then(() => getString('conditionmoved', 'core_reportbuilder', info.element.data('conditionName')))
                .then(addToast)
                .then(() => {
                    dispatchEvent(reportEvents.tableReload, {}, reportElement);
                    return pendingPromise.resolve();
                })
                .catch(Notification.exception);
        }
    });
};
