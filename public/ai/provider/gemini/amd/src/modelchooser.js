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
 * AI provider model selection handler.
 *
 * @module     aiprovider_gemini/modelchooser
 * @copyright  2026 Anupama Sarjoshi <anupama.sarjoshi@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import {populateFields, clearFields} from 'core_ai/helper';

const Selectors = {
    fields: {
        selector: '[data-modelchooser-field="selector"]',
        updateButton: '[data-modelchooser-field="updateButton"]',
        modelSettingsContainer: 'id_modelsettingsheadercontainer',
        endpoint: 'input[name="endpoint"]',
        model: 'input[name="model"]',
        custommodel: 'input[name="custommodel"]',
    },
};

/**
 * Update the endpoint field based on the selected model.
 *
 * @param {string} modelName - The name of the selected model.
 */
const updateEndpoint = (modelName) => {
    const endpointField = document.querySelector(Selectors.fields.endpoint);
    const modelEndpoints = JSON.parse(endpointField.getAttribute('data-modelendpoints'));
    if (modelEndpoints[modelName]) {
        endpointField.value = modelEndpoints[modelName];
    }
};

/**
 * Initialise the AI provider chooser.
 */
export const init = () => {
    const modelSelector = document.querySelector(Selectors.fields.selector);
    if (modelSelector) {
        // If we have stored model settings, populate them in their respective fields.
        const storedModelSettings = JSON.parse(modelSelector.getAttribute('data-storedmodelsettings'));
        const modelSettings = storedModelSettings[modelSelector.value];
        const containerId = Selectors.fields.modelSettingsContainer;

        if (modelSettings) {
            populateFields(modelSettings, containerId);
        } else {
            clearFields(containerId);
        }

        modelSelector.addEventListener('change', e => {
            modelSelector.options[e.target.selectedIndex].selected = true;
            const selectedModel = e.target.value;

            // Update the hidden model field. For custom, custom model name comes from custommodel field.
            const modelField = document.querySelector(Selectors.fields.model);
            if (modelField) {
                if (selectedModel !== 'custom') {
                    modelField.value = selectedModel;
                } else {
                    // For custom model, get the model name from the custommodel input field.
                    modelField.value = document.querySelector(Selectors.fields.custommodel).value || '';
                }
            }

            // Update the endpoint field based on the selected model.
            updateEndpoint(modelField.value);

            const form = e.target.closest('form');
            const updateButton = form.querySelector(Selectors.fields.updateButton);
            updateButton.click();
        });
    }
};
