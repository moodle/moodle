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
 * Javascript controller for the "Actions" panel at the bottom of the page.
 *
 * @module     mod_assign/batch_set_marking_workflow_state
 * @copyright  2025 onwards Catalyst IT {@link http://www.catalyst-eu.net/}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author     Conn Warwicker <conn.warwicker@catalyst-eu.net>
 */

/**
 * Filter which options are enabled in the select menu.
 *
 * @param {object} context
 * @param {array} args
 */
const filterOptions = (context, args) => {
    const options = args[context];
    const workflowstate = document.getElementById('id_markingworkflowstate');
    [...workflowstate.children].forEach((item) => {
        if (options.includes(item.value)) {
            item.removeAttribute('disabled');
        } else {
            item.setAttribute('disabled', '');
        }
    });
};

/**
 * Initialise scripts.
 *
 * @param {array} args
 */
export const init = (args) => {
    const workflowcontext = document.getElementById('id_workflowcontext');
    filterOptions(workflowcontext.value, args);
    workflowcontext.addEventListener('change', (e) => {
        filterOptions(e.target.value, args);
    });
};