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
 * Initializes and handles events fow 'showmore' components.
 *
 * @module      core/showmore
 * @copyright   2023 Mikel Martín <mikel@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

const Selectors = {
    actions: {
        toggleContent: '[data-action="toggle-content"]'
    },
    regions: {
        main: '[data-region="showmore"]'
    }
};

let initialized = false;

/**
 * Initialise module
 *
 * @method
 */
export const init = () => {

    if (initialized) {
        // We already added the event listeners (can be called multiple times by mustache template).
        return;
    }

    // Listen for click events.
    document.addEventListener('click', (event) => {
        const toggleContent = event.target.closest(Selectors.actions.toggleContent);
        if (toggleContent) {
            const region = toggleContent.closest(Selectors.regions.main);
            region.classList.toggle('collapsed');
            const toggleButton = region.querySelector(Selectors.actions.toggleContent);
            toggleButton.setAttribute('aria-expanded', !region.classList.contains('collapsed'));
        }
    });

    initialized = true;
};
