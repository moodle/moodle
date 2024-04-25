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
 * Tiny Record RTC - record base command.
 *
 * @module      tiny_recordrtc/commands
 * @copyright   2024 The Open University
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import {getString} from 'core/str';
import {getButtonImage as getVideoIcon} from 'editor_tiny/utils';
import {
    videoButtonName,
    screenButtonName,
    videoMenuButtonName,
    component,
} from './common';
import {isVideoAllowed, isScreenAllowed} from './options';

export default async() => {
    const [
        videorecordmenutitle,
        buttonImage,
    ] = await Promise.all([
        getString('videorecordmenutitle', component),
        getVideoIcon('video', component),
    ]);

    return (editor) => {
        if (!isVideoAllowed(editor) && !isScreenAllowed(editor)) {
            return;
        }

        const icon = 'video';
        editor.ui.registry.addIcon(icon, buttonImage.html);

        editor.ui.registry.addMenuButton(videoMenuButtonName, {
            icon,
            tooltip: videorecordmenutitle,
            fetch: callback => callback(`${videoButtonName} ${screenButtonName}`),
        });
    };
};
