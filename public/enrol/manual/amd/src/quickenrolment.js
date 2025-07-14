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
 * Quick enrolment AMD module.
 *
 * @module     enrol_manual/quickenrolment
 * @copyright  2016 Damyon Wiese <damyon@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
import * as DynamicTable from 'core_table/dynamic';
import * as Str from 'core/str';
import * as Toast from 'core/toast';
import Config from 'core/config';
import Fragment from 'core/fragment';
import ModalEvents from 'core/modal_events';
import Notification from 'core/notification';
import jQuery from 'jquery';
import Pending from 'core/pending';
import Prefetch from 'core/prefetch';
import ModalSaveCancel from 'core/modal_save_cancel';

const Selectors = {
    cohortSelector: "#id_cohortlist",
    triggerButtons: ".enrolusersbutton.enrol_manual_plugin [type='submit']",
    unwantedHiddenFields: "input[value='_qf__force_multiselect_submission']",
    buttonWrapper: '[data-region="wrapper"]',
};

/**
 * Get the content of the body for the specified context.
 *
 * @param {Number} contextId
 * @returns {Promise}
 */
const getBodyForContext = contextId => {
    return Fragment.loadFragment('enrol_manual', 'enrol_users_form', contextId, {});
};

/**
 * Get the dynamic table for the button.
 *
 * @param {HTMLElement} element
 * @returns {HTMLElement}
 */
const getDynamicTableForElement = element => {
    const wrapper = element.closest(Selectors.buttonWrapper);

    return DynamicTable.getTableFromId(wrapper.dataset.tableUniqueid);
};

/**
 * Register the event listeners for this contextid.
 *
 * @param {Number} contextId
 */
const registerEventListeners = contextId => {
    document.addEventListener('click', e => {
        if (e.target.closest(Selectors.triggerButtons)) {
            e.preventDefault();

            showModal(getDynamicTableForElement(e.target), contextId);

            return;
        }
    });
};

/**
 * Display the modal for this contextId.
 *
 * @param {HTMLElement} dynamicTable The table to beb refreshed when changes are made
 * @param {Number} contextId
 * @returns {Promise}
 */
const showModal = (dynamicTable, contextId) => {
    const pendingPromise = new Pending('enrol_manual/quickenrolment:showModal');

    return ModalSaveCancel.create({
        large: true,
        title: Str.get_string('enrolusers', 'enrol_manual'),
        body: getBodyForContext(contextId),
        buttons: {
            save: Str.get_string('enrolusers', 'enrol_manual'),
        },
        show: true,
    })
    .then(modal => {
        modal.getRoot().on(ModalEvents.save, e => {
            // Trigger a form submission, so that any mform elements can do final tricks before the form submission
            // is processed.
            // The actual submit even tis captured in the next handler.

            e.preventDefault();
            modal.getRoot().find('form').submit();
        });

        modal.getRoot().on('submit', 'form', e => {
            e.preventDefault();

            submitFormAjax(dynamicTable, modal);
        });

        modal.getRoot().on(ModalEvents.hidden, () => {
            modal.destroy();
        });

        return modal;
    })
    .then(modal => Promise.all([modal, modal.getBodyPromise()]))
    .then(([modal, body]) => {
        if (body.get(0).querySelector(Selectors.cohortSelector)) {
            return modal.setSaveButtonText(Str.get_string('enroluserscohorts', 'enrol_manual')).then(() => modal);
        }

        return modal;
    })
    .then(modal => {
        pendingPromise.resolve();

        return modal;
    })
    .catch(Notification.exception);
};

/**
 * Submit the form via ajax.
 *
 * @param {HTMLElement} dynamicTable
 * @param {Object} modal
 */
const submitFormAjax = (dynamicTable, modal) => {
    // Note: We use a jQuery object here so that we can use its serialize functionality.
    const form = modal.getRoot().find('form');

    // Before send the data through AJAX, we need to parse and remove some unwanted hidden fields.
    // This hidden fields are added automatically by mforms and when it reaches the AJAX we get an error.
    form.get(0).querySelectorAll(Selectors.unwantedHiddenFields).forEach(hiddenField => hiddenField.remove());

    modal.hide();
    modal.destroy();

    jQuery.ajax(
        `${Config.wwwroot}/enrol/manual/ajax.php?${form.serialize()}`,
        {
            type: 'GET',
            processData: false,
            contentType: "application/json",
        }
    )
    .then(response => {
        if (response.error) {
            throw new Error(response.error);
        }

        return response.count;
    })
    .then(count => {
        return Promise.all([
            Str.get_string('totalenrolledusers', 'enrol', count),
            DynamicTable.refreshTableContent(dynamicTable),
        ]);
    })
    .then(([notificationBody]) => notificationBody)
    .then(notificationBody => Toast.add(notificationBody))
    .catch(error => {
        Notification.addNotification({
            message: error.message,
            type: 'error',
        });
    });
};

/**
 * Set up quick enrolment for the manual enrolment plugin.
 *
 * @param {Number} contextid The context id to setup for
 */
export const init = ({contextid}) => {
    registerEventListeners(contextid);

    Prefetch.prefetchStrings('enrol_manual', [
        'enrolusers',
        'enroluserscohorts',
    ]);

    Prefetch.prefetchString('enrol', 'totalenrolledusers');
};
