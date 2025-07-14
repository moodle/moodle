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
 * Equation helper for Tiny Equation plugin.
 *
 * @module      tiny_equation/equation
 * @copyright   2022 Huong Nguyen <huongnv13@gmail.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import Selectors from 'tiny_equation/selectors';

let sourceEquation = null;

/**
 * Get the source equation.
 * @returns {Object}
 */
export const getSourceEquation = () => sourceEquation;

/**
 * Get selected equation.
 * @param {TinyMCE} editor
 * @returns {boolean}
 */
export const getSelectedEquation = (editor) => {
    const currentSelection = editor.selection.getSel();
    if (!currentSelection) {
        // Do the early return if there is no text selected.
        return false;
    }
    const textSelection = editor.selection.getNode().textContent;
    const currentCaretPos = currentSelection.focusOffset;
    let returnValue = false;

    Selectors.equationPatterns.forEach((pattern) => {
        // For each pattern in turn, find all whole matches (including the delimiters).
        const regexPattern = new RegExp(pattern.source, "g");
        [...textSelection.matchAll(regexPattern)].forEach((matches) => {
            const match = matches[0];
            // Check each occurrence of this match.
            let startIndex = 0;
            const startOuter = textSelection.indexOf(match, startIndex);
            const endOuter = startOuter + match.length;

            // This match is in our current position - fetch the innerMatch data.
            const innerMatch = match.match(pattern);
            if (innerMatch && innerMatch.length) {
                // We need the start and end of the inner match for later.
                const startInner = textSelection.indexOf(innerMatch[1], startOuter);
                const endInner = startInner + innerMatch[1].length;

                // We need to check the caret position before returning the match.
                if (currentCaretPos >= startOuter && currentCaretPos <= endOuter) {
                    // We'll be returning the inner match for use in the editor itself.
                    returnValue = innerMatch[1];

                    // Save all data for later.
                    sourceEquation = {
                        // Inner match data.
                        startInnerPosition: startInner,
                        endInnerPosition: endInner,
                        innerMatch: innerMatch
                    };

                    return;
                }
            }

            // Update the startIndex to match the end of the current match so that we can continue hunting
            // for further matches.
            startIndex = endOuter;
        });
    });

    // We trim the equation when we load it and then add spaces when we save it.
    if (returnValue !== false) {
        returnValue = returnValue.trim();
    } else {
        // Clear the saved source equation.
        sourceEquation = null;
    }

    return returnValue;
};

/**
 * Get current equation data.
 * @param {TinyMCE} editor
 * @returns {{}}
 */
export const getCurrentEquationData = (editor) => {
    let properties = {};
    const equation = getSelectedEquation(editor);
    if (equation) {
        properties.equation = equation;
    }

    return properties;
};

/**
 * Handle insertion of a new equation, or update of an existing one.
 * @param {Element} currentForm
 * @param {TinyMCE} editor
 */
export const setEquation = (currentForm, editor) => {
    const input = currentForm.querySelector(Selectors.elements.equationTextArea);
    const sourceEquation = getSourceEquation();
    let value = input.value;

    if (value !== '') {
        if (sourceEquation) {
            const selectedNode = editor.selection.getNode();
            const text = selectedNode.textContent;
            value = ' ' + value + ' ';
            selectedNode.textContent = text.slice(0, sourceEquation.startInnerPosition)
                + value
                + text.slice(sourceEquation.endInnerPosition);
        } else {
            value = Selectors.delimiters.start + ' ' + value + ' ' + Selectors.delimiters.end;
            editor.insertContent(value);
        }
    }
};
