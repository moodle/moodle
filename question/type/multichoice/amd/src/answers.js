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
 * Handles events related to the multiple-choice question type answers.
 *
 * @module     qtype_multichoice/answers
 * @package    qtype_multichoice
 * @copyright  2020 Jun Pataleta <jun@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Selectors for this module.
 *
 * @type {{ANSWER_LABEL: string}}
 */
const SELECTORS = {
    ANSWER_LABEL: '[data-region=answer-label]',
};

/**
 * Init method.
 *
 * @param {string} rootId The ID of the question container.
 */
const init = (rootId) => {
    const root = document.getElementById(rootId);

    // Add click event handlers for the divs containing the answer since these cannot be enclosed in a label element.
    const answerLabels = root.querySelectorAll(SELECTORS.ANSWER_LABEL);
    answerLabels.forEach((answerLabel) => {
        answerLabel.addEventListener('click', (e) => {
            const labelId = e.currentTarget.id;
            // Fetch the answer this label is assigned to.
            const linkedOption = root.querySelector(`[aria-labelledby="${labelId}"]`);
            // Trigger the click event.
            linkedOption.click();
        });
    });
};

export default {
    init: init
};
