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
 * JavaScript module to work with the auto-complete of users.
 *
 * @module     report_lsusql/userselector
 * @copyright  2020 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import Ajax from 'core/ajax';
import Templates from 'core/templates';

/**
 * Source of data for Ajax element.
 *
 * @param {String} selector The selector of the auto complete element.
 * @param {String} query The query string.
 * @param {Function} success To be called with the results, when received.
 * @param {Function} failure To be called with any errors.
 */
export function transport(selector, query, success, failure) {
    Ajax.call([{
        methodname: 'report_lsusql_get_users',
        args: {
            query: query,
            capability: document.getElementById('id_capability').value
        }
    }])[0]

    .then((results) => {
        // For each user in the result, render the display, and set it on the _label field.
        return Promise.all(results.map((user) => {
            return Templates.render('report_lsusql/form-user-selector-suggestion', user)
                .then((html) => {
                    user._label = html;
                    return user;
                });
        }));
    })

    .then(success)
    .catch(failure);
}

/**
 * Process the results for auto complete elements.
 *
 * @param {String} selector The selector of the auto complete element.
 * @param {Array} results An array or results.
 * @return {Array} New array of results.
 */
export function processResults(selector, results) {
    return results.map((user) => {
        return {
            value: user.id,
            label: user._label
        };
    });
}
