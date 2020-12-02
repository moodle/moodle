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
 * Enhancements to Bootstrap components for accessibility.
 *
 * @module     theme_boost/aria
 * @copyright  2018 Damyon Wiese <damyon@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import $ from 'jquery';
import Pending from 'core/pending';

/**
 * Drop downs from bootstrap don't support keyboard accessibility by default.
 */
const dropdownFix = () => {
    let focusEnd = false;
    const setFocusEnd = () => {
        focusEnd = true;
    };
    const getFocusEnd = () => {
        const result = focusEnd;
        focusEnd = false;
        return result;
    };

    // Special handling for "up" keyboard control.
    document.addEventListener('keydown', e => {
        if (e.target.matches('[data-toggle="dropdown"]')) {
            const trigger = e.key;

            // Up key opens the menu at the end.
            if (trigger == 'ArrowUp') {
                // Focus the end of the menu, not the beginning.
                setFocusEnd();
            }

            // Escape key only closes the menu, it doesn't open it.
            if (trigger == 'Escape') {
                const expanded = e.target.getAttribute('aria-expanded');
                e.preventDefault();
                if (expanded == "false") {
                    e.target.click();
                }
            }

            // Space key or Enter key opens the menu.
            if (trigger == ' ' || trigger == 'Enter') {
                // Cancel random scroll.
                e.preventDefault();
                // Open the menu instead.
                e.target.click();
            }
        }
    });

    // Special handling for navigation keys when menu is open.
    const shiftFocus = element => {
        const delayedFocus = pendingPromise => {
            element.focus();
            pendingPromise.resolve();
        };
        setTimeout(delayedFocus, 50, new Pending('core/aria:delayed-focus'));
    };

    $('.dropdown').on('shown.bs.dropdown', e => {
        // We need to focus on the first menuitem.
        const menu = e.target.querySelector('[role="menu"]');
        let menuItems = false;
        let foundMenuItem = false;

        if (menu) {
            menuItems = menu.querySelectorAll('[role="menuitem"]');
        }
        if (menuItems && menuItems.length > 0) {
            if (getFocusEnd()) {
                foundMenuItem = menuItems[menuItems.length - 1];
            } else {
                // The first menu entry, pretty reasonable.
                foundMenuItem = menuItems[0];
            }
        }
        if (foundMenuItem) {
            shiftFocus(foundMenuItem);
        }
    });
    // Search for menu items by finding the first item that has
    // text starting with the typed character (case insensitive).
    document.addEventListener('keypress', e => {
        if (e.target.matches('.dropdown [role="menu"] [role="menuitem"]')) {
            const menu = e.target.closest('[role="menu"]');
            if (!menu) {
                return;
            }
            const menuItems = menu.querySelectorAll('[role="menuitem"]');
            if (!menuItems) {
                return;
            }

            const trigger = e.key.toLowerCase();

            for (let i = 0; i < menuItems.length; i++) {
                const item = menuItems[i];
                const itemText = item.text.trim().toLowerCase();
                if (itemText.indexOf(trigger) == 0) {
                    shiftFocus(item);
                    break;
                }
            }
        }
    });

    // Keyboard navigation for arrow keys, home and end keys.
    document.addEventListener('keydown', e => {
        if (e.target.matches('.dropdown [role="menu"] [role="menuitem"]')) {
            const trigger = e.key;
            let next = false;
            const menu = e.target.closest('[role="menu"]');

            if (!menu) {
                return;
            }
            const menuItems = menu.querySelectorAll('[role="menuitem"]');
            if (!menuItems) {
                return;
            }
            // Down key.
            if (trigger == 'ArrowDown') {
                for (let i = 0; i < menuItems.length - 1; i++) {
                    if (menuItems[i] == e.target) {
                        next = menuItems[i + 1];
                        break;
                    }
                }
                if (!next) {
                    // Wrap to first item.
                    next = menuItems[0];
                }

            } else if (trigger == 'ArrowUp') {
                // Up key.
                for (let i = 1; i < menuItems.length; i++) {
                    if (menuItems[i] == e.target) {
                        next = menuItems[i - 1];
                        break;
                    }
                }
                if (!next) {
                    // Wrap to last item.
                    next = menuItems[menuItems.length - 1];
                }

            } else if (trigger == 'Home') {
                // Home key.
                next = menuItems[0];

            } else if (trigger == 'End') {
                // End key.
                next = menuItems[menuItems.length - 1];
            }
            // Variable next is set if we do want to act on the keypress.
            if (next) {
                e.preventDefault();
                shiftFocus(next);
            }
            return;
        }
    });

    $('.dropdown').on('hidden.bs.dropdown', e => {
        // We need to focus on the menu trigger.
        const trigger = e.target.querySelector('[data-toggle="dropdown"]');
        if (trigger) {
            shiftFocus(trigger);
        }
    });
};

/**
 * After page load, focus on any element with special autofocus attribute.
 */
const autoFocus = () => {
    window.addEventListener("load", () => {
        const alerts = document.querySelectorAll('[data-aria-autofocus="true"][role="alert"]');
        Array.prototype.forEach.call(alerts, autofocusElement => {
            // According to the specification an role="alert" region is only read out on change to the content
            // of that region.
            autofocusElement.innerHTML += ' ';
            autofocusElement.removeAttribute('data-aria-autofocus');
        });
    });
};

/**
 * Changes the focus to the correct tab based on the key that is pressed.
 * @param {KeyboardEvent} e
 */
const updateTabFocus = e => {
    const tabList = e.target.closest('[role="tablist"]');
    const vertical = tabList.getAttribute('aria-orientation') == 'vertical';
    const rtl = window.right_to_left();
    const arrowNext = vertical ? 'ArrowDown' : (rtl ? 'ArrowLeft' : 'ArrowRight');
    const arrowPrevious = vertical ? 'ArrowUp' : (rtl ? 'ArrowRight' : 'ArrowLeft');
    const tabs = Array.prototype.filter.call(
        tabList.querySelectorAll('[role="tab"]'),
        tab => getComputedStyle(tab).display !== 'none'); // We only work with the visible tabs.

    for (let i = 0; i < tabs.length; i++) {
        tabs[i].index = i;
    }

    switch (e.key) {
        case arrowNext:
            e.preventDefault();
            if (e.target.index !== undefined && tabs[e.target.index + 1]) {
                tabs[e.target.index + 1].focus();
            } else {
                tabs[0].focus();
            }
            break;
        case arrowPrevious:
            e.preventDefault();
            if (e.target.index !== undefined && tabs[e.target.index - 1]) {
                tabs[e.target.index - 1].focus();
            } else {
                tabs[tabs.length - 1].focus();
            }
            break;
        case 'Home':
            e.preventDefault();
            tabs[0].focus();
            break;
        case 'End':
            e.preventDefault();
            tabs[tabs.length - 1].focus();
            break;
        case 'Enter':
        case ' ':
            e.preventDefault();
            $(e.target).tab('show');
            tabs.forEach(tab => {
                tab.tabIndex = -1;
            });
            e.target.tabIndex = 0;
    }
};

/**
 * Fix accessibility issues regarding tab elements focus and their tab order in Bootstrap navs.
 */
const tabElementFix = () => {
    document.addEventListener('keydown', e => {
        if (['ArrowUp', 'ArrowDown', 'ArrowLeft', 'ArrowRight', 'Home', 'End', 'Enter', ' '].includes(e.key)) {
            if (e.target.matches('[role="tablist"] [role="tab"]')) {
                updateTabFocus(e);
            }
        }
    });

    document.addEventListener('click', e => {
        if (e.target.matches('[role="tablist"] [role="tab"]')) {
            const tabs = e.target.closest('[role="tablist"]').querySelectorAll('[role="tab"]');
            e.preventDefault();
            $(e.target).tab('show');
            tabs.forEach(tab => {
                tab.tabIndex = -1;
            });
            e.target.tabIndex = 0;
        }
    });
};

export const init = () => {
    dropdownFix();
    autoFocus();
    tabElementFix();
};
