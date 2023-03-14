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

import PluginManagementTable from './plugin_management_table';
import {refreshTableContent} from 'core_table/dynamic';
import {call as fetchMany} from 'core/ajax';
import Pending from 'core/pending';
import {fetchNotifications} from 'core/notification';

export default class extends PluginManagementTable {
    constructor() {
        super();
        this.addClickHandler(this.handleBlockProtectToggle);
    }

    /**
     * Set the block protection state.
     *
     * @param {string} plugin
     * @param {number} state
     * @returns {Promise}
     */
    setBlockProtectState(plugin, state) {
        return fetchMany([{
            methodname: 'core_admin_set_block_protection',
            args: {
                plugin,
                state,
            },
        }])[0];
    }

    /**
     * Handle toggling of block protection.
     *
     * @param {HTMLElement} tableRoot
     * @param {Event} e
     */
    async handleBlockProtectToggle(tableRoot, e) {
        const stateToggle = e.target.closest('[data-action="toggleprotectstate"]');
        if (stateToggle) {
            e.preventDefault();
            const pendingPromise = new Pending('core_table/dynamic:processAction');

            await this.setBlockProtectState(
                stateToggle.dataset.plugin,
                stateToggle.dataset.targetState === '1' ? 1 : 0
            );

            const [updatedRoot] = await Promise.all([
                refreshTableContent(tableRoot),
                fetchNotifications(),
            ]);

            // Refocus on the link that as pressed in the first place.
            updatedRoot.querySelector(`[data-action="toggleprotectstate"][data-plugin="${stateToggle.dataset.plugin}"]`).focus();
            pendingPromise.resolve();
        }
    }
}
