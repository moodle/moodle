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
 * Module to handle AJAX interactions with content bank upload files.
 *
 * @module     core_contentbank/displayunlisted
 * @copyright  2023 Daniel Neis Araujo
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
import {call as fetchMany} from 'core/ajax';

/**
 * Initialize upload files to the content bank form as Modal form.
 *
 * @param {String} elementSelector
 */
export const update = (elementSelector) => {
    const element = document.querySelector(elementSelector);
    element.addEventListener('click', function() {
        const args = {
            userid: this.userId,
            preferences: [
                {
                    type: 'core_contentbank_displayunlisted',
                    value: !!element.checked,
                }
            ],
        };
        fetchMany([{
            methodname: 'core_user_update_user_preferences',
            args: args,
        }])[0].done(function() {
            document.location.reload();
        });
    });
};
