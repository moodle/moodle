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

import {getString} from 'core/str';
import {component, linkButtonShortName, unlinkButtonShortName} from 'tiny_link/common';
import {handleAction} from 'tiny_link/ui';
import {toggleActiveState} from 'tiny_link/link';

/**
 * Tiny Link commands.
 *
 * @module      tiny_link/commands
 * @copyright   2023 Huong Nguyen <huongnv13@gmail.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

export const getSetup = async() => {
    const [
        linkButtonText,
        unlinkButtonText,
    ] = await Promise.all([
        getString('link', component),
        getString('unlink', component),
    ]);

    return (editor) => {
        // Register Link button.
        editor.ui.registry.addToggleButton(linkButtonShortName, {
            icon: 'link',
            tooltip: linkButtonText,
            onAction: () => {
                handleAction(editor);
            },
            onSetup: toggleActiveState(editor),
        });

        // Register the Link menu item.
        editor.ui.registry.addMenuItem(linkButtonShortName, {
            icon: 'link',
            shortcut: 'Meta+K',
            text: linkButtonText,
            onAction: () => {
                handleAction(editor);
            },
        });

        // Register Unlink button.
        editor.ui.registry.addToggleButton(unlinkButtonShortName, {
            icon: 'unlink',
            tooltip: unlinkButtonText,
            onAction: () => {
                handleAction(editor, true);
            },
            onSetup: toggleActiveState(editor),
        });

        // Register shortcut.
        editor.shortcuts.add('Meta+K', 'Shortcut for create link', () => {
            handleAction(editor);
        });
    };
};
