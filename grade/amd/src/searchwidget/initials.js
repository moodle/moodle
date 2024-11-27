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
 * A small dropdown to filter users within the gradebook.
 *
 * @module    core_grades/searchwidget/initials
 * @copyright 2022 Mathew May <mathew.solutions>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @deprecated since Moodle 4.5 - please use core_course/actionbar/initials instead.
 * @todo       Final deprecation in Moodle 6.0. See MDL-82421.
 */

import Pending from 'core/pending';
import * as Url from 'core/url';
import CustomEvents from "core/custom_interaction_events";
import Dropdown from 'theme_boost/bootstrap/dropdown';

/**
 * Whether the event listener has already been registered for this module.
 *
 * @type {boolean}
 */
let registered = false;

// Contain our selectors within this file until they could be of use elsewhere.
const selectors = {
    pageListItem: 'page-item',
    pageClickableItem: '.page-link',
    activeItem: 'active',
    formDropdown: '.initialsdropdownform',
    parentDomNode: '.initials-selector',
    firstInitial: 'firstinitial',
    lastInitial: 'lastinitial',
    initialBars: '.initialbar', // Both first and last name use this class.
    targetButton: 'initialswidget',
    formItems: {
        type: 'submit',
        save: 'save',
        cancel: 'cancel'
    }
};

/**
 * Our initial hook into the module which will eventually allow us to handle the dropdown initials bar form.
 *
 * @param {String} callingLink The link to redirect upon form submission.
 * @param {Null|Number} gpr_userid The user id to filter by.
 * @param {Null|String} gpr_search The search value to filter by.
 */
export const init = (callingLink, gpr_userid = null, gpr_search = null) => {
    if (registered) {
        return;
    }
    const pendingPromise = new Pending();
    registerListenerEvents(callingLink, gpr_userid, gpr_search);
    // BS events always bubble so, we need to listen for the event higher up the chain.
    document.querySelector(selectors.parentDomNode).addEventListener('shown.bs.dropdown', () => {
        document.querySelector(selectors.pageClickableItem).focus({preventScroll: true});
    });
    pendingPromise.resolve();
    registered = true;
};

/**
 * Register event listeners.
 *
 * @param {String} callingLink The link to redirect upon form submission.
 * @param {Null|Number} gpr_userid The user id to filter by.
 * @param {Null|String} gpr_search The search value to filter by.
 */
const registerListenerEvents = (callingLink, gpr_userid = null, gpr_search = null) => {
    const events = [
        'click',
        CustomEvents.events.activate,
        CustomEvents.events.keyboardActivate
    ];
    CustomEvents.define(document, events);

    // Register events.
    events.forEach((event) => {
        document.addEventListener(event, (e) => {
            // Always fetch the latest information when we click as state is a fickle thing.
            let {firstActive, lastActive, sifirst, silast} = onClickVariables();
            let itemToReset = '';

            // Prevent the usual form behaviour.
            if (e.target.closest(selectors.formDropdown)) {
                e.preventDefault();
            }

            // Handle the state of active initials before form submission.
            if (e.target.closest(`${selectors.formDropdown} .${selectors.pageListItem}`)) {
                // Ensure the li items don't cause weird clicking emptying out the form.
                if (e.target.classList.contains(selectors.pageListItem)) {
                    return;
                }

                const initialsBar = e.target.closest(selectors.initialBars); // Find out which initial bar we are in.

                // We want to find the current active item in the menu area the user selected.
                // We also want to fetch the raw item out of the array for instant manipulation.
                if (initialsBar.classList.contains(selectors.firstInitial)) {
                    sifirst = e.target;
                    itemToReset = firstActive;
                } else {
                    silast = e.target;
                    itemToReset = lastActive;
                }
                swapActiveItems(itemToReset, e);
            }

            // Handle form submissions.
            if (e.target.closest(`${selectors.formDropdown}`) && e.target.type === selectors.formItems.type) {
                if (e.target.dataset.action === selectors.formItems.save) {
                    // Ensure we strip out the value (All) as it messes with the PHP side of the initials bar.
                    // Then we will redirect the user back onto the page with new filters applied.
                    const params = {
                        'id': e.target.closest(selectors.formDropdown).dataset.courseid,
                        'gpr_search': gpr_search !== null ? gpr_search : '',
                        'sifirst': sifirst.parentElement.classList.contains('initialbarall') ? '' : sifirst.value,
                        'silast': silast.parentElement.classList.contains('initialbarall') ? '' : silast.value,
                    };
                    if (gpr_userid !== null) {
                        params.gpr_userid = gpr_userid;
                    }
                    window.location = Url.relativeUrl(callingLink, params);
                }
                if (e.target.dataset.action === selectors.formItems.cancel) {
                    Dropdown.getOrCreateInstance(document.querySelector(`.${selectors.targetButton}`)).toggle();
                }
            }
        });
    });
};

/**
 * A small abstracted helper function which allows us to ensure we have up-to-date lists of nodes.
 *
 * @returns {{firstActive: HTMLElement, lastActive: HTMLElement, sifirst: ?String, silast: ?String}}
 */
const onClickVariables = () => {
    // Ensure we have an up-to-date initials bar.
    const firstItems = [...document.querySelectorAll(`.${selectors.firstInitial} li`)];
    const lastItems = [...document.querySelectorAll(`.${selectors.lastInitial} li`)];
    const firstActive = firstItems.filter((item) => item.classList.contains(selectors.activeItem))[0];
    const lastActive = lastItems.filter((item) => item.classList.contains(selectors.activeItem))[0];
    // Ensure we retain both of the selections from a previous instance.
    let sifirst = firstActive.querySelector(selectors.pageClickableItem);
    let silast = lastActive.querySelector(selectors.pageClickableItem);
    return {firstActive, lastActive, sifirst, silast};
};

/**
 * Given we are provided the old li and current click event, swap around the active properties.
 *
 * @param {HTMLElement} itemToReset
 * @param {Event} e
 */
const swapActiveItems = (itemToReset, e) => {
    itemToReset.classList.remove(selectors.activeItem);
    itemToReset.querySelector(selectors.pageClickableItem).ariaCurrent = false;

    // Set the select item as the current item.
    const itemToSetActive = e.target.parentElement;
    itemToSetActive.classList.add(selectors.activeItem);
    e.target.ariaCurrent = true;
};
