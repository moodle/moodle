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
 * Options helper for Tiny AI plugin.
 *
 * @module      tiny_aiplacement/options
 * @copyright   2023 Matt Porritt <matt.porritt@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import {getPluginOptionName} from 'editor_tiny/options';
import {pluginName} from 'tiny_aiplacement/common';

const contextIdName = getPluginOptionName(pluginName, 'contextid');
const userIdName = getPluginOptionName(pluginName, 'userid');
const textAllowedName = getPluginOptionName(pluginName, 'generate_text');
const imageAllowedName = getPluginOptionName(pluginName, 'generate_image');
const policyAgreedName = getPluginOptionName(pluginName, 'policyagreed');

/**
 * Options registration function.
 *
 * @param {tinyMCE} editor
 */
export const register = (editor) => {
    const registerOption = editor.options.register;

    registerOption(contextIdName, {
        processor: 'number',
        "default": 0,
    });

    registerOption(userIdName, {
        processor: 'number',
        "default": 0,
    });

    registerOption(textAllowedName, {
        processor: 'boolean',
        "default": false,
    });

    registerOption(imageAllowedName, {
        processor: 'boolean',
        "default": false,
    });

    registerOption(policyAgreedName, {
        processor: 'boolean',
        "default": false,
    });
};

/**
 * Fetch the context ID value for this editor instance.
 *
 * @param {tinyMCE} editor The editor instance to fetch the value for
 * @returns {int} The value of the contextIdName option
 */
export const getContextId = (editor) => editor.options.get(contextIdName);

/**
 * Fetch the user ID value for this editor instance.
 *
 * @param {tinyMCE} editor The editor instance to fetch the value for
 * @returns {int} The value of the userIdName option
 */
export const getUserId = (editor) => editor.options.get(userIdName);

/**
 * Whether text generation is allowed in this instance.
 *
 * @param {TinyMCE} editor
 * @returns {boolean}
 */
export const isTextAllowed = (editor) => editor.options.get(textAllowedName);

/**
 * Whether image generation is allowed in this instance.
 *
 * @param {TinyMCE} editor
 * @returns {boolean}
 */
export const isImageAllowed = (editor) => editor.options.get(imageAllowedName);

export const isPolicyAgreed = (editor) => editor.options.get(policyAgreedName);
