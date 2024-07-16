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

import UserSearch from 'core_user/comboboxsearch/user';
import * as Repository from 'mod_assign/repository';

// Define our standard lookups.
const selectors = {
    component: '.user-search',
    groupid: '[data-region="groupid"]',
    instance: '[data-region="instance"]',
    currentvalue: '[data-region="currentvalue"]',
};
const component = document.querySelector(selectors.component);
const groupID = parseInt(component.querySelector(selectors.groupid).dataset.groupid, 10);
const assignID = parseInt(component.querySelector(selectors.instance).dataset.instance, 10);

/**
 * Allow the user to search for users in the action bar.
 *
 * @module    mod_assign/user
 * @copyright 2024 Ilya Tregubov <ilyatregubov@proton.me>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
export default class User extends UserSearch {

    /**
     * Construct the class.
     *
     * @param {string} baseUrl The base URL for the page.
     */
    constructor(baseUrl) {
        super();
        this.baseUrl = baseUrl;
    }

    /**
     * Allow the class to be invoked via PHP.
     *
     * @param {string} baseUrl The base URL for the page.
     * @returns {User}
     */
    static init(baseUrl) {
        return new User(baseUrl);
    }

    /**
     * Build up the view all link.
     *
     * @returns {string|*}
     */
    selectAllResultsLink() {
        const url = new URL(this.baseUrl);
        url.searchParams.set('search', this.getSearchTerm());

        return url.toString();
    }

    /**
     * Get the data we will be searching against in this component.
     *
     * @returns {Promise<*>}
     */
    fetchDataset() {
        return Repository.userFetch(assignID, groupID).then((r) => r);
    }

    /**
     * Build up the link that is dedicated to a particular result.
     *
     * @param {Number} userID The ID of the user selected.
     * @returns {string|*}
     */
    selectOneLink(userID) {
        const url = new URL(this.baseUrl);
        url.searchParams.set('search', this.getSearchTerm());
        url.searchParams.set('userid', userID.toString());

        return url.toString();
    }
}
