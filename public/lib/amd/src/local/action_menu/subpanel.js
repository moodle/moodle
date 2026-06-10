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
 * Action menu subpanel JS controls.
 *
 * @module      core/local/action_menu/subpanel
 * @copyright   2023 Mikel Martín <mikel@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import {debounce} from 'core/utils';
import {
    isBehatSite,
    isExtraSmall,
    firstFocusableElement,
    lastFocusableElement,
    previousFocusableElement,
    nextFocusableElement,
} from 'core/pagehelpers';
import Pending from 'core/pending';
import {
    hide,
    unhide,
} from 'core/aria';
import EventHandler from 'theme_boost/bootstrap/dom/event-handler';
import * as Popper from 'core/popper2';

const Selectors = {
    mainMenu: '[role="menu"]',
    dropdownRight: '.dropdown-menu-end',
    subPanel: '.dropdown-subpanel',
    subPanelMenuItem: '.dropdown-subpanel > .dropdown-item',
    subPanelContent: '.dropdown-subpanel > .dropdown-menu',
    // Drawer selector.
    drawer: '[data-region="fixed-drawer"]',
    // Lateral blocks columns selectors.
    blockColumn: '.blockcolumn',
    columnLeft: '.columnleft',
};

const Classes = {
    dropRight: 'dropend',
    dropLeft: 'dropstart',
    dropDown: 'dropdown',
    forceLeft: 'downleft',
    contentDisplayed: 'content-displayed',
};

const BootstrapEvents = {
    hideDropdown: 'hidden.bs.dropdown',
};

let initialized = false;

/**
 * Initialize all delegated events into the page.
 */
const initPageEvents = () => {
    if (initialized) {
        return;
    }
    // Hide all subpanels when hiding a dropdown.
    document.addEventListener(BootstrapEvents.hideDropdown, () => {
        document.querySelectorAll(`${Selectors.subPanelContent}.show`).forEach(visibleSubPanel => {
            const dropdownSubPanel = visibleSubPanel.closest(Selectors.subPanel);
            const subPanel = new SubPanel(dropdownSubPanel);
            subPanel.setVisibility(false);
        });
    });

    window.addEventListener('resize', debounce(updateAllPanelsPosition, 400));

    initialized = true;
};

/**
 * Update all the panels position.
 */
const updateAllPanelsPosition = () => {
    document.querySelectorAll(Selectors.subPanel).forEach(dropdown => {
        const subpanel = new SubPanel(dropdown);
        subpanel.updatePosition();
    });
};

/**
 * Subpanel class.
 * @private
 */
class SubPanel {
    /**
     * Constructor.
     * @param {HTMLElement} element The element to initialize.
     */
    constructor(element) {
        this.element = element;
        this.menuItem = element.querySelector(Selectors.subPanelMenuItem);
        this.panelContent = element.querySelector(Selectors.subPanelContent);
        /**
         * Enable preview when the menu item has focus.
         *
         * This is disabled when the user press ESC or shift+TAB to force closing
         *
         * @type {Boolean}
         * @private
         */
        this.showPreviewOnFocus = true;
    }

    /**
     * Initialize the subpanel element.
     *
     * This method adds the event listeners to the subpanel and the position classes.
     */
    init() {
        if (this.element.dataset.subPanelInitialized) {
            return;
        }

        this.updatePosition();

        // Full element events.
        this.element.addEventListener('focusin', this._mainElementFocusInHandler.bind(this));
        // Menu Item events.
        this.menuItem.addEventListener('click', this._menuItemClickHandler.bind(this));
        // Use the Bootstrap key handler for the menu item key handler.
        // This will avoid Boostrap Dropdown handler to prevent the propagation to the subpanel.
        const subpanelMenuItemSelector = `#${this.element.id}${Selectors.subPanelMenuItem}`;
        EventHandler.on(document, 'keydown', subpanelMenuItemSelector, this._menuItemKeyHandler.bind(this));
        if (!isBehatSite()) {
            // Behat in Chrome usually move the mouse over the page when trying clicking a subpanel element.
            // If the menu has more than one subpanel this could cause closing the subpanel by mistake.
            this.menuItem.addEventListener('mouseover', this._menuItemHoverHandler.bind(this));
            this.menuItem.addEventListener('mouseout', this._menuItemHoverOutHandler.bind(this));
        }
        // Subpanel content events.
        this._bindPanelContentKeyHandler();

        this.element.dataset.subPanelInitialized = true;
    }

    /**
     * Bind the panel content keydown event handler on document via Bootstrap's EventHandler.
     *
     * Using EventHandler (capture phase) fires before Bootstrap's own dropdown handler,
     * allowing stopPropagation() to prevent Bootstrap from crashing on .dropdown-menu
     * elements inside nested subpanels that have no [data-bs-toggle] toggle.
     */
    _bindPanelContentKeyHandler() {
        const panelContentSelector = `#${this.element.id} > .dropdown-menu`;
        EventHandler.on(document, 'keydown', panelContentSelector, this._panelContentKeyHandler.bind(this));
    }

    /**
     * Hides the subpanel when mouse leaves the menu item.
     * @param {Event} event
     */
    _hideCurrentSubPanel(event) {
        // Only hide if not hovering over the menu item or subpanel content.
        const related = event.relatedTarget;
        if (!this.menuItem.contains(related) && !this.panelContent.contains(related)) {
            this.setVisibility(false);
        }
    }

    /**
     * Checks if the subpanel has enough space.
     *
     * In general there are two scenarios were the subpanel must be interacted differently:
     * - Extra small screens: The subpanel is displayed below the menu item.
     * - Drawer: The subpanel is displayed one of the drawers.
     * - Block columns: for classic based themes.
     *
     * @returns {Boolean} true if the subpanel should be displayed in small screens.
     */
    _needSmallSpaceBehaviour() {
        return isExtraSmall() ||
            this.element.closest(Selectors.drawer) !== null ||
            this.element.closest(Selectors.blockColumn) !== null;
    }

    /**
     * Check if the subpanel should be displayed on the right.
     *
     * This is defined by the drop right boostrap class. However, if the menu is
     * displayed in a block column on the right, the subpanel should be forced
     * to the right.
     *
     * @returns {Boolean} true if the subpanel should be displayed on the right.
     */
    _needDropdownRight() {
        if (this.element.closest(Selectors.columnLeft) !== null) {
            return false;
        }
        return this.element.closest(Selectors.dropdownRight) !== null;
    }

    /**
     * Main element focus in handler.
     */
    _mainElementFocusInHandler() {
        if (this._needSmallSpaceBehaviour() || !this.showPreviewOnFocus) {
            // Preview is disabled when the user press ESC or shift+TAB to force closing
            // but if the continue navigating with keyboard the preview is enabled again.
            this.showPreviewOnFocus = true;
            return;
        }
        if (!this.getVisibility()) {
            this.setVisibility(true);
        }
    }

    /**
     * Menu item click handler.
     * @param {Event} event
     */
    _menuItemClickHandler(event) {
        // Prevent click on empty href and only toggle the subpanel visibility.
        const href = this.menuItem.getAttribute('href');
        if (href === '#' || href === '' || href === null) {
            event.preventDefault();
        }
        event.stopPropagation();
        if (this._needSmallSpaceBehaviour()) {
            this.setVisibility(!this.getVisibility());
        }
    }

    /**
     * Menu item hover handler.
     * @private
     */
    _menuItemHoverHandler() {
        if (this._needSmallSpaceBehaviour()) {
            return;
        }
        this.setVisibility(true);
    }

    /**
     * Menu item hover out handler.
     * @param {Event} event
     * @private
     */
    _menuItemHoverOutHandler(event) {
        if (this._needSmallSpaceBehaviour()) {
            return;
        }
        this._hideOtherSubPanels();
        // Hide subpanel when the menu item itself is not hovered.
        this._hideCurrentSubPanel(event);
    }

    /**
     * Menu item key handler.
     * @param {Event} event
     * @private
     */
    _menuItemKeyHandler(event) {
        // In small sizes te down key will focus on the panel.
        if (event.key === 'ArrowUp' || (event.key === 'ArrowDown' && !this._needSmallSpaceBehaviour())) {
            this.setVisibility(false);
            return;
        }

        // Keys to move focus to the panel.
        let focusPanel = false;

        if (this._isOpeningArrowFor(this.element, event)) {
            focusPanel = true;
        }
        if (event.key === 'Tab' && !event.shiftKey) {
            focusPanel = true;
        }
        if (event.key === ' ') {
            focusPanel = true;
        }
        // Enter inside multilevel menus follow their href (if any).
        if (event.key === 'Enter') {
            const href = this.menuItem.getAttribute('href');
            if (href === '#' || href === '' || href === null) {
                event.preventDefault();
                focusPanel = true;
            }
        }
        // In extra small screen the panel is shown below the item.
        if (event.key === 'ArrowDown' && this._needSmallSpaceBehaviour() && this.getVisibility()) {
            focusPanel = true;
        }
        if (focusPanel) {
            event.stopPropagation();
            event.preventDefault();
            this.setVisibility(true);
            this._focusPanelContent();
        }
    }

    /**
     * Check whether a horizontal arrow event points in the given subpanel's opening direction.
     *
     * Small-space mode uses .dropdown (neither dropend nor dropstart): any horizontal
     * arrow is treated as opening, preserving the legacy behaviour.
     *
     * @param {HTMLElement} subPanel The subpanel element to check against.
     * @param {KeyboardEvent} event The keyboard event.
     * @returns {Boolean} true if the arrow points in the subpanel's opening direction.
     * @private
     */
    _isOpeningArrowFor(subPanel, event) {
        if (event.key !== 'ArrowRight' && event.key !== 'ArrowLeft') {
            return false;
        }
        const [leftClass, rightClass] = document.documentElement.dir === 'rtl'
            ? [Classes.dropRight, Classes.dropLeft]
            : [Classes.dropLeft, Classes.dropRight];
        const opensRight = subPanel.classList.contains(rightClass);
        const opensLeft = subPanel.classList.contains(leftClass);
        // Small-space mode (no dropend/dropstart) has no horizontal direction:
        // Treat any horizontal arrow as opening to preserve legacy behaviour.
        if (!opensRight && !opensLeft) {
            return true;
        }
        return (opensRight && event.key === 'ArrowRight') || (opensLeft && event.key === 'ArrowLeft');
    }

    /**
     * Check whether a horizontal arrow event opens a nested subpanel inside this panel.
     *
     * Used by `_panelContentKeyHandler` to leave the event for the nested subpanel's
     * own handler to open it, instead of moving focus back to this panel's menu item.
     *
     * @param {KeyboardEvent} event The keyboard event.
     * @returns {Boolean} true if the arrow opens a nested subpanel.
     * @private
     */
    _isNestedOpeningArrow(event) {
        const targetSubPanel = event.target.closest(Selectors.subPanel);
        if (!targetSubPanel || targetSubPanel === this.element) {
            return false;
        }
        return this._isOpeningArrowFor(targetSubPanel, event);
    }

    /**
     * Close any nested subpanel that the event target belongs to.
     *
     * This ensures the nested content doesn't interfere with the focus search
     * when navigating with ArrowUp/ArrowDown.
     *
     * @param {Event} event The keyboard event.
     * @private
     */
    _closeNestedSubPanel(event) {
        const nestedSubPanel = event.target.closest(Selectors.subPanel);
        if (nestedSubPanel && nestedSubPanel !== this.element) {
            new SubPanel(nestedSubPanel).setVisibility(false);
        }
    }

    /**
     * Sub panel content key handler.
     * @param {Event} event
     * @private
     */
    _panelContentKeyHandler(event) {
        // Skip events from within nested subpanel content. Those are handled
        // by the nested subpanel's own handler.
        if (event.target.closest(Selectors.subPanelContent) !== this.panelContent) {
            return;
        }
        // In extra small devices the panel is displayed under the menu item
        // so the arrow up/down switch between subpanel and the menu item.
        const canLoop = !this._needSmallSpaceBehaviour();
        let isBrowsingSubPanel = false;
        let newFocus = null;

        switch (event.key) {
            case 'ArrowRight':
            case 'ArrowLeft':
                if (!this._isNestedOpeningArrow(event)) {
                    this._closeNestedSubPanel(event);
                    newFocus = this.menuItem;
                }
                break;
            case 'Escape':
                newFocus = this.menuItem;
                this.setVisibility(false);
                this.showPreviewOnFocus = false;
                break;
            case 'Tab':
                // According to WCAG Shift+Tab is similar to Escape.
                if (event.shiftKey) {
                    newFocus = this.menuItem;
                    this.setVisibility(false);
                    this.showPreviewOnFocus = false;
                }
                break;
            case 'ArrowUp':
            case 'ArrowDown':
                this._closeNestedSubPanel(event);
                isBrowsingSubPanel = true;
                newFocus = event.key === 'ArrowUp'
                    ? previousFocusableElement(this.panelContent, canLoop)
                    : nextFocusableElement(this.panelContent, canLoop);
                break;
            case 'Home':
                newFocus = firstFocusableElement(this.panelContent);
                isBrowsingSubPanel = true;
                break;
            case 'End':
                newFocus = lastFocusableElement(this.panelContent);
                isBrowsingSubPanel = true;
                break;
        }
        // If the user cannot loop and arrive to the start/end of the subpanel
        // we focus on the menu item.
        if (newFocus === null && isBrowsingSubPanel && !canLoop) {
            newFocus = this.menuItem;
        }
        // Always stop propagation for subpanel browsing keys to prevent
        // Bootstrap's dropdown handler from processing them on .dropdown-menu
        // elements that have no associated [data-bs-toggle] toggle.
        if (newFocus !== null || isBrowsingSubPanel) {
            event.stopPropagation();
            event.preventDefault();
        }
        if (newFocus !== null) {
            newFocus.focus();
        }
    }

    /**
     * Focus on the first focusable element of the subpanel.
     * @private
     */
    _focusPanelContent() {
        const pendingPromise = new Pending('core/action_menu/subpanel:focuscontent');
        // Some Bootstrap events are triggered after the click event.
        // To prevent this from affecting the focus we wait a bit.
        setTimeout(() => {
            const firstFocusable = firstFocusableElement(this.panelContent);
            if (firstFocusable) {
                firstFocusable.focus();
            }
            pendingPromise.resolve();
        }, 100);
    }

    /**
     * Create a Popper instance to position the subpanel content.
     * Using strategy 'fixed' prevents the subpanel from causing page scroll.
     * @private
     */
    _createPopper() {
        // On Behat sites and small-space mode (mobile/drawer), CSS handles the positioning.
        // Popper is only needed on desktop to avoid page scroll when the panel appears near the edge.
        if (isBehatSite() || this._needSmallSpaceBehaviour()) {
            return;
        }
        this._destroyPopper();
        const isRTL = document.documentElement.dir === 'rtl';
        const dropdownRight = this._needDropdownRight();
        const placement = (dropdownRight !== isRTL) ? 'left-start' : 'right-start';
        this.element._popperInstance = Popper.createPopper(this.menuItem, this.panelContent, {
            placement,
            strategy: 'fixed',
            modifiers: [{name: 'flip', enabled: true}],
        });
    }

    /**
     * Destroy the Popper instance if it exists.
     * @private
     */
    _destroyPopper() {
        if (this.element._popperInstance) {
            this.element._popperInstance.destroy();
            this.element._popperInstance = null;
        }
    }

    /**
     * Set the visibility of a subpanel.
     * @param {Boolean} visible true if the subpanel should be visible.
     */
    setVisibility(visible) {
        if (visible) {
            this._hideOtherSubPanels();
        }
        // Aria hidden/unhidden can alter the focus, we only want to do it when needed.
        if (!visible && this.getVisibility()) {
            hide(this.panelContent);
        }
        if (visible && !this.getVisibility()) {
            unhide(this.panelContent);
        }
        this.menuItem.setAttribute('aria-expanded', visible ? 'true' : 'false');
        this.panelContent.classList.toggle('show', visible);
        this.element.classList.toggle(Classes.contentDisplayed, visible);
        if (visible) {
            this._createPopper();
        } else {
            this._destroyPopper();
        }
    }

    /**
     * Hide all other subpanels in the parent menu.
     * @private
     */
    _hideOtherSubPanels() {
        const dropdown = this.element.closest(Selectors.mainMenu);
        dropdown.querySelectorAll(`${Selectors.subPanelContent}.show`).forEach(visibleSubPanel => {
            const dropdownSubPanel = visibleSubPanel.closest(Selectors.subPanel);
            if (dropdownSubPanel === this.element) {
                return;
            }
            // Don't hide subpanels that contain the currently focused element
            // (e.g. nested subpanels the user is interacting with).
            if (dropdownSubPanel.contains(document.activeElement)) {
                return;
            }
            new SubPanel(dropdownSubPanel).setVisibility(false);
        });
    }

    /**
     * Get the visibility of a subpanel.
     * @returns {Boolean} true if the subpanel is visible.
     */
    getVisibility() {
        return this.menuItem.getAttribute('aria-expanded') === 'true';
    }

    /**
     * Update the panels position depending on the screen size and panel position.
     */
    updatePosition() {
        const dropdownRight = this._needDropdownRight();
        if (this._needSmallSpaceBehaviour()) {
            this.element.classList.remove(Classes.dropRight);
            this.element.classList.remove(Classes.dropLeft);
            this.element.classList.add(Classes.dropDown);
            this.element.classList.toggle(Classes.forceLeft, dropdownRight);
        } else {
            this.element.classList.remove(Classes.dropDown);
            this.element.classList.remove(Classes.forceLeft);
            this.element.classList.toggle(Classes.dropRight, !dropdownRight);
            this.element.classList.toggle(Classes.dropLeft, dropdownRight);
        }
        // Update Popper placement if the subpanel is currently visible.
        if (this.element._popperInstance) {
            this.element._popperInstance.update();
        }
    }
}

/**
 * Initialise module for given report
 *
 * @method
 * @param {string} selector The query selector to init.
 */
export const init = (selector) => {
    initPageEvents();
    const subMenu = document.querySelector(selector);
    if (!subMenu) {
        throw new Error(`Sub panel element not found: ${selector}`);
    }
    const subPanel = new SubPanel(subMenu);
    subPanel.init();
};
