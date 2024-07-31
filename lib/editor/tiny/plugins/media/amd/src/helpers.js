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
 * Tiny media plugin helpers for image and embed.
 *
 * @module      tiny_media/helpers
 * @copyright   2024 Stevani Andolo <stevani@hotmail.com.au>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import Templates from 'core/templates';
import Selectors from './selectors';

/**
 * Renders and inserts the body template for inserting an media into the modal.
 *
 * @param {object} templateContext - The context for rendering the template.
 * @param {HTMLElement} root - The root element where the template will be inserted.
 * @returns {Promise<void>}
 */
export const body = async(templateContext, root) => {
    return Templates.renderForPromise(templateContext.bodyTemplate, {...templateContext})
    .then(({html, js}) => {
        Templates.replaceNodeContents(root.querySelector(Selectors[templateContext.selector].elements.bodyTemplate), html, js);
        return;
    })
    .catch(error => {
        window.console.log(error);
    });
};

/**
 * Renders and inserts the footer template for inserting an media into the modal.
 *
 * @param {object} templateContext - The context for rendering the template.
 * @param {HTMLElement} root - The root element where the template will be inserted.
 * @returns {Promise<void>}
 */
export const footer = async(templateContext, root) => {
    return Templates.renderForPromise(templateContext.footerTemplate, {...templateContext})
    .then(({html, js}) => {
        Templates.replaceNodeContents(root.querySelector(Selectors[templateContext.selector].elements.footerTemplate), html, js);
        return;
    })
    .catch(error => {
        window.console.log(error);
    });
};

/**
 * Set extra properties on an instance using incoming data.
 *
 * @param {object} instance
 * @param {object} data
 * @return {object} Modified instance
 */
export const setPropertiesFromData = (instance, data) => {
    for (const property in data) {
        if (typeof data[property] !== 'function') {
            instance[property] = data[property];
        }
    }
    return instance;
};
