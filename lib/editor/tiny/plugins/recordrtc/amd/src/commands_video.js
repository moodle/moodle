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
 * Tiny Record RTC - record video command.
 *
 * @module      tiny_recordrtc/recordVideoCommands
 * @copyright   2022, Stevani Andolo <stevani@hotmail.com.au>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import {get_string as getString} from 'core/str';
import {getButtonImage as getVideoIcon} from 'editor_tiny/utils';
import {
    videoButtonName,
    component
} from './common';
import {isVideoAllowed} from './options';
import Recorder from './video_recorder';

export default async() => {
    if (!Recorder.isBrowserCompatible()) {
        // The browser doesn't support the plugin, so just don't show it.
        return () => false;
    }

    const [
        videoButtonTitle,
        buttonImage,
    ] = await Promise.all([
        getString('videobuttontitle', component),
        getVideoIcon('video', component),
    ]);

    return (editor) => {
        if (!isVideoAllowed(editor)) {
            return;
        }

        const icon = 'video';
        editor.ui.registry.addIcon(icon, buttonImage.html);

        editor.ui.registry.addButton(videoButtonName, {
            icon,
            tooltip: videoButtonTitle,
            onAction: () => Recorder.display(editor),
        });

        editor.ui.registry.addMenuItem(videoButtonName, {
            icon,
            text: videoButtonTitle,
            onAction: () => Recorder.display(editor),
        });
    };
};
