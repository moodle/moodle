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
    const setFocusEnd = (end = true) => {
        focusEnd = end;
    };
    const getFocusEnd = () => {
        const result = focusEnd;
        focusEnd = false;
        return result;
    };

    // Special handling for navigation keys when menu is open.
    const shiftFocus = (element, focusCheck = null) => {
        const pendingPromise = new Pending('core/aria:delayed-focus');
        setTimeout(() => {
            if (!focusCheck || focusCheck()) {
                element.focus();
            }

            pendingPromise.resolve();
        }, 50);
    };

    // Event handling for the dropdown menu button.
    const handleMenuButton = e => {
        const trigger = e.key;
        let fixFocus = false;

        // Space key or Enter key opens the menu.
        if (trigger === ' ' || trigger === 'Enter') {
            fixFocus = true;
            // Cancel random scroll.
            e.preventDefault();
            // Open the menu instead.
            e.target.click();
        }

        // Up and Down keys also open the menu.
        if (trigger === 'ArrowUp' || trigger === 'ArrowDown') {
            fixFocus = true;
        }

        if (!fixFocus) {
            // No need to fix the focus. Return early.
            return;
        }

        // Fix the focus on the menu items when the menu is opened.
        const menu = e.target.parentElement.querySelector('[role="menu"]');
        let menuItems = false;
        let foundMenuItem = false;
        let textInput = false;

        if (menu) {
            menuItems = menu.querySelectorAll('[role="menuitem"]');
            textInput = e.target.parentElement.querySelector('[data-action="search"]');
        }

        if (menuItems && menuItems.length > 0) {
            // Up key opens the menu at the end.
            if (trigger === 'ArrowUp') {
                setFocusEnd();
            } else {
                setFocusEnd(false);
            }

            if (getFocusEnd()) {
                foundMenuItem = menuItems[menuItems.length - 1];
            } else {
                // The first menu entry, pretty reasonable.
                foundMenuItem = menuItems[0];
            }
        }

        if (textInput) {
            shiftFocus(textInput);
        }
        if (foundMenuItem && textInput === null) {
            shiftFocus(foundMenuItem);
        }
    };

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

        // We only want to set focus when users access the dropdown via keyboard as per
        // guidelines defined in w3 aria practices 1.1 menu-button.
        if (e.target.matches('[data-toggle="dropdown"]')) {
            handleMenuButton(e);
        }

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
        const focused = document.activeElement != document.body ? document.activeElement : null;
        if (trigger && focused && e.target.contains(focused)) {
            shiftFocus(trigger, () => {
                if (document.activeElement === document.body) {
                    // If the focus is currently on the body, then we can safely assume that the focus needs to be updated.
                    return true;
                }

                // If the focus is on a child of the clicked element still, then update the focus.
                return e.target.contains(document.activeElement);
            });
        }
    });
};

/**
 * A lot of Bootstrap's out of the box features don't work if dropdown items are not focusable.
 */
const comboboxFix = () => {
    $(document).on('show.bs.dropdown', e => {
        if (e.relatedTarget.matches('[role="combobox"]')) {
            const combobox = e.relatedTarget;
            const listbox = document.querySelector(`#${combobox.getAttribute('aria-controls')}[role="listbox"]`);

            if (listbox) {
                const selectedOption = listbox.querySelector('[role="option"][aria-selected="true"]');

                // To make sure ArrowDown doesn't move the active option afterwards.
                setTimeout(() => {
                    if (selectedOption) {
                        selectedOption.classList.add('active');
                        combobox.setAttribute('aria-activedescendant', selectedOption.id);
                    } else {
                        const firstOption = listbox.querySelector('[role="option"]');
                        firstOption.setAttribute('aria-selected', 'true');
                        firstOption.classList.add('active');
                        combobox.setAttribute('aria-activedescendant', firstOption.id);
                    }
                }, 0);
            }
        }
    });

    $(document).on('hidden.bs.dropdown', e => {
        if (e.relatedTarget.matches('[role="combobox"]')) {
            const combobox = e.relatedTarget;
            const listbox = document.querySelector(`#${combobox.getAttribute('aria-controls')}[role="listbox"]`);

            combobox.removeAttribute('aria-activedescendant');

            if (listbox) {
                setTimeout(() => {
                    // Undo all previously highlighted options.
                    listbox.querySelectorAll('.active[role="option"]').forEach(option => {
                        option.classList.remove('active');
                    });
                }, 0);
            }
        }
    });

    // Handling keyboard events for both navigating through and selecting options.
    document.addEventListener('keydown', e => {
        if (e.target.matches('[role="combobox"][aria-controls]:not([aria-haspopup=dialog])')) {
            const combobox = e.target;
            const trigger = e.key;
            let next = null;
            const listbox = document.querySelector(`#${combobox.getAttribute('aria-controls')}[role="listbox"]`);
            const options = listbox.querySelectorAll('[role="option"]');
            const activeOption = listbox.querySelector('.active[role="option"]');
            const editable = combobox.hasAttribute('aria-autocomplete');

            // Under the special case that the dropdown menu is being shown as a result of the key press (like when the user
            // presses ArrowDown or Enter or ... to open the dropdown menu), activeOption is not set yet.
            // It's because of a race condition with show.bs.dropdown event handler.
            if (options && (activeOption || editable)) {
                if (trigger == 'ArrowDown') {
                    for (let i = 0; i < options.length - 1; i++) {
                        if (options[i] == activeOption) {
                            next = options[i + 1];
                            break;
                        }
                    }
                    if (editable && !next) {
                        next = options[0];
                    }
                } if (trigger == 'ArrowUp') {
                    for (let i = 1; i < options.length; i++) {
                        if (options[i] == activeOption) {
                            next = options[i - 1];
                            break;
                        }
                    }
                    if (editable && !next) {
                        next = options[options.length - 1];
                    }
                } else if (trigger == 'Home') {
                    next = options[0];
                } else if (trigger == 'End') {
                    next = options[options.length - 1];
                } else if ((trigger == ' ' && !editable) || trigger == 'Enter') {
                    e.preventDefault();
                    selectOption(combobox, activeOption);
                } else if (!editable) {
                    // Search for options by finding the first option that has
                    // text starting with the typed character (case insensitive).
                    for (let i = 0; i < options.length; i++) {
                        const option = options[i];
                        const optionText = option.textContent.trim().toLowerCase();
                        const keyPressed = e.key.toLowerCase();
                        if (optionText.indexOf(keyPressed) == 0) {
                            next = option;
                            break;
                        }
                    }
                }

                // Variable next is set if we do want to act on the keypress.
                if (next) {
                    e.preventDefault();
                    if (activeOption) {
                        activeOption.classList.remove('active');
                    }
                    next.classList.add('active');
                    combobox.setAttribute('aria-activedescendant', next.id);
                    next.scrollIntoView({block: 'nearest'});
                }
            }
        }
    });

    document.addEventListener('click', e => {
        const option = e.target.closest('[role="listbox"] [role="option"]');
        if (option) {
            const listbox = option.closest('[role="listbox"]');
            const combobox = document.querySelector(`[role="combobox"][aria-controls="${listbox.id}"]`);
            if (combobox) {
                combobox.focus();
                selectOption(combobox, option);
            }
        }
    });

    // In case some code somewhere else changes the value of the combobox.
    document.addEventListener('change', e => {
        if (e.target.matches('input[type="hidden"][id]')) {
            const combobox = document.querySelector(`[role="combobox"][data-input-element="${e.target.id}"]`);
            const option = e.target.parentElement.querySelector(`[role="option"][data-value="${e.target.value}"]`);

            if (combobox && option) {
                selectOption(combobox, option);
            }
        }
    });

    const selectOption = (combobox, option) => {
        const listbox = option.closest('[role="listbox"]');
        const oldSelectedOption = listbox.querySelector('[role="option"][aria-selected="true"]');

        if (oldSelectedOption != option) {
            if (oldSelectedOption) {
                oldSelectedOption.removeAttribute('aria-selected');
            }
            option.setAttribute('aria-selected', 'true');
        }

        if (combobox.hasAttribute('value')) {
            combobox.value = option.textContent.replace(/[\n\r]+|[\s]{2,}/g, ' ').trim();
        } else {
            combobox.textContent = option.textContent;
        }

        if (combobox.dataset.inputElement) {
            const inputElement = document.getElementById(combobox.dataset.inputElement);
            if (inputElement && (inputElement.value != option.dataset.value)) {
                inputElement.value = option.dataset.value;
                inputElement.dispatchEvent(new Event('change', {bubbles: true}));
            }
        }
    };
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
        tab => !!tab.offsetHeight); // We only work with the visible tabs.

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
    }
};

/**
 * Fix accessibility issues regarding tab elements focus and their tab order in Bootstrap navs.
 */
const tabElementFix = () => {
    document.addEventListener('keydown', e => {
        if (['ArrowUp', 'ArrowDown', 'ArrowLeft', 'ArrowRight', 'Home', 'End'].includes(e.key)) {
            if (e.target.matches('[role="tablist"] [role="tab"]')) {
                updateTabFocus(e);
            }
        }
    });

    document.addEventListener('click', e => {
        if (e.target.matches('[role="tablist"] [data-toggle="tab"], [role="tablist"] [data-toggle="pill"]')) {
            const tabs = e.target.closest('[role="tablist"]').querySelectorAll('[data-toggle="tab"], [data-toggle="pill"]');
            e.preventDefault();
            $(e.target).tab('show');
            tabs.forEach(tab => {
                tab.tabIndex = -1;
            });
            e.target.tabIndex = 0;
        }
    });
};

/**
 * Fix keyboard interaction with Bootstrap Collapse elements.
 *
 * @see {@link https://www.w3.org/TR/wai-aria-practices-1.1/#disclosure|WAI-ARIA Authoring Practices 1.1 - Disclosure (Show/Hide)}
 */
const collapseFix = () => {
    document.addEventListener('keydown', e => {
        if (e.target.matches('[data-toggle="collapse"]')) {
            // Pressing space should toggle expand/collapse.
            if (e.key === ' ') {
                e.preventDefault();
                e.target.click();
            }
        }
    });
};

export const init = () => {
    dropdownFix();
    comboboxFix();
    autoFocus();
    tabElementFix();
    collapseFix();
};
