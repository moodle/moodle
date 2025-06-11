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
 * Tiny Panopto LTI Video Options helper.
 *
 * @module     tiny_panoptoltibutton/options
 * @copyright  2024 Panopto
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import {getPluginOptionName} from 'editor_tiny/options';
import {pluginName} from './common';

// Helper variables for the option names.
const courseid = getPluginOptionName(pluginName, "courseid");
const tool = getPluginOptionName(pluginName, "tool");
const wwwroot = getPluginOptionName(pluginName, "wwwroot");
const contentitempath = getPluginOptionName(pluginName, "contentitempath");
const resourcebase = getPluginOptionName(pluginName, "resourcebase");
const panoptoltibuttondescription = getPluginOptionName(
    pluginName,
    "panoptoltibuttondescription"
);
const panoptoltibuttonlongdescription = getPluginOptionName(
    pluginName,
    "panoptoltibuttonlongdescription"
);
const unprovisionederror = getPluginOptionName(
    pluginName,
    "unprovisionederror"
);

/**
 * Options registration function.
 *
 * @param {tinyMCE} editor
 */
export const register = (editor) => {
    const registerOption = editor.options.register;

    // For each option, register it with the editor.
    registerOption(courseid, {
        processor: "string",
    });
    registerOption(tool, {
        processor: "object",
    });
    registerOption(wwwroot, {
        processor: "string",
    });
    registerOption(contentitempath, {
        processor: "string",
    });
    registerOption(resourcebase, {
        processor: "string",
    });
    registerOption(panoptoltibuttondescription, {
        processor: "string",
    });
    registerOption(panoptoltibuttonlongdescription, {
        processor: "string",
    });
    registerOption(unprovisionederror, {
        processor: "string",
    });
};

/**
 * Fetch the courseid value for this editor instance.
 *
 * @param {tinyMCE} editor The editor instance to fetch the value for
 * @returns {object} The value of the courseid option
 */
export const getCourseId = (editor) => editor.options.get(courseid);

/**
 * Fetch the tool value for this editor instance.
 *
 * @param {tinyMCE} editor The editor instance to fetch the value for
 * @returns {object} The value of the tool option
 */
export const getTool = (editor) => editor.options.get(tool);

/**
 * Fetch the wwwroot value for this editor instance.
 *
 * @param {tinyMCE} editor The editor instance to fetch the value for
 * @returns {object} The value of the wwwroot option
 */
export const getWwwroot = (editor) => editor.options.get(wwwroot);

/**
 * Fetch the contentitempath value for this editor instance.
 *
 * @param {tinyMCE} editor The editor instance to fetch the value for
 * @returns {object} The value of the contentitempath option
 */
export const getContentItemPath = (editor) => editor.options.get(contentitempath);

/**
 * Fetch the resourcebase value for this editor instance.
 *
 * @param {tinyMCE} editor The editor instance to fetch the value for
 * @returns {object} The value of the resourcebase option
 */
export const getResourceBase = (editor) => editor.options.get(resourcebase);

/**
 * Fetch the panoptoltibuttondescription value for this editor instance.
 *
 * @param {tinyMCE} editor The editor instance to fetch the value for
 * @returns {object} The value of the panoptoltibuttondescription option
 */
export const getPanoptoLtiButtonDescription = (editor) =>
    editor.options.get(panoptoltibuttondescription);

/**
 * Fetch the panoptoltibuttonlongdescription value for this editor instance.
 *
 * @param {tinyMCE} editor The editor instance to fetch the value for
 * @returns {object} The value of the panoptoltibuttonlongdescription option
 */
export const getPanoptoLtiButtonLongDescription = (editor) =>
    editor.options.get(panoptoltibuttonlongdescription);

/**
 * Fetch the unprovisionederror value for this editor instance.
 *
 * @param {tinyMCE} editor The editor instance to fetch the value for
 * @returns {object} The value of the unprovisionederror option
 */
export const getUnprovisionedError = (editor) =>
    editor.options.get(unprovisionederror);
