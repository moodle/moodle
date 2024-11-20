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
 * Option helper for TinyMCE Editor Manager.
 *
 * @module editor_tiny/options
 * @copyright  2022 Andrew Lyons <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

const optionContextId = 'moodle:contextid';
const optionDraftItemId = 'moodle:draftitemid';
const filePickers = 'moodle:filepickers';
const optionsMoodleLang = 'moodle:language';
const currentLanguage = 'moodle:currentLanguage';
const optionPlaceholderSelectors = 'moodle:placeholderSelectors';

export const register = (editor, options) => {
    const registerOption = editor.options.register;
    const setOption = editor.options.set;

    registerOption(optionContextId, {
        processor: 'number',
        "default": 0,
    });
    setOption(optionContextId, options.context);

    registerOption(filePickers, {
        processor: 'object',
        "default": {},
    });
    setOption(filePickers, Object.assign({}, options.filepicker));

    registerOption(optionDraftItemId, {
        processor: 'number',
        "default": 0,
    });
    setOption(optionDraftItemId, options.draftitemid);

    registerOption(currentLanguage, {
        processor: 'string',
        "default": 'en',
    });
    setOption(currentLanguage, options.currentLanguage);

    // This is primarily used by the media plugin, but it may be re-used elsewhere so is included here as it is large.
    registerOption(optionsMoodleLang, {
        processor: 'object',
        "default": {},
    });
    setOption(optionsMoodleLang, options.language);

    registerOption(optionPlaceholderSelectors, {
        processor: 'array',
        "default": [],
    });
    setOption(optionPlaceholderSelectors, options.placeholderSelectors);
};

export const getContextId = (editor) => editor.options.get(optionContextId);
export const getDraftItemId = (editor) => editor.options.get(optionDraftItemId);
export const getFilepickers = (editor) => editor.options.get(filePickers);
export const getFilePicker = (editor, type) => getFilepickers(editor)[type];
export const getMoodleLang = (editor) => editor.options.get(optionsMoodleLang);
export const getCurrentLanguage = (editor) => editor.options.get(currentLanguage);

/**
 * Get a set of namespaced options for all defined plugins.
 *
 * @param {object} options
 * @returns {object}
 */
export const getInitialPluginConfiguration = (options) => {
    const config = {};

    Object.entries(options.plugins).forEach(([pluginName, pluginConfig]) => {
        const values = Object.entries(pluginConfig.config ?? {});
        values.forEach(([optionName, value]) => {
            config[getPluginOptionName(pluginName, optionName)] = value;
        });
    });

    return config;
};

/**
 * Get the namespaced option name for a plugin.
 *
 * @param {string} pluginName
 * @param {string} optionName
 * @returns {string}
 */
export const getPluginOptionName = (pluginName, optionName) => `${pluginName}:${optionName}`;

/**
 * Get the placeholder selectors.
 *
 * @param {TinyMCE} editor
 * @returns {array}
 */
export const getPlaceholderSelectors = (editor) => editor.options.get(optionPlaceholderSelectors);

/**
 * Register placeholder selectos.
 *
 * @param {TinyMCE} editor
 * @param {array} selectors
 */
export const registerPlaceholderSelectors = (editor, selectors) => {
    if (selectors.length) {
        let existingData = getPlaceholderSelectors(editor);
        existingData = existingData.concat(selectors);
        editor.options.set(optionPlaceholderSelectors, existingData);
    }
};
