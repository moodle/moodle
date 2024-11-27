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
 * Shows the footer content in a popover.
 *
 * @module     theme_boost/footer-popover
 * @copyright  2021 Bas Brands
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import Popover from './bootstrap/popover';

const SELECTORS = {
    FOOTERCONTAINER: '[data-region="footer-container-popover"]',
    FOOTERCONTENT: '[data-region="footer-content-popover"]',
    FOOTERBUTTON: '[data-action="footer-popover"]',
    FOOTERARROW: '[data-region="footer-container-popover"] .popover-arrow',
};

let footerIsShown = false;

export const init = () => {
    const container = document.querySelector(SELECTORS.FOOTERCONTAINER);
    const footerButton = document.querySelector(SELECTORS.FOOTERBUTTON);
    const footerArrow = document.querySelector(SELECTORS.FOOTERARROW);

    new Popover(footerButton, {
        content: getFooterContent,
        container: container,
        html: true,
        placement: 'top',
        customClass: 'footer',
        trigger: 'click',
        boundary: 'viewport',
        modifiers: [
            {
                name: 'preventOverflow',
                options: {
                    boundariesElement: 'viewport',
                    padding: 48,
                },
            },
            {
                name: 'arrow',
                options: {
                    element: footerArrow,
                },
            },
        ]
    });

    document.addEventListener('click', e => {
        if (footerIsShown && !e.target.closest(SELECTORS.FOOTERCONTAINER)) {
            Popover.getInstance(footerButton).hide();
        }
    },
    true);

    document.addEventListener('keydown', e => {
        if (footerIsShown && e.key === 'Escape') {
            Popover.getInstance(footerButton).hide();
            footerButton.focus();
        }
    });

    document.addEventListener('focus', e => {
        if (footerIsShown && !e.target.closest(SELECTORS.FOOTERCONTAINER)) {
            Popover.getInstance(footerButton).hide();
        }
    },
    true);

    footerButton.addEventListener('show.bs.popover', () => {
        footerIsShown = true;
    });

    footerButton.addEventListener('hide.bs.popover', () => {
        footerIsShown = false;
    });
};

/**
 * Get the footer content for popover.
 *
 * @returns {String} HTML string
 * @private
 */
const getFooterContent = () => {
    return document.querySelector(SELECTORS.FOOTERCONTENT).innerHTML;
};

export {
    Popover
};
