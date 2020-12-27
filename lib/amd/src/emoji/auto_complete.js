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
 * Emoji auto complete.
 *
 * @copyright  2019 Ryan Wyllie <ryan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
import * as EmojiData from 'core/emoji/data';
import {render as renderTemplate} from 'core/templates';
import {debounce} from 'core/utils';
import LocalStorage from 'core/localstorage';
import KeyCodes from 'core/key_codes';

const INPUT_DEBOUNCE_TIMER = 200;
const SUGGESTION_LIMIT = 50;
const MAX_RECENT_COUNT = 27;
const RECENT_EMOJIS_STORAGE_KEY = 'moodle-recent-emojis';

const SELECTORS = {
    EMOJI_BUTTON: '[data-region="emoji-button"]',
    ACTIVE_EMOJI_BUTTON: '[data-region="emoji-button"].active',
};

/**
 * Get the list of recent emojis data from local storage.
 *
 * @return {Array}
 */
const getRecentEmojis = () => {
    const storedData = LocalStorage.get(RECENT_EMOJIS_STORAGE_KEY);
    return storedData ? JSON.parse(storedData) : [];
};

/**
 * Add an emoji data to the set of recent emojis. The new set of recent emojis are
 * saved in local storage.
 *
 * @param {String} unified The char chodes for the emoji
 * @param {String} shortName The emoji short name
 */
const addRecentEmoji = (unified, shortName) => {
    const newEmoji = {
        unified,
        shortnames: [shortName]
    };
    const recentEmojis = getRecentEmojis();
    // Add the new emoji to the start of the list of recent emojis.
    let newRecentEmojis = [newEmoji, ...recentEmojis.filter(emoji => emoji.unified != newEmoji.unified)];
    // Limit the number of recent emojis.
    newRecentEmojis = newRecentEmojis.slice(0, MAX_RECENT_COUNT);

    LocalStorage.set(RECENT_EMOJIS_STORAGE_KEY, JSON.stringify(newRecentEmojis));
};

/**
 * Get the actual emoji string from the short name.
 *
 * @param {String} shortName Emoji short name
 * @return {String|null}
 */
const getEmojiTextFromShortName = (shortName) => {
    const unified = EmojiData.byShortName[shortName];

    if (unified) {
        const charCodes = unified.split('-').map(code => `0x${code}`);
        return String.fromCodePoint.apply(null, charCodes);
    } else {
        return null;
    }
};

/**
 * Render the auto complete list for the given short names.
 *
 * @param {Element} root The root container for the emoji auto complete
 * @param {Array} shortNames The list of short names for emoji suggestions to show
 */
const render = async (root, shortNames) => {
    const renderContext = {
        emojis: shortNames.map((shortName, index) => {
            return {
                active: index === 0,
                emojitext: getEmojiTextFromShortName(shortName),
                displayshortname: `:${shortName}:`,
                shortname: shortName,
                unified: EmojiData.byShortName[shortName]
            };
        })
    };
    const html = await renderTemplate('core/emoji/auto_complete', renderContext);
    root.innerHTML = html;
};

/**
 * Get the list of emoji short names that include the given search term. If
 * the search term is an empty string then the list of recently used emojis
 * will be returned.
 *
 * @param {String} searchTerm Text to match on
 * @param {Number} limit Maximum number of results to return
 * @return {Array}
 */
const searchEmojis = (searchTerm, limit) => {
    if (searchTerm === '') {
        return getRecentEmojis().map(data => data.shortnames[0]).slice(0, limit);
    } else {
        searchTerm = searchTerm.toLowerCase();
        return Object.keys(EmojiData.byShortName)
                .filter(shortName => shortName.includes(searchTerm))
                .slice(0, limit);
    }
};

/**
 * Get the current word at the given position (index) within the text.
 *
 * @param {String} text The text to process
 * @param {Number} position The position (index) within the text to match the word
 * @return {String}
 */
const getWordFromPosition = (text, position) => {
    const startMatches = text.slice(0, position).match(/(\S*)$/);
    const endMatches = text.slice(position).match(/^(\S*)/);
    let startText = '';
    let endText = '';

    if (startMatches) {
        startText = startMatches[startMatches.length - 1];
    }

    if (endMatches) {
        endText = endMatches[endMatches.length - 1];
    }

    return `${startText}${endText}`;
};

/**
 * Check if the given text is a full short name, i.e. has leading and trialing colon
 * characters.
 *
 * @param {String} text The text to process
 * @return {Bool}
 */
const isCompleteShortName = text => /^:[^:\s]+:$/.test(text);

/**
 * Check if the given text is a partial short name, i.e. has a leading colon but no
 * trailing colon.
 *
 * @param {String} text The text to process
 * @return {Bool}
 */
const isPartialShortName = text => /^:[^:\s]*$/.test(text);

/**
 * Remove the colon characters from the given text.
 *
 * @param {String} text The text to process
 * @return {String}
 */
const getShortNameFromText = text => text.replace(/:/g, '');

/**
 * Get the currently active emoji button element in the list of suggestions.
 *
 * @param {Element} root The emoji auto complete container element
 * @return {Element|null}
 */
const getActiveEmojiSuggestion = (root) => {
    return root.querySelector(SELECTORS.ACTIVE_EMOJI_BUTTON);
};

/**
 * Make the previous sibling of the current active emoji active.
 *
 * @param {Element} root The emoji auto complete container element
 */
const selectPreviousEmojiSuggestion = (root) => {
    const activeEmojiSuggestion = getActiveEmojiSuggestion(root);
    const previousSuggestion = activeEmojiSuggestion.previousElementSibling;

    if (previousSuggestion) {
        activeEmojiSuggestion.classList.remove('active');
        previousSuggestion.classList.add('active');
        previousSuggestion.scrollIntoView({behaviour: 'smooth', inline: 'center'});
    }
};

/**
 * Make the next sibling to the current active emoji active.
 *
 * @param {Element} root The emoji auto complete container element
 */
const selectNextEmojiSuggestion = (root) => {
    const activeEmojiSuggestion = getActiveEmojiSuggestion(root);
    const nextSuggestion = activeEmojiSuggestion.nextElementSibling;

    if (nextSuggestion) {
        activeEmojiSuggestion.classList.remove('active');
        nextSuggestion.classList.add('active');
        nextSuggestion.scrollIntoView({behaviour: 'smooth', inline: 'center'});
    }
};

/**
 * Trigger the select callback for the given emoji button element.
 *
 * @param {Element} element The emoji button element
 * @param {Function} selectCallback The callback for when the user selects an emoji
 */
const selectEmojiElement = (element, selectCallback) => {
    const shortName = element.getAttribute('data-short-name');
    const unified = element.getAttribute('data-unified');
    addRecentEmoji(unified, shortName);
    selectCallback(element.innerHTML.trim());
};

/**
 * Initialise the emoji auto complete.
 *
 * @param {Element} root The root container element for the auto complete
 * @param {Element} textArea The text area element to monitor for auto complete
 * @param {Function} hasSuggestionCallback Callback for when there are auto-complete suggestions
 * @param {Function} selectCallback Callback for when the user selects an emoji
 */
export default (root, textArea, hasSuggestionCallback, selectCallback) => {
    let hasSuggestions = false;
    let previousSearchText = '';

    // Debounce the listener so that each keypress delays the execution of the handler. The
    // handler should only run 200 milliseconds after the last keypress.
    textArea.addEventListener('keyup', debounce(() => {
        // This is a "keyup" listener so that it only executes after the text area value
        // has been updated.
        const text = textArea.value;
        const cursorPos = textArea.selectionStart;
        const searchText = getWordFromPosition(text, cursorPos);

        if (searchText === previousSearchText) {
            // Nothing has changed so no need to take any action.
            return;
        } else {
            previousSearchText = searchText;
        }

        if (isCompleteShortName(searchText)) {
            // If the user has entered a full short name (with leading and trialing colons)
            // then see if we can find a match for it and auto complete it.
            const shortName = getShortNameFromText(searchText);
            const emojiText = getEmojiTextFromShortName(shortName);
            hasSuggestions = false;
            if (emojiText) {
                addRecentEmoji(EmojiData.byShortName[shortName], shortName);
                selectCallback(emojiText);
            }
        } else if (isPartialShortName(searchText)) {
            // If the user has entered a partial short name (leading colon but no trailing) then
            // search on the text to see if we can find some suggestions for them.
            const suggestions = searchEmojis(getShortNameFromText(searchText), SUGGESTION_LIMIT);

            if (suggestions.length) {
                render(root, suggestions);
                hasSuggestions = true;
            } else {
                hasSuggestions = false;
            }
        } else {
            hasSuggestions = false;
        }

        hasSuggestionCallback(hasSuggestions);
    }, INPUT_DEBOUNCE_TIMER));

    textArea.addEventListener('keydown', (e) => {
        if (hasSuggestions) {
            const isModifierPressed = (e.shiftKey || e.metaKey || e.altKey || e.ctrlKey);
            if (!isModifierPressed) {
                switch (e.which) {
                    case KeyCodes.escape:
                        // Escape key closes the auto complete.
                        hasSuggestions = false;
                        hasSuggestionCallback(false);
                        break;
                    case KeyCodes.arrowLeft:
                        // Arrow keys navigate through the list of suggetions.
                        selectPreviousEmojiSuggestion(root);
                        e.preventDefault();
                        break;
                    case KeyCodes.arrowRight:
                        // Arrow keys navigate through the list of suggetions.
                        selectNextEmojiSuggestion(root);
                        e.preventDefault();
                        break;
                    case KeyCodes.enter:
                        // Enter key selects the current suggestion.
                        selectEmojiElement(getActiveEmojiSuggestion(root), selectCallback);
                        e.preventDefault();
                        e.stopPropagation();
                        break;
                }
            }
        }
    });

    root.addEventListener('click', (e) => {
        const target = e.target;
        if (target.matches(SELECTORS.EMOJI_BUTTON)) {
            selectEmojiElement(target, selectCallback);
        }
    });
};