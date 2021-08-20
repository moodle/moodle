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
 * @module     core_courseformat/local/courseeditor/mutations
 * @class     core_courseformat/local/courseeditor/mutations
 * @copyright  2021 Ferran Recio <ferran@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
export default class {

    // All course editor mutations for Moodle 4.0 will be located in this file.

    /**
     * Private method to call core_courseformat_update_course webservice.
     *
     * @method _callEditWebservice
     * @param {string} action
     * @param {number} courseId
     * @param {array} ids
     */
    async _callEditWebservice(action, courseId, ids) {
        let ajaxresult = await ajax.call([{
            methodname: 'core_courseformat_update_course',
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
     * @param {StateManager} stateManager the current state
     * @param {array} cmids the list of cm ids to update
     */
    async cmState(stateManager, cmids) {
        const course = stateManager.get('course');
        const updates = await this._callEditWebservice('cm_state', course.id, cmids);
        stateManager.processUpdates(updates);
    }

    /**
     * Get updated state data related to some section ids.
     *
     * @method sectionState
     * @param {StateManager} stateManager the current state
     * @param {array} sectionIds the list of section ids to update
     */
    async sectionState(stateManager, sectionIds) {
        const course = stateManager.get('course');
        const updates = await this._callEditWebservice('section_state', course.id, sectionIds);
        stateManager.processUpdates(updates);
    }

    /**
    * Get the full updated state data of the course.
    *
    * @param {StateManager} stateManager the current state
    */
    async courseState(stateManager) {
        const course = stateManager.get('course');
        const updates = await this._callEditWebservice('course_state', course.id);
        stateManager.processUpdates(updates);
    }

}
