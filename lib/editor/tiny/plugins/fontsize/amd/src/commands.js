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
 * Commands helper for the Moodle tiny_fontsize plugin.
 *
 * @module      plugintype_pluginname/commands
 * @copyright   2023 Mikko Haiku <mikko.haiku@mediamaisteri.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import {getButtonImage} from 'editor_tiny/utils';
import {get_string as getString} from 'core/str';
import {
    component,
    fontsizeButtonName,
    fontsizeMenuItemName,
    icon,
} from './common';

/**
 * Handle the action for your plugin.
 * @param {TinyMCE.editor} editor The tinyMCE editor instance.
 * @param {integer} fontsize Font size in integer.
 */
const handleAction = (editor, fontsize) => {
    editor.selection.dom.setAttrib(editor.selection.getNode(), "style", "font-size: " + fontsize + "pt");
};


/**
 * Get the setup function for the buttons.
 *
 * This is performed in an async function which ultimately returns the registration function as the
 * Tiny.AddOnManager.Add() function does not support async functions.
 *
 * @returns {function} The registration function to call within the Plugin.add function.
 */
export const getSetup = async() => {
    const [
        fontsizeButtonNameTitle,
        fontsizeMenuItemNameTitle,
        buttonImage,
    ] = await Promise.all([
        getString('button_fontsize', component),
        getString('menuitem_fontsize', component),
        getButtonImage('icon', component),
    ]);

    return (editor) => {
        // Register the Moodle SVG as an icon suitable for use as a TinyMCE toolbar button.
        editor.ui.registry.addIcon(icon, buttonImage.html);

        // Register the fontsize Toolbar Button.
        editor.ui.registry.addButton(fontsizeButtonName, {
            icon,
            tooltip: fontsizeButtonNameTitle,
            onAction: () => handleAction(editor),
        });

        // Add the fontsize Menu Item.
        // This allows it to be added to a standard menu, or a context menu.
        editor.ui.registry.addMenuItem(fontsizeMenuItemName, {
            icon,
            text: fontsizeMenuItemNameTitle,
            onAction: () => handleAction(editor),
        });

        // Define the font sizes and their corresponding text labels
        const fontSizes = [
            {size: 8, label: '8 pt'},
            {size: 10, label: '10 pt'},
            {size: 12, label: '12 pt'},
            {size: 14, label: '14 pt'},
            {size: 18, label: '18 pt'},
            {size: 24, label: '24 pt'},
            {size: 36, label: '36 pt'},
        ];

        /**
         * Handle the font size menu item action.
         *
         * @param {Editor} editor - The editor instance.
         * @param {number} size - The font size to set.
         * @returns {Function} - The action handler function.
         */
        function handleFontSize(editor, size) {
            return () => handleAction(editor, size);
        }

        // Create an array of submenu items using a map function
        const submenuItems = fontSizes.map(({size, label}) => ({
            type: 'menuitem',
            text: label,
            onAction: handleFontSize(editor, size),
        }));

        // Add the nested menu item to the editor UI
        editor.ui.registry.addNestedMenuItem(fontsizeMenuItemName, {
            icon,
            text: fontsizeMenuItemNameTitle,
            getSubmenuItems: () => submenuItems,
        });

        editor.ui.registry.addMenuButton(fontsizeButtonName, {
            icon,
            fetch: (callback) => {
                // Pass the dynamically generated items to the callback.
                callback(submenuItems);
            },
          });

    };
};
