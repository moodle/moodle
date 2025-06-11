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
 * User interface functionality for the word limit plugin for the Moodle TinyMCE 6 editor.
 *
 * @module    tiny_wordlimit/ui
 * @copyright 2023 University of Graz
 * @author    André Menrath <andre.menrath@uni-graz.at>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import {get_string as getString} from 'core/str';

const THEME_SILVER_WORDCOUNT_CLASSNAME = 'tox-statusbar__wordcount';
const WORD_BUFFER_BEFORE_WARNING_STATUS = 10;

export const addWordLimitElement = async(editor, wordlimit) => {
    const wordLimitElement = await constructWordLimitElement(wordlimit);
    let statusbarContainer = editor.getContainer().getElementsByClassName(THEME_SILVER_WORDCOUNT_CLASSNAME)[0];
    statusbarContainer.insertAdjacentHTML('afterend', wordLimitElement);
};

const constructWordLimitElement = async(wordlimit) => {
    const wordlimitString = await getString('maxwordlimit', 'qtype_essay');
    return `
        <div class="tox-statusbar__wordlimit">
            ${wordlimitString}: ${wordlimit}
        </div>
    `;
};

export const updateWordCountAlert = (editor, maxWordLimit) => {
    let wordCount = editor.plugins.wordcount.getCount();
    const wordCountElement = editor.getContainer().getElementsByClassName(THEME_SILVER_WORDCOUNT_CLASSNAME)[0];
    if (maxWordLimit > wordCount + WORD_BUFFER_BEFORE_WARNING_STATUS) {
        wordCountElement.classList.remove('warning');
        wordCountElement.classList.remove('danger');
    } else if (maxWordLimit >= wordCount) {
        wordCountElement.classList.remove('danger');
        wordCountElement.classList.add('warning');
    } else {
        wordCountElement.classList.add('danger');
        wordCountElement.classList.remove('warning');
    }
};
