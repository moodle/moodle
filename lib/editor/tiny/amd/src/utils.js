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
 * Get the plugin configuration for the specified plugin.
 *
 * @param {TinyMCE} editor
 * @param {string} plugin
 * @returns {object} The plugin configuration
 */
export const getPluginConfiguration = (editor, plugin) => {
    const config = editor.moodleOptions.plugins[`tiny_${plugin}/plugin`]?.config;

    if (!config) {
        return {};
    }

    return config;
};

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
    if (editor.moodleOptions.filepicker[filetype]) {
        const options = {
            ...editor.moodleOptions.filepicker[filetype],
            formcallback: resolve,
        };
        M.core_filepicker.show(Y, options);
        return;
    }
    reject(`Unknown filetype ${filetype}`);
});
