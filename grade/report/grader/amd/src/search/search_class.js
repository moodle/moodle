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
import CustomEvents from "core/custom_interaction_events";
import {debounce} from 'core/utils';

/**
 * The class that manages the state of the search.
 *
 * @module    gradereport_grader/search/search_class
 * @copyright 2023 Mathew May <mathew.solutions>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
// Reused variables for the class.
const events = [
    'keydown',
    CustomEvents.events.activate,
    CustomEvents.events.keyboardActivate
];
const UP = -1;
const DOWN = 1;

export default class {
    // Define our standard lookups.
    selectors = {
        component: this.setComponentSelector(),
        trigger: this.setTriggerSelector(),
        input: '[data-action="search"]',
        clearSearch: '[data-action="clearsearch"]',
        dropdown: this.setDropdownSelector(),
        resultitems: '[role="option"]',
        viewall: '#select-all',
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
    searchInput = this.component.querySelector(this.selectors.input);
    searchDropdown = this.component.querySelector(this.selectors.dropdown);
    $searchButton = $(this.selectors.trigger);
    clearSearchButton = this.component.querySelector(this.selectors.clearSearch);
    $component = $(this.component);

    constructor() {
        // If we have a search input, try to get the value otherwise fallback.
        this.setSearchTerms(this.searchInput?.value ?? '');
        // Begin handling the base search component.
        this.registerClickHandlers();
        this.registerKeyHandlers();
        // Conditionally set up the input handler since we don't know exactly how we were called.
        if (this.searchInput !== null) {
            this.registerInputHandlers();
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
    setComponentSelector() {
        throw new Error(`setComponentSelector() must be implemented in ${this.constructor.name}`);
    }

    /**
     * Stub out a required function.
     */
    setDropdownSelector() {
        throw new Error(`setDropdownSelector() must be implemented in ${this.constructor.name}`);
    }

    /**
     * Stub out a required function.
     */
    setTriggerSelector() {
        throw new Error(`setTriggerSelector() must be implemented in ${this.constructor.name}`);
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
            clearSearchButton: this.clearSearchButton
        };
    }

    /**
     * When called, close the dropdown and reset the input field attributes.
     *
     * @param {Boolean} clear Conditionality clear the input box.
     */
    closeSearch(clear = false) {
        this.toggleDropdown();
        // Hide the "clear" search button search bar.
        this.clearSearchButton.classList.add('d-none');
        if (clear) {
            // Clear the entered search query in the search bar and hide the search results container.
            this.setSearchTerms('');
            this.searchInput.value = "";
        }
    }

    /**
     * When called, update the dropdown fields.
     *
     * @param {Boolean} on Flag to toggle hiding or showing values.
     */
    toggleDropdown(on = false) {
        this.$component.dropdown('toggle');
        this.$searchButton.attr('aria-expanded', on);
        if (on) {
            this.searchDropdown.classList.add('show');
            $(this.searchDropdown).show();
        } else {
            this.searchDropdown.classList.remove('show');
            $(this.searchDropdown).hide();
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
        // Prevent the click triggering the dropdown.
        this.$searchButton.on('click', () => {
            this.toggleDropdown();
        });

        // Register click events within the component.
        this.component.addEventListener('click', this.clickHandler.bind(this));

        // Register a small click event onto the document since we need to check if they are clicking off the component.
        document.addEventListener('click', (e) => {
            // Since we are handling dropdowns manually, ensure we can close it when clicking off.
            if (!e.target.closest(this.selectors.component) && this.searchDropdown.classList.contains('show')) {
                this.toggleDropdown();
            }
        });
    }

    /**
     * Register key event listeners.
     */
    registerKeyHandlers() {
        CustomEvents.define(document, events);

        // Register click events.
        events.forEach((event) => {
            this.component.addEventListener(event, this.keyHandler.bind(this));
        });
    }

    /**
     * Register input event listener for the text input area.
     */
    registerInputHandlers() {
        // Register & handle the text input.
        this.searchInput.addEventListener('input', debounce(async() => {
            this.setSearchTerms(this.searchInput.value);
            // We can also require a set amount of input before search.
            if (this.getSearchTerm() === '') {
                this.toggleDropdown();
                // Hide the "clear" search button in the search bar.
                this.clearSearchButton.classList.add('d-none');
            } else {
                // Display the "clear" search button in the search bar.
                this.clearSearchButton.classList.remove('d-none');
                await this.renderAndShow();
            }
        }, 300));
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
    }

    /**
     * Set the current focus either on the preceding or next result item.
     *
     * @param {Number} direction Is the user moving up or down the resultset?
     * @param {KeyboardEvent} e The JS event from the event handler.
     */
    keyUpDown(direction, e) {
        e.preventDefault();
        // Stop Bootstrap from being clever.
        e.stopPropagation();
        // Current focus is on the input box so depending on direction, go to the top or the bottom of the displayed results.
        if (document.activeElement === this.searchInput && this.resultNodes.length > 0) {
            if (direction === UP) {
                this.moveToLastNode();
            } else {
                this.moveToFirstNode();
            }
        }
        const index = this.resultNodes.indexOf(this.currentNode);
        if (this.currentNode) {
            if (direction === UP) {
                if (index === 0) {
                    this.moveToLastNode();
                } else {
                    this.moveToNode(index - 1);
                }
            } else {
                if (index + 1 >= this.resultNodes.length) {
                    this.moveToFirstNode();
                } else {
                    this.moveToNode(index + 1);
                }
            }
        } else {
            if (direction === UP) {
                this.moveToLastNode();
            } else {
                this.moveToFirstNode();
            }
        }
    }

    /**
     * The handler for when a user interacts with the component.
     *
     * @param {MouseEvent} e The triggering event that we are working with.
     */
    async clickHandler(e) {
        this.updateNodes();

        // Prevent normal key presses activating this.
        if (e.target.closest('.dropdown-item') && e.button === 0) {
            window.location = e.target.closest('.dropdown-item').href;
        }
        // The "clear search" button is triggered.
        if (e.target.closest(this.selectors.clearSearch) && e.button === 0) {
            this.closeSearch(true);
            this.searchInput.focus({preventScroll: true});
        }
        // User may have accidentally clicked off the dropdown and wants to reopen it.
        if (e.target.closest(this.selectors.input) && this.getSearchTerm() !== '' && e.button === 0) {
            await this.renderAndShow();
        }
    }

    /**
     * The handler for when a user presses a key within the component.
     *
     * @param {KeyboardEvent} e The triggering event that we are working with.
     */
    keyHandler(e) {
        this.updateNodes();
        // Switch the key presses to handle keyboard nav.
        switch (e.key) {
            case 'ArrowUp':
                this.keyUpDown(UP, e);
                break;
            case 'ArrowDown':
                this.keyUpDown(DOWN, e);
                break;
            case 'Home':
                e.preventDefault();
                this.moveToFirstNode();
                break;
            case 'End':
                e.preventDefault();
                this.moveToLastNode();
                break;
            case 'Tab':
                // If the current focus is on the view all link, then close the widget then set focus on the next tertiary nav item.
                if (e.target.closest(this.selectors.viewall)) {
                    this.closeSearch();
                }
                break;
        }
    }

    /**
     * Set focus on a given node after parsed through the calling functions.
     *
     * @param {HTMLElement} node The node to set focus upon.
     */
    selectNode = (node) => {
        node.focus({preventScroll: true});
        this.searchDropdown.scrollTop = node.offsetTop - (node.clientHeight / 2);
    };

    /**
     * Set the focus on the first node within the array.
     */
    moveToFirstNode = () => {
        if (this.resultNodes.length > 0) {
            this.selectNode(this.resultNodes[0]);
        }
    };

    /**
     * Set the focus to the final node within the array.
     */
    moveToLastNode = () => {
        if (this.resultNodes.length > 0) {
            this.selectNode(this.resultNodes[this.resultNodes.length - 1]);
        }
    };

    /**
     * Set focus on any given specified node within the node array.
     *
     * @param {Number} index Which item within the array to set focus upon.
     */
    moveToNode = (index) => {
        if (this.resultNodes.length > 0) {
            this.selectNode(this.resultNodes[index]);
        }
    };
}
