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
 * Template renderer for Moodle. Load and render Moodle templates with Mustache.
 *
 * @module     theme_boost/loader
 * @copyright  2015 Damyon Wiese <damyon@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since      2.9
 */

import * as Aria from './aria';
import * as Bootstrap from 'bootstrap';
import Pending from 'core/pending';
import {eventTypes} from 'core_filters/events';
import {DefaultAllowlist} from 'bootstrap/util/sanitizer';
import setupBootstrapPendingChecks from './pending';
import SelectorEngine from './bootstrap/dom/selector-engine';

/**
 * CSS selector matching only the secondary navigation's own tablist, e.g. admin/search.php's
 * category tabs. Deliberately narrower than "[data-bs-toggle=tab]" alone, which also matches
 * unrelated Bootstrap tabs elsewhere on the page.
 */
const SECONDARY_NAV_TAB_SELECTOR = '.secondary-navigation [role="tablist"] [data-bs-toggle="tab"]';

/**
 * Rember the last visited tabs.
 */
const rememberTabs = () => {
    // Tabs already present in the DOM when this runs, e.g. admin settings page tabs. Kept
    // separate from SECONDARY_NAV_TAB_SELECTOR so that tabs mounted later elsewhere on the page
    // (e.g. the activity chooser) are not picked up by the delegated listener below.
    const pageTabs = new Set(document.querySelectorAll('a[data-bs-toggle="tab"]'));

    // Delegate on the document rather than binding to each tab trigger directly.
    document.addEventListener('shown.bs.tab', (e) => {
        if (!pageTabs.has(e.target) && !e.target.matches(SECONDARY_NAV_TAB_SELECTOR)) {
            return;
        }
        var hash = e.target.getAttribute('href');
        if (history.replaceState) {
            history.replaceState(null, null, hash);
        } else {
            location.hash = hash;
        }
    });

    const hash = window.location.hash;
    if (!hash) {
        return;
    }

    /**
     * Find the tab whose href is the current hash.
     *
     * Compares attribute values rather than building a selector from the hash, so a fragment
     * that isn't a valid CSS identifier can't throw and take the rest of the theme's boot with it.
     *
     * @param {ParentNode} root Where to look for tabs.
     * @returns {Element|null} The matching tab, if any.
     */
    const findTab = (root) => [...root.querySelectorAll('[role="tablist"] [href^="#"]')]
        .find((tab) => tab.getAttribute('href') === hash) ?? null;

    /**
     * Close the "More" dropdown if activating the given tab left it forced open.
     *
     * Bootstrap's Tab treats a tab inside a .dropdown as a dropdown item and adds .show to the
     * dropdown's menu when it activates, without going through the Dropdown API. Nothing else
     * closes that menu on page load, so it would stay open over the page.
     *
     * @param {Element} tab The tab element that was just activated.
     */
    const closeStrayDropdown = (tab) => {
        const outerElem = tab.closest('.dropdown');
        const menu = outerElem && outerElem.querySelector(':scope > .dropdown-menu');
        if (!menu || !menu.classList.contains('show')) {
            return;
        }
        menu.classList.remove('show');
        const toggle = outerElem.querySelector(':scope > .dropdown-toggle');
        if (toggle) {
            toggle.setAttribute('aria-expanded', 'false');
        }
    };

    /**
     * Switch to the pane matching location.hash.
     *
     * Used when the tab is already marked active (e.g. React rendered it from the hash), where
     * Bootstrap's Tab.show() returns early without touching the panes, so the pane is left on
     * the default tab. The Tab API can't be used to correct that, hence the direct class toggle.
     */
    const forceActivatePane = () => {
        let id = hash.slice(1);
        try {
            id = decodeURIComponent(id);
        } catch (e) {
            // Malformed escape sequence, fall back to the raw fragment.
        }
        const pane = document.getElementById(id);
        if (!pane) {
            return;
        }
        const previousPane = pane.parentElement?.querySelector(':scope > .tab-pane.active');
        if (pane === previousPane) {
            return;
        }
        previousPane?.classList.remove('active', 'show');
        pane.classList.add('active', 'show');
    };

    /**
     * Select the given tab.
     *
     * @param {Element} tab The tab matching location.hash.
     */
    const selectTab = (tab) => {
        if (tab.classList.contains('active')) {
            forceActivatePane();
        } else {
            tab.click();
        }
        closeStrayDropdown(tab);
    };

    const existingTab = findTab(document);
    if (existingTab) {
        selectTab(existingTab);
        return;
    }

    // The matching tab is not in the DOM yet, e.g. the secondary navigation's React tablist is
    // still mounting. Watch only that container, and only for added nodes, until it appears.
    const secondaryNav = document.querySelector('.secondary-navigation');
    if (!secondaryNav) {
        return;
    }
    const observer = new MutationObserver((mutations) => {
        for (const mutation of mutations) {
            for (const node of mutation.addedNodes) {
                if (!(node instanceof Element)) {
                    continue;
                }
                const tab = findTab(node) ?? (node.matches('[role="tablist"] [href^="#"]') && findTab(node.parentNode));
                if (tab) {
                    observer.disconnect();
                    selectTab(tab);
                    return;
                }
            }
        }
    });
    observer.observe(secondaryNav, {childList: true, subtree: true});
    setTimeout(() => observer.disconnect(), 5000);
};

/**
 * Enable all popovers
 *
 */
const enablePopovers = () => {
    const popoverTriggerList = document.querySelectorAll('[data-bs-toggle="popover"]');
    // ExcludeSelector lets a call site trim out elements (e.g. other open help popovers) that
    // Bootstrap's own SelectorEngine.focusableChildren() would otherwise include.
    const getTabbableElements = (container, excludeSelector) => {
        const elements = SelectorEngine.focusableChildren(container)
            .filter(element => element.tabIndex >= 0)
            .filter(element => !excludeSelector || !element.matches(excludeSelector));
        const tabbableRadios = new Set();
        elements.filter(element => element.matches('input[type="radio"][name]')).forEach(radio => {
            const group = elements.filter(element => element.matches('input[type="radio"]')
                && element.name === radio.name && element.form === radio.form);
            tabbableRadios.add(group.find(element => element.checked) ?? group[0]);
        });
        return elements.filter(element => !element.matches('input[type="radio"][name]') || tabbableRadios.has(element))
            .sort((elementA, elementB) => {
                if (elementA.tabIndex === elementB.tabIndex) {
                    return 0;
                }
                if (elementA.tabIndex === 0) {
                    return 1;
                }
                if (elementB.tabIndex === 0) {
                    return -1;
                }
                return elementA.tabIndex - elementB.tabIndex;
            });
    };
    const popoverConfig = {
        container: 'body',
        trigger: 'focus',
        allowList: Object.assign(DefaultAllowlist, {table: [], thead: [], tbody: [], tr: [], th: [], td: []}),
    };
    // Maps a help popover's tip element back to its trigger. Looking this up via the trigger's
    // aria-describedby attribute isn't reliable since that attribute is repointed to the tip's
    // content element (see the 'inserted.bs.popover' listener below).
    const helpPopoverTriggers = new WeakMap();
    const initialisePopover = popoverTriggerEl => {
        const isHelpPopover = popoverTriggerEl.classList.contains('help-icon');
        const config = isHelpPopover
            ? {
                ...popoverConfig,
                trigger: 'manual',
                template: Bootstrap.Popover.Default.template.replace('role="tooltip"', 'role="dialog"'),
            }
            : popoverConfig;
        const popover = new Bootstrap.Popover(popoverTriggerEl, config);
        if (isHelpPopover) {
            popoverTriggerEl.setAttribute('aria-haspopup', 'dialog');
        }
        return popover;
    };
    [...popoverTriggerList].map(initialisePopover);

    // Enable dynamically created popovers inside modals.
    document.addEventListener('core/modal:bodyRendered', (e) => {
        const modal = e.target;
        const popoverTriggerList = modal.querySelectorAll('[data-bs-toggle="popover"]');
        [...popoverTriggerList].map(initialisePopover);
    });

    document.addEventListener('keydown', e => {
        const popoverTrigger = e.target.closest('[data-bs-toggle="popover"]');
        const helpPopover = e.target.closest('.help-popover');
        const helpPopoverTrigger = helpPopover ? helpPopoverTriggers.get(helpPopover) : null;
        if (e.key === 'Escape' && popoverTrigger) {
            Bootstrap.Popover.getOrCreateInstance(popoverTrigger).hide();
        }
        if (e.key === 'Escape' && helpPopoverTrigger) {
            // Focus the trigger before hiding so the focusin handler's "already shown" guard
            // is still true, otherwise it re-shows a new tip that the pending hide() then
            // destroys once its (animated, therefore deferred) cleanup callback runs.
            helpPopoverTrigger.focus();
            Bootstrap.Popover.getOrCreateInstance(helpPopoverTrigger).hide();
        }
        if (e.key === 'Enter' && popoverTrigger) {
            const popover = Bootstrap.Popover.getOrCreateInstance(popoverTrigger);
            if (!popover._isShown()) {
                popover.show();
            }
        }
        if (e.key === 'Tab' && !e.shiftKey && popoverTrigger?.classList.contains('help-icon')) {
            const popover = Bootstrap.Popover.getOrCreateInstance(popoverTrigger);
            if (popover._isShown() && popover.tip) {
                const firstFocusableElement = getTabbableElements(popover.tip)[0];
                if (firstFocusableElement) {
                    e.preventDefault();
                    firstFocusableElement.focus();
                }
            }
        }
        if (e.key === 'Tab' && helpPopoverTrigger) {
            const popoverFocusableElements = getTabbableElements(helpPopover);
            const focusedElementIndex = popoverFocusableElements.indexOf(e.target);
            if (e.shiftKey && focusedElementIndex === 0) {
                e.preventDefault();
                helpPopoverTrigger.focus();
                return;
            }
            if (e.shiftKey || focusedElementIndex !== popoverFocusableElements.length - 1) {
                return;
            }
            const focusableElements = getTabbableElements(document.body, '.help-popover, .help-popover *');
            const triggerIndex = focusableElements.indexOf(helpPopoverTrigger);
            const nextFocusableElement = triggerIndex === -1 ? null : focusableElements[triggerIndex + 1];
            if (nextFocusableElement) {
                e.preventDefault();
                nextFocusableElement.focus();
            }
        }
    });
    document.addEventListener('click', e => {
        const popoverTrigger = e.target.closest('[data-bs-toggle="popover"]');
        document.querySelectorAll('.help-icon[aria-describedby]').forEach(trigger => {
            const triggerPopover = Bootstrap.Popover.getOrCreateInstance(trigger);
            if (trigger !== popoverTrigger && !triggerPopover.tip?.contains(e.target)) {
                triggerPopover.hide();
            }
        });
        if (!popoverTrigger) {
            return;
        }
        const popover = Bootstrap.Popover.getOrCreateInstance(popoverTrigger);
        if (!popover._isShown()) {
            popover.show();
        }
    });
    document.addEventListener('focusin', e => {
        const popoverTrigger = e.target.closest('.help-icon[data-bs-toggle="popover"]');
        if (popoverTrigger) {
            const popover = Bootstrap.Popover.getOrCreateInstance(popoverTrigger);
            if (!popover._isShown()) {
                popover.show();
            }
        }
    });
    document.addEventListener('focusout', e => {
        const popoverTrigger = e.target.closest('.help-icon[data-bs-toggle="popover"]');
        const helpPopover = e.target.closest('.help-popover');
        const trigger = popoverTrigger ?? (helpPopover ? helpPopoverTriggers.get(helpPopover) : null);
        if (!trigger) {
            return;
        }
        const popover = Bootstrap.Popover.getOrCreateInstance(trigger);
        const popoverElement = helpPopover ?? popover.tip;
        if (!trigger.contains(e.relatedTarget) && !popoverElement?.contains(e.relatedTarget)) {
            popover.hide();
        }
    });
    document.addEventListener('inserted.bs.popover', e => {
        if (e.target.classList.contains('help-icon')) {
            const tip = Bootstrap.Popover.getOrCreateInstance(e.target).tip;
            helpPopoverTriggers.set(tip, e.target);
            tip.setAttribute('aria-label', e.target.getAttribute('aria-label'));
            // The trigger's aria-describedby points at the tip above. Per the accessible name/
            // description computation, a referenced element's own aria-label takes precedence
            // over its content, so pointing aria-describedby at the tip itself (which now has an
            // aria-label) would make the description collapse to "Help" instead of the actual
            // help text. Point it at the content element instead, which has no aria-label of
            // its own.
            const content = tip.querySelector('.popover-body');
            if (content) {
                content.id = content.id || `${tip.id}-content`;
                e.target.setAttribute('aria-describedby', content.id);
            }
        }
    });
};

/**
 * Enable tooltips
 *
 * @param {Element} rootElement
 */
const enableTooltips = (rootElement = document) => {
    const tooltipTriggerList = rootElement.querySelectorAll('[data-bs-toggle="tooltip"]');
    const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new Bootstrap.Tooltip(tooltipTriggerEl));

    document.addEventListener('keydown', e => {
        if (e.key === 'Escape') {
            tooltipList.forEach(tooltip => {
                tooltip.hide();
            });
        }
    });
};

/**
 * Enable tooltips for dynamic content updates
 */
const enableTooltipsOnContentUpdated = () => {
    document.addEventListener(eventTypes.filterContentUpdated, e => {
        e.detail.nodes.forEach(node => {
            if (node instanceof HTMLElement) {
                enableTooltips(node);
            }
        });
    });
};

const pendingPromise = new Pending('theme_boost/loader:init');

// Add pending promise event listeners to relevant Bootstrap custom events.
setupBootstrapPendingChecks();

// Setup Aria helpers for Bootstrap features.
Aria.init();

// Remember the last visited tabs.
rememberTabs();

// Enable all popovers.
enablePopovers();

// Enable all tooltips.
enableTooltips();
enableTooltipsOnContentUpdated();

pendingPromise.resolve();

export {
    Bootstrap,
};
