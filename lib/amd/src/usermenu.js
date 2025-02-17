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
 * Initializes and handles events in the user menu.
 *
 * @module     core/usermenu
 * @copyright  2021 Moodle
 * @author     Mihail Geshoski <mihail@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import Carousel from 'theme_boost/bootstrap/carousel';
import {space, enter} from 'core/key_codes';

/**
 * User menu constants.
 */
const selectors = {
    userMenu: '.usermenu',
    userMenuCarousel: '.usermenu #usermenu-carousel',
    userMenuCarouselItem: '.usermenu #usermenu-carousel .carousel-item',
    userMenuCarouselItemActive: '.usermenu #usermenu-carousel .carousel-item.active',
    userMenuCarouselNavigationLink: '.usermenu #usermenu-carousel .carousel-navigation-link',
};

/**
 * Register event listeners.
 */
const registerEventListeners = () => {
    const userMenu = document.querySelector(selectors.userMenu);
    const userMenuCarousel = document.querySelector(selectors.userMenuCarousel);

    // Handle the 'shown.bs.dropdown' event (Fired when the dropdown menu is fully displayed).
    userMenu.addEventListener('shown.bs.dropdown', () => {
        const activeCarouselItem = document.querySelector(selectors.userMenuCarouselItemActive);
        // Set the focus on the active carousel item.
        activeCarouselItem.focus();

        userMenu.querySelectorAll(selectors.userMenuCarouselItem).forEach(element => {
            // Resize all non-active carousel items to match the height and width of the current active (main)
            // carousel item to avoid sizing inconsistencies. This has to be done once the dropdown menu is fully
            // displayed ('shown.bs.dropdown') as the offsetWidth and offsetHeight cannot be obtained when the
            // element is hidden.
            if (!element.classList.contains('active')) {
                element.style.width = activeCarouselItem.offsetWidth + 'px';
                element.style.height = activeCarouselItem.offsetHeight + 'px';
            }
        });
    });

    // Handle click events in the user menu.
    userMenu.addEventListener('click', (e) => {

        // Handle click event on the carousel navigation (control) links in the user menu.
        if (e.target.matches(selectors.userMenuCarouselNavigationLink)) {
            carouselManagement(e);
        }
    });

    userMenu.addEventListener('keydown', e => {
        // Handle keydown event on the carousel navigation (control) links in the user menu.
        if ((e.keyCode === space ||
            e.keyCode === enter) &&
            e.target.matches(selectors.userMenuCarouselNavigationLink)) {
            e.preventDefault();
            carouselManagement(e);
        }
    });

    /**
     * We do the same actions here even if the caller was a click or button press.
     *
     * @param {Event} e The triggering element and key presses etc.
     */
    const carouselManagement = e => {
        // By default the user menu dropdown element closes on a click event. This behaviour is not desirable
        // as we need to be able to navigate through the carousel items (submenus of the user menu) within the
        // user menu. Therefore, we need to prevent the propagation of this event and then manually call the
        // carousel transition.
        e.stopPropagation();
        // The id of the targeted carousel item.
        const targetedCarouselItemId = e.target.dataset.carouselTargetId;
        const targetedCarouselItem = userMenu.querySelector('#' + targetedCarouselItemId);
        // Get the position (index) of the targeted carousel item within the parent container element.
        const index = Array.from(targetedCarouselItem.parentNode.children).indexOf(targetedCarouselItem);
        // Navigate to the targeted carousel item.
        Carousel.getOrCreateInstance(userMenuCarousel).to(index);
    };

    // Handle the 'hide.bs.dropdown' event (Fired when the dropdown menu is being closed).
    userMenu.addEventListener('hide.bs.dropdown', () => {
        // Reset the state once the user menu dropdown is closed and return back to the first (main) carousel item
        // if necessary.
        Carousel.getOrCreateInstance(userMenuCarousel).to(0);
    });

    // Handle the 'slid.bs.carousel' event (Fired when the carousel has completed its slide transition).
    userMenuCarousel?.addEventListener('slid.bs.carousel', () => {
        const activeCarouselItem = userMenu.querySelector(selectors.userMenuCarouselItemActive);
        // Set the focus on the newly activated carousel item.
        activeCarouselItem.focus();
    });
};

/**
 * Initialize the user menu.
 */
const init = () => {
    registerEventListeners();
};

export default {
    init: init,
};
