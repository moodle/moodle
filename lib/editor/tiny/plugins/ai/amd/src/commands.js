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
 * Commands helper for the Moodle tiny_ai plugin.
 *
 * @module      tiny_ai/commands
 * @copyright   2024, ISB Bayern
 * @author      Dr. Peter Mayer
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import {getButtonImage} from 'editor_tiny/utils';
import {
    component,
    toolbarButtonName,
    selectionbarButtonName,
    icon,
    selectionbarSource,
    toolbarSource,
    menubarSource
} from 'tiny_ai/common';
import * as Utils from 'tiny_ai/utils';
import {prefetchStrings} from 'core/prefetch';
import {getString} from 'core/str';

/**
 * Get the setup function for the buttons.
 *
 * This is performed in an async function which ultimately returns the registration function as the
 * Tiny.AddOnManager.Add() function does not support async functions.
 *
 * @returns {function} The registration function to call within the Plugin.add function.
 */
export const getSetup = async() => {
    prefetchStrings('tiny_ai', ['toolbarbuttontitle', 'selectionbarbuttontitle']);
    const [
        buttonImage,
        toolbarButtonTitle,
        selectionbarButtonTitle
    ] = await Promise.all([
        getButtonImage('icon', component),
        getString('toolbarbuttontitle', 'tiny_ai'),
        getString('selectionbarbuttontitle', 'tiny_ai')
    ]);


    return (editor) => {
        // Register the Moodle SVG as an icon suitable for use as a TinyMCE toolbar button.
        editor.ui.registry.addIcon(icon, buttonImage.html);

        const uniqid = Math.random().toString(16).slice(2);
        Utils.init(uniqid, editor);

        // Register the AI Toolbar Button.
        editor.ui.registry.addButton(toolbarButtonName, {
            icon,
            tooltip: toolbarButtonTitle,
            onAction: () => {
                Utils.getEditorUtils(uniqid).displayDialogue(toolbarSource);
            }
        });

        // Register the menu item.
        editor.ui.registry.addMenuItem(toolbarButtonName, {
            icon,
            text: toolbarButtonTitle,
            onAction: () => {
                Utils.getEditorUtils(uniqid).displayDialogue(menubarSource);
            }
        });

        editor.ui.registry.addButton(selectionbarButtonName, {
            icon,
            tooltip: selectionbarButtonTitle,
            onAction: () => {
                Utils.getEditorUtils(uniqid).displayDialogue(selectionbarSource);
            }
        });
    };
};
