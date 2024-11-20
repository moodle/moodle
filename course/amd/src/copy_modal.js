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
 * This module provides the course copy modal from the course and
 * category management screen.
 *
 * @module     core_course/copy_modal
 * @copyright  2020 onward The Moodle Users Association <https://moodleassociation.org/>
 * @author     Matt Porritt <mattp@catalyst-au.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since      3.9
 */

import {get_string as getString} from 'core/str';
import Modal from 'core/modal';
import * as ajax from 'core/ajax';
import * as Fragment from 'core/fragment';
import Notification from 'core/notification';
import * as Config from 'core/config';

export default class CopyModal {
    static init(context) {
        return new CopyModal(context);
    }

    constructor(context) {
        this.contextid = context;

        this.registerEventListeners();
    }

    registerEventListeners() {
        document.addEventListener('click', (e) => {
            const copyAction = e.target.closest('.action-copy');
            if (!copyAction) {
                return;
            }
            e.preventDefault(); // Stop. Hammer time.

            const url = new URL(copyAction.href);
            const params = new URLSearchParams(url.search);

            this.fetchCourseData(params.get('id'))
            .then(([course]) => this.createModal(course))
            .catch((error) => Notification.exception(error));
        });
    }

    fetchCourseData(courseid) {
        return ajax.call([{
            methodname: 'core_course_get_courses',
            args: {
                options: {
                    ids: [courseid],
                },
            },
        }])[0];
    }

    submitBackupRequest(jsonformdata) {
        return ajax.call([{
            methodname: 'core_backup_submit_copy_form',
            args: {
                jsonformdata,
            },
        }])[0];
    }

    createModal(
        course,
        formdata = {},
    ) {
        const params = {
            jsonformdata: JSON.stringify(formdata),
            courseid: course.id,
        };

        // Create the Modal.
        return Modal.create({
            title: getString('copycoursetitle', 'backup', course.shortname),
            body: Fragment.loadFragment('course', 'new_base_form', this.contextid, params),
            large: true,
            show: true,
            removeOnClose: true,
        })
        .then((modal) => {
            // Explicitly handle form click events.
            modal.getRoot().on('click', '#id_submitreturn', (e) => {
                this.processModalForm(course, modal, e);
            });
            modal.getRoot().on('click', '#id_cancel', (e) => {
                e.preventDefault();
                modal.destroy();
            });
            modal.getRoot().on('click', '#id_submitdisplay', (e) => {
                e.formredirect = true;
                this.processModalForm(course, modal, e);

            });

            return modal;
        });
    }

    processModalForm(course, modal, e) {
        e.preventDefault(); // Stop modal from closing.

        // Form data.
        const copyform = modal.getRoot().find('form').serialize();
        const formjson = JSON.stringify(copyform);

        // Handle invalid form fields for better UX.
        const invalid = modal.getRoot()[0].querySelectorAll('[aria-invalid="true"], .error');
        if (invalid.length) {
            invalid[0].focus();
            return;
        }

        modal.destroy();

        // Submit form via ajax.
        this.submitBackupRequest(formjson)
        .then(() => {
            if (e.formredirect == true) {
                // We are redirecting to copy progress display.
                const redirect = `${Config.wwwroot}/backup/copyprogress.php?id=${course.id}`;
                window.location.assign(redirect);
            }

            return;
        })
        .catch(() => {
            // Form submission failed server side, redisplay with errors.
            this.createModal(course, copyform);
        });
    }
}
