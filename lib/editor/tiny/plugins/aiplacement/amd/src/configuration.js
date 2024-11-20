// This file is part of Moodle - https://moodle.org/
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
// along with Moodle.  If not, see <https://www.gnu.org/licenses/>.

/**
 * Tiny tiny_aiplacement for Moodle.
 *
 * @module      tiny_aiplacement/configuration
 * @copyright   2024 Matt Porritt <matt.porritt@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import {
    contextMenuName,
    generateImageName,
    generateTextName
} from './common';
import {
    addMenubarItem,
} from 'editor_tiny/utils';

const configureMenu = (menu) => {
    const items = menu.insert.items.split(' ');
    const inserted = items.some((item, index) => {
        // Append after the media or video button.
        if (item.match(/(media)\b/)) {
            items.splice(index + 1, 0, generateImageName, generateTextName);
            return true;
        }

        return false;
    });

    if (inserted) {
        menu.insert.items = items.join(' ');
    } else {
        addMenubarItem(menu, 'insert', `${generateImageName} ${generateTextName}`);
    }

    return menu;
};

const configureToolbar = (toolbar) => {
    // The toolbar contains an array of named sections.
    // The Moodle integration ensures that there is a section called 'content'.

    return toolbar.map((section) => {
        if (section.name === 'content') {
            const inserted = section.items.some((item, index) => {
                // Append after the media or video button.
                if (item.match(/(media)\b/)) {
                    section.items.splice(index + 1, 0, contextMenuName, generateImageName, generateTextName);
                    return true;
                }
                return false;
            });

            if (!inserted) {
                section.items.unshift(contextMenuName, generateImageName, generateTextName);
            }
        }

        return section;
    });
};

export const configure = (instanceConfig) => {
    // Update the instance configuration to add the Media menu option to the menus and toolbars and upload_handler.
    return {
        toolbar: configureToolbar(instanceConfig.toolbar),
        menu: configureMenu(instanceConfig.menu),
    };
};
