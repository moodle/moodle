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
 * A small modal to search users within the gradebook.
 *
 * @module    gradereport_singleview/user
 * @copyright 2022 Mathew May <mathew.solutions>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import Pending from 'core/pending';
import * as Templates from 'core/templates';
import CustomEvents from "core/custom_interaction_events";
import * as Repository from 'core_grades/searchwidget/repository';
import * as WidgetBase from 'core_grades/searchwidget/basewidget';
import {get_string as getString} from 'core/str';
import Url from 'core/url';

/**
 * Our entry point into starting to build the search widget.
 * It'll eventually, based upon the listeners, open the search widget and allow filtering.
 *
 * @method init
 */
export const init = () => {
    const pendingPromise = new Pending();
    registerListenerEvents();
    pendingPromise.resolve();
};

/**
 * Register user search widget related event listeners.
 *
 * @method registerListenerEvents
 */
const registerListenerEvents = () => {
    const events = [
        'click',
        CustomEvents.events.activate,
        CustomEvents.events.keyboardActivate
    ];
    CustomEvents.define(document, events);

    let {bodyPromiseResolver, bodyPromise} = WidgetBase.promisesAndResolvers();

    // Register events.
    events.forEach((event) => {
        document.addEventListener(event, async(e) => {
            const trigger = e.target.closest('.userwidget');
            if (trigger) {
                const courseID = trigger.dataset.courseid;
                const groupId = trigger.dataset.groupid;
                e.preventDefault();

                const actionBaseUrl = Url.relativeUrl('/grade/report/singleview/index.php', {item: 'user'}, false);
                // If an error occurs while fetching the data, display the error within the modal.
                const data = await Repository.userFetch(courseID, actionBaseUrl, groupId).catch(async(e) => {
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
                WidgetBase.init(
                    bodyPromise,
                    data.users,
                    searchUsers(),
                    getString('selectauser', 'grades')
                );
            }
        });
    });
    // Resolvers for passed functions in the modal creation.
    bodyPromiseResolver(Templates.render(
        'core_grades/searchwidget/user/usersearch_body',
        []
    ));
};

/**
 * Define how we want to search and filter users when the user decides to input a search value.
 *
 * @method registerListenerEvents
 * @returns {function(): function(*, *): (*)}
 */
const searchUsers = () => {
    return () => {
        return (users, searchTerm) => {
            if (searchTerm === '') {
                return users;
            }
            searchTerm = searchTerm.toLowerCase();
            const searchResults = [];
            users.forEach((user) => {
                const userName = user.fullname.toLowerCase();
                if (userName.includes(searchTerm)) {
                    searchResults.push(user);
                }
            });
            return searchResults;
        };
    };
};
