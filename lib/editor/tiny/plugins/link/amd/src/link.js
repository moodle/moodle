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
 * Link helper for Tiny Link plugin.
 *
 * @module      tiny_link/link
 * @copyright   2023 Huong Nguyen <huongnv13@gmail.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import Templates from 'core/templates';
import Pending from 'core/pending';
import Selectors from 'tiny_link/selectors';

/**
 * Handle insertion of a new link, or update of an existing one.
 *
 * @param {Element} currentForm
 * @param {TinyMCE} editor
 */
export const setLink = (currentForm, editor) => {
    const input = currentForm.querySelector(Selectors.elements.urlEntry);
    let value = input.value;

    if (value !== '') {
        const pendingPromise = new Pending('tiny_link/setLink');
        // We add a prefix if it is not already prefixed.
        value = value.trim();
        const expr = new RegExp(/^[a-zA-Z]*\.*\/|^#|^[a-zA-Z]*:/);
        if (!expr.test(value)) {
            value = 'http://' + value;
        }

        // Add the link.
        setLinkOnSelection(currentForm, editor, value).then(pendingPromise.resolve);
    }
};

/**
 * Handle unlink of a link
 *
 * @param {TinyMCE} editor
 */
export const unSetLink = (editor) => {
    if (editor.hasPlugin('rtc', true)) {
        editor.execCommand('unlink');
    } else {
        const dom = editor.dom;
        const selection = editor.selection;
        const bookmark = selection.getBookmark();
        const rng = selection.getRng().cloneRange();
        const startAnchorElm = dom.getParent(rng.startContainer, 'a[href]', editor.getBody());
        const endAnchorElm = dom.getParent(rng.endContainer, 'a[href]', editor.getBody());
        if (startAnchorElm) {
            rng.setStartBefore(startAnchorElm);
        }
        if (endAnchorElm) {
            rng.setEndAfter(endAnchorElm);
        }
        selection.setRng(rng);
        editor.execCommand('unlink');
        selection.moveToBookmark(bookmark);
    }
};

/**
 * Final step setting the anchor on the selection.
 *
 * @param {Element} currentForm
 * @param {TinyMCE} editor
 * @param {String} url URL the link will point to.
 */
const setLinkOnSelection = async(currentForm, editor, url) => {
    const urlText = currentForm.querySelector(Selectors.elements.urlText);
    const target = currentForm.querySelector(Selectors.elements.openInNewWindow);
    const selectedNode = editor.selection.getNode();
    const isImage = selectedNode && selectedNode.nodeName.toLowerCase() === 'img';
    let textToDisplay = urlText.value.replace(/(<([^>]+)>)/gi, "").trim();

    if (textToDisplay === '') {
        textToDisplay = url;
    }

    const context = {
        url: url,
        newwindow: target.checked,
    };
    if (urlText.getAttribute('data-link-on-element') || isImage) {
        context.title = textToDisplay;
        context.name = selectedNode.outerHTML;
    } else {
        context.name = textToDisplay;
    }
    const {html} = await Templates.renderForPromise('tiny_link/embed_link', context);
    const currentLink = getSelectedLink(editor);
    if (currentLink) {
        currentLink.outerHTML = html;
    } else {
        editor.insertContent(html);
    }
};

/**
 * Get current link data.
 *
 * @param {TinyMCE} editor
 * @returns {{}}
 */
export const getCurrentLinkData = (editor) => {
    let properties = {};
    const link = getSelectedLink(editor);
    if (link) {
        const url = link.getAttribute('href');
        const target = link.getAttribute('target');
        const textToDisplay = link.innerText;
        const title = link.getAttribute('title');

        if (url !== '') {
            properties.url = url;
        }
        if (target === '_blank') {
            properties.newwindow = true;
        }
        if (title && title !== '') {
            properties.urltext = title.trim();
        } else if (textToDisplay !== '') {
            properties.urltext = textToDisplay.trim();
        }
    } else {
        // Check if the user is selecting some text before clicking on the Link button.
        const selectedNode = editor.selection.getNode();
        if (selectedNode) {
            const textToDisplay = getTextSelection(editor);
            if (textToDisplay !== '') {
                properties.urltext = textToDisplay.trim();
                properties.hasTextToDisplay = true;
                properties.hasPlainTextSelected = true;
            } else {
                if (selectedNode.getAttribute('data-mce-selected')) {
                    properties.setLinkOnElement = true;
                }
            }
        }
    }

    return properties;
};

/**
 * Get selected link.
 *
 * @param {TinyMCE} editor
 * @returns {Element}
 */
const getSelectedLink = (editor) => {
    return getAnchorElement(editor);
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
    return editor.dom.getParent(selectedElm, 'a[href]');
};

/**
 * Get only the selected text.
 * In some cases, window.getSelection() is not run as expected. We should only get the text value
 * For ex: <img src="" alt="XYZ">Some text here
 *          window.getSelection() will return XYZSome text here
 *
 * @param {TinyMCE} editor
 * @return {string} Selected text
 */
const getTextSelection = (editor) => {
    let selText = '';
    const sel = editor.selection.getSel();
    const rangeCount = sel.rangeCount;
    if (rangeCount) {
        let rangeTexts = [];
        for (let i = 0; i < rangeCount; ++i) {
            rangeTexts.push('' + sel.getRangeAt(i));
        }
        selText = rangeTexts.join('');
    }

    return selText;
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
