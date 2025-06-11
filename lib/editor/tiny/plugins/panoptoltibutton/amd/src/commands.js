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
 * Tiny Panopto LTI Video commands.
 *
 * @module     tiny_panoptoltibutton/commands
 * @copyright  2024 Panopto
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import {getButtonImage} from "editor_tiny/utils";
import {handleAction} from "./ui";
import {get_string as getString} from "core/str";
import {component, buttonName, icon} from "./common";
import {getTool} from "./options";

export const getSetup = async() => {
    const [buttonText, buttonImage] = await Promise.all([
        getString("panopto_button_description", component),
        getButtonImage("icon", component),
    ]);

    return (editor) => {

        // Only show button if we have external tool configured.
        if (getTool(editor)) {
            // Register the Moodle SVG as an icon suitable for use as a TinyMCE toolbar button.
            editor.ui.registry.addIcon(icon, buttonImage.html);

            // Register the Panopto LTI Video Toolbar Button.
            editor.ui.registry.addToggleButton(buttonName, {
                icon,
                tooltip: buttonText,
                onAction: () => handleAction(editor),
            });

            // Add the Panopto LTI Video Menu Item.
            // This allows it to be added to a standard menu, or a context menu.
            editor.ui.registry.addMenuItem(buttonName, {
                icon,
                text: buttonText,
                onAction: () => handleAction(editor),
            });
        }
    };
};
