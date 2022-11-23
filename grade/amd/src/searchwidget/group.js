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

import Pending from 'core/pending';
import * as Templates from 'core/templates';
import * as Repository from 'core_grades/searchwidget/repository';
import * as WidgetBase from 'core_grades/searchwidget/basewidget';
import $ from 'jquery';
import * as Selectors from 'core_grades/searchwidget/selectors';

/**
 * Whether the event listener has already been registered for this module.
 *
 * @type {boolean}
 */
let registered = false;

/**
 * Our entry point into starting to build the group search widget.
 *
 * It'll eventually, based upon the listeners, open the search widget and allow filtering.
 *
 * @method init
 */
export const init = () => {
    if (registered) {
        return;
    }
    const pendingPromise = new Pending();
    registerListenerEvents();
    pendingPromise.resolve();
    registered = true;
};

/**
 * Register event listeners.
 *
 * @method registerListenerEvents
 */
const registerListenerEvents = () => {
    let {bodyPromiseResolver, bodyPromise} = WidgetBase.promisesAndResolvers();
    const dropdownMenuContainer = document.querySelector(Selectors.elements.getSearchWidgetDropdownSelector('group'));

    // Handle the 'shown.bs.dropdown' event (Fired when the dropdown menu is fully displayed).
    $(Selectors.elements.getSearchWidgetSelector('group')).on('show.bs.dropdown', async(e) => {
        const courseID = e.relatedTarget.dataset.courseid;
        const actionBaseUrl = e.relatedTarget.dataset.actionBaseUrl;
        // Display a loading icon in the dropdown menu container until the body promise is resolved.
        await WidgetBase.showLoader(dropdownMenuContainer);

        // If an error occurs while fetching the data, display the error within the dropdown menu.
        const data = await Repository.groupFetch(courseID, actionBaseUrl).catch(async(e) => {
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
        );
    });

    // Resolvers for passed functions in the dropdown creation.
    bodyPromiseResolver(Templates.render(
        'core_grades/searchwidget/group/groupsearch_body',
        []
    ));

    // Handle the 'hide.bs.dropdown' event (Fired when the dropdown menu is being closed).
    $(Selectors.elements.getSearchWidgetSelector('group')).on('hide.bs.dropdown', () => {
        // Reset the state once the groups menu dropdown is closed.
        dropdownMenuContainer.innerHTML = '';
    });
};

/**
 * Define how we want to search and filter groups when the user decides to input a search value.
 *
 * @method registerListenerEvents
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
