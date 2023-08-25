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
 * @module filter_mathjaxloader
 * @copyright 2014 Damyon Wiese  <damyon@moodle.com>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
import {eventTypes} from 'core_filters/events';

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
 * @method configure
 * @param {Object} params List of configuration params containing mathjaxconfig (text) and lang
 */
export const configure = (params) => {
    // Add a js configuration object to the head.
    // See "http://docs.mathjax.org/en/latest/dynamic.html#ajax-mathjax"
    let script = document.createElement("script");
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
 * @method setLocale
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
 * Called by the filter when an equation is found while rendering the page.
 *
 * @method typeset
 */
export const typeset = () => {
    if (!configured) {
        setLocale();
        const elements = document.getElementsByClassName('filter_mathjaxloader_equation');
        if (elements) {
            elements.forEach((element) => {
                if (typeof window.MathJax !== "undefined") {
                    window.MathJax.Hub.Queue(["Typeset", window.MathJax.Hub, element]);
                }
            });
        }
    }
};

/**
 * Handle content updated events - typeset the new content.
 *
 * @method contentUpdated
 * @param {CustomEvent} event - Custom event with "nodes" indicating the root of the updated nodes.
 */
export const contentUpdated = (event) => {
    if (typeof window.MathJax === "undefined") {
        return;
    }
    const processDelay = window.MathJax.Hub.processSectionDelay;
    // Set the process section delay to 0 when updating the formula.
    window.MathJax.Hub.processSectionDelay = 0;
    // When content is updated never position to hash, it may cause unexpected document scrolling.
    window.MathJax.Hub.Config({positionToHash: false});
    setLocale();
    // The list of HTMLElements in an Array.
    event.detail.nodes.forEach((node) => {
        const mathjaxElements = node.getElementsByClassName('filter_mathjaxloader_equation');
        mathjaxElements.forEach((node) => {
            window.MathJax.Hub.Queue(["Typeset", window.MathJax.Hub, node]);
        });
    });
    window.MathJax.Hub.processSectionDelay = processDelay;
};
