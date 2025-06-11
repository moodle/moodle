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
 * Module to handle AJAX interactions.
 *
 * @module     mod_lti/repository
 * @copyright  2023 Ilya Tregubov <ilya.a.tregubov@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import Ajax from 'core/ajax';

/**
 * Toggle coursevisible of a tool
 *
 * @param {Number} tooltypeid Too type id
 * @param {Number} courseid Course ID
 * @param {Number} showinactivitychooser showinactivitychooser state
 * @return {Promise}
 */
export const toggleShowInActivityChooser = (
    tooltypeid,
    courseid,
    showinactivitychooser,
) => Ajax.call([{
    methodname: 'mod_lti_toggle_showinactivitychooser',
    args: {
        tooltypeid,
        courseid,
        showinactivitychooser,
    },
}])[0];
