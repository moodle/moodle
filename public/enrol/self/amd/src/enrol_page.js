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
 * Functions for the enrol_self plugin
 *
 * @module     enrol_self/enrol_page
 * @copyright  Marina Glancy
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import ModalForm from 'core_form/modalform';
import {getString} from 'core/str';
import {prefetchStrings} from 'core/prefetch';
import Url from 'core/url';

/**
 * Initialise widget on the course enrolment page - clicking on the button should submit the form
 *
 * @param {Number} instanceId
 */
export function initEnrol(instanceId) {
    prefetchStrings('enrol_self', [
        'enrolme',
    ]);

    const button = document.querySelector('button[type="submit"][data-instance="' + instanceId + '"]');
    if (button) {
        button.addEventListener('click', (e) => {
            e.preventDefault();
            const modalForm = new ModalForm({
                modalConfig: {
                    title: button.dataset.title,
                    large: false, // This is a very small form that does not need a large popup.
                },
                formClass: button.dataset.form,
                args: {id: button.dataset.id, instance: instanceId},
                saveButtonText: getString('enrolme', 'enrol_self'),
                returnFocus: button,
            });

            // Redirect to the course page when the form is submitted.
            modalForm.addEventListener(modalForm.events.FORM_SUBMITTED, event => {
                window.location.href = event.detail ? event.detail :
                    Url.relativeUrl('/course/view.php', {id: button.dataset.id});
            });

            modalForm.show();
        });
    }
}
