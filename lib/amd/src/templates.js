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
 * Template renderer for Moodle. Load and render Moodle templates with Mustache.
 *
 * @module     core/templates
 * @copyright  2015 Damyon Wiese <damyon@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since      2.9
 */

import $ from 'jquery';
import * as config from 'core/config';
import * as filterEvents from 'core_filters/events';
import * as Y from 'core/yui';
import Renderer from './local/templates/renderer';
import {getNormalisedComponent} from 'core/utils';

/**
 * Execute a block of JS returned from a template.
 * Call this AFTER adding the template HTML into the DOM so the nodes can be found.
 *
 * @method runTemplateJS
 * @param {string} source - A block of javascript.
 */
const runTemplateJS = (source) => {
    if (source.trim() !== '') {
        // Note. We continue to use jQuery here because people are doing some dumb things
        // and we need to find, seek, and destroy first.
        // In particular, people are providing a mixture of JS, and HTML content here.
        // jQuery is someohow, magically, detecting this and putting tags into tags.
        const newScript = $('<script>').attr('type', 'text/javascript').html(source);
        $('head').append(newScript);
        if (newScript.find('script').length) {
            window.console.error(
                'Template JS contains a script tag. This is not allowed. Only raw JS should be present here.',
                source,
            );
        }
    }
};

/**
 * Do some DOM replacement and trigger correct events and fire javascript.
 *
 * @method domReplace
 * @param {JQuery} element - Element or selector to replace.
 * @param {String} newHTML - HTML to insert / replace.
 * @param {String} newJS - Javascript to run after the insertion.
 * @param {Boolean} replaceChildNodes - Replace only the childnodes, alternative is to replace the entire node.
 * @return {Array} The list of new DOM Nodes
 * @fires event:filterContentUpdated
 */
const domReplace = (element, newHTML, newJS, replaceChildNodes) => {
    const replaceNode = $(element);
    if (!replaceNode.length) {
        return [];
    }
    // First create the dom nodes so we have a reference to them.
    const newNodes = $(newHTML);
    // Do the replacement in the page.
    if (replaceChildNodes) {
        // Cleanup any YUI event listeners attached to any of these nodes.
        const yuiNodes = new Y.NodeList(replaceNode.children().get());
        yuiNodes.destroy(true);

        // JQuery will cleanup after itself.
        replaceNode.empty();
        replaceNode.append(newNodes);
    } else {
        // Cleanup any YUI event listeners attached to any of these nodes.
        const yuiNodes = new Y.NodeList(replaceNode.get());
        yuiNodes.destroy(true);

        // JQuery will cleanup after itself.
        replaceNode.replaceWith(newNodes);
    }
    // Run any javascript associated with the new HTML.
    runTemplateJS(newJS);
    // Notify all filters about the new content.
    filterEvents.notifyFilterContentUpdated(newNodes);

    return newNodes.get();
};

/**
 * Prepend some HTML to a node and trigger events and fire javascript.
 *
 * @method domPrepend
 * @param {jQuery|String} element - Element or selector to prepend HTML to
 * @param {String} html - HTML to prepend
 * @param {String} js - Javascript to run after we prepend the html
 * @return {Array} The list of new DOM Nodes
 * @fires event:filterContentUpdated
 */
const domPrepend = (element, html, js) => {
    const node = $(element);
    if (!node.length) {
        return [];
    }

    // Prepend the html.
    const newContent = $(html);
    node.prepend(newContent);
    // Run any javascript associated with the new HTML.
    runTemplateJS(js);
    // Notify all filters about the new content.
    filterEvents.notifyFilterContentUpdated(node);

    return newContent.get();
};

/**
 * Append some HTML to a node and trigger events and fire javascript.
 *
 * @method domAppend
 * @param {jQuery|String} element - Element or selector to append HTML to
 * @param {String} html - HTML to append
 * @param {String} js - Javascript to run after we append the html
 * @return {Array} The list of new DOM Nodes
 * @fires event:filterContentUpdated
 */
const domAppend = (element, html, js) => {
    const node = $(element);
    if (!node.length) {
        return [];
    }
    // Append the html.
    const newContent = $(html);
    node.append(newContent);
    // Run any javascript associated with the new HTML.
    runTemplateJS(js);
    // Notify all filters about the new content.
    filterEvents.notifyFilterContentUpdated(node);

    return newContent.get();
};

const wrapPromiseInWhenable = (promise) => $.when(new Promise((resolve, reject) => {
    promise.then(resolve).catch(reject);
}));

export default {
    // Public variables and functions.
    /**
     * Every call to render creates a new instance of the class and calls render on it. This
     * means each render call has it's own class variables.
     *
     * @method render
     * @param {string} templateName - should consist of the component and the name of the template like this:
     *                              core/menu (lib/templates/menu.mustache) or
     *                              tool_bananas/yellow (admin/tool/bananas/templates/yellow.mustache)
     * @param {Object} context - Could be array, string or simple value for the context of the template.
     * @param {string} themeName - Name of the current theme.
     * @return {Promise} JQuery promise object resolved when the template has been rendered.
     */
    render: (templateName, context, themeName = config.theme) => {
        const renderer = new Renderer();

        // Turn the Native Promise into a jQuery Promise for backwards compatability.
        return $.when(new Promise((resolve, reject) => {
            renderer.render(templateName, context, themeName)
            .then(resolve)
            .catch(reject);
        }))
        .then(({html, js}) => $.Deferred().resolve(html, js));
    },

    /**
     * Prefetch a set of templates without rendering them.
     *
     * @method getTemplate
     * @param {Array} templateNames The list of templates to fetch
     * @param {String} [themeName=config.themeName] The name of the theme to use
     * @returns {Promise}
     */
    prefetchTemplates: (templateNames, themeName = config.theme) => {
        const Loader = Renderer.getLoader();

        return Loader.prefetchTemplates(templateNames, themeName);
    },

    /**
     * Every call to render creates a new instance of the class and calls render on it. This
     * means each render call has it's own class variables.
     *
     * This alernate to the standard .render() function returns the html and js in a single object suitable for a
     * native Promise.
     *
     * @method renderForPromise
     * @param {string} templateName - should consist of the component and the name of the template like this:
     *                              core/menu (lib/templates/menu.mustache) or
     *                              tool_bananas/yellow (admin/tool/bananas/templates/yellow.mustache)
     * @param {Object} context - Could be array, string or simple value for the context of the template.
     * @param {string} themeName - Name of the current theme.
     * @return {Promise} JQuery promise object resolved when the template has been rendered.
     */
    renderForPromise: (templateName, context, themeName) => {
        const renderer = new Renderer();
        return renderer.render(templateName, context, themeName);
    },

    /**
     * Every call to renderIcon creates a new instance of the class and calls renderIcon on it. This
     * means each render call has it's own class variables.
     *
     * @method renderPix
     * @param {string} key - Icon key.
     * @param {string} component - Icon component
     * @param {string} title - Icon title
     * @return {Promise} JQuery promise object resolved when the pix has been rendered.
     */
    renderPix: (key, component, title) => {
        const renderer = new Renderer();
        return wrapPromiseInWhenable(renderer.renderIcon(
            key,
            getNormalisedComponent(component),
            title
        ));
    },

    /**
     * Execute a block of JS returned from a template.
     * Call this AFTER adding the template HTML into the DOM so the nodes can be found.
     *
     * @method runTemplateJS
     * @param {string} source - A block of javascript.
     */
    runTemplateJS: runTemplateJS,

    /**
     * Replace a node in the page with some new HTML and run the JS.
     *
     * @method replaceNodeContents
     * @param {JQuery} element - Element or selector to replace.
     * @param {String} newHTML - HTML to insert / replace.
     * @param {String} newJS - Javascript to run after the insertion.
     * @return {Array} The list of new DOM Nodes
     */
    replaceNodeContents: (element, newHTML, newJS) => domReplace(element, newHTML, newJS, true),

    /**
     * Insert a node in the page with some new HTML and run the JS.
     *
     * @method replaceNode
     * @param {JQuery} element - Element or selector to replace.
     * @param {String} newHTML - HTML to insert / replace.
     * @param {String} newJS - Javascript to run after the insertion.
     * @return {Array} The list of new DOM Nodes
     */
    replaceNode: (element, newHTML, newJS) => domReplace(element, newHTML, newJS, false),

    /**
     * Prepend some HTML to a node and trigger events and fire javascript.
     *
     * @method prependNodeContents
     * @param {jQuery|String} element - Element or selector to prepend HTML to
     * @param {String} html - HTML to prepend
     * @param {String} js - Javascript to run after we prepend the html
     * @return {Array} The list of new DOM Nodes
     */
    prependNodeContents: (element, html, js) => domPrepend(element, html, js),

    /**
     * Append some HTML to a node and trigger events and fire javascript.
     *
     * @method appendNodeContents
     * @param {jQuery|String} element - Element or selector to append HTML to
     * @param {String} html - HTML to append
     * @param {String} js - Javascript to run after we append the html
     * @return {Array} The list of new DOM Nodes
     */
    appendNodeContents: (element, html, js) => domAppend(element, html, js),
};
