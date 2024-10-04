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
 * Allow the user to search for grades within the grade area.
 *
 * @module    core_grades/comboboxsearch/grade
 * @copyright 2023 Mathew May <mathew.solutions>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
import search_combobox from 'core/comboboxsearch/search_combobox';
import * as Repository from 'core_grades/searchwidget/repository';
import {renderForPromise, replaceNodeContents} from 'core/templates';
import {debounce} from 'core/utils';

export default class GradeItemSearch extends search_combobox {

    courseID;

    constructor() {
        super();

        // Define our standard lookups.
        this.selectors = {
            ...this.selectors,
            courseid: '[data-region="courseid"]',
            placeholder: '.gradesearchdropdown [data-region="searchplaceholder"]',
        };
        const component = document.querySelector(this.componentSelector());
        this.courseID = component.querySelector(this.selectors.courseid).dataset.courseid;
        this.instance = this.component.querySelector(this.selectors.instance).dataset.instance;

        const searchValueElement = this.component.querySelector(`#${this.searchInput.dataset.inputElement}`);
        searchValueElement.addEventListener('change', () => {
            this.toggleDropdown(); // Otherwise the dropdown stays open when user choose an option using keyboard.

            const valueElement = this.component.querySelector(`#${this.combobox.dataset.inputElement}`);
            if (valueElement.value !== searchValueElement.value) {
                valueElement.value = searchValueElement.value;
                valueElement.dispatchEvent(new Event('change', {bubbles: true}));
            }

            searchValueElement.value = '';
        });

        this.component.addEventListener('hide.bs.dropdown', () => {
            this.searchInput.removeAttribute('aria-activedescendant');

            const listbox = document.querySelector(`#${this.searchInput.getAttribute('aria-controls')}[role="listbox"]`);
            listbox.querySelectorAll('.active[role="option"]').forEach(option => {
                option.classList.remove('active');
            });
            listbox.scrollTop = 0;

            // Use setTimeout to make sure the following code is executed after the click event is handled.
            setTimeout(() => {
                if (this.searchInput.value !== '') {
                    this.searchInput.value = '';
                    this.searchInput.dispatchEvent(new Event('input', {bubbles: true}));
                }
            });
        });

        this.renderDefault();
    }

    static init() {
        return new GradeItemSearch();
    }

    /**
     * The overall div that contains the searching widget.
     *
     * @returns {string}
     */
    componentSelector() {
        return '.grade-search';
    }

    /**
     * The dropdown div that contains the searching widget result space.
     *
     * @returns {string}
     */
    dropdownSelector() {
        return '.gradesearchdropdown';
    }

    /**
     * Build the content then replace the node.
     */
    async renderDropdown() {
        const {html, js} = await renderForPromise('core/local/comboboxsearch/resultset', {
            instance: this.instance,
            results: this.getMatchedResults(),
            hasresults: this.getMatchedResults().length > 0,
            searchterm: this.getSearchTerm(),
        });
        replaceNodeContents(this.selectors.placeholder, html, js);
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

        this.updateNodes();
        this.registerInputEvents();
    }

    /**
     * Get the data we will be searching against in this component.
     *
     * @returns {Promise<*>}
     */
    async fetchDataset() {
        return await Repository.gradeitemFetch(this.courseID).then((r) => r.gradeitems);
    }

    /**
     * Dictate to the search component how and what we want to match upon.
     *
     * @param {Array} filterableData
     * @returns {Array} The users that match the given criteria.
     */
    async filterDataset(filterableData) {
        // Sometimes we just want to show everything.
        if (this.getPreppedSearchTerm() === '') {
            return filterableData;
        }
        return filterableData.filter((grade) => Object.keys(grade).some((key) => {
            if (grade[key] === "") {
                return false;
            }
            return grade[key].toString().toLowerCase().includes(this.getPreppedSearchTerm());
        }));
    }

    /**
     * Given we have a subset of the dataset, set the field that we matched upon to inform the end user.
     */
    filterMatchDataset() {
        this.setMatchedResults(
            this.getMatchedResults().map((grade) => {
                return {
                    id: grade.id,
                    name: grade.name,
                };
            })
        );
    }

    /**
     * Handle any keyboard inputs.
     */
    registerInputEvents() {
        // Register & handle the text input.
        this.searchInput.addEventListener('input', debounce(async() => {
            this.setSearchTerms(this.searchInput.value);
            // We can also require a set amount of input before search.
            if (this.searchInput.value === '') {
                // Hide the "clear" search button in the search bar.
                this.clearSearchButton.classList.add('d-none');
            } else {
                // Display the "clear" search button in the search bar.
                this.clearSearchButton.classList.remove('d-none');
            }
            // User has given something for us to filter against.
            await this.filterrenderpipe();
        }, 300));
    }

    /**
     * The handler for when a user interacts with the component.
     *
     * @param {MouseEvent} e The triggering event that we are working with.
     */
    async clickHandler(e) {
        if (e.target.closest(this.selectors.clearSearch)) {
            e.stopPropagation();
            // Clear the entered search query in the search bar.
            this.searchInput.value = '';
            this.setSearchTerms(this.searchInput.value);
            this.searchInput.focus();
            this.clearSearchButton.classList.add('d-none');
            // Display results.
            await this.filterrenderpipe();
        }
    }

    /**
     * The handler for when a user changes the value of the component (selects an option from the dropdown).
     *
     * @param {Event} e The change event.
     */
    changeHandler(e) {
        window.location = this.selectOneLink(e.target.value);
    }

    /**
     * Override the input event listener for the text input area.
     */
    registerInputHandlers() {
        // Register & handle the text input.
        this.searchInput.addEventListener('input', debounce(() => {
            this.setSearchTerms(this.searchInput.value);
            // We can also require a set amount of input before search.
            if (this.getSearchTerm() === '') {
                // Hide the "clear" search button in the search bar.
                this.clearSearchButton.classList.add('d-none');
            } else {
                // Display the "clear" search button in the search bar.
                this.clearSearchButton.classList.remove('d-none');
            }
        }, 300));
    }

    /**
     * Build up the view all link that is dedicated to a particular result.
     * We will call this function when a user interacts with the combobox to redirect them to show their results in the page.
     *
     * @param {Number} gradeID The ID of the grade item selected.
     */
    selectOneLink(gradeID) {
        throw new Error(`selectOneLink(${gradeID}) must be implemented in ${this.constructor.name}`);
    }
}
