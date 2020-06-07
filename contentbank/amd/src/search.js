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
 * Search methods for finding contents in the content bank.
 *
 * @module     core_contentbank/search
 * @package    core_contentbank
 * @copyright  2020 Sara Arjona <sara@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import $ from 'jquery';
import selectors from 'core_contentbank/selectors';
import {get_string as getString} from 'core/str';
import Pending from 'core/pending';
import {debounce} from 'core/utils';

/**
 * Set up the search.
 *
 * @method init
 */
export const init = () => {
    const pendingPromise = new Pending();

    const root = $(selectors.regions.contentbank);
    registerListenerEvents(root);

    pendingPromise.resolve();
};

/**
 * Register contentbank search related event listeners.
 *
 * @method registerListenerEvents
 * @param {Object} root The root element for the contentbank.
 */
const registerListenerEvents = (root) => {

    const searchInput = root.find(selectors.elements.searchinput)[0];

    root.on('click', selectors.actions.search, function(e) {
        e.preventDefault();
        toggleSearchResultsView(root, searchInput.value);
    });

    root.on('click', selectors.actions.clearSearch, function(e) {
        e.preventDefault();
        searchInput.value = "";
        searchInput.focus();
        toggleSearchResultsView(root, searchInput.value);
    });

    // The search input is also triggered.
    searchInput.addEventListener('input', debounce(() => {
        // Display the search results.
        toggleSearchResultsView(root, searchInput.value);
    }, 300));

};

/**
 * Toggle (display/hide) the search results depending on the value of the search query.
 *
 * @method toggleSearchResultsView
 * @param {HTMLElement} body The root element for the contentbank.
 * @param {String} searchQuery The search query.
 */
const toggleSearchResultsView = async(body, searchQuery) => {
    const clearSearchButton = body.find(selectors.elements.clearsearch)[0];
    const searchIcon = body.find(selectors.elements.searchicon)[0];

    const navbarBreadcrumb = body.find(selectors.elements.cbnavbarbreadcrumb)[0];
    const navbarTotal = body.find(selectors.elements.cbnavbartotalsearch)[0];
    // Update the results.
    const filteredContents = filterContents(body, searchQuery);
    if (searchQuery.length > 0) {
        // As the search query is present, search results should be displayed.

        // Display the "clear" search button in the activity chooser search bar.
        searchIcon.classList.add('d-none');
        clearSearchButton.classList.remove('d-none');

        // Change the cb-navbar to display total items found.
        navbarBreadcrumb.classList.add('d-none');
        navbarTotal.innerHTML = await getString('itemsfound', 'core_contentbank', filteredContents.length);
        navbarTotal.classList.remove('d-none');
    } else {
        // As search query is not present, the search results should be removed.

        // Hide the "clear" search button in the activity chooser search bar.
        clearSearchButton.classList.add('d-none');
        searchIcon.classList.remove('d-none');

        // Display again the breadcrumb in the navbar.
        navbarBreadcrumb.classList.remove('d-none');
        navbarTotal.classList.add('d-none');
    }
};

/**
 * Return the list of contents which have a name that matches the given search term.
 *
 * @method filterContents
 * @param {HTMLElement} body The root element for the contentbank.
 * @param {String} searchTerm The search term to match.
 * @return {Array}
 */
const filterContents = (body, searchTerm) => {
    const contents = Array.from(body.find(selectors.elements.listitem));
    const searchResults = [];
    contents.forEach((content) => {
        const contentName = content.getAttribute('data-name');
        if (searchTerm === '' || contentName.toLowerCase().includes(searchTerm.toLowerCase())) {
            // The content matches the search criteria so it should be displayed and hightlighted.
            searchResults.push(content);
            const contentNameElement = content.querySelector(selectors.regions.cbcontentname);
            contentNameElement.innerHTML = highlight(contentName, searchTerm);
            content.classList.remove('d-none');
        } else {
            content.classList.add('d-none');
        }
    });

    return searchResults;
};

/**
 * Highlight a given string in a text.
 *
 * @method highlight
 * @param  {String} text The whole text.
 * @param  {String} highlightText The piece of text to highlight.
 * @return {String}
 */
const highlight = (text, highlightText) => {
    let result = text;
    if (highlightText !== '') {
        const pos = text.toLowerCase().indexOf(highlightText.toLowerCase());
        if (pos > -1) {
            result = text.substr(0, pos) + '<span class="matchtext">' + text.substr(pos, highlightText.length) + '</span>' +
                text.substr(pos + highlightText.length);
        }
    }

    return result;
};
