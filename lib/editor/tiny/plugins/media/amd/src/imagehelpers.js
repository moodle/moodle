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
 * Tiny media plugin image helpers.
 *
 * @module      tiny_media/imagehelpers
 * @copyright   2024 Meirza <meirza.arson@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import * as Helpers from 'tiny_media/helpers';
import {Selectors} from './selectors';

/**
 * Maximum length allowed for the alt attribute.
 */
export const MAX_LENGTH_ALT = 750;

/**
 * Renders and inserts the body template for inserting an image into the modal.
 *
 * @param {object} templateContext - The context for rendering the template.
 * @param {HTMLElement} root - The root element where the template will be inserted.
 * @returns {Promise<void>}
 * @deprecated Since Moodle 5.0 See {@link module:core_editor/tiny/plugins/media/helpers.body}
 */
export const bodyImageInsert = async(templateContext, root) => {
    window.console.warn('This function is deprecated. Please use core_editor/tiny/plugins/media/helpers.body instead.');

    templateContext.bodyTemplate = 'tiny_media/insert_image_modal_insert';
    templateContext.selector = Selectors.IMAGE.type;
    return Helpers.body(templateContext, root);
};

/**
 * Renders and inserts the footer template for inserting an image into the modal.
 *
 * @param {object} templateContext - The context for rendering the template.
 * @param {HTMLElement} root - The root element where the template will be inserted.
 * @returns {Promise<void>}
 * @deprecated Since Moodle 5.0 See {@link module:core_editor/tiny/plugins/media/helpers.footer}
 */
export const footerImageInsert = async(templateContext, root) => {
    window.console.warn(`This function is deprecated.
        Please use core_editor/tiny/plugins/media/helpers.footer instead.`);

    templateContext.footerTemplate = 'tiny_media/insert_image_modal_insert_footer';
    templateContext.selector = Selectors.IMAGE.type;
    return Helpers.footer(templateContext, root);
};

/**
 * Renders and inserts the body template for displaying image details in the modal.
 *
 * @param {object} templateContext - The context for rendering the template.
 * @param {HTMLElement} root - The root element where the template will be inserted.
 * @returns {Promise<void>}
 * @deprecated Since Moodle 5.0 See {@link module:core_editor/tiny/plugins/media/helpers.body}
 */
export const bodyImageDetails = async(templateContext, root) => {
    window.console.warn(`This function is deprecated.
        Please use core_editor/tiny/plugins/media/helpers.body instead.`);

    templateContext.bodyTemplate = 'tiny_media/insert_image_modal_details';
    templateContext.selector = Selectors.IMAGE.type;
    return Helpers.body(templateContext, root);
};

/**
 * Renders and inserts the footer template for displaying image details in the modal.
 * @param {object} templateContext - The context for rendering the template.
 * @param {HTMLElement} root - The root element where the template will be inserted.
 * @returns {Promise<void>}
 * @deprecated Since Moodle 5.0 See {@link module:core_editor/tiny/plugins/media/helpers.footer}
 */
export const footerImageDetails = async(templateContext, root) => {
    window.console.warn(`This function is deprecated.
        Please use core_editor/tiny/plugins/media/helpers.footer instead.`);

    templateContext.footerTemplate = 'tiny_media/insert_image_modal_details_footer';
    templateContext.selector = Selectors.IMAGE.type;
    return Helpers.footer(templateContext, root);
};

/**
 * Show the element(s).
 *
 * @param {string|string[]} elements - The CSS selector for the elements to toggle.
 * @param {object} root - The CSS selector for the elements to toggle.
 * @deprecated Since Moodle 5.0 See {@link module:core_editor/tiny/plugins/media/helpers.showElements}
 */
export const showElements = (elements, root) => {
    window.console.warn(`This function is deprecated.
        Please use core_editor/tiny/plugins/media/helpers.showElements instead.`);
    Helpers.showElements(elements, root);
};

/**
 * Hide the element(s).
 *
 * @param {string|string[]} elements - The CSS selector for the elements to toggle.
 * @param {object} root - The CSS selector for the elements to toggle.
 * @deprecated Since Moodle 5.0 See {@link module:core_editor/tiny/plugins/media/helpers.hideElements}
 */
export const hideElements = (elements, root) => {
    window.console.warn(`This function is deprecated.
        Please use core_editor/tiny/plugins/media/helpers.hideElements instead.`);
    Helpers.hideElements(elements, root);
};

/**
 * Checks if the given value is a percentage value.
 *
 * @param {string} value - The value to check.
 * @returns {boolean} True if the value is a percentage value, false otherwise.
 * @deprecated Since Moodle 5.0 See {@link module:core_editor/tiny/plugins/media/helpers.isPercentageValue}
 */
export const isPercentageValue = (value) => {
    window.console.warn(`This function is deprecated.
        Please use core_editor/tiny/plugins/media/helpers.isPercentageValue instead.`);
    return Helpers.isPercentageValue(value);
};
