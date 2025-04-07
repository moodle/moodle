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
 * Backward compatibility for Bootstrap 5.
 *
 * This module silently adapts the current page to Bootstrap 5.
 * When the Boostrap 4 backward compatibility period ends in MDL-84465,
 * this module will be removed.
 *
 * @module     theme_boost/bs4-compat
 * @copyright  2025 Mikel Martín <mikel@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @deprecated since Moodle 5.0
 * @todo       Final deprecation in Moodle 6.0. See MDL-84465.
 */

import {DefaultAllowlist} from './bootstrap/util/sanitizer';
import Popover from 'theme_boost/bootstrap/popover';
import Tooltip from 'theme_boost/bootstrap/tooltip';
import log from 'core/log';

/**
 * List of Bootstrap 4 elements to replace with Bootstrap 5 elements.
 * This list is based on the Bootstrap 4 to 5 migration guide:
 * https://getbootstrap.com/docs/5.0/migration/
 *
 * The list is not exhaustive and it will be updated as needed.
 */
const bootstrapElements = [
    {
        selector: '.alert button.close',
        replacements: [
            {bs4: 'data-dismiss', bs5: 'data-bs-dismiss'},
        ],
    },
    {
        selector: '[data-toggle="modal"]',
        replacements: [
            {bs4: 'data-toggle', bs5: 'data-bs-toggle'},
            {bs4: 'data-target', bs5: 'data-bs-target'},
        ],
    },
    {
        selector: '.modal .modal-header button.close',
        replacements: [
            {bs4: 'data-dismiss', bs5: 'data-bs-dismiss'},
        ],
    },
    {
        selector: '[data-toggle="dropdown"]',
        replacements: [
            {bs4: 'data-toggle', bs5: 'data-bs-toggle'},
        ],
    },
    {
        selector: '[data-toggle="collapse"]',
        replacements: [
            {bs4: 'data-toggle', bs5: 'data-bs-toggle'},
            {bs4: 'data-target', bs5: 'data-bs-target'},
            {bs4: 'data-parent', bs5: 'data-bs-parent'},
        ],
    },
    {
        selector: '.carousel [data-slide]',
        replacements: [
            {bs4: 'data-slide', bs5: 'data-bs-slide'},
            {bs4: 'data-target', bs5: 'data-bs-target'},
        ],
    },
    {
        selector: '[data-toggle="tooltip"]',
        replacements: [
            {bs4: 'data-toggle', bs5: 'data-bs-toggle'},
            {bs4: 'data-placement', bs5: 'data-bs-placement'},
            {bs4: 'data-animation', bs5: 'data-bs-animation'},
            {bs4: 'data-delay', bs5: 'data-bs-delay'},
            {bs4: 'data-title', bs5: 'data-bs-title'},
            {bs4: 'data-html', bs5: 'data-bs-html'},
            {bs4: 'data-trigger', bs5: 'data-bs-trigger'},
            {bs4: 'data-selector', bs5: 'data-bs-selector'},
            {bs4: 'data-container', bs5: 'data-bs-container'},
        ],
    },
    {
        selector: '[data-toggle="popover"]',
        replacements: [
            {bs4: 'data-toggle', bs5: 'data-bs-toggle'},
            {bs4: 'data-content', bs5: 'data-bs-content'},
            {bs4: 'data-placement', bs5: 'data-bs-placement'},
            {bs4: 'data-animation', bs5: 'data-bs-animation'},
            {bs4: 'data-delay', bs5: 'data-bs-delay'},
            {bs4: 'data-title', bs5: 'data-bs-title'},
            {bs4: 'data-html', bs5: 'data-bs-html'},
            {bs4: 'data-trigger', bs5: 'data-bs-trigger'},
            {bs4: 'data-selector', bs5: 'data-bs-selector'},
            {bs4: 'data-container', bs5: 'data-bs-container'},
        ],
    },
    {
        selector: '[data-toggle="tab"]',
        replacements: [
            {bs4: 'data-toggle', bs5: 'data-bs-toggle'},
            {bs4: 'data-target', bs5: 'data-bs-target'},
        ],
    },
];

/**
 * Replace Bootstrap 4 attributes with Bootstrap 5 attributes.
 *
 * @param {HTMLElement} container The element to search for Bootstrap 4 elements.
 */
const replaceBootstrap4Attributes = (container) => {
    for (const bootstrapElement of bootstrapElements) {
        const elements = container.querySelectorAll(bootstrapElement.selector);
        for (const element of elements) {
            for (const replacement of bootstrapElement.replacements) {
                if (element.hasAttribute(replacement.bs4)) {
                    element.setAttribute(replacement.bs5, element.getAttribute(replacement.bs4));
                    element.removeAttribute(replacement.bs4);
                    log.debug(`Silent Bootstrap 4 to 5 compatibility: ${replacement.bs4} replaced by ${replacement.bs5}`);
                    log.debug(element);
                }
            }
        }
    }
};

/**
 * Ensure Bootstrap 4 components are initialized.
 *
 * Some elements (tooltip and popovers) needs to be initialized manually after adding the data attributes.
 *
 * @param {HTMLElement} container The element to search for Bootstrap 4 elements.
 */
const initializeBootsrap4Components = (container) => {
    const popoverConfig = {
        container: 'body',
        trigger: 'focus',
        allowList: Object.assign(DefaultAllowlist, {table: [], thead: [], tbody: [], tr: [], th: [], td: []}),
    };
    container.querySelectorAll('[data-bs-toggle="popover"]').forEach((tooltipTriggerEl) => {
        const popOverInstance = Popover.getInstance(tooltipTriggerEl);
        if (!popOverInstance) {
            new Popover(tooltipTriggerEl, popoverConfig);
        }
    });

    container.querySelectorAll('[data-bs-toggle="tooltip"]').forEach((tooltipTriggerEl) => {
        const tooltipInstance = Tooltip.getInstance(tooltipTriggerEl);
        if (!tooltipInstance) {
            new Tooltip(tooltipTriggerEl);
        }
    });
};

/**
 * Init Bootstrap 4 compatibility.
 *
 * @deprecated since Moodle 5.0
 * @param {HTMLElement} element The element to search for Bootstrap 4 elements.
 */
export const init = (element) => {
    if (!element) {
        element = document;
    }
    replaceBootstrap4Attributes(element);
    initializeBootsrap4Components(element);
};
