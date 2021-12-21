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
 * Report builder sidebar component
 *
 * @module      core_reportbuilder/sidebar
 * @copyright   2021 Paul Holden <paulh@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import Pending from 'core/pending';
import {debounce} from 'core/utils';
import * as reportSelectors from 'core_reportbuilder/local/selectors';

const DEBOUNCE_TIMER = 250;

const CLASSES = {
    EXPANDED: 'show',
    COLLAPSED: 'collapsed',
    SHOW: 'd-flex',
    HIDE: 'd-none',
};

/**
 * Initialise module
 *
 * @param {Event} event
 * @param {Element} sidebarMenu
 */
const sidebarCardFilter = (event, sidebarMenu) => {
    const pendingPromise = new Pending('core_reportbuilder/sidebar:cardFilter');

    const sidebarCards = sidebarMenu.querySelectorAll(reportSelectors.regions.sidebarCard);
    const sidebarItems = sidebarMenu.querySelectorAll('.list-group-item');
    const searchTerm = event.target.value.toLowerCase();

    // Toggle items according to match against search term.
    sidebarItems.forEach(item => {
        let itemName = item.dataset.name.toLowerCase();
        if (itemName.includes(searchTerm)) {
            item.classList.replace(CLASSES.HIDE, CLASSES.SHOW);
        } else {
            item.classList.replace(CLASSES.SHOW, CLASSES.HIDE);
        }
    });

    // Toggle cards according to whether they have any visible items.
    sidebarCards.forEach(card => {
        if (card.querySelectorAll(`.${CLASSES.SHOW}`).length > 0) {
            card.classList.remove(CLASSES.HIDE);
            expandCard(card);
        } else {
            card.classList.add(CLASSES.HIDE);
        }
    });

    pendingPromise.resolve();
};

/**
 * Show a collapsed card.
 * This function simulates the behaviour of JQuery show method on a collapsible element.
 *
 * @param {Element} card
 */
const expandCard = (card) => {
    let cardButton = card.querySelector('[data-toggle="collapse"]');
    if (cardButton.classList.contains(CLASSES.COLLAPSED)) {
        cardButton.classList.remove(CLASSES.COLLAPSED);
        cardButton.setAttribute('aria-expanded', "true");
        let cardContent = card.querySelector(cardButton.dataset.target);
        cardContent.classList.add(CLASSES.EXPANDED);
    }
};

/**
 * Initialise module
 *
 * @param {string} selectorId
 */
export const init = (selectorId) => {
    const sidebarMenu = document.querySelector(selectorId + reportSelectors.regions.sidebarMenu);
    const sidebarSearch = sidebarMenu.querySelector(reportSelectors.actions.sidebarSearch);

    // Debounce the event listener to allow the user to finish typing.
    sidebarSearch.addEventListener('keyup', event => {
        const pendingPromise = new Pending('core_reportbuilder/sidebar:keyup');

        debounce(sidebarCardFilter, DEBOUNCE_TIMER)(event, sidebarMenu);
        setTimeout(() => {
            pendingPromise.resolve();
        }, DEBOUNCE_TIMER);
    });
};
