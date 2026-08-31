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
 * Shared collapse controls for course content sections.
 *
 * @module    core_courseformat/local/collapse
 * @copyright 2026 David Woloszyn <david.woloszyn@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import {Collapse} from 'bootstrap';

/**
 * Initialise collapse/expand behaviour for a course content root.
 *
 * @param {HTMLElement} root The element containing the collapsible sections.
 * @param {Object} options Optional configuration.
 * @param {string} options.toggleAllSelector Selector for the all-sections toggle.
 * @param {string} options.collapseSelector Selector for collapsible sections.
 * @param {Function|null} options.onToggleAll Callback receiving the event and collapse state.
 * @returns {Object|null} Collapse controls, or null when no toggle exists.
 */
export const init = (root, options = {}) => {
    const {
        toggleAllSelector = '[data-toggle="toggleall"]',
        collapseSelector = '[data-bs-toggle="collapse"]',
        onToggleAll = null,
    } = options;

    const toggleAll = root.querySelector(toggleAllSelector);

    if (!toggleAll) {
        return null;
    }

    const getCollapseElements = () => Array.from(root.querySelectorAll(collapseSelector)).map(element => {
        if (!element.matches('[data-bs-toggle="collapse"]')) {
            return element;
        }

        const targetSelector = element.dataset.bsTarget ?? element.getAttribute('href');
        return targetSelector ? root.querySelector(targetSelector) : null;
    }).filter(Boolean);

    const isExpanded = element => {
        const toggler = root.querySelector(`[data-bs-target="#${element.id}"], [href="#${element.id}"]`);
        return toggler ? toggler.getAttribute('aria-expanded') === 'true' : element.classList.contains('show');
    };

    const refresh = () => {
        const hasExpanded = getCollapseElements().some(isExpanded);
        toggleAll.classList.toggle('collapsed', !hasExpanded);
        toggleAll.setAttribute('aria-expanded', hasExpanded ? 'true' : 'false');
    };

    toggleAll.setAttribute('aria-controls', getCollapseElements().map(element => element.id).join(' '));

    const toggle = (event) => {
        event.preventDefault();
        const collapse = !toggleAll.classList.contains('collapsed');
        if (onToggleAll) {
            onToggleAll(event, collapse);
            return;
        }

        getCollapseElements().forEach(element => {
            const instance = Collapse.getOrCreateInstance(element, {toggle: false});
            if (collapse) {
                instance.hide();
            } else {
                instance.show();
            }
        });
        refresh();
    };

    toggleAll.addEventListener('click', toggle);
    toggleAll.addEventListener('keydown', event => {
        if (event.key === ' ') {
            toggle(event);
        }
    });

    getCollapseElements().forEach(element => {
        element.addEventListener('shown.bs.collapse', refresh);
        element.addEventListener('hidden.bs.collapse', refresh);
    });

    refresh();
    return {refresh};
};
