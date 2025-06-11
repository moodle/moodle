// This file is part of Moodle - https://moodle.org/
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
// along with Moodle.  If not, see <https://www.gnu.org/licenses/>.

/**
 * Tiny Panopto LTI Video plugin for Moodle.
 *
 * @module     tiny_panoptoltibutton
 * @copyright  2024 Panopto
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import { getTinyMCE } from "editor_tiny/loader";
import { getPluginMetadata } from "editor_tiny/utils";
import { component, pluginName } from "./common";
import { register as registerOptions } from "./options";
import { getSetup as getCommandSetup } from "./commands";
import * as Configuration from "./configuration";

// Setup the tiny_panoptoltibutton Plugin.
export default new Promise(async (resolve) => {
    // Note: The PluginManager.add function does not support asynchronous configuration.
    // Perform any asynchronous configuration here, and then call the PluginManager.add function.
    const [tinyMCE, pluginMetadata, setupCommands] = await Promise.all([
        getTinyMCE(),
        getPluginMetadata(component, pluginName),
        getCommandSetup(),
    ]);

    tinyMCE.init(Configuration.configure({}));

    tinyMCE.PluginManager.add(pluginName, (editor) => {
        // Register any options that your plugin has.
        registerOptions(editor);

        // Setup any commands such as buttons, menu items, and so on.
        setupCommands(editor);

        // Return the pluginMetadata object. This is used by TinyMCE to display a help link for your plugin.
        return pluginMetadata;
    });

    resolve([pluginName, Configuration]);
});
