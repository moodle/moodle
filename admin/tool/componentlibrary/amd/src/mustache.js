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
 * Render mustache template examples within the component library.
 *
 * @module     tool_componentlibrary/mustache
 * @copyright  2021 Bas Brands <bas@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import selectors from 'tool_componentlibrary/selectors';
import Ajax from 'core/ajax';
import Config from 'core/config';
import Templates from 'core/templates';
import Log from 'core/log';
import Notification from 'core/notification';

/**
 * Handle a template loaded response.
 *
 * @method
 * @private
 * @param {String} container The template container
 * @param {String} templateName The template name
 * @param {String} context Data for the template.
 */
const renderTemplate = async(container, templateName, context) => {
    try {
        context = JSON.parse(context);
    } catch (e) {
        Log.debug('Could not parse json example context for template.');
        Log.debug(e);
    }

    const {html, js} = await Templates.renderForPromise(templateName, context);

    const rendercontainer = container.querySelector(selectors.mustacherendered);

    // Load the rendered content in the renderer tab.
    await Templates.replaceNodeContents(rendercontainer, html, js);
};

/**
 * Load the a template source from Moodle.
 *
 * @method
 * @private
 * @param {String} container The template container
 */
const loadTemplate = container => {
        const sourcecontainer = container.querySelector(selectors.mustachesource);
        const contextcontainer = container.querySelector(selectors.mustachecontext);
        const templateName = container.dataset.template;
        let context = container.querySelector(selectors.mustacherawcontext).textContent;

        const parts = templateName.split('/');
        const component = parts.shift();
        const name = parts.join('/');

        const request = {
            methodname: 'core_output_load_template',
            args: {
                component: component,
                template: name,
                themename: Config.theme,
                includecomments: true
            }
        };

        Ajax.call([request])[0]
            .done((source) => {
                // Load the source template in Template tab.
                sourcecontainer.textContent = source;
                if (!context) {
                    const example = source.match(/Example context \(json\):([\s\S]+?)(}})/);
                    context = example[1];
                    // Load the variables in the Variables tab.
                    const precontainer = document.createElement("pre");
                    precontainer.innerHTML = JSON.stringify(JSON.parse(context), null, 4);
                    contextcontainer.parentNode.appendChild(precontainer);
                    contextcontainer.classList.add('d-none');
                }
                renderTemplate(container, templateName, context);
            })
            .fail(Notification.exception);
};

/**
 * Initialize the module.
 *
 * @method
 */
export const mustache = () => {
    document.querySelectorAll(selectors.mustachecode).forEach((container) => {
        loadTemplate(container);
    });
};
