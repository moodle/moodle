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
 * Commands helper for the Moodle tiny_fontcolor plugin.
 *
 * @module      tiny_fontcolor
 * @copyright   2023 Luca Bösch <luca.boesch@bfh.ch>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import {register$c} from "./colorswat";
import {component} from './common';
import {get_strings as getStrings} from 'core/str';

/**
 * Get the setup function for the buttons.
 *
 * This is performed in an async function which ultimately returns the registration function as the
 * Tiny.AddOnManager.Add() function does not support async functions.
 *
 * @returns {function} The registration function to call within the Plugin.add function.
 */
export const getSetup = async() => {
    const [
        btnFgColor,
        menuItemFgcolor,
        btnBgcolor,
        menuItemBgcolor,
        customColor,
        removeColor,
        colorPickerTitle,
        colorPickerColor,
        colorPickerSave,
        colorPickerCancel,
        colorPickerErrHexCode,
        colorPickerErrRgbCode,
    ] = await getStrings([
        'btnFgcolor',
        'menuItemFgcolor',
        'btnBgcolor',
        'menuItemBgcolor',
        'customColor',
        'removeColor',
        'colorPickerTitle',
        'colorPickerColor',
        'colorPickerSave',
        'colorPickerCancel',
        'colorPickerErrHexCode',
        'colorPickerErrRgbCode'
    ].map((key) => ({key, component})));
    return (editor) => {
        register$c(editor, {
            'btnFgColor': btnFgColor,
            'menuItemFgcolor': menuItemFgcolor,
            'btnBgcolor': btnBgcolor,
            'menuItemBgcolor': menuItemBgcolor,
            'customColor': customColor,
            'removeColor': removeColor,
            'colorPickerTitle': colorPickerTitle,
            'colorPickerColor': colorPickerColor,
            'colorPickerSave': colorPickerSave,
            'colorPickerCancel': colorPickerCancel,
            'colorPickerErrHexCode': colorPickerErrHexCode,
            'colorPickerErrRgbCode': colorPickerErrRgbCode,
        });
    };
};
