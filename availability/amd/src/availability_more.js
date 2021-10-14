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
 * Show more action for availablity information.
 *
 * @module     core_availability/availability_more
 * @copyright  2021 Bas Brands <bas@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Availability info selectors.
 */
const Selectors = {
    regions: {
        availability: '[data-region="availability-multiple"]',
    },
    actions: {
        showmorelink: '[data-action="showmore"]'
    },
    classes: {
        hidden: 'd-none',
        visible: 'd-block',

    }
};

/**
 * Displays all the availability information in case part of it is hidden.
 *
 * @param {Event} event the triggered event
 */
const showMoreHandler = (event) => {
    const triggerElement = event.target.closest(Selectors.actions.showmorelink);
    if (triggerElement === null) {
        return;
    }
    const container = triggerElement.closest(Selectors.regions.availability);
    container.querySelectorAll('.' + Selectors.classes.hidden).forEach(function(node) {
        node.classList.remove(Selectors.classes.hidden);
    });
    container.querySelectorAll('.' + Selectors.classes.visible).forEach(function(node) {
        node.classList.remove(Selectors.classes.visible);
        node.classList.add(Selectors.classes.hidden);
    });
    event.preventDefault();
};

/**
 * Initialise the eventlister for the showmore action on availability information.
 *
 * @method  init
 */
export const init = () => {
    const body = document.querySelector('body');
    if (!body.dataset.showmoreactive) {
        document.addEventListener('click', showMoreHandler);
        body.dataset.showmoreactive = 1;
    }
};
