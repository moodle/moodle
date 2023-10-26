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
 * Options helper for the Moodle Tiny Autosave plugin.
 *
 * @module      tiny_autosave/plugin
 * @copyright   2022 Andrew Lyons <andrew@nicols.co.uk>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import {pluginName} from './common';
import {
    getContextId,
    getDraftItemId,
    getPluginOptionName,
} from 'editor_tiny/options';
import {ensureEditorIsValid} from 'editor_tiny/utils';

const initialisedOptionName = getPluginOptionName(pluginName, 'initialised');
const pageHashName = getPluginOptionName(pluginName, 'pagehash');
const pageInstanceName = getPluginOptionName(pluginName, 'pageinstance');
const backoffTime = getPluginOptionName(pluginName, 'backoffTime');
const autosaveHasReset = getPluginOptionName(pluginName, 'autosaveHasReset');

export const register = (editor) => {
    const registerOption = editor.options.register;
    registerOption(initialisedOptionName, {
        processor: 'boolean',
        "default": false,
    });

    registerOption(pageHashName, {
        processor: 'string',
        "default": '',
    });

    registerOption(pageInstanceName, {
        processor: 'string',
        "default": '',
    });
    registerOption(pageInstanceName, {
        processor: 'string',
        "default": '',
    });
    registerOption(backoffTime, {
        processor: 'number',
        "default": 500,
    });
    registerOption(autosaveHasReset, {
        processor: 'boolean',
        "default": false,
    });
};

export const isInitialised = (editor) => {
    if (!ensureEditorIsValid(editor)) {
        return false;
    }

    return editor.options.get(initialisedOptionName);
};
export const markInitialised = (editor) => editor.options.set(initialisedOptionName, true);
export const getPageHash = (editor) => editor.options.get(pageHashName);
export const getPageInstance = (editor) => editor.options.get(pageInstanceName);
export const getBackoffTime = (editor) => editor.options.get(backoffTime);
export const setAutosaveHasReset = (editor) => editor.options.set(autosaveHasReset, true);
export const hasAutosaveHasReset = (editor) => editor.options.get(autosaveHasReset);

export {
    getContextId,
    getDraftItemId,
};
