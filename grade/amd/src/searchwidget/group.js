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
 * A widget to search groups within the gradebook.
 *
 * @module    core_grades/searchwidget/group
 * @copyright 2022 Mathew May <mathew.solutions>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import * as FocusLockManager from 'core/local/aria/focuslock';
import Pending from 'core/pending';
import * as Templates from 'core/templates';
import * as Repository from 'core_grades/searchwidget/repository';
import * as WidgetBase from 'core_grades/searchwidget/basewidget';
import $ from 'jquery';
import * as Selectors from 'core_grades/searchwidget/selectors';

/**
 * Whether this module is already initialised.
 *
 * @type {boolean}
 */
let initialised = false;

/**
 * Our entry point into starting to build the group search widget.
 *
 * It'll eventually, based upon the listeners, open the search widget and allow filtering.
 *
 * @method init
 */
export const init = () => {
    if (!initialised && document.querySelector(Selectors.elements.getSearchWidgetSelector('group'))) {
        const pendingPromise = new Pending();
        registerListenerEvents();
        pendingPromise.resolve();
    }
    initialised = true;
};

/**
 * Register event listeners.
 *
 * @method registerListenerEvents
 */
const registerListenerEvents = () => {
    let {bodyPromiseResolver, bodyPromise} = WidgetBase.promisesAndResolvers();
    const dropdownMenuContainer = document.querySelector(Selectors.elements.getSearchWidgetDropdownSelector('group'));
    const menuContainer = document.querySelector(Selectors.elements.getSearchWidgetSelector('group'));
    const inputElement = menuContainer.querySelector('input[name="group"]');

    // Handle the 'shown.bs.dropdown' event (Fired when the dropdown menu is fully displayed).
    $(menuContainer).on('show.bs.dropdown', async(e) => {
        const courseID = e.relatedTarget.dataset.courseid;
        // Display a loading icon in the dropdown menu container until the body promise is resolved.
        await WidgetBase.showLoader(dropdownMenuContainer);

        // If an error occurs while fetching the data, display the error within the dropdown menu.
        const data = await Repository.groupFetch(courseID).catch(async(e) => {
            const errorTemplateData = {
                'errormessage': e.message
            };
            bodyPromiseResolver(
                await Templates.render('core_grades/searchwidget/error', errorTemplateData)
            );
        });
        // Early return if there is no module data.
        if (data === []) {
            return;
        }
        await WidgetBase.init(
            dropdownMenuContainer,
            bodyPromise,
            data.groups,
            searchGroups(),
            null,
            afterSelect
        );

        // Lock tab control. It has to be locked because the dropdown's role is dialog.
        FocusLockManager.trapFocus(dropdownMenuContainer);
    });

    // Resolvers for passed functions in the dropdown creation.
    bodyPromiseResolver(Templates.render(
        'core_grades/searchwidget/group/groupsearch_body',
        []
    ));

    // Handle the 'hide.bs.dropdown' event (Fired when the dropdown menu is being closed).
    $(menuContainer).on('hide.bs.dropdown', () => {
        FocusLockManager.untrapFocus();
    });

    inputElement.addEventListener('change', e => {
        const toggle = menuContainer.querySelector('.dropdown-toggle');
        const courseId = toggle.dataset.courseid;
        const actionUrl = toggle.dataset.actionBaseUrl ?
            new URL(toggle.dataset.actionBaseUrl.replace(/&amp;/g, "&")) :
            new URL(location.href);
        actionUrl.searchParams.set('id', courseId);
        actionUrl.searchParams.set('group', e.target.value);
        actionUrl.searchParams.delete('page');

        location.href = actionUrl.href;

        e.stopPropagation();
    });
};

/**
 * Define how we want to search and filter groups when the user decides to input a search value.
 *
 * @method searchGroups
 * @returns {function(): function(*, *): (*)}
 */
const searchGroups = () => {
    return () => {
        return (groups, searchTerm) => {
            if (searchTerm === '') {
                return groups;
            }
            searchTerm = searchTerm.toLowerCase();
            const searchResults = [];
            groups.forEach((group) => {
                const groupName = group.name.toLowerCase();
                if (groupName.includes(searchTerm)) {
                    searchResults.push(group);
                }
            });
            return searchResults;
        };
    };
};

/**
 * Define the action to be performed when an item is selected by the search widget.
 *
 * @param {String} selected The selected item's value.
 */
const afterSelect = (selected) => {
    const menuContainer = document.querySelector(Selectors.elements.getSearchWidgetSelector('group'));
    const inputElement = menuContainer.querySelector('input[name="group"]');

    $(menuContainer).dropdown('hide'); // Otherwise the dropdown stays open when user choose an option using keyboard.

    if (inputElement.value != selected) {
        inputElement.value = selected;
        inputElement.dispatchEvent(new Event('change', {bubbles: true}));
    }
};
