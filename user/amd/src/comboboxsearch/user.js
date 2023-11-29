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
 * Allow the user to search for learners.
 *
 * @module    core_user/comboboxsearch/user
 * @copyright 2023 Mathew May <mathew.solutions>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
import search_combobox from 'core/comboboxsearch/search_combobox';
import {getStrings} from 'core/str';
import {renderForPromise, replaceNodeContents} from 'core/templates';
import $ from 'jquery';
import Notification from 'core/notification';

export default class UserSearch extends search_combobox {

    courseID;
    groupID;
    bannedFilterFields = ['profileimageurlsmall', 'profileimageurl', 'id', 'link', 'matchingField', 'matchingFieldName'];

    // A map of user profile field names that is human-readable.
    profilestringmap = null;

    constructor() {
        super();
        // Register a small click event onto the document since we need to check if they are clicking off the component.
        document.addEventListener('click', (e) => {
            // Since we are handling dropdowns manually, ensure we can close it when clicking off.
            if (!e.target.closest(this.selectors.component) && this.searchDropdown.classList.contains('show')) {
                this.toggleDropdown();
            }
        });

        // Define our standard lookups.
        this.selectors = {...this.selectors,
            courseid: '[data-region="courseid"]',
            groupid: '[data-region="groupid"]',
            resetPageButton: '[data-action="resetpage"]',
        };

        const component = document.querySelector(this.componentSelector());
        this.courseID = component.querySelector(this.selectors.courseid).dataset.courseid;
        this.groupID = document.querySelector(this.selectors.groupid)?.dataset?.groupid;
    }

    static init() {
        return new UserSearch();
    }

    /**
     * The overall div that contains the searching widget.
     *
     * @returns {string}
     */
    componentSelector() {
        return '.user-search';
    }

    /**
     * The dropdown div that contains the searching widget result space.
     *
     * @returns {string}
     */
    dropdownSelector() {
        return '.usersearchdropdown';
    }

    /**
     * The triggering div that contains the searching widget.
     *
     * @returns {string}
     */
    triggerSelector() {
        return '.usersearchwidget';
    }

    /**
     * Build the content then replace the node.
     */
    async renderDropdown() {
        const {html, js} = await renderForPromise('core_user/comboboxsearch/resultset', {
            users: this.getMatchedResults().slice(0, 5),
            hasresults: this.getMatchedResults().length > 0,
            matches: this.getMatchedResults().length,
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
        throw new Error(`fetchDataset() must be implemented in ${this.constructor.name}`);
    }

    /**
     * Dictate to the search component how and what we want to match upon.
     *
     * @param {Array} filterableData
     * @returns {Array} The users that match the given criteria.
     */
    async filterDataset(filterableData) {
        return filterableData.filter((user) => Object.keys(user).some((key) => {
            if (user[key] === "" || user[key] === null || this.bannedFilterFields.includes(key)) {
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
                    // Sometimes users have null values in their profile fields.
                    if (value === null) {
                        continue;
                    }

                    const valueString = value.toString().toLowerCase();
                    const preppedSearchTerm = this.getPreppedSearchTerm();
                    const searchTerm = this.getSearchTerm();

                    if (valueString.includes(preppedSearchTerm) && !this.bannedFilterFields.includes(key)) {
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
        super.clickHandler(e).catch(Notification.exception);
        if (e.target.closest(this.selectors.component)) {
            // Forcibly prevent BS events so that we can control the open and close.
            // Really needed because by default input elements cant trigger a dropdown.
            e.stopImmediatePropagation();
        }
        if (e.target === this.getHTMLElements().currentViewAll && e.button === 0) {
            window.location = this.selectAllResultsLink();
        }
        if (e.target.closest(this.selectors.resetPageButton)) {
            window.location = e.target.closest(this.selectors.resetPageButton).href;
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
                    if (e.key === 'Enter' && this.selectAllResultsLink() !== null) {
                        window.location = this.selectAllResultsLink();
                    }
                }
                if (document.activeElement === this.getHTMLElements().clearSearchButton) {
                    this.closeSearch(true);
                    break;
                }
                if (e.target.closest(this.selectors.resetPageButton)) {
                    window.location = e.target.closest(this.selectors.resetPageButton).href;
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
     * When called, hide or show the users dropdown.
     *
     * @param {Boolean} on Flag to toggle hiding or showing values.
     */
    toggleDropdown(on = false) {
        if (on) {
            this.searchDropdown.classList.add('show');
            $(this.searchDropdown).show();
            this.component.setAttribute('aria-expanded', 'true');
        } else {
            this.searchDropdown.classList.remove('show');
            $(this.searchDropdown).hide();
            this.component.setAttribute('aria-expanded', 'false');
        }
    }

    /**
     * Build up the view all link.
     */
    selectAllResultsLink() {
        throw new Error(`selectAllResultsLink() must be implemented in ${this.constructor.name}`);
    }

    /**
     * Build up the view all link that is dedicated to a particular result.
     *
     * @param {Number} userID The ID of the user selected.
     */
    selectOneLink(userID) {
        throw new Error(`selectOneLink(${userID}) must be implemented in ${this.constructor.name}`);
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
