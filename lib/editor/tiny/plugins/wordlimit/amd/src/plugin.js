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
 * Tiny Wordlimit plugin for the Moodle TinyMCE 6 editor.
 *
 * @module    tiny_wordlimit/plugin
 * @copyright 2023 University of Graz
 * @author    André Menrath <andre.menrath@uni-graz.at>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
import {getTinyMCE} from 'editor_tiny/loader';
import {getPluginMetadata} from 'editor_tiny/utils';

import {component, pluginName} from './common';
import {setupWordLimit} from './wordlimit';
import * as Options from './options';

/**
 * This is the entry point for the javascript of tinymce editor plugins in moodle.
 */
// eslint-disable-next-line no-async-promise-executor
export default new Promise(async(resolve) => {
    const [tinyMCE, pluginMetadata] = await Promise.all([
        getTinyMCE(),
        getPluginMetadata(component, pluginName),
    ]);

    // Note: The PluginManager.add function does not accept a Promise.
    // Any asynchronous code must be run before this point.
    tinyMCE.PluginManager.add(pluginName, (editor) => {
        // Register options.
        Options.register(editor);

        // Setup the wordlimit indicator in the status bar.
        setupWordLimit(editor);

        return pluginMetadata;
    });

    resolve(pluginName);
});

