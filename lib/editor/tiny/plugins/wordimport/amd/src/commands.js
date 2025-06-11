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
 * Commands helper for the Moodle tiny_wordimport plugin.
 *
 * @module      tiny_wordimport/commands
 * @copyright   2023 University of Graz
 * @author      André Menrath <andre.menrath@uni-graz.at>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import {get_string as getString} from 'core/str';
import {getButtonImage} from 'editor_tiny/utils';
import {
    allowedFileType,
    component,
    wordimportButtonName,
    wordimportMenuItemName,
    icon
} from './common';
import {
    droppedWordFileHandler,
    importWordFileHandler
} from './wordimport';

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
        wordimportButtonNameTitle,
        wordimportMenuItemNameTitle,
        buttonImage,
    ] = await Promise.all([
        getString('button_wordimport', component),
        getString('menuitem_wordimport', component),
        getButtonImage('icon', component),
    ]);

    return (editor) => {
        // Register the Moodle SVG as an icon suitable for use as a TinyMCE toolbar button.
        editor.ui.registry.addIcon(icon, buttonImage.html);

        // Register the wordimport Toolbar Button.
        editor.ui.registry.addButton(wordimportButtonName, {
            icon,
            tooltip: wordimportButtonNameTitle,
            onAction: () => importWordFileHandler(editor),
        });

        // Add the wordimport Menu Item.
        // This allows it to be added to a standard menu, or a context menu.
        editor.ui.registry.addMenuItem(wordimportMenuItemName, {
            icon,
            text: wordimportMenuItemNameTitle,
            onAction: () => importWordFileHandler(editor),
        });

        // Add a handler which allows dragging and dropping .docx files directly into the editor.
        editor.on('dragdrop drop', async(event) => {
            const {files} = event.dataTransfer || {};
            if (!files || files.length !== 1) {
                return;
            }
            const file = files[0];
            if (file.type !== allowedFileType) {
                return;
            }
            event.preventDefault();
            droppedWordFileHandler(editor, file);
        });

    };
};
