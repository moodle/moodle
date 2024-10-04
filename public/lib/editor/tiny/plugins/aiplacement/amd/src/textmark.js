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
 * Tiny AI Mark Changed text.
 *
 * This module marks text that was returned by the AI service
 * and that has been changed by a human prior to being inserted.
 *
 * @module      tiny_aiplacement/textmark
 * @copyright   2023 Matt Porritt <matt.porritt@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

export default class TinyAiTextMarker {
    /**
     * Finds the longest common subsequence of two strings.
     *
     * @param {string} a The first string.
     * @param {string} b The second string.
     * @returns {string} The longest common subsequence.
     */
    static longestCommonSubsequence(a, b) {
        const lengths = Array(a.length + 1)
            .fill(null)
            .map(() => Array(b.length + 1).fill(0));

        for (let i = 0; i < a.length; i++) {
            for (let j = 0; j < b.length; j++) {
                if (a[i] === b[j]) {
                    lengths[i + 1][j + 1] = lengths[i][j] + 1;
                } else {
                    lengths[i + 1][j + 1] = Math.max(lengths[i + 1][j], lengths[i][j + 1]);
                }
            }
        }

        let i = a.length;
        let j = b.length;
        let lcs = '';

        while (i > 0 && j > 0) {
            if (a[i - 1] === b[j - 1]) {
                lcs = a[i - 1] + lcs;
                i--;
                j--;
            } else if (lengths[i - 1][j] > lengths[i][j - 1]) {
                i--;
            } else {
                j--;
            }
        }

        return lcs;
    }

    /**
     * Finds the differences between the original and edited text using the LCS algorithm.
     *
     * @param {string} originalText The original text.
     * @param {string} editedText The edited text.
     * @returns {Array<Object>} An array of difference objects with start, end, and text properties.
     */
    static findDifferences(originalText, editedText) {
        const lcs = TinyAiTextMarker.longestCommonSubsequence(originalText, editedText);
        let differences = [];
        let i = 0;
        let j = 0;

        for (let k = 0; k < lcs.length; k++) {
            let commonChar = lcs[k];

            while (originalText[i] !== commonChar || editedText[j] !== commonChar) {
                let start = j;
                while (editedText[j] !== commonChar) {
                    j++;
                }
                let editedSection = editedText.slice(start, j);
                differences.push({start, end: j, text: editedSection});

                while (originalText[i] !== commonChar) {
                    i++;
                }
            }

            i++;
            j++;
        }

        if (j < editedText.length) {
            differences.push({start: j, end: editedText.length, text: editedText.slice(j)});
        }

        return differences;
    }

    /**
     * Wraps the given edited section in a span tag with a 'user-edited' class.
     *
     * @param {string} editedSection The edited section of the text.
     * @returns {Promise<string>} A promise that resolves with the wrapped edited section.
     */
    static async wrapInSpan(editedSection) {
        return new Promise((resolve, reject) => {
            try {
                let wrappedText = `<span class="user-edited">${editedSection}</span>`;
                resolve(wrappedText);
            } catch (error) {
                reject(error);
            }
        });
    }

    /**
     * Wraps the edited sections of the text in span tags with a 'user-edited' class.
     *
     * @param {string} originalText The original text.
     * @param {string} editedText The edited text.
     * @returns {Promise<string>} A promise that resolves with the edited text, where edited sections are wrapped in span tags.
     */
    static async wrapEditedSections(originalText, editedText) {
        let differences = TinyAiTextMarker.findDifferences(originalText, editedText);
        let wrappedText = editedText;

        for (let i = differences.length - 1; i >= 0; i--) {
            let {start, end, text} = differences[i];
            let wrappedSection = await TinyAiTextMarker.wrapInSpan(text);
            wrappedText = wrappedText.slice(0, start) + wrappedSection + wrappedText.slice(end);
        }

        return wrappedText;
    }

}
