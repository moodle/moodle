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
 * @copyright  2021 Moodle
 * @author     Bas Brands <bas@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import $ from 'jquery';
import menu_navigation from "core/menu_navigation";
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
        hidden: 'd-none',
        active: 'active',
        nav: 'nav',
        navlink: 'nav-link',
        observed: 'observed',
    },
    attributes: {
        menu: '[role="menu"]',
        dropdowntoggle: '[data-toggle="dropdown"]'
    }
};

let isTabListMenu = false;

/**
 * Auto Collapse navigation items that wrap into a dropdown menu.
 *
 * @param {HTMLElement} menu The navbar container.
 */
const autoCollapse = menu => {

    const maxHeight = menu.parentNode.offsetHeight + 1;

    const moreDropdown = menu.querySelector(Selectors.regions.moredropdown);
    const moreButton = menu.querySelector(Selectors.regions.morebutton);

    // If the menu items wrap and the menu height is larger than the height of the
    // parent then start pushing navlinks into the moreDropdown.
    if (menu.offsetHeight > maxHeight) {
        moreButton.classList.remove(Selectors.classes.hidden);

        const menuNodes = Array.from(menu.children).reverse();
        menuNodes.forEach(item => {
            if (!item.classList.contains(Selectors.classes.dropdownmoremenu)) {
                // After moving the menu items into the moreDropdown check again
                // if the menu height is still larger then the height of the parent.
                if (menu.offsetHeight > maxHeight) {
                    const lastNode = menu.removeChild(item);
                    // Move this node into the more dropdown menu.
                    moveIntoMoreDropdown(menu, lastNode, true);
                }
            }
        });
    } else {
        // If the menu height is smaller than the height of the parent, then try returning navlinks to the menu.
        if ('children' in moreDropdown) {
            // Iterate through the nodes within the more dropdown menu.
            Array.from(moreDropdown.children).forEach(item => {
                // Don't move the node to the more menu if it is explicitly defined that
                // this node should be displayed in the more dropdown menu at all times.
                if (menu.offsetHeight < maxHeight && item.dataset.forceintomoremenu !== 'true') {
                    const lastNode = moreDropdown.removeChild(item);
                    // Move this node from the more dropdown menu into the main section of the menu.
                    moveOutOfMoreDropdown(menu, lastNode);
                }
            });
            // If there are no more nodes in the more dropdown menu we can hide the moreButton.
            if (Array.from(moreDropdown.children).length === 0) {
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
 * Move a node into the "more" dropdown menu.
 *
 * This method forces a given navigation node to be added and displayed within the "more" dropdown menu.
 *
 * @param {HTMLElement} menu The navbar moremenu.
 * @param {HTMLElement} navNode The navigation node.
 * @param {boolean} prepend Whether to prepend or append the node to the content in the more dropdown menu.
 */
const moveIntoMoreDropdown = (menu, navNode, prepend = false) => {
    const moreDropdown = menu.querySelector(Selectors.regions.moredropdown);
    const dropdownToggle = menu.querySelector(Selectors.attributes.dropdowntoggle);

    const navLink = navNode.querySelector('.' + Selectors.classes.navlink);
    // If there are navLinks that contain an active link in the moreDropdown
    // make the dropdownToggle in the moreButton active.
    if (navLink.classList.contains(Selectors.classes.active)) {
        dropdownToggle.classList.add(Selectors.classes.active);
        dropdownToggle.setAttribute('tabindex', '0');
        navLink.setAttribute('tabindex', '-1'); // So that we don't have a single tabbable menu item.
        // Remove aria-selected if the more menu is rendered as a tab list.
        if (isTabListMenu) {
            navLink.removeAttribute('aria-selected');
        }
        navLink.setAttribute('aria-current', 'true');
    }

    // This will become a menu item instead of a tab.
    navLink.setAttribute('role', 'menuitem');

    // Change the styling of the navLink to a dropdownitem and push it into
    // the moreDropdown.
    navLink.classList.remove(Selectors.classes.navlink);
    navLink.classList.add(Selectors.classes.dropdownitem);
    if (prepend) {
        moreDropdown.prepend(navNode);
    } else {
        moreDropdown.append(navNode);
    }
};

/**
 * Move a node out of the "more" dropdown menu.
 *
 * This method forces a given node from the "more" dropdown menu to be displayed in the main section of the menu.
 *
 * @param {HTMLElement} menu The navbar moremenu.
 * @param {HTMLElement} navNode The navigation node.
 */
const moveOutOfMoreDropdown = (menu, navNode) => {
    const moreButton = menu.querySelector(Selectors.regions.morebutton);
    const dropdownToggle = menu.querySelector(Selectors.attributes.dropdowntoggle);
    const navLink = navNode.querySelector('.' + Selectors.classes.dropdownitem);

    // If the more menu is rendered as a tab list,
    // this will become a tab instead of a menuitem when moved out of the more menu dropdown.
    if (isTabListMenu) {
        navLink.setAttribute('role', 'tab');
    }

    // Stop displaying the active state on the dropdownToggle if
    // the active navlink is removed.
    if (navLink.classList.contains(Selectors.classes.active)) {
        dropdownToggle.classList.remove(Selectors.classes.active);
        dropdownToggle.setAttribute('tabindex', '-1');
        navLink.setAttribute('tabindex', '0');
        if (isTabListMenu) {
            // Replace aria selection state when necessary.
            navLink.removeAttribute('aria-current');
            navLink.setAttribute('aria-selected', 'true');
        }
    }
    navLink.classList.remove(Selectors.classes.dropdownitem);
    navLink.classList.add(Selectors.classes.navlink);
    menu.insertBefore(navNode, moreButton);
};

/**
 * Initialise the more menus.
 *
 * @param {HTMLElement} menu The navbar moremenu.
 */
export default menu => {
    isTabListMenu = menu.getAttribute('role') === 'tablist';

    // Select the first menu item if there's nothing initially selected.
    const hash = window.location.hash;
    if (!hash) {
        const itemRole = isTabListMenu ? 'tab' : 'menuitem';
        const menuListItem = menu.firstElementChild;
        const roleSelector = `[role=${itemRole}]`;
        const menuItem = menuListItem.querySelector(roleSelector);
        const ariaAttribute = isTabListMenu ? 'aria-selected' : 'aria-current';
        if (!menu.querySelector(`[${ariaAttribute}='true']`)) {
            menuItem.setAttribute(ariaAttribute, 'true');
            menuItem.setAttribute('tabindex', '0');
        }
    }

    // Pre-populate the "more" dropdown menu with navigation nodes which are set to be displayed in this menu
    // by default at all times.
    if ('children' in menu) {
        const moreButton = menu.querySelector(Selectors.regions.morebutton);
        const menuNodes = Array.from(menu.children);
        menuNodes.forEach((item) => {
            if (!item.classList.contains(Selectors.classes.dropdownmoremenu) &&
                    item.dataset.forceintomoremenu === 'true') {
                // Append this node into the more dropdown menu.
                moveIntoMoreDropdown(menu, item, false);
                // After adding the node into the more dropdown menu, make sure that the more dropdown menu button
                // is displayed.
                if (moreButton.classList.contains(Selectors.classes.hidden)) {
                    moreButton.classList.remove(Selectors.classes.hidden);
                }
            }
        });
    }
    // Populate the more dropdown menu with additional nodes if necessary, depending on the current screen size.
    autoCollapse(menu);
    menu_navigation(menu);

    // When the screen size changes make sure the menu still fits.
    window.addEventListener('resize', () => {
        autoCollapse(menu);
        menu_navigation(menu);
    });

    const toggledropdown = e => {
        const innerMenu = e.target.parentNode.querySelector(Selectors.attributes.menu);
        if (innerMenu) {
            innerMenu.classList.toggle('show');
        }
        e.stopPropagation();
    };

    // If there are dropdowns in the MoreMenu, add a new
    // event listener to show the contents on click and prevent the
    // moreMenu from closing.
    $('.' + Selectors.classes.dropdownmoremenu).on('show.bs.dropdown', function() {
        const moreDropdown = menu.querySelector(Selectors.regions.moredropdown);
        moreDropdown.querySelectorAll('.dropdown').forEach((dropdown) => {
            dropdown.removeEventListener('click', toggledropdown, true);
            dropdown.addEventListener('click', toggledropdown, true);
        });
    });
};
