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
 * Content bank UI actions.
 *
 * @module     core_contentbank/sort
 * @package    core_contentbank
 * @copyright  2020 Bas Brands <bas@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import selectors from './selectors';
import {get_string as getString} from 'core/str';
import Prefetch from 'core/prefetch';
import Ajax from 'core/ajax';
import Notification from 'core/notification';

/**
 * Set up the contentbank views.
 *
 * @method init
 */
export const init = () => {
    const contentBank = document.querySelector(selectors.regions.contentbank);
    Prefetch.prefetchStrings('contentbank', ['sortbyx', 'sortbyxreverse', 'contentname',
        'lastmodified', 'size', 'type']);
    registerListenerEvents(contentBank);
};

/**
 * Register contentbank related event listeners.
 *
 * @method registerListenerEvents
 * @param {HTMLElement} contentBank The DOM node of the content bank
 */
const registerListenerEvents = (contentBank) => {

    // The search.
    const fileArea = document.querySelector(selectors.regions.filearea);
    const shownItems = fileArea.querySelectorAll(selectors.elements.listitem);

    // The view buttons.
    const viewGrid = contentBank.querySelector(selectors.actions.viewgrid);
    const viewList = contentBank.querySelector(selectors.actions.viewlist);

    viewGrid.addEventListener('click', () => {
        contentBank.classList.remove('view-list');
        contentBank.classList.add('view-grid');
        viewGrid.classList.add('active');
        viewList.classList.remove('active');
        setViewListPreference(false);
    });

    viewList.addEventListener('click', () => {
        contentBank.classList.remove('view-grid');
        contentBank.classList.add('view-list');
        viewList.classList.add('active');
        viewGrid.classList.remove('active');
        setViewListPreference(true);
    });

    // Sort by file name alphabetical
    const sortByName = contentBank.querySelector(selectors.actions.sortname);
    sortByName.addEventListener('click', () => {
        const ascending = updateSortButtons(contentBank, sortByName);
        updateSortOrder(fileArea, shownItems, 'data-file', ascending);
    });

    // Sort by date.
    const sortByDate = contentBank.querySelector(selectors.actions.sortdate);
    sortByDate.addEventListener('click', () => {
        const ascending = updateSortButtons(contentBank, sortByDate);
        updateSortOrder(fileArea, shownItems, 'data-timemodified', ascending);
    });

    // Sort by size.
    const sortBySize = contentBank.querySelector(selectors.actions.sortsize);
    sortBySize.addEventListener('click', () => {
        const ascending = updateSortButtons(contentBank, sortBySize);
        updateSortOrder(fileArea, shownItems, 'data-bytes', ascending);
    });

    // Sort by type
    const sortByType = contentBank.querySelector(selectors.actions.sorttype);
    sortByType.addEventListener('click', () => {
        const ascending = updateSortButtons(contentBank, sortByType);
        updateSortOrder(fileArea, shownItems, 'data-type', ascending);
    });
};


/**
 * Set the contentbank user preference in list view
 *
 * @param  {Bool} viewList view ContentBank as list.
 * @return {Promise} Repository promise.
 */
const setViewListPreference = function(viewList) {

    // If the given status is not hidden, the preference has to be deleted with a null value.
    if (viewList === false) {
        viewList = null;
    }

    const request = {
        methodname: 'core_user_update_user_preferences',
        args: {
            preferences: [
                {
                    type: 'core_contentbank_view_list',
                    value: viewList
                }
            ]
        }
    };

    return Ajax.call([request])[0].catch(Notification.exception);
};

/**
 * Update the sort button view.
 *
 * @method updateSortButtons
 * @param {HTMLElement} contentBank The DOM node of the contentbank button
 * @param {HTMLElement} sortButton The DOM node of the sort button
 * @return {Bool} sort ascending
 */
const updateSortButtons = (contentBank, sortButton) => {
    const sortButtons = contentBank.querySelectorAll(selectors.elements.sortbutton);

    sortButtons.forEach((button) => {
        if (button !== sortButton) {
            button.classList.remove('dir-asc');
            button.classList.remove('dir-desc');
            button.classList.add('dir-none');

            updateButtonTitle(button, false);
        }
    });

    let ascending = true;

    if (sortButton.classList.contains('dir-none')) {
        sortButton.classList.remove('dir-none');
        sortButton.classList.add('dir-asc');
    } else if (sortButton.classList.contains('dir-asc')) {
        sortButton.classList.remove('dir-asc');
        sortButton.classList.add('dir-desc');
        ascending = false;
    } else if (sortButton.classList.contains('dir-desc')) {
        sortButton.classList.remove('dir-desc');
        sortButton.classList.add('dir-asc');
    }

    updateButtonTitle(sortButton, ascending);

    return ascending;
};

/**
 * Update the button title.
 *
 * @method updateButtonTitle
 * @param {HTMLElement} button Button to update
 * @param {Bool} ascending Sort direction
 * @return {Promise} string promise
 */
const updateButtonTitle = (button, ascending) => {

    const sortString = (ascending ? 'sortbyxreverse' : 'sortbyx');

    return getString(button.dataset.string, 'contentbank')
    .then(columnName => {
        return getString(sortString, 'core', columnName);
    })
    .then(sortByString => {
        button.setAttribute('title', sortByString);
        return sortByString;
    })
    .catch();
};

/**
 * Update the sort order of the itemlist and update the DOM
 *
 * @method updateSortOrder
 * @param {HTMLElement} fileArea the Dom container for the itemlist
 * @param {Array} itemList Nodelist of Dom elements
 * @param {String} attribute, the attribut to sort on
 * @param {Bool} ascending, Sort Ascending
 */
const updateSortOrder = (fileArea, itemList, attribute, ascending) => {
    const sortList = [].slice.call(itemList).sort(function(a, b) {

        let aa = a.getAttribute(attribute);
        let bb = b.getAttribute(attribute);
        if (!isNaN(aa)) {
           aa = parseInt(aa);
           bb = parseInt(bb);
        }

        if (ascending) {
            return aa > bb ? 1 : -1;
        } else {
            return aa < bb ? 1 : -1;
        }
    });
    sortList.forEach(listItem => fileArea.appendChild(listItem));
};
