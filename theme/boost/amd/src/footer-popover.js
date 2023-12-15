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

import $ from 'jquery';
import Popover from './popover';

const SELECTORS = {
    FOOTERCONTAINER: '[data-region="footer-container-popover"]',
    FOOTERCONTENT: '[data-region="footer-content-popover"]',
    FOOTERBUTTON: '[data-action="footer-popover"]'
};

let footerIsShown = false;

export const init = () => {
    const container = document.querySelector(SELECTORS.FOOTERCONTAINER);
    const footerButton = document.querySelector(SELECTORS.FOOTERBUTTON);

    // All jQuery in this code can be replaced when MDL-71979 is integrated.
    $(footerButton).popover({
        content: getFooterContent,
        container: container,
        html: true,
        placement: 'top',
        customClass: 'footer',
        trigger: 'click',
        boundary: 'viewport',
        popperConfig: {
            modifiers: {
                preventOverflow: {
                    boundariesElement: 'viewport',
                    padding: 48
                },
                offset: {},
                flip: {
                    behavior: 'flip'
                },
                arrow: {
                    element: '.arrow'
                },
            }
        }
    });

    document.addEventListener('click', e => {
        if (footerIsShown && !e.target.closest(SELECTORS.FOOTERCONTAINER)) {
            $(footerButton).popover('hide');
        }
    },
    true);

    document.addEventListener('keydown', e => {
        if (footerIsShown && e.key === 'Escape') {
            $(footerButton).popover('hide');
            footerButton.focus();
        }
    });

    document.addEventListener('focus', e => {
        if (footerIsShown && !e.target.closest(SELECTORS.FOOTERCONTAINER)) {
            $(footerButton).popover('hide');
        }
    },
    true);

    $(footerButton).on('show.bs.popover', () => {
        footerIsShown = true;
    });

    $(footerButton).on('hide.bs.popover', () => {
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
