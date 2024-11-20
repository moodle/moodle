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
 * Add search filtering of capabilities
 *
 * @module      tool_capability/search
 * @copyright   2023 Paul Holden <paulh@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import Pending from 'core/pending';
import {debounce} from 'core/utils';

const Selectors = {
    capabilityOverviewForm: '#capability-overview-form',
    capabilitySelect: '[data-search="capability"]',
    capabilitySearch: '[data-action="search"]',
};

const debounceTimer = 250;

/**
 * Initialize module
 */
export const init = () => {
    const capabilityOverviewForm = document.querySelector(Selectors.capabilityOverviewForm);
    if (!capabilityOverviewForm) {
        return;
    }

    const capabilitySelect = capabilityOverviewForm.querySelector(Selectors.capabilitySelect);
    const capabilitySearch = capabilityOverviewForm.querySelector(Selectors.capabilitySearch);

    const capabilitySelectFilter = searchTerm => {
        const pendingPromise = new Pending('tool_capability/search:filter');

        // Remove existing options, remembering which were previously selected.
        let capabilitySelected = [];
        capabilitySelect.querySelectorAll('option').forEach(option => {
            if (option.selected) {
                capabilitySelected.push(option.value);
            }
            option.remove();
        });

        // Filter for matching capabilities.
        const availableCapabilities = JSON.parse(capabilitySelect.dataset.availableCapabilities);
        const filteredCapabilities = Object.keys(availableCapabilities).reduce((matches, capability) => {
            if (availableCapabilities[capability].toLowerCase().includes(searchTerm)) {
                matches[capability] = availableCapabilities[capability];
            }
            return matches;
        }, []);

        // Re-create filtered options.
        Object.entries(filteredCapabilities).forEach(([capability, capabilityText]) => {
            const option = document.createElement('option');
            option.value = capability;
            option.innerText = capabilityText;
            option.selected = capabilitySelected.indexOf(capability) > -1;
            capabilitySelect.append(option);
        });

        pendingPromise.resolve();
    };

    // Cache initial capability options.
    const availableCapabilities = {};
    capabilitySelect.querySelectorAll('option').forEach(option => {
        availableCapabilities[option.value] = option.text;
    });
    capabilitySelect.dataset.availableCapabilities = JSON.stringify(availableCapabilities);

    // Debounce the event listener on the search element to allow user to finish typing.
    const capabilitySearchDebounce = debounce(capabilitySelectFilter, debounceTimer);
    capabilitySearch.addEventListener('keyup', event => {
        const pendingPromise = new Pending('tool_capability/search:keyup');

        capabilitySearchDebounce(event.target.value.toLowerCase());
        setTimeout(() => {
            pendingPromise.resolve();
        }, debounceTimer);
    });

    // Ensure filter is applied on form load.
    if (capabilitySearch.value !== '') {
        capabilitySelectFilter(capabilitySearch.value.toLowerCase());
    }
};
