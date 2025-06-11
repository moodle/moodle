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
 * Options helper for the Moodle tiny_wordimport plugin.
 *
 * @module      tiny_wordimport/options
 * @copyright   2023 University of Graz
 * @author      André Menrath <andre.menrath@uni-graz.at>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import {
    getPluginOptionName,
} from 'editor_tiny/options';

import {pluginName} from './common';

// Helper variables for the option names.
const heading1StyleLevelName = getPluginOptionName(pluginName, 'heading1StyleLevel');
const wordFilePickerOptionName = getPluginOptionName(pluginName, 'wordFilePickerOption');

/**
 * Options registration function.
 *
 * @param {tinyMCE} editor
 */
export const register = (editor) => {
    const registerOption = editor.options.register;
    const getOption = editor.options.get;
    const setOption = editor.options.set;
    const filePickers = 'moodle:filepickers';

    // For each option, register it with the editor.
    // Valid type are defined in https://www.tiny.cloud/docs/tinymce/6/apis/tinymce.editoroptions/
    registerOption(heading1StyleLevelName, {
        processor: 'int',
    });

    registerOption(wordFilePickerOptionName, {
        processor: 'object',
        "default": {},
    });

    // Get the current filepicker setting which contains the allowed upload file types.
    var filepicker = getOption(filePickers);
    // Add docx as additional file type to the allowance list for uploads.
    filepicker.docx = getWordFilePickerOption(editor);
    // Update the filepicker option.
    setOption(filePickers, filepicker);
};

/**
 * Fetch the heading1StyleLevel value for this editor instance.
 *
 * @param {tinyMCE} editor The editor instance to fetch the value for
 * @returns {object} The value of the heading1StyleLevel option
 */
export const getHeading1StyleLevel = (editor) => editor.options.get(heading1StyleLevelName);

/**
 * Fetch the wordFilePickerOptionName value for this editor instance.
 *
 * @param {tinyMCE} editor The editor instance to fetch the value for
 * @returns {object} The value of the heading1StyleLevel option
 */
export const getWordFilePickerOption = (editor) => editor.options.get(wordFilePickerOptionName);

const permissionsName = getPluginOptionName('tiny_media/plugin', 'permissions');

/**
 * Get the permissions configuration for the Tiny Word Import plugin.
 *
 * @param {TinyMCE} editor
 * @returns {object}
 */
export const getEmbedPermissions = (editor) => editor.options.get(permissionsName);
