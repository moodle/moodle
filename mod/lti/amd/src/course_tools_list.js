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
 * Course LTI External tools list management.
 *
 * @module      mod_lti/course_tools_list
 * @copyright   2023 Jake Dallimore <jrhdallimore@gmail.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

"use strict";

import Notification from 'core/notification';
import Pending from 'core/pending';
import Ajax from 'core/ajax';
import {add as addToast} from 'core/toast';
import {getString, getStrings} from 'core/str';
import {refreshTableContent} from 'core_table/dynamic';
import * as Selectors from 'core_table/local/dynamic/selectors';
import {toggleShowInActivityChooser} from "./repository";

/**
 * Initialise module.
 */
export const init = () => {
    document.addEventListener('click', event => {

        const courseToolDelete = event.target.closest('[data-action="course-tool-delete"]');
        if (courseToolDelete) {
            event.preventDefault();

            // A different message is used in the modal if the tool has usages within the course.
            const usage = courseToolDelete.dataset.courseToolUsage;
            const deleteBodyStringId = usage > 0 ? 'deletecoursetoolwithusageconfirm' : 'deletecoursetoolconfirm';
            const requiredStrings = [
                {key: 'deletecoursetool', component: 'mod_lti', param: courseToolDelete.dataset.courseToolName},
                {key: deleteBodyStringId, component: 'mod_lti', param: courseToolDelete.dataset.courseToolName},
                {key: 'delete', component: 'core', param: courseToolDelete.dataset.courseToolName},
                {key: 'coursetooldeleted', component: 'mod_lti', param: courseToolDelete.dataset.courseToolName}
            ];
            // Use triggerElement to return focus to the action menu toggle.
            const triggerElement = courseToolDelete.closest('.dropdown').querySelector('.dropdown-toggle');

            getStrings(requiredStrings).then(([modalTitle, modalBody, deleteLabel]) => {
                return Notification.deleteCancelPromise(
                    modalTitle,
                    modalBody,
                    deleteLabel,
                    {triggerElement});
            }).then(() => {
                const pendingPromise = new Pending('mod_lti/course_tools:delete');

                const request = {
                    methodname: 'mod_lti_delete_course_tool_type',
                    args: {tooltypeid: courseToolDelete.dataset.courseToolId}
                };
                return Ajax.call([request])[0]
                    .then(addToast(getString('coursetooldeleted', 'mod_lti', courseToolDelete.dataset.courseToolName)))
                    .then(() => {
                        const tableRoot = triggerElement.closest(Selectors.main.region);
                        return refreshTableContent(tableRoot);
                    })
                    .then(pendingPromise.resolve)
                    .catch(Notification.exception);
            }).catch(() => {
                return;
            });
        }

        const courseShowInActivityChooser = event.target.closest('[data-action="showinactivitychooser-toggle"]');
        if (courseShowInActivityChooser) {
            const showInActivityChooserStateToggle = courseShowInActivityChooser.dataset.state === "0" ? 1 : 0;
            return toggleShowInActivityChooser(
                courseShowInActivityChooser.dataset.id,
                courseShowInActivityChooser.dataset.courseid,
                showInActivityChooserStateToggle,
            );
        }
    });
};
