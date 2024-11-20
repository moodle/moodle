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
 * Event handlers for the mod_lti mod_form.
 *
 * @module      mod_lti/mod_form
 * @copyright   2023 Jake Dallimore <jrhdallimore@gmail.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

"use strict";

import ContentItem from 'mod_lti/contentitem';

/**
 * Initialise module.
 *
 * @param {int} courseId the course id.
 */
const init = (courseId) => {
    const contentItemButton = document.querySelector('[name="selectcontent"]');

    if (!contentItemButton) {
        return;
    }

    contentItemButton.addEventListener('click', () => {
        const contentItemUrl = contentItemButton.getAttribute('data-contentitemurl');
        const contentItemId = document.querySelector('#hidden_typeid').value;
        if (contentItemId) {
            const title = document.querySelector('#id_name').value.trim();
            const text = document.querySelector('#id_introeditor').value.trim();
            const postData = {
                id: contentItemId,
                course: courseId,
                title: title,
                text: text
            };

            // The callback below is called after the content item has been returned and processed.
            ContentItem.init(contentItemUrl, postData, (returnData) => {
                if (!returnData.multiple) {
                    // The state of the grade checkbox has already been set by processContentItemReturnData() but that
                    // hasn't fired the click/change event required by formslib to show/hide the dependent grade fields.
                    // Fire it now.
                    const allowGrades = document.querySelector('#id_instructorchoiceacceptgrades');
                    let allowGradesChangeEvent = new Event('change');
                    allowGrades.dispatchEvent(allowGradesChangeEvent);

                    // If the tool is set to accept grades, make sure "Point" is selected.
                    if (allowGrades.checked) {
                        const gradeType = document.querySelector('#id_grade_modgrade_type');
                        gradeType.value = "point";
                        let gradeTypeChangeEvent = new Event('change');
                        gradeType.dispatchEvent(gradeTypeChangeEvent);
                    }
                }
            });
        }
    });
};

export default {
    init: init
};
