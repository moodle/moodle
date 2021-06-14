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

import ajax from 'core/ajax';

/**
 * Default mutation manager
 *
 * @module     core_course/local/courseeditor/mutations
 * @class     core_course/local/courseeditor/mutations
 * @copyright  2021 Ferran Recio <ferran@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
export default class {

    // All course editor mutations for Moodle 4.0 will be located in this file.

    /**
     * Private method to call core_course_update_course webservice.
     *
     * @method _callEditWebservice
     * @param {string} action
     * @param {int} courseId
     * @param {array} ids
     */
    async _callEditWebservice(action, courseId, ids) {
        let ajaxresult = await ajax.call([{
            methodname: 'core_course_update_course',
            args: {
                action,
                courseid: courseId,
                ids,
            }
        }])[0];
        return JSON.parse(ajaxresult);
    }

    /**
     * Get updated state data related to some cm ids.
     *
     * @method cmState
     * @param {StateManager} statemanager the current state
     * @param {array} cmids the list of cm ids to update
     */
    async cmState(statemanager, cmids) {
        const state = statemanager.state;
        const updates = await this._callEditWebservice('cm_state', state.course.id, cmids);
        statemanager.setReadOnly(false);
        this._processUpdates(statemanager, updates);
    }

    /**
     * Get updated state data related to some section ids.
     *
     * @method sectionState
     * @param {StateManager} statemanager the current state
     * @param {array} sectionIds the list of section ids to update
     */
    async sectionState(statemanager, sectionIds) {
        const state = statemanager.state;
        const updates = await this._callEditWebservice('section_state', state.course.id, sectionIds);
        this._processUpdates(statemanager, updates);
    }

    /**
     * Helper to propcess both section_state and cm_state action results.
     *
     * @param {StateManager} statemanager the current state
     * @param {Array} updates of updates.
     */
    _processUpdates(statemanager, updates) {

        const state = statemanager.state;

        statemanager.setReadOnly(false);

        // The cm_state and section_state state action returns only updated states. However, most of the time we need this
        // mutation to fix discrepancies between the course content and the course state because core_course_edit_module
        // does not provide enough information to rebuild some state objects. This is the reason why we cannot use
        // the batch method processUpdates as the rest of mutations do.
        updates.forEach((update) => {
            if (update.name === undefined) {
                throw Error('Missing state update name');
            }
            // Compare the action with the current state.
            let current = state[update.name];
            if (current instanceof Map) {
                current = state[update.name].get(update.fields.id);
            }
            if (!current) {
                update.action = 'create';
            }

            statemanager.processUpdate(update.name, update.action, update.fields);
        });

        statemanager.setReadOnly(true);
    }
}
