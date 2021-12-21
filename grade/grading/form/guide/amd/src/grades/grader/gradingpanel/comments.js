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
 * Grading panel frequently used comments selector.
 *
 * @module     gradingform_guide/grades/grader/gradingpanel/comments
 * @copyright  2019 Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import Selectors from './comments/selectors';

/**
 * Manage the frequently used comments in the Marking Guide form.
 *
 * @param {String} rootId
 */
export const init = (rootId) => {
    const rootNode = document.querySelector(`#${rootId}`);

    rootNode.addEventListener('click', (e) => {
        if (!e.target.matches(Selectors.frequentComment)) {
            return;
        }

        e.preventDefault();

        const clicked = e.target.closest(Selectors.frequentComment);
        const criterion = clicked.closest(Selectors.criterion);
        const remark = criterion.querySelector(Selectors.remark);

        if (!remark) {
            return;
        }

        // Either append the comment to an existing comment or set it as the comment.
        if (remark.value.trim()) {
            remark.value += `\n${clicked.innerHTML}`;
        } else {
            remark.value += clicked.innerHTML;
        }
    });
};
