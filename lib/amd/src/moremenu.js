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
 * Moves wrapping navigation items into a more menu.
 *
 * @module     core/moremenu
 * @package    core
 * @copyright  2021 Moodle
 * @author     Bas Brands <bas@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import $ from 'jquery';
/**
 * Moremenu selectors.
 */
const Selectors = {
    regions: {
        moredropdown: '[data-region="moredropdown"]',
        morebutton: '[data-region="morebutton"]'
    },
    classes: {
        dropdownitem: 'dropdown-item',
        dropdownmoremenu: 'dropdownmoremenu',
        dropdowntoggle: 'dropdown-toggle',
        hidden: 'd-none',
        active: 'active',
        nav: 'nav',
        navlink: 'nav-link',
        observed: 'observed',
    },
    attributes: {
        menu: '[role="menu"]'
    }
};

const maxMenuItems = 6;
/**
 * Auto Collapse navigation items that wrap into a dropdown menu.
 *
 * @param {HTMLElement} menu The navbar container.
 */
const autoCollapse = menu => {

    const maxHeight = menu.parentNode.offsetHeight + 1;

    const moreDropdown = menu.querySelector(Selectors.regions.moredropdown);
    const moreButton = menu.querySelector(Selectors.regions.morebutton);

    const dropdownToggle = menu.querySelector('.' + Selectors.classes.dropdowntoggle);

    // If the menuitems wrap and the menu height is larger than the height of the
    // parent. Or if the number if menuitems is larger than the maximum menu items
    // allowed then start pushing navlinks into the moreDropdown.
    if (menu.offsetHeight > maxHeight || menu.children.length > maxMenuItems) {

        moreButton.classList.remove(Selectors.classes.hidden);

        const menuNodes = Array.from(menu.children).reverse();
        menuNodes.forEach(item => {
            if (!item.classList.contains(Selectors.classes.dropdownmoremenu)) {
                // After moving the menuitems into the moreDropdown check again
                // if the menuheight is still larger then the height of the parent or if the
                // menu still has more items than maxMenuItems.
                if (menu.offsetHeight > maxHeight || menu.children.length > maxMenuItems) {
                    const lastNode = menu.removeChild(item);
                    const navLink = lastNode.querySelector('.' + Selectors.classes.navlink);
                    if (navLink && !navLink.hasAttribute('role')) {
                        // Adding the menuitem role so the dropdown includes the
                        // Accessibility improvements from theme/boost/amd/src/aria.js
                        navLink.setAttribute('role', 'menuitem');
                    }

                    // If there are navLinks that contain an active link in the moreDropdown
                    // make the dropdownToggle in the moreButton active.
                    if (navLink.classList.contains(Selectors.classes.active)) {
                        dropdownToggle.classList.add(Selectors.classes.active);
                    }

                    // Change the styling of the navLink to a dropdownitem and push it into
                    // the moreDropdown.
                    navLink.classList.remove(Selectors.classes.navlink);
                    navLink.classList.add(Selectors.classes.dropdownitem);
                    moreDropdown.prepend(lastNode);
                }
            }
        });
    } else {
        // If the the menu height is smaller than the height of the parent and there are
        // less than the maximum items in the menu, then try returning navlinks to the menu.

        if ('children' in moreDropdown) {
            const menuNodes = Array.from(moreDropdown.children);
            menuNodes.forEach(item => {

                if (menu.offsetHeight < maxHeight && menu.children.length < maxMenuItems) {
                    const lastNode = moreDropdown.removeChild(item);
                    const navLink = lastNode.querySelector('.' + Selectors.classes.dropdownitem);
                    if (navLink) {
                        const currentAttribute = navLink.getAttribute('role');
                        if (currentAttribute === 'menuitem') {
                            navLink.removeAttribute('role');
                        }
                    }

                    // Stop displaying the active state on the dropdownToggle if
                    // the active navlink is removed.
                    if (navLink.classList.contains(Selectors.classes.active)) {
                        dropdownToggle.classList.remove(Selectors.classes.active);
                    }
                    navLink.classList.remove(Selectors.classes.dropdownitem);
                    navLink.classList.add(Selectors.classes.navlink);
                    menu.insertBefore(lastNode, moreButton);
                }
            });

            // If there are no more menuNodes in the dropdown we can hide the moreButton.
            if (menuNodes.length === 0) {
                moreButton.classList.add(Selectors.classes.hidden);
            }
        }

        if (menu.offsetHeight > maxHeight) {
            autoCollapse(menu);
        }
    }
    menu.parentNode.classList.add(Selectors.classes.observed);
};

/**
 * Initialise the more menus.
 *
 * @param {HTMLElement} menu The navbar moremenu.
 */
export default menu => {
    autoCollapse(menu);

    // When the screen size changes make sure the menu still fits.
    window.addEventListener('resize', () => {
        autoCollapse(menu);
    });

    const toggledropdown = e => {
        const innerMenu = e.target.parentNode.querySelector(Selectors.attributes.menu);
        if (innerMenu) {
            innerMenu.classList.toggle('show');
        }
        e.stopPropagation();
    };

    // If there are dropdowns in the MoreMenu, add a new
    // eventlistener to show the contents on click and prevent the
    // moreMenu from closing.
    $('.' + Selectors.classes.dropdownmoremenu).on('show.bs.dropdown', function() {
        const moreDropdown = menu.querySelector(Selectors.regions.moredropdown);
        moreDropdown.querySelectorAll('.dropdown').forEach((dropdown) => {
            dropdown.removeEventListener('click', toggledropdown, true);
            dropdown.addEventListener('click', toggledropdown, true);
        });
    });
};
