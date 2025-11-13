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
 * Tiny Media configuration.
 *
 * @module      tiny_media/configuration
 * @copyright   2022 Huong Nguyen <huongnv13@gmail.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import {
    imageButtonName,
    videoButtonName,
    mediaManagerButtonName,
} from './common';
import uploadFile from 'editor_tiny/uploader';
import {
    addContextmenuItem,
} from 'editor_tiny/utils';

const configureMenu = (menu) => {
    // Replace the standard Media plugin with the Moodle embed.
    if (menu.insert.items.match(/\bmedia\b/)) {
        menu.insert.items = menu.insert.items.replace(/\bmedia\b/, videoButtonName);
    } else {
        menu.insert.items = `${videoButtonName} ${menu.insert.items}`;
    }

    // Replace the standard image plugin with the Moodle image.
    if (menu.insert.items.match(/\bimage\b/)) {
        menu.insert.items = menu.insert.items.replace(/\bimage\b/, imageButtonName);
    } else {
        menu.insert.items = `${imageButtonName} ${menu.insert.items}`;
    }

    // Add the Media Manager to the end of the Tools menu.
    menu.tools.items += ` ${mediaManagerButtonName}`;

    return menu;
};

const configureToolbar = (toolbar) => {
    // The toolbar contains an array of named sections.
    // The Moodle integration ensures that there is a section called 'content'.

    return toolbar.map((section) => {
        if (section.name === 'content') {
            // Insert the image, and embed, buttons at the start of it.
            section.items.unshift(imageButtonName, videoButtonName);
        }

        return section;
    });
};

export const configure = (instanceConfig) => {
    // Update the instance configuration to add the Media menu option to the menus and toolbars and upload_handler.
    return {
        contextmenu: addContextmenuItem(instanceConfig.contextmenu, imageButtonName, videoButtonName),
        menu: configureMenu(instanceConfig.menu),
        toolbar: configureToolbar(instanceConfig.toolbar),

        // eslint-disable-next-line camelcase
        images_upload_handler: (blobInfo, progress) => uploadFile(
            window.tinymce.activeEditor,
            'image',
            blobInfo.blob(),
            blobInfo.filename(),
            progress
        ),

        // eslint-disable-next-line camelcase
        images_reuse_filename: true,
    };
};
