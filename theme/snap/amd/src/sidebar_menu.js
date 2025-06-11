/* eslint-disable no-trailing-spaces */
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

import {isSmall} from 'core/pagehelpers';
import {setUserPreferences, getUserPreferences} from 'core_user/repository';

/**
 * JavaScript for the Snap theme sidebar menu functionality
 *
 * @module     theme_snap/sidebar_menu
 * @copyright  2024 Open LMS (https://www.openlms.net)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

const SELECTORS = {
    SIDEBAR: '#snap-sidebar-menu',
    TRIGGER: '.snap-sidebar-menu-trigger',
    TRIGGER_ICON: '.snap-sidebar-menu-trigger i',
    HEADER: 'header',
    DRAWER_BUTTON: '.snap-sidebar-menu-item[data-activeselector]',
    MESSAGES_POPOVER: '[data-region="popover-region-messages"]',
    CLOSE_DRAWER_BUTTON: '[data-action="closedrawer"]',
    SIDEBAR_MENU_ITEM: '.snap-sidebar-menu-item',
    NAV_UNPINNED: '#mr-nav.headroom--unpinned',
    GOTO_TOP_LINK: '#goto-top-link',
    COURSE_TOC: '#course-toc',
};

const CLASSES = {
    CUSTOM_MENU_ITEM: 'custom-menu-item',
    SHOW: 'show',
    ACTIVE: 'active',
    COLLAPSED: 'collapsed',
    ROTATE: 'rotate-180',
    STATE_VISIBLE: 'state-visible',
    POSITIONING_OFFSCREEN: 'positioning-offscreen',
};

const DRAWERS = {
    SELECTORS: [
        '.drawer',
        '.block_settings.block',
        '#snap_feeds_side_menu',
        '.drawer:has(.message-app)'
    ],
    ACTIVE_SELECTORS: [
        '.drawer.show',
        '.block_settings.block.state-visible',
        '#snap_feeds_side_menu.state-visible',
        '.drawer:not(.hidden):has(.message-app)'
    ]
};

const POPOVERS_DROPDOWNS = {
    CLICKABLE_SELECTORS: [
        '#user-menu-toggle', // User menu
        '#nav-intellicart-popover-container', // Intellicart
        '#nav-notification-popover-container', // Notifications
    ]
};

const ACTIVE_SELECTORS = {
    BLOCKS_DRAWER: '[data-activeselector="#theme_snap-drawers-blocks.show"]',
    SNAP_FEEDS: '[data-activeselector="#snap_feeds_side_menu_trigger.active"]',
    MESSAGES_DRAWER: '[data-activeselector=\'[data-region="popover-region-messages"]:not(.collapsed)\']',
};

const PREFERENCES = {
    BLOCKS_DRAWER: 'drawer-open-block',
    SNAP_FEEDS: 'snap-feeds-open',
    MESSAGES_DRAWER: 'snap-message-drawer-open',
};

const PREFERENCE_MAP = {
    [PREFERENCES.BLOCKS_DRAWER]: ACTIVE_SELECTORS.BLOCKS_DRAWER,
    [PREFERENCES.SNAP_FEEDS]: ACTIVE_SELECTORS.SNAP_FEEDS,
    [PREFERENCES.MESSAGES_DRAWER]: ACTIVE_SELECTORS.MESSAGES_DRAWER,
};

let lastScrollX = 0;

/**
 * Toggle sidebar menu visibility and update its position
 */
const toggleSidebar = () => {
    const sidebar = document.querySelector(SELECTORS.SIDEBAR);
    const icon = document.querySelector(SELECTORS.TRIGGER_ICON);
    const isClosing = sidebar.classList.contains(CLASSES.SHOW);

    sidebar.classList.toggle(CLASSES.SHOW);
    icon.classList.toggle(CLASSES.ROTATE);
    updateElementPositions();
    
    // If we're closing the sidebar, close any open drawers
    if (isClosing) {
        closeAllDrawers();
    }
};

/**
 * Update the position of UI elements relative to the header
 * @param {Array|string|null} selectors - CSS selector(s) for elements to update, or null for sidebar only
 */
const updateElementPositions = (selectors = null) => {
    const header = document.querySelector(SELECTORS.HEADER);
    if (!header) {
        return;
    }

    const headerRect = header.getBoundingClientRect();
    const visibleHeight = window.innerHeight;
    const topPosition = Math.max(0, headerRect.bottom);
    const isNavUnpinned = document.querySelector(SELECTORS.NAV_UNPINNED);
    
    const sidebar = document.querySelector(SELECTORS.SIDEBAR);
    if (sidebar) {
        if (isNavUnpinned) {
            sidebar.style.top = '0px';
            sidebar.style.height = '100vh';
        } else {
            sidebar.style.top = `${topPosition}px`;
            sidebar.style.height = `${visibleHeight - topPosition}px`;
        }
        
        // Remove positioning-offscreen class after positioning is complete
        // Add a small delay before removing the positioning-offscreen class
        setTimeout(() => {
            sidebar.classList.remove(CLASSES.POSITIONING_OFFSCREEN);
        }, 100);
    }

    if (selectors) {
        const selectorsArray = Array.isArray(selectors) ? selectors : [selectors];
        
        // Update each element's position
        selectorsArray.forEach(selector => {
            const elements = document.querySelectorAll(selector);
            
            elements.forEach(element => {    
                if (isNavUnpinned) {
                    element.style.top = '0px';
                    element.style.height = '100vh';
                } else {
                    element.style.top = `${topPosition}px`;
                    element.style.height = `${visibleHeight - topPosition}px`;
                }
                
                // Ensure the element is visible within the viewport if it's active
                if (element.classList.contains(CLASSES.SHOW) || 
                    element.classList.contains(CLASSES.ACTIVE) || 
                    !element.classList.contains(CLASSES.COLLAPSED)) {
                    element.style.maxHeight = isNavUnpinned ? '100vh' : `${visibleHeight - topPosition}px`;
                }
            });
        });
    }
};

/**
 * Handle drawer button clicks
 * @param {Event} e - The event object
 */
const handleDrawerButtonClick = (e) => {
    setTimeout(() => {
        const button = e.target.closest(SELECTORS.DRAWER_BUTTON);
        repositionGotoTopLink();
        if (!button) {
            return;
        }

        const activeSelector = button.dataset.activeselector;
        if (!activeSelector) {
            return;
        }

        const activeElements = document.querySelectorAll(activeSelector);
        const isActive = Array.from(activeElements).some(
            (el) =>
            el.classList.contains(CLASSES.SHOW) ||
            el.classList.contains(CLASSES.ACTIVE) ||
            !el.classList.contains(CLASSES.COLLAPSED) // Consider not collapsed as active
        );

        if (isActive) {
            // If this drawer is being opened, close others
            closeOtherDrawers(activeSelector, button);
            button.classList.add(CLASSES.ACTIVE);
            setDrawerPreference(activeSelector, true);
        } else {
            button.classList.remove(CLASSES.ACTIVE);
            setDrawerPreference(activeSelector, false);
        }
    }, 50); // Small delay to allow the drawer state to update
};

/**
 * Close all active drawers except the one matching the given selector
 * @param {string} currentSelector - The selector for the drawer to keep open
 * @param {Element} currentButton - The button that was clicked
 */
const closeOtherDrawers = (currentSelector, currentButton) => {
    const drawerButtons = document.querySelectorAll(SELECTORS.DRAWER_BUTTON);
    repositionGotoTopLink();
    drawerButtons.forEach(button => {
        if (button === currentButton) {
            return;
        }

        const activeSelector = button.dataset.activeselector;
        if (!activeSelector || activeSelector === currentSelector) {
            return;
        }

        const activeElements = document.querySelectorAll(activeSelector);
        const isActive = Array.from(activeElements).some(el =>
            el.classList.contains(CLASSES.SHOW) ||
            el.classList.contains(CLASSES.ACTIVE) ||
            !el.classList.contains(CLASSES.COLLAPSED) // Consider not collapsed as active
        );

        if (isActive) {
            const isCustomContent = button.classList.contains(CLASSES.CUSTOM_MENU_ITEM);
            if (isCustomContent) {
                const clickableElement = button.querySelector('a, button') || button;
                clickableElement.click();
            } else {
                button.click();
            }
            setDrawerPreference(activeSelector, false);
            button.classList.remove(CLASSES.ACTIVE);
        }
    });
};

/**
 * Close all active drawers
 */
const closeAllDrawers = () => {
    const drawerButtons = document.querySelectorAll(SELECTORS.DRAWER_BUTTON);
    repositionGotoTopLink();
    drawerButtons.forEach(button => {
        const activeSelector = button.dataset.activeselector;
        if (!activeSelector) {
            return;
        }
        
        const activeElements = document.querySelectorAll(activeSelector);
        const isActive = Array.from(activeElements).some(el =>
            el.classList.contains(CLASSES.SHOW) ||
            el.classList.contains(CLASSES.ACTIVE) ||
            !el.classList.contains(CLASSES.COLLAPSED) // Consider not collapsed as active
        );

        if (isActive) {
            const isCustomContent = button.classList.contains(CLASSES.CUSTOM_MENU_ITEM);
            if (isCustomContent) {
                const clickableElement = button.querySelector('a, button') || button;
                clickableElement.click();
            } else {
                button.click();
            }
            button.classList.remove(CLASSES.ACTIVE);
        }
    });
};

/**
 * Handle messages popover click
 * @param {Event} e - The event object
 */
const handleMessagesPopoverClick = (e) => {
    const sidebarItem = e.currentTarget.closest(SELECTORS.SIDEBAR_MENU_ITEM);
    repositionGotoTopLink();
    if (sidebarItem) {
        const isCollapsed = e.currentTarget.classList.contains(CLASSES.COLLAPSED);
        if (isCollapsed) {
            e.currentTarget.classList.remove(CLASSES.COLLAPSED);
        } else {
            e.currentTarget.classList.add(CLASSES.COLLAPSED);
        }
    }
};

/**
 * Set the Actual Drawer based on user preferences.
 *
 * @return {Promise}
 */
const setActiveDrawer = async() => {
    const preferences = await getUserPreferences();
    const preferencesArray = {};
    // BEGIN LSU adding check for preferences otherwise an error is generated.
    if (preferences.hasOwnProperty('preferences')) {
        preferences.preferences.forEach(pref => {
            preferencesArray[pref.name] = pref.value;
        });
    }
    // END LSU adding check for preferences otherwise an error is generated.

    // Review which user preference is set to true, from PREFERENCE_MAP
    for (const [prefKey, drawerSelector] of Object.entries(PREFERENCE_MAP)) {
        // See if any Drawer was opened. (Preference set to 1)
        const shouldOpen = preferencesArray[prefKey] === 1 || preferencesArray[prefKey] === '1';

        if (shouldOpen) {
            const button = document.querySelector(drawerSelector);
            if (button) {
                // Simulate click on button.
                const clickableElement = button.querySelector('a, button') || button;
                clickableElement.click();
            }
        }
    }
};

/**
 * Set User preferences for the corresponding Drawer selected.
 * If "Value" = true, sets selected drawer to open and others to closed.
 * If "Value" = false, sets selected drawer to closed only.
 * @param {string} activeSelector - The selector for the drawer requested
 * @param {boolean} value - The value for the preference
 */
const setDrawerPreference = (activeSelector, value) => {
    // Loop all preferences map and set true or false to selected one.
    for (const [preference, selector] of Object.entries(PREFERENCE_MAP)) {
        if (selector.includes(activeSelector) && !isSmall() && value) {
            // Set open status to selected Drawer.
            setUserPreferences([{name: preference, value: true}]);
        } else if (value) {
            // Set closed status to other Drawers.
            setUserPreferences([{name: preference, value: false}]);
        } else if (selector.includes(activeSelector) && !value) {
            // Set closed status to selected Drawer.
            setUserPreferences([{name: preference, value: false}]);
        }
    }
};


/**
 * Handle close drawer button clicks
 */
const handleCloseDrawerClick = () => {
    repositionGotoTopLink();
    // Remove active classes from all drawer buttons
    document.querySelectorAll(SELECTORS.DRAWER_BUTTON).forEach(button => {
        button.classList.remove(CLASSES.ACTIVE);
    });
    
    // Add collapsed class to messages popover if it's open
    const messagesPopover = document.querySelector(SELECTORS.MESSAGES_POPOVER);
    if (messagesPopover && !messagesPopover.classList.contains(CLASSES.COLLAPSED)) {
        messagesPopover.classList.add(CLASSES.COLLAPSED);
    }
};

/**
 * Setup all event listeners
 */
const setupEventListeners = () => {
    const trigger = document.querySelector(SELECTORS.TRIGGER);
    if (trigger) {
        trigger.addEventListener('click', toggleSidebar);
    }

    // Update both sidebar and drawer positions on resize and scroll
    window.addEventListener('resize', () => {
        updateElementPositions(DRAWERS.SELECTORS);
    });
    
    window.addEventListener('scroll', () => {
        // Add a small delay to avoid performance issues with rapid scroll events
        setTimeout(() => {
            updateElementPositions(DRAWERS.SELECTORS);
            
            // Check if Go to Top link is visible and reposition it if needed
            const gotoTopLink = document.querySelector(SELECTORS.GOTO_TOP_LINK);
            if (gotoTopLink) {
                const computedStyle = window.getComputedStyle(gotoTopLink);
                if (computedStyle.visibility === 'visible') {
                    repositionGotoTopLink();
                }
            }
            
            // Handle horizontal scrolling to control sticky elements (e.g. grader)
            toggleSidebarOnHorizontalScroll(window.scrollX);
        }, 50);
    });

    // Add click event listeners to drawer buttons
    document.querySelectorAll(SELECTORS.DRAWER_BUTTON).forEach(button => {
        button.addEventListener('click', handleDrawerButtonClick);
    });
    
    // Add click event listener to messages popover
    const messagesPopover = document.querySelector(SELECTORS.MESSAGES_POPOVER);
    if (messagesPopover) {
        messagesPopover.addEventListener('click', handleMessagesPopoverClick);
    }
    
    // Add click event listeners to elements with data-action="closedrawer"
    document.querySelectorAll(SELECTORS.CLOSE_DRAWER_BUTTON).forEach(element => {
        element.addEventListener('click', handleCloseDrawerClick);
    });
    
    // Set up popover/dropdown click handlers
    setupPopoverClickHandlers();
};

/**
 * Initialize the sidebar menu functionality
 */
export const init = () => {
    setupEventListeners();
    updateElementPositions();
    
    // Update positions of all drawers
    updateElementPositions(DRAWERS.SELECTORS);
    // Open active Drawer.
    setActiveDrawer();
};

/**
 * Query active drawers, applying a workaround for selectors containing ':has' if needed.
 * TODO: Delete this when the selenium version of the job is higher than 3.141.59
 *
 * @param {string} selector The CSS selector to query.
 * @returns {NodeListOf<Element>|Array<Element>} A NodeList or an Array of matching elements.
 */
const queryActiveDrawers = (selector) => {
    // Check if the selector string contains ':has(' and matches the specific known case
    if (selector === '.drawer:not(.hidden):has(.message-app)') {
        // Workaround for :has(.message-app)
        const potentialDrawers = document.querySelectorAll('.drawer:not(.hidden)');
        return Array.from(potentialDrawers).filter(drawer => drawer.querySelector('.message-app'));
    } else {
        // Standard query for other selectors
        return document.querySelectorAll(selector);
    }
};

/**
 * Reposition the "Go to Top" button based on open drawers
 */
const repositionGotoTopLink = () => {
    const gotoTopLink = document.querySelector(SELECTORS.GOTO_TOP_LINK);
    if (!gotoTopLink) {
        return;
    }
    
    gotoTopLink.style.marginRight = '';
    
    // Check if sidebar is showing
    const sidebar = document.querySelector(SELECTORS.SIDEBAR);
    const isSidebarShowing = sidebar && sidebar.classList.contains(CLASSES.SHOW);
    
    // Only proceed if sidebar is showing
    if (isSidebarShowing) {
        // Check each drawer selector using the helper function
        for (const selector of DRAWERS.ACTIVE_SELECTORS) {
            const activeDrawers = queryActiveDrawers(selector); // Use the helper function

            if (activeDrawers.length > 0) {
                // Get the first active drawer found for this selector type
                const drawer = activeDrawers[0];
                if (drawer.offsetWidth > 0) {
                    // Get the width of the drawer
                    const drawerWidth = drawer.offsetWidth;
                    // Add margin to position the link to the left of the drawer
                    gotoTopLink.style.marginRight = `${drawerWidth}px`;
                    return; // Exit after finding the first open drawer
                }
            }
        }
    }
};

/**
 * Hide or show the sidebar based on horizontal scroll position
 * @param {number} scrollX - The horizontal scroll position
 */
const toggleSidebarOnHorizontalScroll = (scrollX) => {
    const sidebar = document.querySelector(SELECTORS.SIDEBAR);
    if (!sidebar) {
        return;
    }
    if (scrollX !== 0) {
        if (lastScrollX === 0) {
            // Hide sidebar
            sidebar.style.right = '-100%';
            
            // Hide active drawers
            DRAWERS.ACTIVE_SELECTORS.forEach(selector => {
                const activeDrawers = queryActiveDrawers(selector); // Use the helper function
                activeDrawers.forEach(drawer => {
                    drawer.style.right = '-100%';
                });
            });
        }
    } else if (lastScrollX !== 0) {
        // When returning to scroll position 0
        sidebar.style.right = '';
        
        // Restore active drawers visibility
        DRAWERS.ACTIVE_SELECTORS.forEach(selector => {
            const activeDrawers = queryActiveDrawers(selector); // Use the helper function
            activeDrawers.forEach(drawer => {
                drawer.style.right = '';
            });
        });
    }
    lastScrollX = scrollX;
};

/**
 * Add event listeners to popover/dropdown elements to close drawers first
 */
const setupPopoverClickHandlers = () => {
    let isClosingDrawers = false;

    const checkAndCloseDrawers = () => {
        if (isClosingDrawers) {
            return false;
        }

        let hasOpenDrawers = false;
        DRAWERS.ACTIVE_SELECTORS.forEach(selector => {
            const activeDrawers = queryActiveDrawers(selector); // Use the helper function
            if (activeDrawers.length > 0) {
                hasOpenDrawers = true;
            }
        });

        if (hasOpenDrawers) {
            // Set flag to prevent recursive calls
            isClosingDrawers = true;
            // Close all drawers first
            closeAllDrawers();
            isClosingDrawers = false;
            return true;
        }

        return false;
    };

    POPOVERS_DROPDOWNS.CLICKABLE_SELECTORS.forEach(selector => {
        const elements = document.querySelectorAll(selector);

        elements.forEach(element => {
            // Handle mouse clicks
            element.addEventListener('click', () => {
                checkAndCloseDrawers();
            }, true);

            // Handle keyboard events (Enter key)
            element.addEventListener('keydown', (e) => {
                // Check if the Enter key was pressed
                if (e.key === 'Enter' || e.keyCode === 13) {
                    checkAndCloseDrawers();
                }
            }, true);
        });
    });
};
