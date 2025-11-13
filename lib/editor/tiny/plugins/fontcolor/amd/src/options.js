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
 * Options helper for Tiny Font Color plugin.
 *
 * @module      tiny_fontcolor
 * @copyright   2023 Luca Bösch <luca.boesch@bfh.ch>
 * @copyright   2023 Stephan Robotta <stephan.robotta@bfh.ch>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import {getPluginOptionName} from 'editor_tiny/options';
import {pluginName} from './common';
import {isArrayOf, isString, mapColors} from "./polyfill";

const forecolorMap = getPluginOptionName(pluginName, 'textcolors');
const backcolorMap = getPluginOptionName(pluginName, 'backgroundcolors');
const forecolorPicker = getPluginOptionName(pluginName, 'textcolorpicker');
const backcolorPicker = getPluginOptionName(pluginName, 'backgroundcolorpicker');
const useCssClassnames = getPluginOptionName(pluginName, 'usecssclassnames');
const forecolorClasses = getPluginOptionName(pluginName, 'textcolors_classlist');
const backcolorClasses = getPluginOptionName(pluginName, 'backgroundcolors_classlist');

/**
 * Register the options for the Tiny Equation plugin.
 *
 * @param {TinyMCE} editor
 */
export const register = (editor) => {

  editor.options.register(forecolorMap, {
    processor: value => {
      if (isArrayOf(value, isString)) {
        return {
          value: mapColors(value),
          valid: true
        };
      } else {
        return {
          valid: false,
          message: 'Must be an array of strings.'
        };
      }
    },
    "default": [],
  });

  editor.options.register(backcolorMap, {
    processor: value => {
      if (isArrayOf(value, isString)) {
        return {
          value: mapColors(value),
          valid: true
        };
      } else {
        return {
          valid: false,
          message: 'Must be an array of strings.'
        };
      }
    },
    "default": [],
  });

  editor.options.register(forecolorPicker, {
    processor: 'boolean',
    "default": false,
  });

  editor.options.register(backcolorPicker, {
    processor: 'boolean',
    "default": false,
  });

  editor.options.register(useCssClassnames, {
    processor: 'boolean',
    "default": false,
  });

  editor.options.register(forecolorClasses, {
    processor: value => {
      return {
        value: Object.entries(value),
        valid: true
      };
    },
    "default": [],
  });

  editor.options.register(backcolorClasses, {
    processor: value => {
      return {
        value: Object.entries(value),
        valid: true
      };
    },
    "default": [],
  });
};

/**
 * Get the defined colors for the text color.
 *
 * @param {TinyMCE.Editor} editor
 * @returns {array}
 */
export const getForecolorMap = (editor) => editor.options.get(forecolorMap);
/**
 * Get the defined colors for the background color.
 *
 * @param {TinyMCE.Editor} editor
 * @returns {array}
 */
export const getBackcolorMap = (editor) => editor.options.get(backcolorMap);
/**
 * Get whether the color picker for the text color is enabled.
 *
 * @param {TinyMCE.Editor} editor
 * @returns {boolean}
 */
export const isForecolorPickerOn = (editor) => editor.options.get(forecolorPicker);
/**
 * Get whether the color picker for the background color is enabled.
 *
 * @param {TinyMCE.Editor} editor
 * @returns {boolean}
 */
export const isBackcolorPickerOn = (editor) => editor.options.get(backcolorPicker);
/**
 * Get whether the the color codes or css classes are used.
 *
 * @param {TinyMCE.Editor} editor
 * @returns {boolean}
 */
export const useCssClasses = (editor) => editor.options.get(useCssClassnames);
/**
 * Get the list of textcolor css classes.
 *
 * @param {TinyMCE.Editor} editor
 * @returns {boolean}
 */
export const getForecolorClasses = (editor) => editor.options.get(forecolorClasses);
/**
 * Get the list of background color css classes.
 *
 * @param {TinyMCE.Editor} editor
 * @returns {boolean}
 */
export const getBackcolorClasses = (editor) => editor.options.get(backcolorClasses);
