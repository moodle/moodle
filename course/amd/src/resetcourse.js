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
 * Javascript module for resetting a course
 *
 * @module      core_course/resetcourse
 * @copyright   2024 Sara Arjona <sara@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import Notification from 'core/notification';
import {prefetchStrings} from 'core/prefetch';
import {getString} from 'core/str';

const selectors = {
    resetCoursetButton: '[data-action="resetcourse"]',
};

/**
 * Initialize module
 */
export const init = () => {
    prefetchStrings('core', [
        'resetcourseconfirm',
        'resetcoursewarning',
        'resetcourse',
    ]);

    registerEventListeners();
};

/**
 * Register events for course reset button.
 */
const registerEventListeners = () => {
    document.addEventListener('click', (event) => {
        const resetButton = event.target.closest(selectors.resetCoursetButton);
        if (resetButton) {
            event.preventDefault();
            resetCourseConfirm(resetButton);
        }
    });
};

/**
 * Show the confirmation modal to reset the course.
 *
 * @param {HTMLElement} resetButton the element to delete.
 *
 */
const resetCourseConfirm = async(resetButton) => {
    const courseName = resetButton.dataset.coursename;

    Notification.deleteCancelPromise(
        getString('resetcourseconfirm'),
        getString('resetcoursewarning', 'core', courseName),
        getString('resetcourse'),
    ).then(() => {
        resetButton.closest('form').submit();
        return;
    }).catch(() => {
        return;
    });
};
