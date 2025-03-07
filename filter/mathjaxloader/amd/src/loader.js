// This file is part of Moodle - http://moodle.org/ //
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
 * Mathjax JS Loader.
 *
 * @module filter_mathjaxloader/loader
 * @copyright 2014 Damyon Wiese  <damyon@moodle.com>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
import {
    eventTypes,
    notifyFilterContentRenderingComplete,
} from 'core_filters/events';

/**
 * Called by the filter when it is active on any page.
 * This does not load MathJAX yet - it addes the configuration to the head incase it gets loaded later.
 * It also subscribes to the filter-content-updated event so MathJax can respond to content loaded by Ajax.
 *
 * @param {Object} params List of configuration params containing mathjaxconfig (text) and lang
 */
export const configure = (params) => {
    if (window.MathJax) {
        // Let's still set the locale even if the localization is not yet ported to version 3.2.2
        // https://docs.mathjax.org/en/v3.2-latest/upgrading/v2.html#not-yet-ported-to-version-3.
        window.MathJax.config.locale = params.lang;
    }

    // Listen for events triggered when new text is added to a page that needs
    // processing by a filter.
    document.addEventListener(eventTypes.filterContentUpdated, contentUpdated);
};

/**
 * Add the node to the typeset queue.
 *
 * @param {HTMLElement} node The Node to be processed by MathJax
 * @private
 */
const typesetNode = (node) => {
    if (!(node instanceof HTMLElement)) {
        // We may have been passed a #text node.
        // These cannot be formatted.
        return;
    }

    if (window.MathJax) {
        window.MathJax.typesetPromise([node]).then(() => {
            notifyFilterContentRenderingComplete([node]);
            return;
        })
        .catch(e => {
            window.console.log(e);
        });
    }
};

/**
 * Called by the filter when an equation is found while rendering the page.
 */
export const typeset = () => {
    const elements = document.getElementsByClassName('filter_mathjaxloader_equation');
    for (const element of elements) {
        if (typeof window.MathJax !== "undefined") {
            typesetNode(element);
        }
    }
};

/**
 * Handle content updated events - typeset the new content.
 *
 * @param {CustomEvent} event - Custom event with "nodes" indicating the root of the updated nodes.
 */
export const contentUpdated = (event) => {
    if (typeof window.MathJax === "undefined") {
        return;
    }

    let listOfElementContainMathJax = [];
    let hasMathJax = false;
    // The list of HTMLElements in an Array.
    event.detail.nodes.forEach((node) => {
        if (!(node instanceof HTMLElement)) {
            // We may have been passed a #text node.
            return;
        }
        const mathjaxElements = node.querySelectorAll('.filter_mathjaxloader_equation');
        if (mathjaxElements.length > 0) {
            hasMathJax = true;
        }
        listOfElementContainMathJax.push(mathjaxElements);
    });

    if (!hasMathJax) {
        return;
    }

    listOfElementContainMathJax.forEach((mathjaxElements) => {
        mathjaxElements.forEach((node) => typesetNode(node));
    });
};
