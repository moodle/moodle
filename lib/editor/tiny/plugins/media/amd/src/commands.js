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
 * Tiny Media commands.
 *
 * @module      tiny_media/commands
 * @copyright   2022 Huong Nguyen <huongnv13@gmail.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import {get_string as getString} from 'core/str';
import {
    component,
    imageButtonName,
} from './common';
import {MediaImage} from './image';

export const getSetup = async() => {
    const [
        imageButtonText,
    ] = await Promise.all([
        getString('imagebuttontitle', component),
    ]);

    return (editor) => {
        const mediaImage = new MediaImage(editor);
        const icon = 'image';

        // Register the Menu Button as a toggle.
        // This means that when highlighted over an existing Media Image element it will show as toggled on.
        editor.ui.registry.addToggleButton(imageButtonName, {
            icon,
            text: imageButtonText,
            tooltip: imageButtonText,
            onAction: () => {mediaImage.displayDialogue();},
            onSetup: api => {
                return editor.selection.selectorChangedWithUnbind(
                    'img:not([data-mce-object]):not([data-mce-placeholder]),figure.image',
                    api.setActive
                ).unbind;
            }
        });

        editor.ui.registry.addMenuItem(imageButtonName, {
            icon,
            text: imageButtonText,
            onAction: () => {mediaImage.displayDialogue();}
        });

        editor.ui.registry.addContextToolbar(imageButtonName, {
            predicate: node => {
                return node.nodeName.toLowerCase() === 'img';
            },
            items: imageButtonName,
            position: 'node',
            scope: 'node'
        });
    };
};
