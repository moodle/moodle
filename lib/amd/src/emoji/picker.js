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
 * Emoji picker.
 *
 * @module core/emoji/picker
 * @copyright  2019 Ryan Wyllie <ryan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
import LocalStorage from 'core/localstorage';
import * as EmojiData from 'core/emoji/data';
import {throttle, debounce} from 'core/utils';
import {get_string as getString} from 'core/str';
import {render as renderTemplate} from 'core/templates';

const VISIBLE_ROW_COUNT = 10;
const ROW_RENDER_BUFFER_COUNT = 5;
const RECENT_EMOJIS_STORAGE_KEY = 'moodle-recent-emojis';
const ROW_HEIGHT_RAW = 40;
const EMOJIS_PER_ROW = 7;
const MAX_RECENT_COUNT = EMOJIS_PER_ROW * 3;
const ROW_TYPE = {
    EMOJI: 0,
    HEADER: 1
};
const SELECTORS = {
    CATEGORY_SELECTOR: '[data-action="show-category"]',
    EMOJIS_CONTAINER: '[data-region="emojis-container"]',
    EMOJI_PREVIEW: '[data-region="emoji-preview"]',
    EMOJI_SHORT_NAME: '[data-region="emoji-short-name"]',
    ROW_CONTAINER: '[data-region="row-container"]',
    SEARCH_INPUT: '[data-region="search-input"]',
    SEARCH_RESULTS_CONTAINER: '[data-region="search-results-container"]'
};

/**
 * Create the row data for a category.
 *
 * @method
 * @param {String} categoryName The category name
 * @param {String} categoryDisplayName The category display name
 * @param {Array} emojis The emoji data
 * @param {Number} totalRowCount The total number of rows generated so far
 * @return {Array}
 */
const createRowDataForCategory = (categoryName, categoryDisplayName, emojis, totalRowCount) => {
    const rowData = [];
    rowData.push({
        index: totalRowCount + rowData.length,
        type: ROW_TYPE.HEADER,
        data: {
            name: categoryName,
            displayName: categoryDisplayName
        }
    });

    for (let i = 0; i < emojis.length; i += EMOJIS_PER_ROW) {
        const rowEmojis = emojis.slice(i, i + EMOJIS_PER_ROW);
        rowData.push({
            index: totalRowCount + rowData.length,
            type: ROW_TYPE.EMOJI,
            data: rowEmojis
        });
    }

    return rowData;
};

/**
 * Add each row's index to it's value in the row data.
 *
 * @method
 * @param {Array} rowData List of emoji row data
 * @return {Array}
 */
const addIndexesToRowData = (rowData) => {
    return rowData.map((data, index) => {
        return {...data, index};
    });
};

/**
 * Calculate the scroll position for the beginning of each category from
 * the row data.
 *
 * @method
 * @param {Array} rowData List of emoji row data
 * @return {Object}
 */
const getCategoryScrollPositionsFromRowData = (rowData) => {
    return rowData.reduce((carry, row, index) => {
        if (row.type === ROW_TYPE.HEADER) {
            carry[row.data.name] = index * ROW_HEIGHT_RAW;
        }
        return carry;
    }, {});
};

/**
 * Create a header row element for the category name.
 *
 * @method
 * @param {Number} rowIndex Index of the row in the row data
 * @param {String} name The category display name
 * @return {Element}
 */
const createHeaderRow = async(rowIndex, name) => {
    const context = {
        index: rowIndex,
        text: name
    };
    const html = await renderTemplate('core/emoji/header_row', context);
    const temp = document.createElement('div');
    temp.innerHTML = html;
    return temp.firstChild;
};

/**
 * Create an emoji row element.
 *
 * @method
 * @param {Number} rowIndex Index of the row in the row data
 * @param {Array} emojis The list of emoji data for the row
 * @return {Element}
 */
const createEmojiRow = async(rowIndex, emojis) => {
    const context = {
        index: rowIndex,
        emojis: emojis.map(emojiData => {
            const charCodes = emojiData.unified.split('-').map(code => `0x${code}`);
            const emojiText = String.fromCodePoint.apply(null, charCodes);
            return {
                shortnames: `:${emojiData.shortnames.join(': :')}:`,
                unified: emojiData.unified,
                text: emojiText,
                spacer: false
            };
        }),
        spacers: Array(EMOJIS_PER_ROW - emojis.length).fill(true)
    };
    const html = await renderTemplate('core/emoji/emoji_row', context);
    const temp = document.createElement('div');
    temp.innerHTML = html;
    return temp.firstChild;
};

/**
 * Check if the element is an emoji element.
 *
 * @method
 * @param {Element} element Element to check
 * @return {Bool}
 */
const isEmojiElement = element => element.getAttribute('data-short-names') !== null;

/**
 * Search from an element and up through it's ancestors to fine the category
 * selector element and return it.
 *
 * @method
 * @param {Element} element Element to begin searching from
 * @return {Element|null}
 */
const findCategorySelectorFromElement = element => {
    if (!element) {
        return null;
    }

    if (element.getAttribute('data-action') === 'show-category') {
        return element;
    } else {
        return findCategorySelectorFromElement(element.parentElement);
    }
};

const getCategorySelectorByCategoryName = (root, name) => {
    return root.querySelector(`[data-category="${name}"]`);
};

/**
 * Sets the given category selector element as active.
 *
 * @method
 * @param {Element} root The root picker element
 * @param {Element} element The category selector element to make active
 */
const setCategorySelectorActive = (root, element) => {
    const allCategorySelectors = root.querySelectorAll(SELECTORS.CATEGORY_SELECTOR);

    for (let i = 0; i < allCategorySelectors.length; i++) {
        const selector = allCategorySelectors[i];
        selector.classList.remove('selected');
    }

    element.classList.add('selected');
};

/**
 * Get the category selector element and the scroll positions for the previous and
 * next categories for the given scroll position.
 *
 * @method
 * @param {Element} root The picker root element
 * @param {Number} position The position to get the category for
 * @param {Object} categoryScrollPositions Set of scroll positions for all categories
 * @return {Array}
 */
const getCategoryByScrollPosition = (root, position, categoryScrollPositions) => {
    let positions = [];

    if (position < 0) {
        position = 0;
    }

    // Get all of the category positions.
    for (const categoryName in categoryScrollPositions) {
        const categoryPosition = categoryScrollPositions[categoryName];
        positions.push([categoryPosition, categoryName]);
    }

    // Sort the positions in ascending order.
    positions.sort(([a], [b]) => {
        if (a < b) {
            return -1;
        } else if (a > b) {
            return 1;
        } else {
            return 0;
        }
    });

    // Get the current category name as well as the previous and next category
    // positions from the sorted list of positions.
    const {categoryName, previousPosition, nextPosition} = positions.reduce(
        (carry, candidate) => {
            const [categoryPosition, categoryName] = candidate;

            if (categoryPosition <= position) {
                carry.categoryName = categoryName;
                carry.previousPosition = carry.currentPosition;
                carry.currentPosition = position;
            } else if (carry.nextPosition === null) {
                carry.nextPosition = categoryPosition;
            }

            return carry;
        },
        {
            categoryName: null,
            currentPosition: null,
            previousPosition: null,
            nextPosition: null
        }
    );

    return [getCategorySelectorByCategoryName(root, categoryName), previousPosition, nextPosition];
};

/**
 * Get the list of recent emojis data from local storage.
 *
 * @method
 * @return {Array}
 */
const getRecentEmojis = () => {
    const storedData = LocalStorage.get(RECENT_EMOJIS_STORAGE_KEY);
    return storedData ? JSON.parse(storedData) : [];
};

/**
 * Save the list of recent emojis in local storage.
 *
 * @method
 * @param {Array} recentEmojis List of emoji data to save
 */
const saveRecentEmoji = (recentEmojis) => {
    LocalStorage.set(RECENT_EMOJIS_STORAGE_KEY, JSON.stringify(recentEmojis));
};

/**
 * Add an emoji data to the set of recent emojis. This function will update the row
 * data to ensure that the recent emoji rows are correct and all of the rows are
 * re-indexed.
 *
 * The new set of recent emojis are saved in local storage and the full set of updated
 * row data and new emoji row count are returned.
 *
 * @method
 * @param {Array} rowData The emoji rows data
 * @param {Number} recentEmojiRowCount Count of the recent emoji rows
 * @param {Object} newEmoji The emoji data for the emoji to add to the recent emoji list
 * @return {Array}
 */
const addRecentEmoji = (rowData, recentEmojiRowCount, newEmoji) => {
    // The first set of rows is always the recent emojis.
    const categoryName = rowData[0].data.name;
    const categoryDisplayName = rowData[0].data.displayName;
    const recentEmojis = getRecentEmojis();
    // Add the new emoji to the start of the list of recent emojis.
    let newRecentEmojis = [newEmoji, ...recentEmojis.filter(emoji => emoji.unified != newEmoji.unified)];
    // Limit the number of recent emojis.
    newRecentEmojis = newRecentEmojis.slice(0, MAX_RECENT_COUNT);
    const newRecentEmojiRowData = createRowDataForCategory(categoryName, categoryDisplayName, newRecentEmojis);

    // Save the new list in local storage.
    saveRecentEmoji(newRecentEmojis);

    return [
        // Return the new rowData and re-index it to make sure it's all correct.
        addIndexesToRowData(newRecentEmojiRowData.concat(rowData.slice(recentEmojiRowCount))),
        newRecentEmojiRowData.length
    ];
};

/**
 * Calculate which rows should be visible based on the given scroll position. Adds a
 * buffer to amount to either side of the total number of requested rows so that
 * scrolling the emoji rows container is smooth.
 *
 * @method
 * @param {Number} scrollPosition Scroll position within the emoji container
 * @param {Number} visibleRowCount How many rows should be visible
 * @param {Array} rowData The emoji rows data
 * @return {Array}
 */
const getRowsToRender = (scrollPosition, visibleRowCount, rowData) => {
    const minVisibleRow = scrollPosition > ROW_HEIGHT_RAW ? Math.floor(scrollPosition / ROW_HEIGHT_RAW) : 0;
    const start = minVisibleRow >= ROW_RENDER_BUFFER_COUNT ? minVisibleRow - ROW_RENDER_BUFFER_COUNT : minVisibleRow;
    const end = minVisibleRow + visibleRowCount + ROW_RENDER_BUFFER_COUNT;
    const rows = rowData.slice(start, end);
    return rows;
};

/**
 * Create a row element from the row data.
 *
 * @method
 * @param {Object} rowData The emoji row data
 * @return {Element}
 */
const createRowElement = async(rowData) => {
    let row = null;
    if (rowData.type === ROW_TYPE.HEADER) {
        row = await createHeaderRow(rowData.index, rowData.data.displayName);
    } else {
        row = await createEmojiRow(rowData.index, rowData.data);
    }

    row.style.position = 'absolute';
    row.style.left = 0;
    row.style.right = 0;
    row.style.top = `${rowData.index * ROW_HEIGHT_RAW}px`;

    return row;
};

/**
 * Check if the given rows match.
 *
 * @method
 * @param {Object} a The first row
 * @param {Object} b The second row
 * @return {Bool}
 */
const doRowsMatch = (a, b) => {
    if (a.index !== b.index) {
        return false;
    }

    if (a.type !== b.type) {
        return false;
    }

    if (typeof a.data != typeof b.data) {
        return false;
    }

    if (a.type === ROW_TYPE.HEADER) {
        return a.data.name === b.data.name;
    } else {
        if (a.data.length !== b.data.length) {
            return false;
        }

        for (let i = 0; i < a.data.length; i++) {
            if (a.data[i].unified != b.data[i].unified) {
                return false;
            }
        }
    }

    return true;
};

/**
 * Update the visible rows. Deletes any row elements that should no longer
 * be visible and creates the newly visible row elements. Any rows that haven't
 * changed visibility will be left untouched.
 *
 * @method
 * @param {Element} rowContainer The container element for the emoji rows
 * @param {Array} currentRows List of row data that matches the currently visible rows
 * @param {Array} nextRows List of row data containing the new list of rows to be made visible
 */
const renderRows = async(rowContainer, currentRows, nextRows) => {
    // We need to add any rows that are in nextRows but not in currentRows.
    const toAdd = nextRows.filter(nextRow => !currentRows.some(currentRow => doRowsMatch(currentRow, nextRow)));
    // Remember which rows will still be visible so that we can insert our element in the correct place in the DOM.
    let toKeep = currentRows.filter(currentRow => nextRows.some(nextRow => doRowsMatch(currentRow, nextRow)));
    // We need to remove any rows that are in currentRows but not in nextRows.
    const toRemove = currentRows.filter(currentRow => !nextRows.some(nextRow => doRowsMatch(currentRow, nextRow)));
    const toRemoveElements = toRemove.map(rowData => rowContainer.querySelectorAll(`[data-row="${rowData.index}"]`));

    // Render all of the templates first.
    const rows = await Promise.all(toAdd.map(rowData => createRowElement(rowData)));

    rows.forEach((row, index) => {
        const rowData = toAdd[index];
        let nextRowIndex = null;

        for (let i = 0; i < toKeep.length; i++) {
            const candidate = toKeep[i];
            if (candidate.index > rowData.index) {
                nextRowIndex = i;
                break;
            }
        }

        // Make sure the elements get added to the DOM in the correct order (ascending by row data index)
        // so that they appear naturally in the tab order.
        if (nextRowIndex !== null) {
            const nextRowData = toKeep[nextRowIndex];
            const nextRowNode = rowContainer.querySelector(`[data-row="${nextRowData.index}"]`);

            rowContainer.insertBefore(row, nextRowNode);
            toKeep.splice(nextRowIndex, 0, toKeep);
        } else {
            toKeep.push(rowData);
            rowContainer.appendChild(row);
        }
    });

    toRemoveElements.forEach(rows => {
        for (let i = 0; i < rows.length; i++) {
            const row = rows[i];
            rowContainer.removeChild(row);
        }
    });
};

/**
 * Build a function to render the visible emoji rows for a given scroll
 * position.
 *
 * @method
 * @param {Element} rowContainer The container element for the emoji rows
 * @return {Function}
 */
const generateRenderRowsAtPositionFunction = (rowContainer) => {
    let currentRows = [];
    let nextRows = [];
    let rowCount = 0;
    let isRendering = false;
    const renderNextRows = async() => {
        if (!nextRows.length) {
            return;
        }

        if (isRendering) {
            return;
        }

        isRendering = true;
        const nextRowsToRender = nextRows.slice();
        nextRows = [];

        await renderRows(rowContainer, currentRows, nextRowsToRender);
        currentRows = nextRowsToRender;
        isRendering = false;
        renderNextRows();
    };

    return (scrollPosition, rowData, rowLimit = VISIBLE_ROW_COUNT) => {
        nextRows = getRowsToRender(scrollPosition, rowLimit, rowData);
        renderNextRows();

        if (rowCount !== rowData.length) {
            // Adjust the height of the container to match the number of rows.
            rowContainer.style.height = `${rowData.length * ROW_HEIGHT_RAW}px`;
        }

        rowCount = rowData.length;
    };
};

/**
 * Show the search results container and hide the emoji container.
 *
 * @method
 * @param {Element} emojiContainer The emojis container
 * @param {Element} searchResultsContainer The search results container
 */
const showSearchResults = (emojiContainer, searchResultsContainer) => {
    searchResultsContainer.classList.remove('hidden');
    emojiContainer.classList.add('hidden');
};

/**
 * Hide the search result container and show the emojis container.
 *
 * @method
 * @param {Element} emojiContainer The emojis container
 * @param {Element} searchResultsContainer The search results container
 * @param {Element} searchInput The search input
 */
const clearSearch = (emojiContainer, searchResultsContainer, searchInput) => {
    searchResultsContainer.classList.add('hidden');
    emojiContainer.classList.remove('hidden');
    searchInput.value = '';
};

/**
 * Build function to handle mouse hovering an emoji. Shows the preview.
 *
 * @method
 * @param {Element} emojiPreview The emoji preview element
 * @param {Element} emojiShortName The emoji short name element
 * @return {Function}
 */
const getHandleMouseEnter = (emojiPreview, emojiShortName) => {
    return (e) => {
        const target = e.target;
        if (isEmojiElement(target)) {
            emojiShortName.textContent = target.getAttribute('data-short-names');
            emojiPreview.textContent = target.textContent;
        }
    };
};

/**
 * Build function to handle mouse leaving an emoji. Removes the preview.
 *
 * @method
 * @param {Element} emojiPreview The emoji preview element
 * @param {Element} emojiShortName The emoji short name element
 * @return {Function}
 */
const getHandleMouseLeave = (emojiPreview, emojiShortName) => {
    return (e) => {
        const target = e.target;
        if (isEmojiElement(target)) {
            emojiShortName.textContent = '';
            emojiPreview.textContent = '';
        }
    };
};

/**
 * Build the function to handle a user clicking something in the picker.
 *
 * The function currently handles clicking on the category selector or selecting
 * a specific emoji.
 *
 * @method
 * @param {Number} recentEmojiRowCount Number of rows of recent emojis
 * @param {Element} emojiContainer Container element for the visible of emojis
 * @param {Element} searchResultsContainer Contaienr element for the search results
 * @param {Element} searchInput Search input element
 * @param {Function} selectCallback Callback function to execute when a user selects an emoji
 * @param {Function} renderAtPosition Render function to display current visible emojis
 * @return {Function}
 */
const getHandleClick = (
    recentEmojiRowCount,
    emojiContainer,
    searchResultsContainer,
    searchInput,
    selectCallback,
    renderAtPosition
) => {
    return (e, rowData, categoryScrollPositions) => {
        const target = e.target;
        let newRowData = rowData;
        let newCategoryScrollPositions = categoryScrollPositions;

        // Hide the search results if they are visible.
        clearSearch(emojiContainer, searchResultsContainer, searchInput);

        if (isEmojiElement(target)) {
            // Emoji selected.
            const unified = target.getAttribute('data-unified');
            const shortnames = target.getAttribute('data-short-names').replace(/:/g, '').split(' ');
            // Build the emoji data from the selected element.
            const emojiData = {unified, shortnames};
            const currentScrollTop = emojiContainer.scrollTop;
            const isRecentEmojiRowVisible = emojiContainer.querySelector(`[data-row="${recentEmojiRowCount - 1}"]`) !== null;
            // Save the selected emoji in the recent emojis list.
            [newRowData, recentEmojiRowCount] = addRecentEmoji(rowData, recentEmojiRowCount, emojiData);
            // Re-index the category scroll positions because the additional recent emoji may have
            // changed their positions.
            newCategoryScrollPositions = getCategoryScrollPositionsFromRowData(newRowData);

            if (isRecentEmojiRowVisible) {
                // If the list of recent emojis is currently visible then we need to re-render the emojis
                // to update the display and show the newly selected recent emoji.
                renderAtPosition(currentScrollTop, newRowData);
            }

            // Call the client's callback function with the selected emoji.
            selectCallback(target.textContent);
            // Return the newly calculated row data and scroll positions.
            return [newRowData, newCategoryScrollPositions];
        }

        const categorySelector = findCategorySelectorFromElement(target);
        if (categorySelector) {
            // Category selector.
            const selectedCategory = categorySelector.getAttribute('data-category');
            const position = categoryScrollPositions[selectedCategory];
            // Scroll the container to the selected category. This will trigger the
            // on scroll handler to re-render the visibile emojis.
            emojiContainer.scrollTop = position;
        }

        return [newRowData, newCategoryScrollPositions];
    };
};

/**
 * Build the function that handles scrolling of the emoji container to display the
 * correct emojis.
 *
 * We render the emoji rows as they are needed rather than all up front so that we
 * can avoid adding tends of thousands of elements to the DOM unnecessarily which
 * would bog down performance.
 *
 * @method
 * @param {Element} root The picker root element
 * @param {Number} currentVisibleRowScrollPosition The current scroll position of the container
 * @param {Element} emojiContainer The emojis container element
 * @param {Object} initialCategoryScrollPositions Scroll positions for each category
 * @param {Function} renderAtPosition Function to render the appropriate emojis for a scroll position
 * @return {Function}
 */
const getHandleScroll = (
    root,
    currentVisibleRowScrollPosition,
    emojiContainer,
    initialCategoryScrollPositions,
    renderAtPosition
) => {
    // Scope some local variables to track the scroll positions of the categories. We need to
    // recalculate these because adding recent emojis can change those positions by adding
    // additional rows.
    let [
        currentCategoryElement,
        previousCategoryPosition,
        nextCategoryPosition
    ] = getCategoryByScrollPosition(root, emojiContainer.scrollTop, initialCategoryScrollPositions);

    return (categoryScrollPositions, rowData) => {
        const newScrollPosition = emojiContainer.scrollTop;
        const upperScrollBound = currentVisibleRowScrollPosition + ROW_HEIGHT_RAW;
        const lowerScrollBound = currentVisibleRowScrollPosition - ROW_HEIGHT_RAW;
        // We only need to update the active category indicator if the user has scrolled into a
        // new category scroll position.
        const updateActiveCategory = (newScrollPosition >= nextCategoryPosition) ||
                       (newScrollPosition < previousCategoryPosition);
        // We only need to render new emoji rows if the user has scrolled far enough that a new row
        // would be visible (i.e. they've scrolled up or down more than 40px - the height of a row).
        const updateRenderRows = (newScrollPosition < lowerScrollBound) || (newScrollPosition > upperScrollBound);

        if (updateActiveCategory) {
            // New category is visible so update the active category selector and re-index the
            // positions incase anything has changed.
            [
                currentCategoryElement,
                previousCategoryPosition,
                nextCategoryPosition
            ] = getCategoryByScrollPosition(root, newScrollPosition, categoryScrollPositions);
            setCategorySelectorActive(root, currentCategoryElement);
        }

        if (updateRenderRows) {
            // A new row should be visible so re-render the visible emojis at this new position.
            // We request an animation frame from the browser so that we're not blocking anything.
            // The animation only needs to occur as soon as the browser is ready not immediately.
            requestAnimationFrame(() => {
                renderAtPosition(newScrollPosition, rowData);
                // Remember the updated position.
                currentVisibleRowScrollPosition = newScrollPosition;
            });
        }
    };
};

/**
 * Build the function that handles search input from the user.
 *
 * @method
 * @param {Element} searchInput The search input element
 * @param {Element} searchResultsContainer Container element to display the search results
 * @param {Element} emojiContainer Container element for the emoji rows
 * @return {Function}
 */
const getHandleSearch = (searchInput, searchResultsContainer, emojiContainer) => {
    const rowContainer = searchResultsContainer.querySelector(SELECTORS.ROW_CONTAINER);
    // Build a render function for the search results.
    const renderSearchResultsAtPosition = generateRenderRowsAtPositionFunction(rowContainer);
    searchResultsContainer.appendChild(rowContainer);

    return async() => {
        const searchTerm = searchInput.value.toLowerCase();

        if (searchTerm) {
            // Display the search results container and hide the emojis container.
            showSearchResults(emojiContainer, searchResultsContainer);

            // Find which emojis match the user's search input.
            const matchingEmojis = Object.keys(EmojiData.byShortName).reduce((carry, shortName) => {
                if (shortName.includes(searchTerm)) {
                    carry.push({
                        shortnames: [shortName],
                        unified: EmojiData.byShortName[shortName]
                    });
                }
                return carry;
            }, []);

            const searchResultsString = await getString('searchresults', 'core');
            const rowData = createRowDataForCategory(searchResultsString, searchResultsString, matchingEmojis, 0);
            // Show the emoji rows for the search results.
            renderSearchResultsAtPosition(0, rowData, rowData.length);
        } else {
            // Hide the search container and show the emojis container.
            clearSearch(emojiContainer, searchResultsContainer, searchInput);
        }
    };
};

/**
 * Register the emoji picker event listeners.
 *
 * @method
 * @param {Element} root The picker root element
 * @param {Element} emojiContainer Root element containing the list of visible emojis
 * @param {Function} renderAtPosition Function to render the visible emojis at a given scroll position
 * @param {Number} currentVisibleRowScrollPosition What is the current scroll position
 * @param {Function} selectCallback Function to execute when the user picks an emoji
 * @param {Object} categoryScrollPositions Scroll positions for where each of the emoji categories begin
 * @param {Array} rowData Data representing each of the display rows for hte emoji container
 * @param {Number} recentEmojiRowCount Number of rows of recent emojis
 */
const registerEventListeners = (
    root,
    emojiContainer,
    renderAtPosition,
    currentVisibleRowScrollPosition,
    selectCallback,
    categoryScrollPositions,
    rowData,
    recentEmojiRowCount
) => {
    const searchInput = root.querySelector(SELECTORS.SEARCH_INPUT);
    const searchResultsContainer = root.querySelector(SELECTORS.SEARCH_RESULTS_CONTAINER);
    const emojiPreview = root.querySelector(SELECTORS.EMOJI_PREVIEW);
    const emojiShortName = root.querySelector(SELECTORS.EMOJI_SHORT_NAME);
    // Build the click handler function.
    const clickHandler = getHandleClick(
        recentEmojiRowCount,
        emojiContainer,
        searchResultsContainer,
        searchInput,
        selectCallback,
        renderAtPosition
    );
    // Build the scroll handler function.
    const scrollHandler = getHandleScroll(
        root,
        currentVisibleRowScrollPosition,
        emojiContainer,
        categoryScrollPositions,
        renderAtPosition
    );
    const searchHandler = getHandleSearch(searchInput, searchResultsContainer, emojiContainer);

    // Mouse enter/leave events to show the emoji preview on hover or focus.
    root.addEventListener('focus', getHandleMouseEnter(emojiPreview, emojiShortName), true);
    root.addEventListener('blur', getHandleMouseLeave(emojiPreview, emojiShortName), true);
    root.addEventListener('mouseenter', getHandleMouseEnter(emojiPreview, emojiShortName), true);
    root.addEventListener('mouseleave', getHandleMouseLeave(emojiPreview, emojiShortName), true);
    // User selects an emoji or clicks on one of the emoji category selectors.
    root.addEventListener('click', e => {
        // Update the row data and category scroll positions because they may have changes if the
        // user selects an emoji which updates the recent emojis list.
        [rowData, categoryScrollPositions] = clickHandler(e, rowData, categoryScrollPositions);
    });
    // Throttle the scroll event to only execute once every 50 milliseconds to prevent performance issues
    // in the browser when re-rendering the picker emojis. The scroll event fires a lot otherwise.
    emojiContainer.addEventListener('scroll', throttle(() => scrollHandler(categoryScrollPositions, rowData), 50));
    // Debounce the search input so that it only executes 200 milliseconds after the user has finished typing.
    searchInput.addEventListener('input', debounce(searchHandler, 200));
};

/**
 * Initialise the emoji picker.
 *
 * @method
 * @param {Element} root The root element for the picker
 * @param {Function} selectCallback Callback for when the user selects an emoji
 */
export default (root, selectCallback) => {
    const emojiContainer = root.querySelector(SELECTORS.EMOJIS_CONTAINER);
    const rowContainer = emojiContainer.querySelector(SELECTORS.ROW_CONTAINER);
    const recentEmojis = getRecentEmojis();
    // Add the recent emojis category to the list of standard categories.
    const allData = [{
        name: 'Recent',
        emojis: recentEmojis
    }, ...EmojiData.byCategory];
    let rowData = [];
    let recentEmojiRowCount = 0;

    /**
     * Split categories data into rows which represent how they will be displayed in the
     * picker. Each category will add a row containing the display name for the category
     * and a row for every 9 emojis in the category. The row data will be used to calculate
     * which emojis should be visible in the picker at any given time.
     *
     * E.g.
     * input = [
     *  {name: 'example1', emojis: [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12]},
     *  {name: 'example2', emojis: [13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23]},
     * ]
     * output = [
     *      {type: 'categoryName': data: 'Example 1'},
     *      {type: 'emojiRow': data: [1, 2, 3, 4, 5, 6, 7, 8, 9]},
     *      {type: 'emojiRow': data: [10, 11, 12]},
     *      {type: 'categoryName': data: 'Example 2'},
     *      {type: 'emojiRow': data: [13, 14, 15, 16, 17, 18, 19, 20, 21]},
     *      {type: 'emojiRow': data: [22, 23]},
     * ]
     */
    allData.forEach(category => {
        const categorySelector = getCategorySelectorByCategoryName(root, category.name);
        // Get the display name from the category selector button so that we don't need to
        // send an ajax request for the string.
        const categoryDisplayName = categorySelector.title;
        const categoryRowData = createRowDataForCategory(category.name, categoryDisplayName, category.emojis, rowData.length);

        if (category.name === 'Recent') {
            // Remember how many recent emoji rows there are because it needs to be used to
            // re-index the row data later when we're adding more recent emojis.
            recentEmojiRowCount = categoryRowData.length;
        }

        rowData = rowData.concat(categoryRowData);
    });

    // Index the row data so that we can calculate which rows should be visible.
    rowData = addIndexesToRowData(rowData);
    // Calculate the scroll positions for each of the categories within the emoji container.
    // These are used to know where to jump to when the user selects a specific category.
    const categoryScrollPositions = getCategoryScrollPositionsFromRowData(rowData);
    const renderAtPosition = generateRenderRowsAtPositionFunction(rowContainer);
    // Display the initial set of emojis.
    renderAtPosition(0, rowData);

    registerEventListeners(
        root,
        emojiContainer,
        renderAtPosition,
        0,
        selectCallback,
        categoryScrollPositions,
        rowData,
        recentEmojiRowCount
    );
};
