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
 * Javascript to handle survey validation.
 *
 * @module     mod_survey/validation
 * @copyright  2017 Dan Poltawski <dan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since      3.3
 */
import {get_string as getString} from 'core/str';
import Notification from 'core/notification';

/**
 * Prevents form submission until all radio buttons are chosen, displays
 * modal error if any choices are missing.
 *
 * @param {String} formid HTML id of form
 */
export const ensureRadiosChosen = (formid) => {
    const form = document.getElementById(formid);
    form.addEventListener('submit', (e) => {
        const optionsToSet = form.querySelectorAll('input[type="radio"][data-survey-default="true"]:checked');
        if (optionsToSet.length !== 0) {
            Notification.alert(
                getString('error'),
                getString('questionsnotanswered', 'survey'),
                getString('ok'),
            );
            e.preventDefault();
        }
    });
};
