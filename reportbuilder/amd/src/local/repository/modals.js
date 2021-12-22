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
 * Module to handle modal form requests
 *
 * @module      core_reportbuilder/local/repository/modals
 * @copyright   2021 David Matamoros <davidmc@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import ModalForm from 'core_form/modalform';
import {get_string as getString} from 'core/str';

/**
 * Return modal instance
 *
 * @param {EventTarget} triggerElement
 * @param {Promise} modalTitle
 * @param {String} formClass
 * @param {Object} formArgs
 * @return {ModalForm}
 */
const createModalForm = (triggerElement, modalTitle, formClass, formArgs) => {
    return new ModalForm({
        modalConfig: {
            title: modalTitle,
        },
        formClass: formClass,
        args: formArgs,
        saveButtonText: getString('save', 'moodle'),
        returnFocus: triggerElement,
    });
};

/**
 * Return report modal instance
 *
 * @param {EventTarget} triggerElement
 * @param {Promise} modalTitle
 * @param {Number} reportId
 * @return {ModalForm}
 */
export const createReportModal = (triggerElement, modalTitle, reportId = 0) => {
    return createModalForm(triggerElement, modalTitle, 'core_reportbuilder\\form\\report', {
        id: reportId,
    });
};

/**
 * Return schedule modal instance
 *
 * @param {EventTarget} triggerElement
 * @param {Promise} modalTitle
 * @param {Number} reportId
 * @param {Number} scheduleId
 * @return {ModalForm}
 */
export const createScheduleModal = (triggerElement, modalTitle, reportId, scheduleId = 0) => {
    return createModalForm(triggerElement, modalTitle, 'core_reportbuilder\\form\\schedule', {
        reportid: reportId,
        id: scheduleId,
    });
};
