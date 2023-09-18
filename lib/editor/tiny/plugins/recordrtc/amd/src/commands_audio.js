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
 * Tiny Record RTC - record audio command.
 *
 * @module      tiny_recordrtc/commands_audio
 * @copyright   2022, Stevani Andolo <stevani@hotmail.com.au>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import {getString} from 'core/str';
import {getButtonImage} from 'editor_tiny/utils';
import {
    audioButtonName,
    component
} from './common';
import Recorder from './audio_recorder';
import {isAudioAllowed} from './options';

export default async() => {
    if (!Recorder.isBrowserCompatible()) {
        // The browser doesn't support the plugin, so just don't show it.
        return () => false;
    }

    const [
        audioButtonTitle,
        audio,
    ] = await Promise.all([
        getString('audiobuttontitle', component),
        getButtonImage('audio', component),
    ]);

    return (editor) => {
        if (!isAudioAllowed(editor)) {
            return;
        }

        const icon = 'audio';
        editor.ui.registry.addIcon(icon, audio.html);

        editor.ui.registry.addButton(audioButtonName, {
            icon,
            tooltip: audioButtonTitle,
            onAction: () => Recorder.display(editor),
        });

        editor.ui.registry.addMenuItem(audioButtonName, {
            icon,
            text: audioButtonTitle,
            onAction: () => Recorder.display(editor),
        });
    };
};
