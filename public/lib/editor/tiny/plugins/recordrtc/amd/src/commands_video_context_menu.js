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
 * Tiny Record RTC - Video context menu command.
 *
 * @module      tiny_recordrtc/commands_video_context_menu
 * @copyright   2024 The Open University
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import {getString} from 'core/str';
import {getButtonImage} from 'editor_tiny/utils';
import {
    videoButtonName,
    screenButtonName,
    videoContextMenuName,
    component,
} from './common';
import {isVideoAllowed, isScreenAllowed} from './options';
import videoRecorder from "./video_recorder";
import screenRecorder from "./screen_recorder";

export default async() => {
    const [
        videoContextMenuTitle,
        videoButtonTitle,
        screenButtonTitle,
        buttonImageVideo,
        buttonImageScreen,
    ] = await Promise.all([
        getString('videorecordmenutitle', component),
        getString('videobuttontitle', component),
        getString('screenbuttontitle', component),
        getButtonImage('video', component),
        getButtonImage('screen', component),
    ]);

    return (editor) => {
        let useContextMenu = true;
        let singleButton = 'video';
        let singleButtonTitle = videoButtonTitle;
        let imageHtml = buttonImageVideo.html;
        let recorder;
        if (!isVideoAllowed(editor) && !isScreenAllowed(editor)) {
            return;
        } else if (isVideoAllowed(editor) && !isScreenAllowed(editor)) {
            // Only video recording is allowed.
            useContextMenu = false;
            recorder = videoRecorder;
        } else if (isScreenAllowed(editor) && !isVideoAllowed(editor)) {
            // Only screen recording is allowed.
            useContextMenu = false;
            singleButton = 'screen';
            singleButtonTitle = screenButtonTitle;
            imageHtml = buttonImageScreen.html;
            recorder = screenRecorder;
        }
        editor.ui.registry.addIcon(singleButton, imageHtml);

        if (useContextMenu) {
            // Add the video and screen buttons to the context menu.
            editor.ui.registry.addMenuButton(videoContextMenuName, {
                icon: singleButton,
                tooltip: videoContextMenuTitle,
                fetch: callback => callback(`${videoButtonName} ${screenButtonName}`),
            });
        } else {
            // Add the video or screen button to the toolbar.
            editor.ui.registry.addButton(videoContextMenuName, {
                icon: singleButton,
                tooltip: singleButtonTitle,
                onAction: () => recorder.display(editor),
            });
        }
    };
};
