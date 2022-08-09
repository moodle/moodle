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

import {renderForPromise} from 'core/templates';
import {getFilePicker} from './options';

/**
 * Get the image path for the specified image.
 *
 * @param {string} identifier The name of the image
 * @param {string} component The component name
 * @return {string} The image URL path
 */
export const getImagePath = (identifier, component = 'editor_tiny') => Promise.resolve(M.util.image_url(identifier, component));

export const getButtonImage = async(identifier, component = 'editor_tiny') => renderForPromise('editor_tiny/toolbar_button', {
    image: await getImagePath(identifier, component),
});

/**
 * Helper to display a filepicker and return a Promise.
 *
 * The Promise will resolve when a file is selected, or reject if the file type is not found.
 *
 * @param {TinyMCE} editor
 * @param {string} filetype
 * @returns {Promise<object>} The file object returned by the filepicker
 */
export const displayFilepicker = (editor, filetype) => new Promise((resolve, reject) => {
    const configuration = getFilePicker(editor, filetype);
    if (configuration) {
        const options = {
            ...configuration,
            formcallback: resolve,
        };
        M.core_filepicker.show(Y, options);
        return;
    }
    reject(`Unknown filetype ${filetype}`);
});

/**
 * Given a TinyMCE Toolbar configuration, add the specified button to the named section.
 *
 * @param {object} toolbar
 * @param {string} section
 * @param {string} button
 * @returns {object} The toolbar configuration
 */
export const addToolbarButton = (toolbar, section, button) => {
    if (!toolbar) {
        return [{
            name: section,
            items: [button],
        }];
    }

    const mutatedToolbar = JSON.parse(JSON.stringify(toolbar));
    return mutatedToolbar.map((item) => {
        if (item.name === section) {
            item.items.push(button);
        }

        return item;
    });
};

/**
 * Given a TinyMCE Menubar configuration, add the specified button to the named section.
 *
 * @param {object} menubar
 * @param {string} section
 * @param {string} menuitem
 * @returns {object}
 */
export const addMenubarItem = (menubar, section, menuitem) => {
    if (!menubar) {
        const emptyMenubar = {};
        emptyMenubar[section] = {
            title: section,
            items: menuitem,
        };
    }

    const mutatedMenubar = JSON.parse(JSON.stringify(menubar));
    Array.from(Object.entries(mutatedMenubar)).forEach(([name, menu]) => {
        if (name === section) {
            menu.items = `${menu.items} ${menuitem}`;
        }
    });

    return mutatedMenubar;
};

/**
 * Given a TinyMCE contextmenu configuration, add the specified button to the end.
 *
 * @param {string} contextmenu
 * @param {string[]} menuitems
 * @returns {string}
 */
export const addContextmenuItem = (contextmenu, ...menuitems) => {
    const contextmenuItems = (contextmenu ?? '').split(' ');

    return contextmenuItems
        .concat(menuitems)
        .filter((item) => item !== '')
        .join(' ');
};
