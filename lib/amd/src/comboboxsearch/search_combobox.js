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

import $ from 'jquery';
import Dropdown from 'theme_boost/bootstrap/dropdown';
import {debounce} from 'core/utils';
import Pending from 'core/pending';
import {get_string as getString} from 'core/str';


/**
 * The class that manages the state of the search within a combobox.
 *
 * @module    core/comboboxsearch/search_combobox
 * @copyright 2023 Mathew May <mathew.solutions>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

export default class {
    // Define our standard lookups.
    selectors = {
        component: this.componentSelector(),
        toggle: '[data-bs-toggle="dropdown"]',
        instance: '[data-region="instance"]',
        input: '[data-action="search"]',
        clearSearch: '[data-action="clearsearch"]',
        dropdown: this.dropdownSelector(),
        resultitems: '[role="option"]',
        viewall: '#select-all',
        combobox: '[role="combobox"]',
    };

    // The results from the called filter function.
    matchedResults = [];

    // What did the user search for?
    searchTerm = '';

    // What the user searched for as a lowercase.
    preppedSearchTerm = null;

    // The DOM nodes after the dropdown render.
    resultNodes = [];

    // Where does the user currently have focus?
    currentNode = null;

    // The current node for the view all link.
    currentViewAll = null;

    dataset = null;

    datasetSize = 0;

    // DOM nodes that persist.
    component = document.querySelector(this.selectors.component);
    instance = this.component.dataset.instance;
    toggle = this.component.querySelector(this.selectors.toggle);
    searchInput = this.component.querySelector(this.selectors.input);
    searchDropdown = this.component.querySelector(this.selectors.dropdown);
    clearSearchButton = this.component.querySelector(this.selectors.clearSearch);
    combobox = this.component.querySelector(this.selectors.combobox);
    $component = $(this.component);

    constructor() {
        // If we have a search input, try to get the value otherwise fallback.
        this.setSearchTerms(this.searchInput?.value ?? '');
        // Begin handling the base search component.
        this.registerClickHandlers();

        // Conditionally set up the input handler since we don't know exactly how we were called.
        // If the combobox is rendered later, then you'll need to call this.registerInputHandlers() manually.
        // An example of this is the collapse columns in the gradebook.
        if (this.searchInput !== null) {
            this.registerInputHandlers();
            this.registerChangeHandlers();
        }

        // If we have a search term, show the clear button.
        if (this.getSearchTerm() !== '') {
            this.clearSearchButton.classList.remove('d-none');
        }
    }

    /**
     * Stub out a required function.
     */
    fetchDataset() {
        throw new Error(`fetchDataset() must be implemented in ${this.constructor.name}`);
    }

    /**
     * Stub out a required function.
     * @param {Array} dataset
     */
    filterDataset(dataset) {
        throw new Error(`filterDataset(${dataset}) must be implemented in ${this.constructor.name}`);
    }

    /**
     * Stub out a required function.
     */
    filterMatchDataset() {
        throw new Error(`filterMatchDataset() must be implemented in ${this.constructor.name}`);
    }

    /**
     * Stub out a required function.
     */
    renderDropdown() {
        throw new Error(`renderDropdown() must be implemented in ${this.constructor.name}`);
    }

    /**
     * Stub out a required function.
     */
    componentSelector() {
        throw new Error(`componentSelector() must be implemented in ${this.constructor.name}`);
    }

    /**
     * Stub out a required function.
     */
    dropdownSelector() {
        throw new Error(`dropdownSelector() must be implemented in ${this.constructor.name}`);
    }

    /**
     * Stub out a required function.
     * @deprecated since Moodle 4.4
     */
    triggerSelector() {
        window.console.warning('triggerSelector() is deprecated. Consider using this.selectors.toggle');
    }

    /**
     * Return the dataset that we will be searching upon.
     *
     * @returns {Promise<null>}
     */
    async getDataset() {
        if (!this.dataset) {
            this.dataset = await this.fetchDataset();
        }
        this.datasetSize = this.dataset.length;
        return this.dataset;
    }

    /**
     * Return the size of the dataset.
     *
     * @returns {number}
     */
    getDatasetSize() {
        return this.datasetSize;
    }

    /**
     * Return the results of the filter upon the dataset.
     *
     * @returns {Array}
     */
    getMatchedResults() {
        return this.matchedResults;
    }

    /**
     * Given a filter has been run across the dataset, store the matched results.
     *
     * @param {Array} result
     */
    setMatchedResults(result) {
        this.matchedResults = result;
    }

    /**
     * Get the value that the user entered.
     *
     * @returns {string}
     */
    getSearchTerm() {
        return this.searchTerm;
    }

    /**
     * Get the transformed search value.
     *
     * @returns {string}
     */
    getPreppedSearchTerm() {
        return this.preppedSearchTerm;
    }

    /**
     * When a user searches for something, set our variable to manage it.
     *
     * @param {string} result
     */
    setSearchTerms(result) {
        this.searchTerm = result;
        this.preppedSearchTerm = result.toLowerCase();
    }

    /**
     * Return an object containing a handfull of dom nodes that we sometimes need the value of.
     *
     * @returns {object}
     */
    getHTMLElements() {
        this.updateNodes();
        return {
            searchDropdown: this.searchDropdown,
            currentViewAll: this.currentViewAll,
            searchInput: this.searchInput,
            clearSearchButton: this.clearSearchButton,
            trigger: this.component.querySelector(this.selectors.trigger),
        };
    }

    /**
     * When called, close the dropdown and reset the input field attributes.
     *
     * @param {Boolean} clear Conditionality clear the input box.
     */
    closeSearch(clear = false) {
        this.toggleDropdown();
        if (clear) {
            // Hide the "clear" search button search bar.
            this.clearSearchButton.classList.add('d-none');
            // Clear the entered search query in the search bar and hide the search results container.
            this.setSearchTerms('');
            this.searchInput.value = "";
        }
    }

    /**
     * Check whether search results are currently visible.
     *
     * @returns {Boolean}
     */
    searchResultsVisible() {
        const {searchDropdown} = this.getHTMLElements();
        // If a Node is not visible, then the offsetParent is null.
        return searchDropdown.offsetParent !== null;
    }

    /**
     * When called, update the dropdown fields.
     *
     * @param {Boolean} on Flag to toggle hiding or showing values.
     */
    toggleDropdown(on = false) {
        if (on) {
            Dropdown.getOrCreateInstance(this.toggle).show();
        } else {
            Dropdown.getOrCreateInstance(this.toggle).hide();
        }
    }

    /**
     * These class members change when a new result set is rendered. So update for fresh data.
     */
    updateNodes() {
        this.resultNodes = [...this.component.querySelectorAll(this.selectors.resultitems)];
        this.currentNode = this.resultNodes.find(r => r.id === document.activeElement.id);
        this.currentViewAll = this.component.querySelector(this.selectors.viewall);
        this.clearSearchButton = this.component.querySelector(this.selectors.clearSearch);
        this.searchInput = this.component.querySelector(this.selectors.input);
        this.searchDropdown = this.component.querySelector(this.selectors.dropdown);
    }

    /**
     * Register clickable event listeners.
     */
    registerClickHandlers() {
        // Register click events within the component.
        this.component.addEventListener('click', this.clickHandler.bind(this));
    }

    /**
     * Register change event listeners.
     */
    registerChangeHandlers() {
        const valueElement = this.component.querySelector(`#${this.combobox.dataset.inputElement}`);
        valueElement.addEventListener('change', this.changeHandler.bind(this));
    }

    /**
     * Register input event listener for the text input area.
     */
    registerInputHandlers() {
        // Register & handle the text input.
        this.searchInput.addEventListener('input', debounce(async() => {
            if (this.getSearchTerm() === this.searchInput.value && this.searchResultsVisible()) {
                window.console.warn(`Search term matches input value - skipping`);
                // The debounce canhappen multiple times quickly. GRrargh
                return;
            }
            this.setSearchTerms(this.searchInput.value);

            const pendingPromise = new Pending();
            if (this.getSearchTerm() === '') {
                this.toggleDropdown();
                this.clearSearchButton.classList.add('d-none');
                await this.filterrenderpipe();
            } else {
                this.clearSearchButton.classList.remove('d-none');
                await this.renderAndShow();
            }
            pendingPromise.resolve();
        }, 300, {pending: true}));
    }

    /**
     * Update any changeable nodes, filter and then render the result.
     *
     * @returns {Promise<void>}
     */
    async filterrenderpipe() {
        this.updateNodes();
        this.setMatchedResults(await this.filterDataset(await this.getDataset()));
        this.filterMatchDataset();
        await this.renderDropdown();
        await this.updateLiveRegion();
    }

    /**
     * A combo method to take the matching fields and render out the results.
     *
     * @returns {Promise<void>}
     */
    async renderAndShow() {
        // User has given something for us to filter against.
        this.setMatchedResults(await this.filterDataset(await this.getDataset()));
        await this.filterMatchDataset();
        // Replace the dropdown node contents and show the results.
        await this.renderDropdown();
        // Set the dropdown to open.
        this.toggleDropdown(true);
        await this.updateLiveRegion();
    }

    /**
     * The handler for when a user interacts with the component.
     *
     * @param {MouseEvent} e The triggering event that we are working with.
     */
    async clickHandler(e) {
        this.updateNodes();
        // The "clear search" button is triggered.
        if (e.target.closest(this.selectors.clearSearch)) {
            this.closeSearch(true);
            this.searchInput.focus();
            // Remove aria-activedescendant when the available options change.
            this.searchInput.removeAttribute('aria-activedescendant');
        }
        // User may have accidentally clicked off the dropdown and wants to reopen it.
        if (
            this.getSearchTerm() !== ''
            && !this.getHTMLElements().searchDropdown.classList.contains('show')
            && e.target.closest(this.selectors.input)
        ) {
            await this.renderAndShow();
        }
    }

    /**
     * The handler for when a user changes the value of the component (selects an option from the dropdown).
     *
     * @param {Event} e The change event.
     */
    // eslint-disable-next-line no-unused-vars
    changeHandler(e) {
        // Components may override this method to do something.
    }

    /**
     * Updates the screen reader live region with the result count.
     */
    async updateLiveRegion() {
        if (!this.searchDropdown?.id) {
            return;
        }

        const idParts = this.searchDropdown.id.split('-');

        if (idParts.length < 3) {
            return;
        }
        const [, instanceId, id] = idParts; // E.g. dialog-12-34 only want the last two parts.
        const liveRegion = document.getElementById(`combobox-status-${instanceId}-${id}`);

        if (!liveRegion) {
            return;
        }

        const resultCount = this.getMatchedResults().length;
        let message;
        if (resultCount === 0) {
            message = await getString('noitemsfound', 'core');
        } else if (resultCount === 1) {
            message = await getString('oneitemfound', 'core');
        } else {
            message = await getString('multipleitemsfound', 'core', resultCount);
        }

        liveRegion.textContent = message;

        // Reset previous timeout if it exists.
        if (this.liveRegionTimeout) {
            clearTimeout(this.liveRegionTimeout);
        }

        // Clear the feedback message after 4 seconds. This is similar to the default timeout of toast messages
        // before disappearing from view. It is important to clear the message to prevent screen reader users from navigating
        // into this region and avoiding confusion.
        this.liveRegionTimeout = setTimeout(() => {
            liveRegion.textContent = '';
            this.liveRegionTimeout = null;
        }, 4000);
    }

}
