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
 * Commands helper for the Moodle tiny_kalturamedia plugin.
 *
 * @module      tiny_kalturmedia/commands
 * @copyright   2023 Roi Levi <roi.levi@kaltura.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import {getButtonImage} from 'editor_tiny/utils';
import {get_string as getString} from 'core/str';
import {
    component,
    buttonName,
    icon,
} from './common';
import {getContextId} from './options';

/**
 * Handle the action for your plugin.
 * @param {TinyMCE.editor} editor The tinyMCE editor instance.
 * @param {string} title The dialog box title
 */
const handleAction = (editor, title) => {
    editor.windowManager.openUrl({
        title,
        width: 1200,
        height: 700,
        url: M.cfg.wwwroot + '/lib/editor/tiny/plugins/kalturamedia/ltibrowse.php?lang=' +
             editor.getParam('language') + '&contextid=' + getContextId(editor),
        buttons: [{type: 'cancel', text:'Cancel'}]
    });
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
        buttonText,
        buttonImage,
    ] = await Promise.all([
        getString('buttontitle', component),
        getButtonImage('icon', component),
    ]);

    return (editor) => {
        // Register the Moodle SVG as an icon suitable for use as a TinyMCE toolbar button.
        editor.ui.registry.addIcon(icon, buttonImage.html);

        // Register the KalturaMedia Button.
        editor.ui.registry.addButton(buttonName, {
            icon,
            tooltip: buttonText,
            onAction: () => handleAction(editor, buttonText),
        });

       // Add the KalturaMedia Menu Item.
      // This allows it to be added to a standard menu, or a context menu.
      editor.ui.registry.addMenuItem(buttonName, {
           icon,
           text: buttonText,
           onAction: () => handleAction(editor, buttonText),
        });
    };
};
