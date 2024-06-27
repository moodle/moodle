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
 * Options helper for Tiny Record RTC plugin.
 *
 * @module      tiny_recordrtc/options
 * @copyright   2022, Stevani Andolo <stevani@hotmail.com.au>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import {pluginName} from './common';
import {getPluginOptionName} from 'editor_tiny/options';

const dataName = getPluginOptionName(pluginName, 'data');
const videoAllowedName = getPluginOptionName(pluginName, 'videoAllowed');
const audioAllowedName = getPluginOptionName(pluginName, 'audioAllowed');
const screenAllowedName = getPluginOptionName(pluginName, 'screenAllowed');
const pausingAllowedName = getPluginOptionName(pluginName, 'pausingAllowed');

export const register = (editor) => {
    const registerOption = editor.options.register;

    registerOption(dataName, {
        processor: 'object',
    });

    registerOption(videoAllowedName, {
        processor: 'boolean',
        "default": false,
    });

    registerOption(audioAllowedName, {
        processor: 'boolean',
        "default": false,
    });

    registerOption(screenAllowedName, {
        processor: 'boolean',
        "default": false,
    });

    registerOption(pausingAllowedName, {
        processor: 'boolean',
        "default": false,
    });
};

export const getData = (editor) => editor.options.get(dataName);

/**
 * Whether video may be recorded in this instance.
 *
 * @param {TinyMCE} editor
 * @returns {boolean}
 */
export const isAudioAllowed = (editor) => editor.options.get(audioAllowedName);

/**
 * Whether audio may be recorded in this instance.
 *
 * @param {TinyMCE} editor
 * @returns {boolean}
 */
export const isVideoAllowed = (editor) => editor.options.get(videoAllowedName);

/**
 * Whether screen may be recorded in this instance.
 *
 * @param {TinyMCE} editor
 * @returns {boolean}
 */
export const isScreenAllowed = (editor) => editor.options.get(screenAllowedName);

/**
 * Whether pausing is allowed in this instance.
 *
 * @param {TinyMCE} editor
 * @returns {boolean}
 */
export const isPausingAllowed = (editor) => editor.options.get(pausingAllowedName);
