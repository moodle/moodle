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
 * Javascript for preview.
 *
 * @module     qbank_previewquestion/preview
 * @copyright  2021 Catalyst IT Australia Pty Ltd
 * @author     Safat Shahin <safatshahin@catalyst-au.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import {initForm as initQuestionEngineForm} from 'core_question/question_engine';

/**
 * Set up the actions.
 *
 * @method init
 * @param {bool} redirect Redirect.
 * @param {string} url url to redirect.
 */
export const init = (redirect, url) => {
    if (!redirect) {
        const closeButton = document.getElementById('close-previewquestion-page');
        closeButton.addEventListener('click', (e) => {
            e.preventDefault();
            if (window.opener === null) {
                location.href = url;
            } else {
                window.close();
            }
        });
    }
    // Set up the form to be displayed.
    initQuestionEngineForm('#responseform');
};
