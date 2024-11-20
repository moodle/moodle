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
 * Tiny Record RTC - record screen command.
 *
 * @module      tiny_recordrtc/commands_screen
 * @copyright   2024 The Open University
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import {getString} from 'core/str';
import {getButtonImage as getScreenIcon} from 'editor_tiny/utils';
import {
    screenButtonName,
    component,
} from './common';
import {isScreenAllowed} from './options';
import Recorder from './screen_recorder';

export default async() => {
    if (!Recorder.isBrowserCompatible()) {
        // The browser doesn't support the plugin, so just don't show it.
        return () => false;
    }

    const [
        screenButtonTitle,
        buttonImage,
    ] = await Promise.all([
        getString('screenbuttontitle', component),
        getScreenIcon('screen', component),
    ]);

    return (editor) => {
        // Screen recording is not currently supported on mobile devices.
        // Therefore, it will be disabled and should be considered for future implementation.
        if (!isScreenAllowed(editor) || !editor.editorManager.Env.deviceType.isDesktop()) {
            return;
        }

        const icon = 'screen';
        editor.ui.registry.addIcon(icon, buttonImage.html);
        editor.ui.registry.addButton(screenButtonName, {
            icon,
            tooltip: screenButtonTitle,
            onAction: () => Recorder.display(editor),
        });

        editor.ui.registry.addMenuItem(screenButtonName, {
            icon,
            text: screenButtonTitle,
            onAction: () => Recorder.display(editor),
        });
    };
};
