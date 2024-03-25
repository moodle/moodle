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
 * Allow the user to search for learners within the user report.
 *
 * @module    gradereport_user/user
 * @copyright 2023 Mathew May <mathew.solutions>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
import UserSearch from 'core_user/comboboxsearch/user';
import Url from 'core/url';
import {renderForPromise, replaceNodeContents} from 'core/templates';
import * as Repository from 'core_grades/searchwidget/repository';

export default class User extends UserSearch {

    constructor() {
        super();
    }

    static init() {
        return new User();
    }

    /**
     * Build the content then replace the node.
     */
    async renderDropdown() {
        const {html, js} = await renderForPromise('core_user/comboboxsearch/resultset', {
            users: this.getMatchedResults().slice(0, 5),
            hasresults: this.getMatchedResults().length > 0,
            instance: this.instance,
            matches: this.getDatasetSize(),
            searchterm: this.getSearchTerm(),
            selectall: this.selectAllResultsLink(),
        });
        replaceNodeContents(this.getHTMLElements().searchDropdown, html, js);
        // Remove aria-activedescendant when the available options change.
        this.searchInput.removeAttribute('aria-activedescendant');
    }

    /**
     * Build up the view all link.
     *
     * @returns {string|*}
     */
    selectAllResultsLink() {
        return Url.relativeUrl('/grade/report/user/index.php', {
            id: this.courseID,
            userid: 0,
            searchvalue: this.getSearchTerm()
        }, false);
    }

    /**
     * Build up the link that is dedicated to a particular result.
     *
     * @param {Number} userID The ID of the user selected.
     * @returns {string|*}
     */
    selectOneLink(userID) {
        return Url.relativeUrl('/grade/report/user/index.php', {
            id: this.courseID,
            searchvalue: this.getSearchTerm(),
            userid: userID,
        }, false);
    }

    /**
     * Get the data we will be searching against in this component.
     *
     * @returns {Promise<*>}
     */
    fetchDataset() {
        // Small typing checks as sometimes groups don't exist therefore the element returns a empty string.
        const gts = typeof (this.groupID) === "string" && this.groupID === '' ? 0 : this.groupID;
        return Repository.userFetch(this.courseID, gts).then((r) => r.users);
    }
}
