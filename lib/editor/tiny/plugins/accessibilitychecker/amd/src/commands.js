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
 * Tiny Media Manager commands.
 *
 * @module      tiny_accessibilitychecker/commands
 * @copyright   2022, Stevani Andolo <stevani@hotmail.com.au>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import {get_string as getString} from 'core/str';
import {
    component,
    accessbilityButtonName,
    icon,
} from './common';
import Checker from './checker';

export const getSetup = async() => {
    const [
        buttonTooltip,
    ] = await Promise.all([
        getString('pluginname', component),
    ]);

    return (editor) => {
        // Register the Menu Button as a toggle.
        editor.ui.registry.addButton(accessbilityButtonName, {
            icon,
            tooltip: buttonTooltip,
            onAction: () => {
                const checker = new Checker(editor);
                checker.displayDialogue();
            }
        });

        editor.ui.registry.addMenuItem(accessbilityButtonName, {
            icon,
            text: buttonTooltip,
            onAction: () => {
                const checker = new Checker(editor);
                checker.displayDialogue();
            }
        });
    };
};
