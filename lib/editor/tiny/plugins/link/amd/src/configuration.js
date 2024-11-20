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
 * Tiny Link configuration.
 *
 * @module      tiny_link/configuration
 * @copyright   2023 Huong Nguyen <huongnv13@gmail.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import {linkButtonShortName, unlinkButtonShortName} from 'tiny_link/common';
import {addToolbarButtons} from 'editor_tiny/utils';

const configureMenu = (menu) => {
    // Replace the standard Link plugin with the Moodle link.
    if (menu.insert.items.match(/\blink\b/)) {
        menu.insert.items = menu.insert.items.replace(/\blink\b/, linkButtonShortName);
    } else {
        menu.insert.items = `${linkButtonShortName} ${menu.insert.items}`;
    }

    return menu;
};

export const configure = (instanceConfig) => {
    // Update the instance configuration to add the Link option to the menus and toolbars.
    return {
        menu: configureMenu(instanceConfig.menu),
        toolbar: addToolbarButtons(instanceConfig.toolbar, 'content', [linkButtonShortName, unlinkButtonShortName]),
    };
};
