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
 * The users current language - this can't be set until MathJax is loaded - so we need to store it.
 * @property {string} lang
 * @default ''
 * @private
 */
let lang = '';

/**
 * Used to prevent configuring MathJax twice.
 * @property {boolean} configured
 * @default false
 * @private
 */
let configured = false;

/**
 * Called by the filter when it is active on any page.
 * This does not load MathJAX yet - it addes the configuration to the head incase it gets loaded later.
 * It also subscribes to the filter-content-updated event so MathJax can respond to content loaded by Ajax.
 *
 * @param {Object} params List of configuration params containing mathjaxconfig (text) and lang
 */
export const configure = (params) => {
    // Add a js configuration object to the head.
    // See "https://docs.mathjax.org/en/v2.7-latest/advanced/dynamic.html"
    const script = document.createElement("script");
    script.type = "text/x-mathjax-config";
    script[(window.opera ? "innerHTML" : "text")] = params.mathjaxconfig;
    document.getElementsByTagName("head")[0].appendChild(script);

    // Save the lang config until MathJax is actually loaded.
    lang = params.lang;

    // Listen for events triggered when new text is added to a page that needs
    // processing by a filter.
    document.addEventListener(eventTypes.filterContentUpdated, contentUpdated);
};

/**
 * Set the correct language for the MathJax menus. Only do this once.
 *
 * @private
 */
const setLocale = () => {
    if (!configured) {
        if (typeof window.MathJax !== "undefined") {
            window.MathJax.Hub.Queue(function() {
                window.MathJax.Localization.setLocale(lang);
            });
            window.MathJax.Hub.Configured();
            configured = true;
        }
    }
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

    // MathJax 2.X does not notify when complete. The best we can do, according to their docs, is to queue a callback.
    // See https://docs.mathjax.org/en/v2.7-latest/advanced/typeset.html
    // Note that the MathJax.Hub.Queue() method will return immediately, regardless of whether the typesetting has taken place
    // or not, so you can not assume that the mathematics is visible after you make this call.
    // That means that things like the size of the container for the mathematics may not yet reflect the size of the
    // typeset mathematics. If you need to perform actions that depend on the mathematics being typeset, you should push those
    // actions onto the MathJax.Hub.queue as well.
    window.MathJax.Hub.Queue(["Typeset", window.MathJax.Hub, node]);
    window.MathJax.Hub.Queue([(node) => {
        // The notifyFilterContentRenderingComplete event takes an Array of NodeElements or a NodeList.
        // We cannot create a NodeList so we use an HTMLElement[].
        notifyFilterContentRenderingComplete([node]);
    }, node]);
};

/**
 * Called by the filter when an equation is found while rendering the page.
 */
export const typeset = () => {
    if (!configured) {
        setLocale();
        const elements = document.getElementsByClassName('filter_mathjaxloader_equation');
        for (const element of elements) {
            if (typeof window.MathJax !== "undefined") {
                typesetNode(element);
            }
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
    const processDelay = window.MathJax.Hub.processSectionDelay;
    // Set the process section delay to 0 when updating the formula.
    window.MathJax.Hub.processSectionDelay = 0;
    // When content is updated never position to hash, it may cause unexpected document scrolling.
    window.MathJax.Hub.Config({positionToHash: false});
    setLocale();
    listOfElementContainMathJax.forEach((mathjaxElements) => {
        mathjaxElements.forEach((node) => typesetNode(node));
    });
    window.MathJax.Hub.processSectionDelay = processDelay;
};
