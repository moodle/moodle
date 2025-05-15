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
import {DefaultAllowlist} from './bootstrap/util/sanitizer';
import setupBootstrapPendingChecks from './pending';
import EventHandler from './bootstrap/dom/event-handler';

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
    const popoverConfig = {
        container: 'body',
        trigger: 'focus',
        allowList: Object.assign(DefaultAllowlist, {table: [], thead: [], tbody: [], tr: [], th: [], td: []}),
    };
    [...popoverTriggerList].map(popoverTriggerEl => new Bootstrap.Popover(popoverTriggerEl, popoverConfig));

    // Enable dynamically created popovers inside modals.
    document.addEventListener('core/modal:bodyRendered', (e) => {
        const modal = e.target;
        const popoverTriggerList = modal.querySelectorAll('[data-bs-toggle="popover"]');
        [...popoverTriggerList].map(popoverTriggerEl => new Bootstrap.Popover(popoverTriggerEl, popoverConfig));
    });

    document.addEventListener('keydown', e => {
        const popoverTrigger = e.target.closest('[data-bs-toggle="popover"]');
        if (e.key === 'Escape' && popoverTrigger) {
            Bootstrap.Popover.getOrCreateInstance(popoverTrigger).hide();
        }
        if (e.key === 'Enter' && popoverTrigger) {
            Bootstrap.Popover.getOrCreateInstance(popoverTrigger).show();
        }
    });
    document.addEventListener('click', e => {
        const popoverTrigger = e.target.closest('[data-bs-toggle="popover"]');
        if (!popoverTrigger) {
            return;
        }
        const popover = Bootstrap.Popover.getOrCreateInstance(popoverTrigger);
        if (!popover._isShown()) {
            popover.show();
        }
    });
};

/**
 * Enable tooltips
 *
 */
const enableTooltips = () => {
    const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
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

// Realocate Bootstrap events to the body element.
realocateBootstrapEvents();

pendingPromise.resolve();

export {
    Bootstrap,
};
