// This file is part of Moodle - http://moodle.org/ //
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

import PluginManagementTable from 'core_admin/plugin_management_table';
import {call as fetchMany} from 'core/ajax';

let watching = false;

/**
 * Handles setting plugin state for the AI provider management table.
 *
 * @module     core_ai/aiprovider_action_management_table
 * @copyright  2024 Matt Porritt <matt.porritt@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
export default class extends PluginManagementTable {

    /**
     * Constructor for the class.
     *
     * @param {int} providerid The provider id.
     */
    constructor(providerid) {
        super(); // Call the parent constructor, so inherited properties and methods initialize properly.
        this.providerid = providerid; // Store provider id as an instance field.
    }

    /**
     * Initialise an instance of the class.
     *
     * @param {int} providerid The provider id.
     */
    static init(providerid) {
        if (watching) {
            return;
        }
        watching = true;
        new this(providerid);
    }

    /**
     * Set the plugin state (enabled or disabled).
     *
     * @param {string} methodname The web service to call.
     * @param {string} plugin The name of the plugin and action to set the state for.
     * @param {number} state The state to set.
     * @returns {Promise}
     */
    setPluginState(methodname, plugin, state) {
        const providerid = this.providerid;
        return fetchMany([{
            methodname,
            args: {
                plugin,
                state,
                providerid,
            },
        }])[0];
    }

}
