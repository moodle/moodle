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
 * Collapse or expand all form sections on clicking the expand all / collapse al link.
 *
 * @module core_form/collapsesections
 * @copyright 2021 Bas Brands
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since 4.0
 */

import $ from 'jquery';
import Pending from 'core/pending';

const SELECTORS = {
    FORM: '.mform',
    FORMHEADER: '.fheader',
    FORMCONTAINER: 'fieldset > .fcontainer',
};

const CLASSES = {
    SHOW: 'show',
    COLLAPSED: 'collapsed'
};

/**
 * Initialises the form section collapse / expand action.
 *
 * @param {string} collapsesections the collapse/expand link id.
 */
export const init = collapsesections => {
    // All jQuery in this code can be replaced when MDL-71979 is integrated (move to Bootstrap 5).
    const pendingPromise = new Pending('core_form/collapsesections');
    const collapsemenu = document.querySelector(collapsesections);

    const formParent = collapsemenu.closest(SELECTORS.FORM);
    const formContainers = formParent.querySelectorAll(SELECTORS.FORMCONTAINER);

    collapsemenu.addEventListener('keydown', e => {
        if (e.key === 'Enter' || e.key === ' ') {
            e.preventDefault();
            collapsemenu.click();
        }
    });

    // Override default collapse class if all containers are expanded on page load
    let expandedcount = 0;
    const formcontainercount = $(SELECTORS.FORMCONTAINER).length;
    formContainers.forEach(container => {
        if (container.classList.contains(CLASSES.SHOW)) {
            expandedcount++;
        }
    });

    if (formcontainercount === expandedcount) {
        collapsemenu.classList.remove(CLASSES.COLLAPSED);
        collapsemenu.setAttribute('aria-expanded', true);
    }

    // When the collapse menu is toggled, update each form container to match.
    collapsemenu.addEventListener('click', () => {
        let action = 'hide';
        if (collapsemenu.classList.contains(CLASSES.COLLAPSED)) {
            action = 'show';
        }

        formContainers.forEach(container => $(container).collapse(action));
    });

    // Ensure collapse menu button adds aria-controls attribute referring to each collapsible element.
    const collapseElements = formParent.querySelectorAll(SELECTORS.FORMHEADER);
    const collapseElementIds = [...collapseElements].map((element, index) => {
        element.id = element.id || `collapseElement-${index}`;
        return element.id;
    });
    collapsemenu.setAttribute('aria-controls', collapseElementIds.join(' '));

    // When any form container is toggled, re-calculate collapse menu state.
    $(SELECTORS.FORMCONTAINER).on('hidden.bs.collapse', () => {
        const allCollapsed = [...formContainers].every(container => !container.classList.contains(CLASSES.SHOW));
        if (allCollapsed) {
            collapsemenu.classList.add(CLASSES.COLLAPSED);
            collapsemenu.setAttribute('aria-expanded', false);
        }
    });
    $(SELECTORS.FORMCONTAINER).on('shown.bs.collapse', () => {
        const allExpanded = [...formContainers].every(container => container.classList.contains(CLASSES.SHOW));
        if (allExpanded) {
            collapsemenu.classList.remove(CLASSES.COLLAPSED);
            collapsemenu.setAttribute('aria-expanded', true);
        }
    });
    pendingPromise.resolve();
};
