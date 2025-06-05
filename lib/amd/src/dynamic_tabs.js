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
 * Dynamic Tabs UI element with AJAX loading of tabs content
 *
 * @module      core/dynamic_tabs
 * @copyright   2021 David Matamoros <davidmc@moodle.com> based on code from Marina Glancy
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import $ from 'jquery';
import Templates from 'core/templates';
import {addIconToContainer} from 'core/loadingicon';
import Notification from 'core/notification';
import {prependPageTitle} from 'core/page_title';
import Pending from 'core/pending';
import {getString} from 'core/str';
import {getContent} from 'core/local/repository/dynamic_tabs';
import {isAnyWatchedFormDirty, resetAllFormDirtyStates} from 'core_form/changechecker';

const SELECTORS = {
    dynamicTabs: '.dynamictabs',
    activeTab: '.dynamictabs .nav-link.active',
    allActiveTabs: '.dynamictabs .nav-link[data-bs-toggle="tab"]:not(.disabled)',
    tabContent: '.dynamictabs .tab-pane [data-tab-content]',
    tabToggle: 'a[data-bs-toggle="tab"]',
    tabPane: '.dynamictabs .tab-pane',
};

SELECTORS.forTabName = tabName => `.dynamictabs [data-tab-content="${tabName}"]`;
SELECTORS.forTabId = tabName => `.dynamictabs [data-bs-toggle="tab"][href="#${tabName}"]`;

let watchedFormDirtyNotification = false;

/**
 * Initialises the tabs view on the page (only one tabs view per page is supported)
 */
export const init = () => {
    const tabToggles = document.querySelectorAll(SELECTORS.tabToggle);
    tabToggles.forEach(tabToggle => {
        // Listen to click, warn user if they are navigating away with unsaved form changes.
        tabToggle.addEventListener('show.bs.tab', (event) => {
            if (isAnyWatchedFormDirty()) {
                event.preventDefault();
                event.stopPropagation();

                // Prevent double execution of event listener.
                if (!watchedFormDirtyNotification) {
                    watchedFormDirtyNotification = true;

                    Notification.saveCancelPromise(
                        getString('changesmade'),
                        getString('changesmadereallygoaway'),
                        getString('confirm'),
                        {triggerElement: tabToggle}
                    ).then(() => {
                        // Reset form dirty state on confirmation, re-trigger the event.
                        resetAllFormDirtyStates();
                        tabToggle.dispatchEvent(new Event('click', {bubbles: true}));
                        return;
                    }).catch(() => {
                        // User cancelled the dialogue.
                    }).finally(() => {
                        watchedFormDirtyNotification = false;
                    });
                }

                return;
            }

            // Clean content from previous tab.
            const previousTabName = getActiveTabName();
            if (previousTabName) {
                const previousTab = document.querySelector(SELECTORS.forTabName(previousTabName));
                previousTab.textContent = '';
            }
        });

        tabToggle.addEventListener('shown.bs.tab', () => {
            const tabPane = document.getElementById(tabToggle.getAttribute('href').replace(/^#/, ''));
            if (tabPane) {
                loadTab(tabPane.id);
            }
        });
    });

    if (!openTabFromHash()) {
        const tabs = document.querySelector(SELECTORS.allActiveTabs);
        if (tabs) {
            openTab(tabs.getAttribute('aria-controls'));
        } else {
            // We may hide tabs if there is only one available, just load the contents of the first tab.
            const tabPane = document.querySelector(SELECTORS.tabPane);
            if (tabPane) {
                tabPane.classList.add('active', 'show');
                loadTab(tabPane.getAttribute('id'));
            }
        }
    }
};

/**
 * Returns id/name of the currently active tab
 *
 * @return {String|null}
 */
const getActiveTabName = () => {
    const element = document.querySelector(SELECTORS.activeTab);
    return element?.getAttribute('aria-controls') || null;
};

/**
 * Returns the id/name of the first tab
 *
 * @return {String|null}
 */
const getFirstTabName = () => {
    const element = document.querySelector(SELECTORS.tabContent);
    return element?.dataset.tabContent || null;
};

/**
 * Loads contents of a tab using an AJAX request
 *
 * @param {String} tabName
 */
const loadTab = (tabName) => {
    // If tabName is not specified find the active tab, or if is not defined, the first available tab.
    tabName = tabName ?? getActiveTabName() ?? getFirstTabName();
    const tab = document.querySelector(SELECTORS.forTabName(tabName));
    if (!tab) {
        return;
    }

    const pendingPromise = new Pending('core/dynamic_tabs:loadTab:' + tabName);

    const tabLabelledBy = document.getElementById(tab.getAttribute('aria-labelledby'));
    prependPageTitle(tabLabelledBy.innerText);

    addIconToContainer(tab)
    .then(() => {
        let tabArgs = {...tab.dataset};
        delete tabArgs.tabClass;
        delete tabArgs.tabContent;
        return getContent(tab.dataset.tabClass, JSON.stringify(tabArgs));
    })
    .then(response => Promise.all([
        $.parseHTML(response.javascript, null, true).map(node => node.innerHTML).join("\n"),
        Templates.renderForPromise(response.template, JSON.parse(response.content)),
    ]))
    .then(([responseJs, {html, js}]) => Templates.replaceNodeContents(tab, html, js + responseJs))
    .then(() => pendingPromise.resolve())
    .catch(Notification.exception);
};

/**
 * Return the tab given the tab name
 *
 * @param {String} tabName
 * @return {HTMLElement}
 */
const getTab = (tabName) => {
    return document.querySelector(SELECTORS.forTabId(tabName));
};

/**
 * Return the tab pane given the tab name
 *
 * @param {String} tabName
 * @return {HTMLElement}
 */
const getTabPane = (tabName) => {
    return document.getElementById(tabName);
};

/**
 * Open the tab on page load. If this script loads before theme_boost/tab we need to open tab ourselves
 *
 * @param {String} tabName
 * @return {Boolean}
 */
const openTab = (tabName) => {
    const tab = getTab(tabName);
    if (!tab) {
        return false;
    }

    loadTab(tabName);
    tab.classList.add('active');
    getTabPane(tabName).classList.add('active', 'show');
    return true;
};

/**
 * If there is a location hash that is the same as the tab name - open this tab.
 *
 * @return {Boolean}
 */
const openTabFromHash = () => {
    const hash = document.location.hash;
    if (hash.match(/^#\w+$/g)) {
        return openTab(hash.replace(/^#/g, ''));
    }

    return false;
};
