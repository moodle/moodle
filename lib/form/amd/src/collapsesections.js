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
    FORMHEADER: '.fheader',
    FORMCONTAINER: '.fcontainer',
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
    const formParent = collapsemenu.closest('form');
    collapsemenu.addEventListener('keydown', e => {
        if (e.key === 'Enter' || e.key === ' ') {
            e.preventDefault();
            collapsemenu.click();
        }
    });

    // Override default collapse class if all containers are expanded on page load
    let expandedcount = 0;
    const formcontainercount = $(SELECTORS.FORMCONTAINER).length;
    $(SELECTORS.FORMCONTAINER).each((_, collapsecontainer) => {
        if (collapsecontainer.classList.contains(CLASSES.SHOW)) {
            expandedcount++;
        }
    });

    if (formcontainercount === expandedcount) {
        collapsemenu.classList.remove(CLASSES.COLLAPSED);
        collapsemenu.setAttribute('aria-expanded', true);
    }

    collapsemenu.addEventListener('click', () => {
        let action = 'hide';
        if (collapsemenu.classList.contains(CLASSES.COLLAPSED)) {
            action = 'show';
        }

        formParent.querySelectorAll(SELECTORS.FORMCONTAINER).forEach((collapsecontainer) => {
            $(collapsecontainer).collapse(action);
        });
    });

    // Ensure collapse menu button adds aria-controls attribute referring to each collapsible element.
    const collapseElements = $(SELECTORS.FORMHEADER);
    const collapseElementIds = [...collapseElements].map((element, index) => {
        element.id = element.id || `collapseElement-${index}`;
        return element.id;
    });
    collapsemenu.setAttribute('aria-controls', collapseElementIds.join(' '));

    $(SELECTORS.FORMCONTAINER).on('hidden.bs.collapse', () => {
        let allCollapsed = true;

        formParent.querySelectorAll(SELECTORS.FORMCONTAINER).forEach((collapsecontainer) => {
            if (collapsecontainer.classList.contains(CLASSES.SHOW)) {
                allCollapsed = false;
            }
        });

        if (allCollapsed) {
            collapsemenu.classList.add(CLASSES.COLLAPSED);
            collapsemenu.setAttribute('aria-expanded', false);
        }
    });
    $(SELECTORS.FORMCONTAINER).on('shown.bs.collapse', () => {
        let allExpanded = true;

        formParent.querySelectorAll(SELECTORS.FORMCONTAINER).forEach((collapsecontainer) => {
            if (!collapsecontainer.classList.contains(CLASSES.SHOW)) {
                allExpanded = false;
            }
        });

        if (allExpanded) {
            collapsemenu.classList.remove(CLASSES.COLLAPSED);
            collapsemenu.setAttribute('aria-expanded', true);
        }
    });
    pendingPromise.resolve();
};
