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
    // All jQuery in this code can be replaced when MDL-79179 is integrated.
    const pendingPromise = new Pending('core_form/collapsesections');
    const collapsemenu = document.querySelector(collapsesections);
    collapsemenu.addEventListener('click', () => {
        let action = 'hide';
        if (collapsemenu.classList.contains(CLASSES.COLLAPSED)) {
            action = 'show';
        }

        document.querySelectorAll(SELECTORS.FORMCONTAINER).forEach((collapsecontainer) => {
            $(collapsecontainer).collapse(action);
        });
    });
    $(SELECTORS.FORMCONTAINER).on('hidden.bs.collapse', () => {
        let allcollapsed = true;
        $(SELECTORS.FORMCONTAINER).each((_, collapsecontainer) => {
            if (collapsecontainer.classList.contains(CLASSES.SHOW)) {
                allcollapsed = false;
            }
        });
        if (allcollapsed) {
            collapsemenu.classList.add(CLASSES.COLLAPSED);
        }
    });
    $(SELECTORS.FORMCONTAINER).on('shown.bs.collapse', () => {
        var allexpanded = true;
        $(SELECTORS.FORMCONTAINER).each((_, collapsecontainer) => {
            if (!collapsecontainer.classList.contains(CLASSES.SHOW)) {
                allexpanded = false;
            }
        });

        if (allexpanded) {
            collapsemenu.classList.remove(CLASSES.COLLAPSED);
        }
    });
    pendingPromise.resolve();
};
