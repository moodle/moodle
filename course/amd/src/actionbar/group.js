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

import GroupSearch from 'core_group/comboboxsearch/group';

/**
 * Allow the user to search for groups in the action bar.
 *
 * @module    core_course/actionbar/group
 * @copyright 2024 Shamim Rezaie <shamim@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
export default class Group extends GroupSearch {

    baseUrl;

    /**
     * Construct the class.
     *
     * @param {string} baseUrl The base URL for the page.
     * @param {int|null} cmid ID of the course module initiating the group search (optional).
     */
    constructor(baseUrl, cmid = null) {
        super(cmid);
        this.baseUrl = baseUrl;
    }

    /**
     * Allow the class to be invoked via PHP.
     *
     * @param {string} baseUrl The base URL for the page.
     * @param {int|null} cmid ID of the course module initiating the group search (optional).
     * @returns {Group}
     */
    static init(baseUrl, cmid = null) {
        return new Group(baseUrl, cmid);
    }

    /**
     * Build up the link that is dedicated to a particular result.
     *
     * @param {Number} groupID The ID of the group selected.
     * @returns {string}
     */
    selectOneLink(groupID) {
        const url = new URL(this.baseUrl);
        url.searchParams.set('groupsearchvalue', this.getSearchTerm());
        url.searchParams.set('group', groupID);

        return url.toString();
    }
}
