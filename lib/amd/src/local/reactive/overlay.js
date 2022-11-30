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
 * Element overlay methods.
 *
 * This module is used to create overlay information on components. For example
 * to generate or destroy file drop-zones.
 *
 * @module     core/local/reactive/overlay
 * @copyright  2022 Ferran Recio <ferran@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import Templates from 'core/templates';
import Prefetch from 'core/prefetch';

// Prefetch the overlay html.
const overlayTemplate = 'core/local/reactive/overlay';
Prefetch.prefetchTemplate(overlayTemplate);

/**
 * @var {boolean} isInitialized if the module is capturing the proper page events.
 */
let isInitialized = false;

/**
 * @var {Object} isInitialized if the module is capturing the proper page events.
 */
const selectors = {
    OVERLAY: "[data-overlay]",
    REPOSITION: "[data-overlay-dynamic]",
    NAVBAR: "nav.navbar.fixed-top",
};

/**
 * Adds an overlay to a specific page element.
 *
 * @param {Object} definition the overlay definition.
 * @param {String|Promise} definition.content an optional overlay content.
 * @param {String|Promise} definition.icon an optional icon content.
 * @param {String} definition.classes an optional CSS classes
 * @param {HTMLElement} parent the parent object
 * @return {HTMLElement|undefined} the new page element.
 */
export const addOverlay = async(definition, parent) => {
    // Validate non of the passed params is a promise.
    if (definition.content && typeof definition.content !== 'string') {
        definition.content = await definition.content;
    }
    if (definition.icon && typeof definition.icon !== 'string') {
        definition.icon = await definition.icon;
    }
    const data = {
        content: definition.content,
        css: definition.classes ?? 'file-drop-zone',
    };
    let overlay;
    try {
        const {html, js} = await Templates.renderForPromise(overlayTemplate, data);
        Templates.appendNodeContents(parent, html, js);
        overlay = parent.querySelector(selectors.OVERLAY);
        rePositionPreviewInfoElement(overlay);
        init();
    } catch (error) {
        throw error;
    }
    return overlay;
};

/**
 * Adds an overlay to a specific page element.
 *
 * @param {HTMLElement} overlay the parent object
 */
export const removeOverlay = (overlay) => {
    if (!overlay || !overlay.parentNode) {
        return;
    }
    // Remove any forced parentNode position.
    if (overlay.dataset?.overlayPosition) {
        delete overlay.parentNode.style.position;
    }
    overlay.parentNode.removeChild(overlay);
};

export const removeAllOverlays = () => {
    document.querySelectorAll(selectors.OVERLAY).forEach(
        (overlay) => {
            removeOverlay(overlay);
        }
    );
};

/**
 * Re-position the preview information element by calculating the section position.
 *
 * @param {Object} overlay the overlay element.
 */
const rePositionPreviewInfoElement = function(overlay) {
    if (!overlay) {
        throw new Error('Inexistent overlay element');
    }
    // Add relative position to the parent object.
    if (!overlay.parentNode?.style?.position) {
        overlay.parentNode.style.position = 'relative';
        overlay.dataset.overlayPosition = "true";
    }
    // Get the element to reposition.
    const target = overlay.querySelector(selectors.REPOSITION);
    if (!target) {
        return;
    }
    // Get the new bounds.
    const rect = overlay.getBoundingClientRect();
    const sectionHeight = parseInt(window.getComputedStyle(overlay).height, 10);
    const sectionOffset = rect.top;
    const previewHeight = parseInt(window.getComputedStyle(target).height, 10) +
        (2 * parseInt(window.getComputedStyle(target).padding, 10));
    // Calculate the new target position.
    let top, bottom;
    if (sectionOffset < 0) {
        if (sectionHeight + sectionOffset >= previewHeight) {
            // We have enough space here, just stick the preview to the top.
            let offSetTop = 0 - sectionOffset;
            const navBar = document.querySelector(selectors.NAVBAR);
            if (navBar) {
                offSetTop = offSetTop + navBar.offsetHeight;
            }
            top = offSetTop + 'px';
            bottom = 'unset';
        } else {
            // We do not have enough space here, just stick the preview to the bottom.
            top = 'unset';
            bottom = 0;
        }
    } else {
        top = 0;
        bottom = 'unset';
    }

    target.style.top = top;
    target.style.bottom = bottom;
};

// Update overlays when the page scrolls.
const init = () => {
    if (isInitialized) {
        return;
    }
    // Add scroll events.
    document.addEventListener('scroll', () => {
        document.querySelectorAll(selectors.OVERLAY).forEach(
            (overlay) => {
                rePositionPreviewInfoElement(overlay);
            }
        );
    }, true);
};
