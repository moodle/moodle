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
 * Prints the add item gradebook form
 *
 * @module core_grades
 * @copyright 2023 Mathew May <mathew.solutions>
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 */

import ModalForm from 'core_form/modalform';
import {getString} from 'core/str';
import Notification from 'core/notification';
import * as FormChangeChecker from 'core_form/changechecker';
import PendingPromise from 'core/pending';

const Selectors = {
    advancedFormLink: 'a.showadvancedform'
};

const getDetailsFromEvent = (event) => {
    if (event.target.closest('[data-trigger="add-item-form"]')) {
        const trigger = event.target.closest('[data-trigger="add-item-form"]');

        return {
            trigger,
            formClass: 'core_grades\\form\\add_item',
            titleKey: trigger.getAttribute('data-itemid') === '-1' ? 'newitem' : 'itemsedit',
            args: {
                itemid: trigger.getAttribute('data-itemid'),
            },
        };
    } else if (event.target.closest('[data-trigger="add-category-form"]')) {
        const trigger = event.target.closest('[data-trigger="add-category-form"]');
        return {
            trigger,
            formClass: 'core_grades\\form\\add_category',
            titleKey: trigger.getAttribute('data-category') === '-1' ? 'newcategory' : 'categoryedit',
            args: {
                category: trigger.getAttribute('data-category'),
            },
        };
    } else if (event.target.closest('[data-trigger="add-outcome-form"]')) {
        const trigger = event.target.closest('[data-trigger="add-outcome-form"]');
        return {
            trigger,
            formClass: 'core_grades\\form\\add_outcome',
            titleKey: trigger.getAttribute('data-itemid') === '-1' ? 'newoutcomeitem' : 'outcomeitemsedit',
            args: {
                itemid: trigger.getAttribute('data-itemid'),
            },
        };
    }

    return null;
};

/**
 * Initialize module
 */
export const init = () => {
    // Sometimes the trigger does not exist, so lets conditionally add it.
    document.addEventListener('click', event => {
        const triggerData = getDetailsFromEvent(event);

        if (triggerData) {
            event.preventDefault();
            const pendingPromise = new PendingPromise(`core_grades:add_item:${triggerData.args.itemid}`);

            const {trigger, formClass, titleKey, args} = triggerData;
            args.courseid = trigger.getAttribute('data-courseid');
            args.gpr_plugin = trigger.getAttribute('data-gprplugin');

            const modalForm = new ModalForm({
                modalConfig: {
                    title: getString(titleKey, 'core_grades'),
                },
                formClass: formClass,
                args: args,
                saveButtonText: getString('save', 'core'),
                returnFocus: trigger,
            });

            // Show a toast notification when the form is submitted.
            modalForm.addEventListener(modalForm.events.FORM_SUBMITTED, event => {
                if (event.detail.result) {
                    new PendingPromise('core_grades:form_submitted');
                    window.location.assign(event.detail.url);
                } else {
                    Notification.addNotification({
                        type: 'error',
                        message: getString('saving_failed', 'core_grades')
                    });
                }
            });

            modalForm.show();
            pendingPromise.resolve();
        }

        const showAdvancedForm = event.target.closest(Selectors.advancedFormLink);
        if (showAdvancedForm) {
            // Navigate to the advanced form page and cary over any entered data.
            event.preventDefault();

            // Do not resolve this pendingPromise - it will be cleared when the page changes.
            new PendingPromise('core_grades:show_advanced_form');
            const form = event.target.closest('form');
            form.action = showAdvancedForm.href;
            // Disable the form change checker as we are going to carry over the data to the advanced form.
            FormChangeChecker.disableAllChecks();
            form.submit();
        }
    });
};
