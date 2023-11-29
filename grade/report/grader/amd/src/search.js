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
 * Allow the user to search for learners within the grader report.
 * Have to basically search twice on the dataset to avoid passing around massive csv params whilst allowing debouncing.
 *
 * @module    gradereport_grader/search
 * @copyright 2023 Mathew May <mathew.solutions>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
import GradebookSearchClass from 'gradereport_grader/search/search_class';
import * as Repository from 'gradereport_grader/search/repository';
import {get_strings as getStrings} from 'core/str';
import Url from 'core/url';
import {renderForPromise, replaceNodeContents} from 'core/templates';

// Define our standard lookups.
const selectors = {
    component: '.user-search',
    courseid: '[data-region="courseid"]',
    resetPageButton: '[data-action="resetpage"]',
};
const component = document.querySelector(selectors.component);
const courseID = component.querySelector(selectors.courseid).dataset.courseid;
const bannedFilterFields = ['profileimageurlsmall', 'profileimageurl', 'id', 'link', 'matchingField', 'matchingFieldName'];

export default class UserSearch extends GradebookSearchClass {

    // A map of user profile field names that is human-readable.
    profilestringmap = null;

    constructor() {
        super();
    }

    static init() {
        return new UserSearch();
    }

    /**
     * The overall div that contains the searching widget.
     *
     * @returns {string}
     */
    setComponentSelector() {
        return '.user-search';
    }

    /**
     * The dropdown div that contains the searching widget result space.
     *
     * @returns {string}
     */
    setDropdownSelector() {
        return '.usersearchdropdown';
    }

    /**
     * The triggering div that contains the searching widget.
     *
     * @returns {string}
     */
    setTriggerSelector() {
        return '.usersearchwidget';
    }

    /**
     * Build the content then replace the node.
     */
    async renderDropdown() {
        const {html, js} = await renderForPromise('gradereport_grader/search/resultset', {
            users: this.getMatchedResults().slice(0, 5),
            hasusers: this.getMatchedResults().length > 0,
            matches: this.getMatchedResults().length,
            showing: this.getMatchedResults().slice(0, 5).length,
            searchterm: this.getSearchTerm(),
            selectall: this.selectAllResultsLink(),
        });
        replaceNodeContents(this.getHTMLElements().searchDropdown, html, js);
    }

    /**
     * Get the data we will be searching against in this component.
     *
     * @returns {Promise<*>}
     */
    fetchDataset() {
        return Repository.userFetch(courseID).then((r) => r.users);
    }

    /**
     * Dictate to the search component how and what we want to match upon.
     *
     * @param {Array} filterableData
     * @returns {Array} The users that match the given criteria.
     */
    async filterDataset(filterableData) {
        return filterableData.filter((user) => Object.keys(user).some((key) => {
            if (user[key] === "" || bannedFilterFields.includes(key)) {
                return false;
            }
            return user[key].toString().toLowerCase().includes(this.getPreppedSearchTerm());
        }));
    }

    /**
     * Given we have a subset of the dataset, set the field that we matched upon to inform the end user.
     *
     * @returns {Array} The results with the matched fields inserted.
     */
    async filterMatchDataset() {
        const stringMap = await this.getStringMap();
        this.setMatchedResults(
            this.getMatchedResults().map((user) => {
                for (const [key, value] of Object.entries(user)) {
                    const valueString = value.toString().toLowerCase();
                    const preppedSearchTerm = this.getPreppedSearchTerm();
                    const searchTerm = this.getSearchTerm();

                    if (!valueString.includes(preppedSearchTerm)) {
                        continue;
                    }

                    // Ensure we have a good string, otherwise fallback to the key.
                    user.matchingFieldName = stringMap.get(key) ?? key;

                    // Safely prepare our matching results.
                    const escapedValueString = valueString.replace(/</g, '&lt;');
                    const escapedMatchingField = escapedValueString.replace(
                        preppedSearchTerm.replace(/</g, '&lt;'),
                        `<span class="font-weight-bold">${searchTerm.replace(/</g, '&lt;')}</span>`
                    );

                    user.matchingField = `${escapedMatchingField} (${user.email})`;
                    user.link = this.selectOneLink(user.id);
                    break;
                }
                return user;
            })
        );
    }

    /**
     * The handler for when a user interacts with the component.
     *
     * @param {MouseEvent} e The triggering event that we are working with.
     */
    clickHandler(e) {
        super.clickHandler(e);
        if (e.target === this.getHTMLElements().currentViewAll && e.button === 0) {
            window.location = this.selectAllResultsLink();
        }
        if (e.target.closest(selectors.resetPageButton)) {
            window.location = e.target.closest(selectors.resetPageButton).href;
        }
    }

    /**
     * The handler for when a user presses a key within the component.
     *
     * @param {KeyboardEvent} e The triggering event that we are working with.
     */
    keyHandler(e) {
        super.keyHandler(e);

        if (e.target === this.getHTMLElements().currentViewAll && (e.key === 'Enter' || e.key === 'Space')) {
            window.location = this.selectAllResultsLink();
        }

        // Switch the key presses to handle keyboard nav.
        switch (e.key) {
            case 'Enter':
            case ' ':
                if (document.activeElement === this.getHTMLElements().searchInput) {
                    if (e.key === ' ') {
                        break;
                    } else {
                        window.location = this.selectAllResultsLink();
                        break;
                    }
                }
                if (document.activeElement === this.getHTMLElements().clearSearchButton) {
                    this.closeSearch(true);
                    break;
                }
                if (e.target.closest(selectors.resetPageButton)) {
                    window.location = e.target.closest(selectors.resetPageButton).href;
                    break;
                }
                if (e.target.closest('.dropdown-item')) {
                    e.preventDefault();
                    window.location = e.target.closest('.dropdown-item').href;
                    break;
                }
                break;
            case 'Escape':
                this.toggleDropdown();
                this.searchInput.focus({preventScroll: true});
                break;
            case 'Tab':
                // If the current focus is on clear search, then check if viewall exists then around tab to it.
                if (e.target.closest(this.selectors.clearSearch)) {
                    if (this.currentViewAll && !e.shiftKey) {
                        e.preventDefault();
                        this.currentViewAll.focus({preventScroll: true});
                    } else {
                        this.closeSearch();
                    }
                }
                break;
        }
    }

    /**
     * Build up the view all link.
     *
     * @returns {string|*}
     */
    selectAllResultsLink() {
        return Url.relativeUrl('/grade/report/grader/index.php', {
            id: courseID,
            gpr_search: this.getSearchTerm()
        }, false);
    }

    /**
     * Build up the view all link that is dedicated to a particular result.
     *
     * @param {Number} userID The ID of the user selected.
     * @returns {string|*}
     */
    selectOneLink(userID) {
        return Url.relativeUrl('/grade/report/grader/index.php', {
            id: courseID,
            gpr_search: this.getSearchTerm(),
            gpr_userid: userID,
            }, false);
    }

    /**
     * Given the set of profile fields we can possibly search, fetch their strings,
     * so we can report to screen readers the field that matched.
     *
     * @returns {Promise<void>}
     */
    getStringMap() {
        if (!this.profilestringmap) {
            const requiredStrings = [
                'username',
                'firstname',
                'lastname',
                'email',
                'city',
                'country',
                'department',
                'institution',
                'idnumber',
                'phone1',
                'phone2',
            ];
            this.profilestringmap = getStrings(requiredStrings.map((key) => ({key})))
                .then((stringArray) => new Map(
                    requiredStrings.map((key, index) => ([key, stringArray[index]]))
                ));
        }
        return this.profilestringmap;
    }
}
