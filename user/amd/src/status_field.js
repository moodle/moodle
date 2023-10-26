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
 * AMD module for the user enrolment status field in the course participants page.
 *
 * @module     core_user/status_field
 * @copyright  2017 Jun Pataleta
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import * as DynamicTable from 'core_table/dynamic';
import * as Repository from './repository';
import * as Str from 'core/str';
import DynamicTableSelectors from 'core_table/local/dynamic/selectors';
import Fragment from 'core/fragment';
import ModalEvents from 'core/modal_events';
import ModalFactory from 'core/modal_factory';
import Notification from 'core/notification';
import Templates from 'core/templates';
import {add as notifyUser} from 'core/toast';

const Selectors = {
    editEnrolment: '[data-action="editenrolment"]',
    showDetails: '[data-action="showdetails"]',
    unenrol: '[data-action="unenrol"]',
    statusElement: '[data-status]',
};

/**
 * Get the dynamic table from the specified link.
 *
 * @param {HTMLElement} link
 * @returns {HTMLElement}
 */
const getDynamicTableFromLink = link => link.closest(DynamicTableSelectors.main.region);

/**
 * Get the status container from the specified link.
 *
 * @param {HTMLElement} link
 * @returns {HTMLElement}
 */
const getStatusContainer = link => link.closest(Selectors.statusElement);

/**
 * Get user enrolment id from the specified link
 *
 * @param {HTMLElement} link
 * @returns {Number}
 */
const getUserEnrolmentIdFromLink = link => link.getAttribute('rel');

/**
 * Register all event listeners for the status fields.
 *
 * @param {Number} contextId
 * @param {Number} uniqueId
 */
const registerEventListeners = (contextId, uniqueId) => {
    const getBodyFunction = (userEnrolmentId, formData) => getBody(contextId, userEnrolmentId, formData);

    document.addEventListener('click', e => {
        const tableRoot = e.target.closest(DynamicTableSelectors.main.fromRegionId(uniqueId));
        if (!tableRoot) {
            return;
        }

        const editLink = e.target.closest(Selectors.editEnrolment);
        if (editLink) {
            e.preventDefault();

            showEditDialogue(editLink, getBodyFunction);
        }

        const unenrolLink = e.target.closest(Selectors.unenrol);
        if (unenrolLink) {
            e.preventDefault();

            showUnenrolConfirmation(unenrolLink);
        }

        const showDetailsLink = e.target.closest(Selectors.showDetails);
        if (showDetailsLink) {
            e.preventDefault();

            showStatusDetails(showDetailsLink);
        }
    });
};

/**
 * Show the edit dialogue.
 *
 * @param {HTMLElement} link
 * @param {Function} getBody Function to get the body for the specified user enrolment
 */
const showEditDialogue = (link, getBody) => {
    const container = getStatusContainer(link);
    const userEnrolmentId = getUserEnrolmentIdFromLink(link);

    ModalFactory.create({
        large: true,
        title: Str.get_string('edituserenrolment', 'enrol', container.dataset.fullname),
        type: ModalFactory.types.SAVE_CANCEL,
        body: getBody(userEnrolmentId)
    })
    .then(modal => {
        // Handle save event.
        modal.getRoot().on(ModalEvents.save, e => {
            // Don't close the modal yet.
            e.preventDefault();

            // Submit form data.
            submitEditFormAjax(link, getBody, modal, userEnrolmentId, container.dataset);
        });

        // Handle hidden event.
        modal.getRoot().on(ModalEvents.hidden, () => {
            // Destroy when hidden.
            modal.destroy();
        });

        // Show the modal.
        modal.show();

        return modal;
    })
    .catch(Notification.exception);
};

/**
 * Show and handle the unenrolment confirmation dialogue.
 *
 * @param {HTMLElement} link
 */
const showUnenrolConfirmation = link => {
    const container = getStatusContainer(link);
    const userEnrolmentId = getUserEnrolmentIdFromLink(link);

    ModalFactory.create({
        type: ModalFactory.types.SAVE_CANCEL,
    })
    .then(modal => {
        // Handle confirm event.
        modal.getRoot().on(ModalEvents.save, e => {
            // Don't close the modal yet.
            e.preventDefault();

            // Submit data.
            submitUnenrolFormAjax(
                link,
                modal,
                {
                    ueid: userEnrolmentId,
                },
                container.dataset
            );
        });

        // Handle hidden event.
        modal.getRoot().on(ModalEvents.hidden, () => {
            // Destroy when hidden.
            modal.destroy();
        });

        // Display the delete confirmation modal.
        modal.show();

        const stringData = [
            {
                key: 'unenrol',
                component: 'enrol',
            },
            {
                key: 'unenrolconfirm',
                component: 'enrol',
                param: {
                    user: container.dataset.fullname,
                    course: container.dataset.coursename,
                    enrolinstancename: container.dataset.enrolinstancename,
                }
            }
        ];

        return Promise.all([Str.get_strings(stringData), modal]);
    })
    .then(([strings, modal]) => {
        modal.setTitle(strings[0]);
        modal.setSaveButtonText(strings[0]);
        modal.setBody(strings[1]);

        return modal;
    })
    .catch(Notification.exception);
};

/**
 * Show the user details dialogue.
 *
 * @param {HTMLElement} link
 */
const showStatusDetails = link => {
    const container = getStatusContainer(link);

    const context = {
        editenrollink: '',
        statusclass: container.querySelector('span.badge').getAttribute('class'),
        ...container.dataset,
    };

    // Find the edit enrolment link.
    const editEnrolLink = container.querySelector(Selectors.editEnrolment);
    if (editEnrolLink) {
        // If there's an edit enrolment link for this user, clone it into the context for the modal.
        context.editenrollink = editEnrolLink.outerHTML;
    }

    ModalFactory.create({
        large: true,
        type: ModalFactory.types.CANCEL,
        title: Str.get_string('enroldetails', 'enrol'),
        body: Templates.render('core_user/status_details', context),
    })
    .then(modal => {
        if (editEnrolLink) {
            modal.getRoot().on('click', Selectors.editEnrolment, e => {
                e.preventDefault();
                modal.hide();

                // Trigger click event for the edit enrolment link to show the edit enrolment modal.
                editEnrolLink.click();
            });
        }

        modal.show();

        // Handle hidden event.
        modal.getRoot().on(ModalEvents.hidden, () => modal.destroy());

        return modal;
    })
    .catch(Notification.exception);
};

/**
 * Submit the edit dialogue.
 *
 * @param {HTMLElement} clickedLink
 * @param {Function} getBody
 * @param {Object} modal
 * @param {Number} userEnrolmentId
 * @param {Object} userData
 */
const submitEditFormAjax = (clickedLink, getBody, modal, userEnrolmentId, userData) => {
    const form = modal.getRoot().find('form');

    Repository.submitUserEnrolmentForm(form.serialize())
    .then(data => {
        if (!data.result) {
            throw data.result;
        }

        // Dismiss the modal.
        modal.hide();
        modal.destroy();

        return data;
    })
    .then(() => {
        DynamicTable.refreshTableContent(getDynamicTableFromLink(clickedLink))
        .catch(Notification.exception);

        return Str.get_string('enrolmentupdatedforuser', 'core_enrol', userData);
    })
    .then(notificationString => {
        notifyUser(notificationString);

        return;
    })
    .catch(() => {
        modal.setBody(getBody(userEnrolmentId, JSON.stringify(form.serialize())));

        return modal;
    });
};

/**
 * Submit the unenrolment form.
 *
 * @param {HTMLElement} clickedLink
 * @param {Object} modal
 * @param {Object} args
 * @param {Object} userData
 */
const submitUnenrolFormAjax = (clickedLink, modal, args, userData) => {
    Repository.unenrolUser(args.ueid)
    .then(data => {
        if (!data.result) {
            // Display an alert containing the error message
            Notification.alert(data.errors[0].key, data.errors[0].message);

            return data;
        }

        // Dismiss the modal.
        modal.hide();
        modal.destroy();

        return data;
    })
    .then(() => {
        DynamicTable.refreshTableContent(getDynamicTableFromLink(clickedLink))
        .catch(Notification.exception);

        return Str.get_string('unenrolleduser', 'core_enrol', userData);
    })
    .then(notificationString => {
        notifyUser(notificationString);

        return;
    })
    .catch(Notification.exception);
};

/**
 * Get the body fragment.
 *
 * @param {Number} contextId
 * @param {Number} ueid The user enrolment id
 * @param {Object} formdata
 * @returns {Promise}
 */
const getBody = (contextId, ueid, formdata = null) => Fragment.loadFragment(
    'enrol',
    'user_enrolment_form',
    contextId,
    {
        ueid,
        formdata,
    }
);

/**
 * Initialise the statu field handler.
 *
 * @param {object} param
 * @param {Number} param.contextid
 * @param {Number} param.uniqueid
 */
export const init = ({contextid, uniqueid}) => {
    registerEventListeners(contextid, uniqueid);
};
