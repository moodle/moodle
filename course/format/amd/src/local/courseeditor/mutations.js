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
     * Mutation module initialize.
     *
     * The reactive instance will execute this method when addMutations or setMutation is invoked.
     *
     * @param {StateManager} stateManager the state manager
     */
    init(stateManager) {
        // Add a method to prepare the fields when some update is comming from the server.
        stateManager.addUpdateTypes({
            prepareFields: this._prepareFields,
        });
    }

    /**
     * Add default values to state elements.
     *
     * This method is called every time a webservice returns a update state message.
     *
     * @param {Object} stateManager the state manager
     * @param {String} updateName the state element to update
     * @param {Object} fields the new data
     * @returns {Object} final fields data
     */
    _prepareFields(stateManager, updateName, fields) {
        // Any update should unlock the element.
        fields.locked = false;
        return fields;
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
        this.cmLock(stateManager, cmids, true);
        const updates = await this._callEditWebservice('cm_move', course.id, cmids, targetSectionId, targetCmId);
        stateManager.processUpdates(updates);
        this.cmLock(stateManager, cmids, false);
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
        this.sectionLock(stateManager, sectionIds, true);
        const updates = await this._callEditWebservice('section_move', course.id, sectionIds, targetSectionId);
        stateManager.processUpdates(updates);
        this.sectionLock(stateManager, sectionIds, false);
    }

    /**
     * Add a new section to a specific course location.
     *
     * @param {StateManager} stateManager the current state manager
     * @param {number} targetSectionId optional the target section id
     */
    async addSection(stateManager, targetSectionId) {
        if (!targetSectionId) {
            targetSectionId = 0;
        }
        const course = stateManager.get('course');
        const updates = await this._callEditWebservice('section_add', course.id, [], targetSectionId);
        stateManager.processUpdates(updates);
    }

    /**
     * Delete sections.
     *
     * @param {StateManager} stateManager the current state manager
     * @param {array} sectionIds the list of course modules ids
     */
    async sectionDelete(stateManager, sectionIds) {
        const course = stateManager.get('course');
        const updates = await this._callEditWebservice('section_delete', course.id, sectionIds);
        stateManager.processUpdates(updates);
    }

    /**
     * Mark or unmark course modules as dragging.
     *
     * @param {StateManager} stateManager the current state manager
     * @param {array} cmIds the list of course modules ids
     * @param {bool} dragValue the new dragging value
     */
    cmDrag(stateManager, cmIds, dragValue) {
        this._setElementsValue(stateManager, 'cm', cmIds, 'dragging', dragValue);
    }

    /**
     * Mark or unmark course sections as dragging.
     *
     * @param {StateManager} stateManager the current state manager
     * @param {array} sectionIds the list of section ids
     * @param {bool} dragValue the new dragging value
     */
    sectionDrag(stateManager, sectionIds, dragValue) {
        this._setElementsValue(stateManager, 'section', sectionIds, 'dragging', dragValue);
    }

    /**
     * Mark or unmark course modules as complete.
     *
     * @param {StateManager} stateManager the current state manager
     * @param {array} cmIds the list of course modules ids
     * @param {bool} complete the new completion value
     */
    cmCompletion(stateManager, cmIds, complete) {
        const newValue = (complete) ? 1 : 0;
        this._setElementsValue(stateManager, 'cm', cmIds, 'completionstate', newValue);
    }

    /**
     * Lock or unlock course modules.
     *
     * @param {StateManager} stateManager the current state manager
     * @param {array} cmIds the list of course modules ids
     * @param {bool} lockValue the new locked value
     */
    cmLock(stateManager, cmIds, lockValue) {
        this._setElementsValue(stateManager, 'cm', cmIds, 'locked', lockValue);
    }

    /**
     * Lock or unlock course sections.
     *
     * @param {StateManager} stateManager the current state manager
     * @param {array} sectionIds the list of section ids
     * @param {bool} lockValue the new locked value
     */
    sectionLock(stateManager, sectionIds, lockValue) {
        this._setElementsValue(stateManager, 'section', sectionIds, 'locked', lockValue);
    }

    _setElementsValue(stateManager, name, ids, fieldName, newValue) {
        stateManager.setReadOnly(false);
        ids.forEach((id) => {
            const element = stateManager.get(name, id);
            if (element) {
                element[fieldName] = newValue;
            }
        });
        stateManager.setReadOnly(true);
    }

    /**
     * Unlock all course elements.
     *
     * @param {StateManager} stateManager the current state manager
     */
    unlockAll(stateManager) {
        const state = stateManager.state;
        stateManager.setReadOnly(false);
        state.section.forEach((section) => {
            section.locked = false;
        });
        state.cm.forEach((cm) => {
            cm.locked = false;
        });
        stateManager.setReadOnly(true);
    }

    /*
     * Get updated user preferences and state data related to some section ids.
     *
     * @param {StateManager} stateManager the current state
     * @param {array} sectionIds the list of section ids to update
     * @param {Object} preferences the new preferences values
     */
    async sectionPreferences(stateManager, sectionIds, preferences) {
        stateManager.setReadOnly(false);
        // Check if we need to update preferences.
        let updatePreferences = false;
        sectionIds.forEach(sectionId => {
            const section = stateManager.get('section', sectionId);
            if (section === undefined) {
                return;
            }
            let newValue = preferences.contentcollapsed ?? section.contentcollapsed;
            if (section.contentcollapsed != newValue) {
                section.contentcollapsed = newValue;
                updatePreferences = true;
            }
            newValue = preferences.indexcollapsed ?? section.indexcollapsed;
            if (section.indexcollapsed != newValue) {
                section.indexcollapsed = newValue;
                updatePreferences = true;
            }
        });
        stateManager.setReadOnly(true);

        if (updatePreferences) {
            // Build the preference structures.
            const course = stateManager.get('course');
            const state = stateManager.state;
            const prefKey = `coursesectionspreferences_${course.id}`;
            const preferences = {
                contentcollapsed: [],
                indexcollapsed: [],
            };
            state.section.forEach(section => {
                if (section.contentcollapsed) {
                    preferences.contentcollapsed.push(section.id);
                }
                if (section.indexcollapsed) {
                    preferences.indexcollapsed.push(section.id);
                }
            });
            const jsonString = JSON.stringify(preferences);
            M.util.set_user_preference(prefKey, jsonString);
        }
    }

    /**
     * Get updated state data related to some cm ids.
     *
     * @method cmState
     * @param {StateManager} stateManager the current state
     * @param {array} cmids the list of cm ids to update
     */
    async cmState(stateManager, cmids) {
        this.cmLock(stateManager, cmids, true);
        const course = stateManager.get('course');
        const updates = await this._callEditWebservice('cm_state', course.id, cmids);
        stateManager.processUpdates(updates);
        this.cmLock(stateManager, cmids, false);
    }

    /**
     * Get updated state data related to some section ids.
     *
     * @method sectionState
     * @param {StateManager} stateManager the current state
     * @param {array} sectionIds the list of section ids to update
     */
    async sectionState(stateManager, sectionIds) {
        this.sectionLock(stateManager, sectionIds, true);
        const course = stateManager.get('course');
        const updates = await this._callEditWebservice('section_state', course.id, sectionIds);
        stateManager.processUpdates(updates);
        this.sectionLock(stateManager, sectionIds, false);
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
