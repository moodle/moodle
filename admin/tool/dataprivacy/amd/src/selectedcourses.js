// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle. If not, see <http://www.gnu.org/licenses/>.

/**
 * Selected courses.
 *
 * @module     tool_dataprivacy/selectedcourses
 * @copyright  2021 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since      Moodle 4.3
 */

import Ajax from 'core/ajax';
import Notification from 'core/notification';
import ModalSaveCancel from 'core/modal_save_cancel';
import ModalEvents from 'core/modal_events';
import Fragment from 'core/fragment';
import {prefetchStrings} from 'core/prefetch';
import {getString} from 'core/str';

prefetchStrings('tool_dataprivacy', [
    'selectcourses',
    'approverequest',
    'errornoselectedcourse',
]);

/**
 * Selected Courses popup modal.
 *
 */
export default class SelectedCourses {
    /**
     * @var {String} contextId Context ID to load the fragment.
     * @private
     */
    contextId = 0;

    /**
     * @var {String} requestId ID of data export request.
     * @private
     */
    requestId = 0;

    /**
     * Constructor
     *
     * @param {String} contextId Context ID to load the fragment.
     * @param {String} requestId ID of data export request.
     */
    constructor(contextId, requestId) {
        this.contextId = contextId;
        this.requestId = requestId;
        // Now create the modal.
        ModalSaveCancel.create({
            title: getString('selectcourses', 'tool_dataprivacy'),
            body: this.getBody({requestid: requestId}),
            large: true,
            removeOnClose: true,
            buttons: {
                save: getString('approverequest', 'tool_dataprivacy'),
            },
        }).then((modal) => {
            this.modal = modal;

            return modal;
        }).then((modal) => {
            // We catch the modal save event, and use it to submit the form inside the modal.
            // Triggering a form submission will give JS validation scripts a chance to check for errors.
            modal.getRoot().on(ModalEvents.save, this.submitForm.bind(this));

            // We also catch the form submit event and use it to submit the form with ajax.
            modal.getRoot().on('submit', 'form', this.submitFormAjax.bind(this));
            modal.show();
            return modal;
        }).catch(Notification.exception);
    }

    /**
     * Get body of modal.
     *
     * @method getBody
     * @param {Object} formdata
     * @private
     * @return {Promise}
     */
    getBody(formdata) {
        const params = formdata ? {jsonformdata: JSON.stringify(formdata)} : null;

        // Get the content of the modal.
        return Fragment.loadFragment('tool_dataprivacy', 'selectcourses_form', this.contextId, params);
    }

    /**
     * This triggers a form submission, so that any mform elements can do final tricks before the form submission is processed.
     *
     * @method submitForm
     * @param {Event} e Form submission event.
     * @private
     */
    submitForm(e) {
        e.preventDefault();
        this.modal.getRoot().find('form').submit();
    }

    /**
     * Submit select courses form using ajax.
     *
     * @method submitFormAjax
     * @private
     * @param {Event} e Form submission event.
     */
    submitFormAjax(e) {
        e.preventDefault();

        // Convert all the form elements values to a serialised string.
        let formData = this.modal.getRoot().find('form').serialize();

        if (formData.indexOf('coursecontextids') === -1) {
            const customSelect = this.modal.getRoot().find('.form-select');
            const invalidText = this.modal.getRoot().find('.invalid-feedback');
            customSelect.addClass('is-invalid');
            invalidText.attr('style', 'display: block');
            getString('errornoselectedcourse', 'tool_dataprivacy').then(value => {
                invalidText.empty().append(value);
                return;
            }).catch(Notification.exception);
            return;
        }

        Ajax.call([{
            methodname: 'tool_dataprivacy_submit_selected_courses_form',
            args: {requestid: this.requestId, jsonformdata: JSON.stringify(formData)},
        }])[0]
        .then((data) => {
            if (data.warnings.length > 0) {
                this.modal.setBody(this.getBody(formData));
            } else {
                this.modal.destroy();
                document.location.reload();
            }
            return data;
        })
        .catch((error) => Notification.exception(error));
    }
}
