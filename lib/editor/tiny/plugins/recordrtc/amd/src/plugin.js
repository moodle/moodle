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
 * Tiny Record RTC plugin for Moodle.
 *
 * @module      tiny_recordrtc/plugin
 * @copyright   2022, Stevani Andolo <stevani@hotmail.com.au>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
import {getTinyMCE} from 'editor_tiny/loader';
import {getPluginMetadata} from 'editor_tiny/utils';
import getSetupAudioCommands from './commands_audio';
import getSetupVideoCommands from './commands_video';
import getSetupScreenCommands from './commands_screen';
import getSetupVideoContextMenuCommands from './commands_video_context_menu';
import * as Configuration from './configuration';
import * as Options from './options';
import {
    component,
    pluginName
} from './common';

// eslint-disable-next-line no-async-promise-executor
export default new Promise(async(resolve) => {
    const [
        tinyMCE,
        setupAudioCommands,
        setupVideoCommands,
        setupScreenCommands,
        setupVideoContextMenuCommands,
        pluginMetadata,
    ] = await Promise.all([
        getTinyMCE(),
        getSetupAudioCommands(),
        getSetupVideoCommands(),
        getSetupScreenCommands(),
        getSetupVideoContextMenuCommands(),
        getPluginMetadata(component, pluginName),
    ]);

    tinyMCE.PluginManager.add(`${component}/plugin`, (editor) => {
        // Register options.
        Options.register(editor);

        // Setup the Commands (buttons, menu items, and so on) for video.
        setupVideoCommands(editor);

        // Setup the Commands (buttons, menu items, and so on) for audio.
        setupAudioCommands(editor);

        // Setup the Commands (buttons, menu items, and so on) for screen.
        setupScreenCommands(editor);

        // Setup the Commands (context menu) for video (recording and screen-sharing).
        setupVideoContextMenuCommands(editor);

        return pluginMetadata;
    });

    // Resolve the Media Plugin and include configuration.
    resolve([`${component}/plugin`, Configuration]);
});
