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
 * Module to handle presets AJAX requests
 *
 * @module     tool_admin_presets/repository
 * @copyright  2024 David Carrillo <davidmc@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import Ajax from 'core/ajax';

/**
 * Delete given preset
 *
 * @param {Number} presetId
 * @return {Promise}
 */
export const deletePreset = presetId => {
    const request = {
        methodname: 'tool_admin_presets_delete_preset',
        args: {id: presetId}
    };

    return Ajax.call([request])[0];
};
