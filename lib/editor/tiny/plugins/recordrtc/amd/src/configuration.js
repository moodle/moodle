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
 * Tiny Record RTC configuration.
 *
 * @module      tiny_recordrtc/configuration
 * @copyright   2022, Stevani Andolo <stevani@hotmail.com.au>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import {
    audioButtonName,
    videoButtonName
} from './common';
import {
    addMenubarItem,
} from 'editor_tiny/utils';

const configureMenu = (menu) => {
    const items = menu.insert.items.split(' ');
    const inserted = items.some((item, index) => {
        // Append after the media or video button.
        if (item.match(/(media|video)\b/)) {
            items.splice(index + 1, 0, audioButtonName, videoButtonName);
            return true;
        }

        return false;
    });

    if (inserted) {
        menu.insert.items = items.join(' ');
    } else {
        addMenubarItem(menu, 'insert', `${audioButtonName} ${videoButtonName}`);
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
                if (item.match(/(media|video)\b/)) {
                    section.items.splice(index + 1, 0, audioButtonName, videoButtonName);
                    return true;
                }
                return false;
            });

            if (!inserted) {
                section.items.unshift(audioButtonName, videoButtonName);
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
