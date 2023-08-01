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
import {prefetchStrings} from 'core/prefetch';
import {get_string as getString} from 'core/str';
import {refreshTableContent} from 'core_table/dynamic';
import * as Selectors from 'core_table/local/dynamic/selectors';

/**
 * Initialise module.
 */
export const init = () => {
    prefetchStrings('mod_lti', [
        'deletecoursetool',
        'deletecoursetoolconfirm',
        'coursetooldeleted'
    ]);

    prefetchStrings('core', [
        'delete',
    ]);

    document.addEventListener('click', event => {

        const courseToolDelete = event.target.closest('[data-action="course-tool-delete"]');
        if (courseToolDelete) {
            event.preventDefault();

            // Use triggerElement to return focus to the action menu toggle.
            const triggerElement = courseToolDelete.closest('.dropdown').querySelector('.dropdown-toggle');
            Notification.saveCancelPromise(
                getString('deletecoursetool', 'mod_lti'),
                getString('deletecoursetoolconfirm', 'mod_lti', courseToolDelete.dataset.courseToolName),
                getString('delete', 'core'),
                {triggerElement}
            ).then(() => {
                const pendingPromise = new Pending('mod_lti/course_tools:delete');

                const request = {
                    methodname: 'mod_lti_delete_course_tool_type',
                    args: {tooltypeid: courseToolDelete.dataset.courseToolId}
                };
                return Ajax.call([request])[0]
                    .then(addToast(getString('coursetooldeleted', 'mod_lti')))
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
    });
};
