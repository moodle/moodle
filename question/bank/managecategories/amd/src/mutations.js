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

import {setUserPreference} from 'core_user/repository';

/**
 * Default mutation manager
 *
 * @module qbank_managecategories/mutations
 */
class Mutations {
    /**
     * Set the showdescriptions property to true or false in the page's state.
     *
     * @param {stateManager} stateManager
     * @param {boolean} showDescriptions
     * @return {Promise<void>}
     */
    async toggleDescriptions(stateManager, showDescriptions) {
        stateManager.setReadOnly(false);
        await setUserPreference('qbank_managecategories_showdescriptions', showDescriptions);
        stateManager.state.page.showdescriptions = showDescriptions;
        stateManager.setReadOnly(true);
    }

    /**
     * Set the draghandle property to true in a given category's state.
     *
     * @param {stateManager} stateManager
     * @param {Number} categoryId
     * @return {Promise<void>}
     */
    async showDragHandle(stateManager, categoryId) {
        stateManager.setReadOnly(false);
        stateManager.state.categories.get(categoryId).draghandle = true;
        stateManager.setReadOnly(true);
    }
}

export const mutations = new Mutations();
