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

import Templates from 'core/templates';

/**
 * Renders and inserts the body template for inserting an image into the modal.
 *
 * @param {object} templateContext - The context for rendering the template.
 * @param {HTMLElement} root - The root element where the template will be inserted.
 * @returns {Promise<void>}
 */
export const bodyImageInsert = async(templateContext, root) => {
    return Templates.renderForPromise('tiny_media/insert_image_modal_insert', {...templateContext})
    .then(({html, js}) => {
        Templates.replaceNodeContents(root.querySelector('.tiny_image_body_template'), html, js);
        return;
    })
    .catch(error => {
        window.console.log(error);
    });
};

/**
 * Renders and inserts the footer template for inserting an image into the modal.
 *
 * @param {object} templateContext - The context for rendering the template.
 * @param {HTMLElement} root - The root element where the template will be inserted.
 * @returns {Promise<void>}
 */
export const footerImageInsert = async(templateContext, root) => {
    return Templates.renderForPromise('tiny_media/insert_image_modal_insert_footer', {...templateContext})
    .then(({html, js}) => {
        Templates.replaceNodeContents(root.querySelector('.tiny_image_footer_template'), html, js);
        return;
    })
    .catch(error => {
        window.console.log(error);
    });
};
