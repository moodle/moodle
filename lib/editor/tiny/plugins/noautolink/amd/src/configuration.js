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
 * Tiny noautolink configuration.
 *
 * @module      tiny_noautolink/configuration
 * @copyright   2023 Meirza <meirza.arson@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import {component as buttonName} from './common';
import {addQuickbarsToolbarItem, addToolbarButton, addMenubarItem} from 'editor_tiny/utils';

export const configure = (instanceConfig) => {
    // Update the instance configuration to add the noautolink menu option to the menus and toolbars.
    return {
        // eslint-disable-next-line camelcase
        quickbars_selection_toolbar: addQuickbarsToolbarItem(instanceConfig.quickbars_selection_toolbar, '|', buttonName),
        toolbar: addToolbarButton(instanceConfig.toolbar, 'content', buttonName),
        menu: addMenubarItem(instanceConfig.menu, 'format', ' | ' + buttonName),
    };
};
