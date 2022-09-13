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
 * Options helper for Tiny Equation plugin.
 *
 * @module      tiny_equation/options
 * @copyright   2022 Huong Nguyen <huongnv13@gmail.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import {getPluginOptionName} from 'editor_tiny/options';
import {pluginName} from 'tiny_equation/common';

const librariesName = getPluginOptionName(pluginName, 'libraries');
const texFilterName = getPluginOptionName(pluginName, 'texfilter');
const contextIdName = getPluginOptionName(pluginName, 'contextid');
const texDocsUrlName = getPluginOptionName(pluginName, 'texdocsurl');

/**
 * Register the options for the Tiny Equation plugin.
 *
 * @param {TinyMCE} editor
 */
export const register = (editor) => {
    const registerOption = editor.options.register;

    registerOption(librariesName, {
        processor: 'array',
        "default": [],
    });

    registerOption(texFilterName, {
        processor: 'boolean',
        "default": false,
    });

    registerOption(contextIdName, {
        processor: 'number',
        "default": 0,
    });

    registerOption(texDocsUrlName, {
        processor: 'string',
        "default": '',
    });
};

/**
 * Get the libraries configuration for the Tiny Equation plugin.
 *
 * @param {TinyMCE} editor
 * @returns {object}
 */
export const getLibraries = (editor) => editor.options.get(librariesName);
/**
 * Check if the TEX filter is active or not for the Tiny Equation plugin.
 *
 * @param {TinyMCE} editor
 * @returns {boolean}
 */
export const isTexFilterActive = (editor) => editor.options.get(texFilterName);
/**
 * Get the context id for the Tiny Equation plugin.
 *
 * @param {TinyMCE} editor
 * @returns {number}
 */
export const getContextId = (editor) => editor.options.get(contextIdName);
/**
 * Get the Tex Docs Url for the Tiny Equation plugin.
 *
 * @param {TinyMCE} editor
 * @returns {string}
 */
export const getTexDocsUrl = (editor) => editor.options.get(texDocsUrlName);
