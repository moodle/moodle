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
import * as Bootstrap from './index';
import Pending from 'core/pending';
import {eventTypes} from 'core_filters/events';
import {DefaultAllowlist} from './bootstrap/util/sanitizer';
import setupBootstrapPendingChecks from './pending';
import EventHandler from './bootstrap/dom/event-handler';
import SelectorEngine from './bootstrap/dom/selector-engine';

/**
 * Rember the last visited tabs.
 */
const rememberTabs = () => {
    const tabTriggerList = document.querySelectorAll('a[data-bs-toggle="tab"]');
    [...tabTriggerList].map(tabTriggerEl => tabTriggerEl.addEventListener('shown.bs.tab', (e) => {
        var hash = e.target.getAttribute('href');
        if (history.replaceState) {
            history.replaceState(null, null, hash);
        } else {
            location.hash = hash;
        }
    }));
    const hash = window.location.hash;
    if (hash) {
        const tab = document.querySelector('[role="tablist"] [href="' + hash + '"]');
        if (tab) {
            tab.click();
        }
    }
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

/**
 * Realocate Bootstrap events to the body element.
 *
 * Bootstrap 5 has a unique event handling mechanism that attaches all event handlers at the document level
 * during the capture phase, rather than the usual bubbling phase. As a result, original Bootstrap events
 * cannot be stopped or prevented, since the document is the first node executed in the capture phase.
 * For certain advanced UI elements, such as form autocomplete, it is important to capture key-down events before
 * Bootstrap's handlers to prevent unintended closures of elements. Therefore, we need to change the Bootstrap handler
 * so that it operates one level lower, specifically at the body level.
 */
const realocateBootstrapEvents = () => {
    EventHandler.off(document, 'keydown.bs.dropdown.data-api', '.dropdown-menu', Bootstrap.Dropdown.dataApiKeydownHandler);
    EventHandler.on(document.body, 'keydown.bs.dropdown.data-api', '.dropdown-menu', Bootstrap.Dropdown.dataApiKeydownHandler);
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

// Realocate Bootstrap events to the body element.
realocateBootstrapEvents();

pendingPromise.resolve();

export {
    Bootstrap,
};
