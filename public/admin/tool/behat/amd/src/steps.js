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

import Ajax from 'core/ajax';
import Templates from 'core/templates';
import PendingJS from 'core/pending';

/**
 * Enhancements for the step definitions page.
 *
 * @module tool_behat/steps
 * @copyright 2022 Catalyst IT EU
 * @author Mark Johnson <mark.johnson@catalyst-eu.net>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Call the get_entity_generator web service function
 *
 * Takes the name of an entity generator and returns an object containing a list of the required fields.
 *
 * @param {String} entityType
 * @returns {Promise}
 */
const getGeneratorEntities = (entityType) => Ajax.call([{
    methodname: 'tool_behat_get_entity_generator',
    args: {entitytype: entityType}
}])[0];

/**
 * Render HTML for required fields
 *
 * Takes the entity data returned from getGeneratorEntities and renders the HTML to display the required fields.
 *
 * @param {String} entityData
 * @return {Promise}
 */
const getRequiredFieldsContent = (entityData) => {
    if (!entityData.required?.length) {
        return Promise.resolve({
            html: '',
            js: ''
        });
    }
    return Templates.renderForPromise('tool_behat/steprequiredfields', {fields: entityData.required});
};

export const init = () => {
    // When an entity is selected in the "the following exist" step, fetch and display the required fields.
    document.addEventListener('change', async(e) => {
        const entityElement = e.target.closest('.entities');
        const stepElement = e.target.closest('.stepcontent');
        if (!entityElement || !stepElement) {
            return;
        }

        const pendingPromise = new PendingJS('tool_behat/steps:change');

        const entityData = await getGeneratorEntities(e.target.value);
        const {html, js} = await getRequiredFieldsContent(entityData);

        const stepRequiredFields = stepElement.querySelector('.steprequiredfields');
        if (stepRequiredFields) {
            await Templates.replaceNode(stepRequiredFields, html, js);
        } else {
            await Templates.appendNodeContents(stepElement, html, js);
        }
        pendingPromise.resolve();
    });
};
