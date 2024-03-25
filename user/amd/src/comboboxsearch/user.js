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

export default class UserSearch extends search_combobox {

    courseID;
    groupID;

    // A map of user profile field names that is human-readable.
    profilestringmap = null;

    constructor() {
        super();
        // Register a couple of events onto the document since we need to check if they are moving off the component.
        ['click', 'focus'].forEach(eventType => {
            // Since we are handling dropdowns manually, ensure we can close it when moving off.
            document.addEventListener(eventType, e => {
                if (this.searchDropdown.classList.contains('show') && !this.combobox.contains(e.target)) {
                    this.toggleDropdown();
                }
            }, true);
        });

        // Register keyboard events.
        this.component.addEventListener('keydown', this.keyHandler.bind(this));

        // Define our standard lookups.
        this.selectors = {...this.selectors,
            courseid: '[data-region="courseid"]',
            groupid: '[data-region="groupid"]',
            resetPageButton: '[data-action="resetpage"]',
        };

        this.courseID = this.component.querySelector(this.selectors.courseid).dataset.courseid;
        this.groupID = document.querySelector(this.selectors.groupid)?.dataset?.groupid;
        this.instance = this.component.querySelector(this.selectors.instance).dataset.instance;

        // We need to render some content by default for ARIA purposes.
        this.renderDefault();
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
     * Build the content then replace the node.
     */
    async renderDropdown() {
        const {html, js} = await renderForPromise('core_user/comboboxsearch/resultset', {
            users: this.getMatchedResults().slice(0, 5),
            hasresults: this.getMatchedResults().length > 0,
            instance: this.instance,
            matches: this.getMatchedResults().length,
            searchterm: this.getSearchTerm(),
            selectall: this.selectAllResultsLink(),
        });
        replaceNodeContents(this.getHTMLElements().searchDropdown, html, js);
        // Remove aria-activedescendant when the available options change.
        this.searchInput.removeAttribute('aria-activedescendant');
    }

    /**
     * Build the content then replace the node by default we want our form to exist.
     */
    async renderDefault() {
        this.setMatchedResults(await this.filterDataset(await this.getDataset()));
        this.filterMatchDataset();

        await this.renderDropdown();
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
        if (this.getPreppedSearchTerm()) {
            const stringMap = await this.getStringMap();
            return filterableData.filter((user) => Object.keys(user).some((key) => {
                if (user[key] === "" || user[key] === null || !stringMap.get(key)) {
                    return false;
                }
                return user[key].toString().toLowerCase().includes(this.getPreppedSearchTerm());
            }));
        } else {
            return [];
        }
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

                    // Ensure we match only on expected keys.
                    const matchingFieldName = stringMap.get(key);
                    if (matchingFieldName && valueString.includes(preppedSearchTerm)) {
                        user.matchingFieldName = matchingFieldName;

                        // Safely prepare our matching results.
                        const escapedValueString = valueString.replace(/</g, '&lt;');
                        const escapedMatchingField = escapedValueString.replace(
                            preppedSearchTerm.replace(/</g, '&lt;'),
                            `<span class="font-weight-bold">${searchTerm.replace(/</g, '&lt;')}</span>`
                        );

                        user.matchingField = `${escapedMatchingField} (${user.email})`;
                        break;
                    }
                }
                return user;
            })
        );
    }

    /**
     * The handler for when a user changes the value of the component (selects an option from the dropdown).
     *
     * @param {Event} e The change event.
     */
    changeHandler(e) {
        this.toggleDropdown(); // Otherwise the dropdown stays open when user choose an option using keyboard.

        if (e.target.value === '0') {
            window.location = this.selectAllResultsLink();
        } else {
            window.location = this.selectOneLink(e.target.value);
        }
    }

    /**
     * The handler for when a user presses a key within the component.
     *
     * @param {KeyboardEvent} e The triggering event that we are working with.
     */
    keyHandler(e) {
        // Switch the key presses to handle keyboard nav.
        switch (e.key) {
            case 'ArrowUp':
            case 'ArrowDown':
                if (
                    this.getSearchTerm() !== ''
                    && !this.searchDropdown.classList.contains('show')
                    && e.target.contains(this.combobox)
                ) {
                    this.renderAndShow();
                }
                break;
            case 'Enter':
            case ' ':
                if (e.target.closest(this.selectors.resetPageButton)) {
                    e.stopPropagation();
                    window.location = e.target.closest(this.selectors.resetPageButton).href;
                    break;
                }
                break;
            case 'Escape':
                this.toggleDropdown();
                this.searchInput.focus({preventScroll: true});
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
            this.getHTMLElements().searchInput.setAttribute('aria-expanded', 'true');
            this.searchInput.focus({preventScroll: true});
        } else {
            this.searchDropdown.classList.remove('show');
            $(this.searchDropdown).hide();

            // As we are manually handling the dropdown, we need to do some housekeeping manually.
            this.getHTMLElements().searchInput.setAttribute('aria-expanded', 'false');
            this.searchInput.removeAttribute('aria-activedescendant');
            this.searchDropdown.querySelectorAll('.active[role="option"]').forEach(option => {
                option.classList.remove('active');
            });
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
     * We will call this function when a user interacts with the combobox to redirect them to show their results in the page.
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
                'fullname',
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
