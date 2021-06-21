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
     * @param {number} targetSectionId optional target section id (for moving actions)
     * @param {number} targetCmId optional target cm id (for moving actions)
     */
    async _callEditWebservice(action, courseId, ids, targetSectionId, targetCmId) {
        const args = {
            action,
            courseid: courseId,
            ids,
        };
        if (targetSectionId) {
            args.targetsectionid = targetSectionId;
        }
        if (targetCmId) {
            args.targetcmid = targetCmId;
        }
        let ajaxresult = await ajax.call([{
            methodname: 'core_courseformat_update_course',
            args,
        }])[0];
        return JSON.parse(ajaxresult);
    }

    /**
     * Move course modules to specific course location.
     *
     * Note that one of targetSectionId or targetCmId should be provided in order to identify the
     * new location:
     *  - targetCmId: the activities will be located avobe the target cm. The targetSectionId
     *                value will be ignored in this case.
     *  - targetSectionId: the activities will be appended to the section. In this case
     *                     targetSectionId should not be present.
     *
     * @param {StateManager} stateManager the current state manager
     * @param {array} cmids the list of cm ids to move
     * @param {number} targetSectionId the target section id
     * @param {number} targetCmId the target course module id
     */
    async cmMove(stateManager, cmids, targetSectionId, targetCmId) {
        if (!targetSectionId && !targetCmId) {
            throw new Error(`Mutation cmMove requires targetSectionId or targetCmId`);
        }
        const course = stateManager.get('course');
        const updates = await this._callEditWebservice('cm_move', course.id, cmids, targetSectionId, targetCmId);
        stateManager.processUpdates(updates);
    }

    /**
     * Move course modules to specific course location.
     *
     * @param {StateManager} stateManager the current state manager
     * @param {array} sectionIds the list of section ids to move
     * @param {number} targetSectionId the target section id
     */
    async sectionMove(stateManager, sectionIds, targetSectionId) {
        if (!targetSectionId) {
            throw new Error(`Mutation sectionMove requires targetSectionId`);
        }
        const course = stateManager.get('course');
        const updates = await this._callEditWebservice('section_move', course.id, sectionIds, targetSectionId);
        stateManager.processUpdates(updates);
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
