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
 * Helper for Tiny noautolink plugin.
 *
 * @module      tiny_noautolink/noautolink
 * @copyright   2023 Meirza <meirza.arson@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import Pending from 'core/pending';

const noautolinkClassName = 'nolink';
const noautolinkTagHTML = 'span';
const notificationTimeout = 2000;

/**
 * Handle action.
 *
 * @param {TinyMCE} editor
 * @param {object} messages
 */
export const handleAction = (editor, messages) => {
    const toggleState = isInAnchor(editor, editor.selection.getNode());
    const urlString = getSelectedContent(editor);
    if (!toggleState && urlString !== '') {
        setNoAutoLink(editor, messages, urlString);
    } else if (toggleState) {
        unsetNoAutoLink(editor, messages, urlString);
    } else {
        editor.notificationManager.open({text: messages.infoEmptySelection, type: 'info', timeout: notificationTimeout});
    }
};

/**
 * Display notification feedback when applying the noautolink to the selected text.
 *
 * @param {TinyMCE} editor
 * @param {object} messages
 * @param {String} urlString
 */
const setNoAutoLink = (editor, messages, urlString) => {
    const pendingPromise = new Pending('tiny_noautolink/setNoautolink');
    // Applying the auto-link prevention.
    setNoautolinkOnSelection(editor, urlString)
    .catch(error => {
        editor.notificationManager.open({text: error, type: 'error', timeout: notificationTimeout});
    })
    .finally(() => {
        editor.notificationManager.open({text: messages.infoAddSuccess, type: 'success', timeout: notificationTimeout});
        pendingPromise.resolve();
    });
};

/**
 * Display notification feedback when removing the noautolink to the selected text.
 *
 * @param {TinyMCE} editor
 * @param {object} messages
 */
const unsetNoAutoLink = (editor, messages) => {
    const nodeString = editor.selection.getNode().outerHTML.trim();
    // Convert HTML string to DOM element to get nolink class.
    const wrapper = document.createElement('div');
    wrapper.innerHTML = nodeString;
    const tempElement = wrapper.firstChild;
    if (tempElement.classList.contains('nolink')) {
        const pendingPromise = new Pending('tiny_noautolink/setNoautolink');
        // Removing the auto-link prevention.
        unsetNoautolinkOnSelection(editor, nodeString)
        .catch(error => {
            editor.notificationManager.open({text: error, type: 'error', timeout: notificationTimeout});
            pendingPromise.reject(error); // Handle the error as needed.
        })
        .finally(() => {
            editor.notificationManager.open({text: messages.infoRemoveSuccess, type: 'success', timeout: notificationTimeout});
            pendingPromise.resolve();
        });
    }
};

/**
 * Return the full string based on the position of the cursor within the string.
 *
 * @param {TinyMCE} editor
 * @returns {String}
 */
const getSelectedContent = (editor) => {
    const selection = editor.selection; // Get the selection object.
    let content = selection.getContent({format: 'text'}).trim();
    if (content == '') {
        const range = selection.getRng(); // Get the range object.

        // Check if the cursor is within a text node.
        if (range.startContainer.nodeType === Node.TEXT_NODE) {
            const textContent = range.startContainer.textContent;
            const cursorOffset = range.startOffset;

            // Find the word boundaries around the cursor.
            let wordStart = cursorOffset;
            while (wordStart > 0 && /\S/.test(textContent[wordStart - 1])) {
                wordStart--;
            }

            let wordEnd = cursorOffset;
            while (wordEnd < textContent.length && /\S/.test(textContent[wordEnd])) {
                wordEnd++;
            }

            // Set the selection range to the word.
            selection.setRng({
                startContainer: range.startContainer,
                startOffset: wordStart,
                endContainer: range.startContainer,
                endOffset: wordEnd,
            });
            content = selection.getContent({format: 'text'}).trim();
        }
    }
    return content;
};

/**
 * Wrap the selection with the nolink class.
 *
 * @param {TinyMCE} editor
 * @param {String} url URL the link will point to.
 */
const setNoautolinkOnSelection = async(editor, url) => {
    const newContent = `<${noautolinkTagHTML} class="${noautolinkClassName}">${url}</${noautolinkTagHTML}>`;
    editor.selection.setContent(newContent);

    // Select the new content.
    const currentNode = editor.selection.getNode();
    const currentDOM = editor.dom.select(`${noautolinkTagHTML}.${noautolinkClassName}`, currentNode);
    currentDOM.forEach(function(value, index) {
        if (value.outerHTML == newContent) {
            editor.selection.select(currentDOM[index]);
            return;
        }
    });
};

/**
 * Remove the nolink on the selection.
 *
 * @param {TinyMCE} editor
 * @param {String} url URL the link will point to.
 */
const unsetNoautolinkOnSelection = async(editor, url) => {
    const regex = new RegExp(`</?${noautolinkTagHTML}[^>]*>`, "g");
    url = url.replace(regex, "");
    const currentSpan = editor.dom.getParent(editor.selection.getNode(), noautolinkTagHTML);
    currentSpan.outerHTML = url;
};

/**
 * Get anchor element.
 *
 * @param {TinyMCE} editor
 * @param {Element} selectedElm
 * @returns {Element}
 */
const getAnchorElement = (editor, selectedElm) => {
    selectedElm = selectedElm || editor.selection.getNode();
    return editor.dom.getParent(selectedElm, `${noautolinkTagHTML}.${noautolinkClassName}`);
};


/**
 * Check the current selected element is an anchor or not.
 *
 * @param {TinyMCE} editor
 * @param {Element} selectedElm
 * @returns {boolean}
 */
const isInAnchor = (editor, selectedElm) => getAnchorElement(editor, selectedElm) !== null;

/**
 * Change state of button.
 *
 * @param {TinyMCE} editor
 * @param {function()} toggler
 * @returns {function()}
 */
const toggleState = (editor, toggler) => {
    editor.on('NodeChange', toggler);
    return () => editor.off('NodeChange', toggler);
};

/**
 * Change the active state of button.
 *
 * @param {TinyMCE} editor
 * @returns {function(*): function(): *}
 */
export const toggleActiveState = (editor) => (api) => {
    const updateState = () => api.setActive(!editor.mode.isReadOnly() && isInAnchor(editor, editor.selection.getNode()));
    updateState();
    return toggleState(editor, updateState);
};
