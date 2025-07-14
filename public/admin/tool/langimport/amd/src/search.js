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
 * Add search filtering of available language packs
 *
 * @module      tool_langimport/search
 * @copyright   2021 Paul Holden <paulh@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import Pending from 'core/pending';
import {debounce} from 'core/utils';

const SELECTORS = {
    AVAILABLE_LANG_SELECT: 'select',
    AVAILABLE_LANG_SEARCH: '[data-action="search"]',
};

const DEBOUNCE_TIMER = 250;

/**
 * Initialize module
 *
 * @param {Element} form
 */
export const init = form => {
    const availableLangsElement = form.querySelector(SELECTORS.AVAILABLE_LANG_SELECT);

    const availableLangsFilter = (event) => {
        const pendingPromise = new Pending('tool_langimport/search:filter');

        // Remove existing options.
        availableLangsElement.querySelectorAll('option').forEach((option) => {
            option.remove();
        });

        // Filter for matching languages.
        const searchTerm = event.target.value.toLowerCase();
        const availableLanguages = JSON.parse(availableLangsElement.dataset.availableLanguages);
        const filteredLanguages = Object.keys(availableLanguages).reduce((matches, langcode) => {
            if (availableLanguages[langcode].toLowerCase().includes(searchTerm)) {
                matches[langcode] = availableLanguages[langcode];
            }
            return matches;
        }, []);

        // Re-create filtered options.
        Object.entries(filteredLanguages).forEach(([langcode, langname]) => {
            const option = document.createElement('option');
            option.value = langcode;
            option.innerText = langname;
            availableLangsElement.append(option);
        });

        pendingPromise.resolve();
    };

    // Cache initial available language options.
    const availableLanguages = {};
    availableLangsElement.querySelectorAll('option').forEach((option) => {
        availableLanguages[option.value] = option.text;
    });
    availableLangsElement.dataset.availableLanguages = JSON.stringify(availableLanguages);

    // Register event listeners on the search element.
    const availableLangsSearch = form.querySelector(SELECTORS.AVAILABLE_LANG_SEARCH);
    availableLangsSearch.addEventListener('keydown', (event) => {
        if (event.key === 'Enter') {
            event.preventDefault();
        }
    });

    // Debounce the event listener to allow the user to finish typing.
    const availableLangsSearchDebounce = debounce(availableLangsFilter, DEBOUNCE_TIMER);
    availableLangsSearch.addEventListener('keyup', (event) => {
        const pendingPromise = new Pending('tool_langimport/search:keyup');

        availableLangsSearchDebounce(event);
        setTimeout(() => {
            pendingPromise.resolve();
        }, DEBOUNCE_TIMER);
    });
};
